<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\Club;
use App\Models\Team;
use App\Models\Category;
use App\Models\Season;
use App\Rules\NoAccentsEmail;

class ClubSetupController extends Controller
{
    /**
     * Mostrar el wizard de configuración del club
     */
    public function index(Request $request, $clubId = null)
    {
        $club = null;
        
        if ($clubId) {
            $club = Club::findOrFail($clubId);
            
            // Verificar permisos
            if (!$this->canManageClub($club)) {
                abort(403, 'No tiene permisos para configurar este club');
            }
        }

        return view('club.setup', compact('club'));
    }

    /**
     * Completar la configuración del club
     */
    public function complete(Request $request)
    {
        $validated = $request->validate([
            'clubId' => 'nullable|exists:clubs,id',
            'data' => 'required|array',
            'data.name' => 'required|string|max:255',
            'data.short_name' => 'required|string|max:50',
            'data.founded_year' => 'nullable|integer|min:1900|max:' . date('Y'),
            'data.description' => 'nullable|string',
            'data.colors' => 'nullable|array',
            'data.address' => 'nullable|string',
            'data.phone' => 'nullable|string',
            'data.email' => 'nullable|email',
            'data.categories' => 'nullable|array',
            'data.teams' => 'nullable|array',
            'data.seasons' => 'nullable|array',
        ]);

        DB::beginTransaction();
        
        try {
            $clubData = $validated['data'];
            $clubId = $validated['clubId'];
            
            // Crear o actualizar club
            if ($clubId) {
                $club = Club::findOrFail($clubId);
                $club->update([
                    'name' => $clubData['name'],
                    'short_name' => $clubData['short_name'],
                    'founded_year' => $clubData['founded_year'] ?? null,
                    'description' => $clubData['description'] ?? null,
                    'colors' => $clubData['colors'] ?? null,
                    'address' => $clubData['address'] ?? null,
                    'phone' => $clubData['phone'] ?? null,
                    'email' => $clubData['email'] ?? null,
                    'setup_completed' => true,
                    'setup_completed_at' => now(),
                ]);
            } else {
                $club = Club::create([
                    'name' => $clubData['name'],
                    'short_name' => $clubData['short_name'],
                    'founded_year' => $clubData['founded_year'] ?? null,
                    'description' => $clubData['description'] ?? null,
                    'colors' => $clubData['colors'] ?? null,
                    'address' => $clubData['address'] ?? null,
                    'phone' => $clubData['phone'] ?? null,
                    'email' => $clubData['email'] ?? null,
                    'setup_completed' => true,
                    'setup_completed_at' => now(),
                    'created_by' => Auth::id(),
                ]);
            }

            // Asociar categorías
            if (!empty($clubData['categories'])) {
                $this->associateCategories($club, $clubData['categories']);
            }

            // Crear equipos iniciales
            if (!empty($clubData['teams'])) {
                $this->createInitialTeams($club, $clubData['teams']);
            }

            // Crear temporadas
            if (!empty($clubData['seasons'])) {
                $this->createSeasons($club, $clubData['seasons']);
            }

            // Registrar actividad
            activity()
                ->performedOn($club)
                ->causedBy(Auth::user())
                ->log('Club setup completed');

            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Configuración del club completada exitosamente',
                'club' => $club->load(['categories', 'teams', 'seasons'])
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error completando setup del club: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'club_id' => $clubId,
                'data' => $clubData
            ]);
            
            return response()->json([
                'error' => 'Error completando la configuración del club'
            ], 500);
        }
    }

    /**
     * Obtener datos para un paso específico
     */
    public function getStepData(Request $request, $step)
    {
        try {
            switch ($step) {
                case 'categories':
                    return response()->json([
                        'categories' => Category::active()->get(['id', 'name', 'min_age', 'max_age', 'gender'])
                    ]);
                    
                case 'departments':
                    return response()->json([
                        'departments' => DB::table('departments')
                            ->with('cities')
                            ->get(['id', 'name'])
                    ]);
                    
                default:
                    return response()->json(['error' => 'Paso no válido'], 400);
            }
        } catch (\Exception $e) {
            Log::error('Error obteniendo datos del paso: ' . $e->getMessage());
            return response()->json(['error' => 'Error obteniendo datos'], 500);
        }
    }

    /**
     * Verificar si el usuario puede manejar el club
     */
    private function canManageClub(Club $club): bool
    {
        $user = Auth::user();
        
        // Super admin puede todo
        if ($user->hasRole('super_admin')) {
            return true;
        }
        
        // Admin de liga puede manejar clubes de su liga
        if ($user->hasRole('league_admin') && $club->league_id === $user->league_id) {
            return true;
        }
        
        // Admin del club puede manejar su propio club
        if ($user->hasRole('club_admin') && $club->id === $user->club_id) {
            return true;
        }
        
        return false;
    }

    /**
     * Asociar categorías al club
     */
    private function associateCategories(Club $club, array $categoryIds)
    {
        $validCategories = Category::whereIn('id', $categoryIds)->pluck('id');
        $club->categories()->sync($validCategories);
    }

    /**
     * Crear equipos iniciales
     */
    private function createInitialTeams(Club $club, array $teamsData)
    {
        foreach ($teamsData as $teamData) {
            if (empty($teamData['name']) || empty($teamData['category_id'])) {
                continue;
            }
            
            Team::create([
                'club_id' => $club->id,
                'category_id' => $teamData['category_id'],
                'name' => $teamData['name'],
                'gender' => $teamData['gender'] ?? 'mixed',
                'max_players' => $teamData['max_players'] ?? 12,
                'is_active' => true,
                'created_by' => Auth::id(),
            ]);
        }
    }

    /**
     * Crear temporadas
     */
    private function createSeasons(Club $club, array $seasonsData)
    {
        foreach ($seasonsData as $seasonData) {
            if (empty($seasonData['name']) || empty($seasonData['start_date'])) {
                continue;
            }
            
            Season::create([
                'club_id' => $club->id,
                'name' => $seasonData['name'],
                'start_date' => $seasonData['start_date'],
                'end_date' => $seasonData['end_date'] ?? null,
                'is_active' => $seasonData['is_active'] ?? false,
                'description' => $seasonData['description'] ?? null,
                'created_by' => Auth::id(),
            ]);
        }
    }

    /**
     * Validar datos de un paso específico
     */
    public function validateStep(Request $request)
    {
        $step = $request->input('step');
        $data = $request->input('data', []);
        
        try {
            switch ($step) {
                case 1: // Información básica
                    $validated = validator($data, [
                        'name' => 'required|string|max:255',
                        'short_name' => 'required|string|max:50',
                        'founded_year' => 'nullable|integer|min:1900|max:' . date('Y'),
                        'description' => 'nullable|string|max:1000',
                        'colors.primary' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
                        'colors.secondary' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
                    ])->validate();
                    break;
                    
                case 2: // Ubicación
                    $validated = validator($data, [
                        'address' => 'nullable|string|max:255',
                        'department_id' => 'nullable|exists:departments,id',
                        'city_id' => 'nullable|exists:cities,id',
                        'phone' => 'nullable|string|max:20',
                        'email' => ['nullable', new NoAccentsEmail(), 'max:255'],
                        'website' => 'nullable|url|max:255',
                    ])->validate();
                    break;
                    
                default:
                    $validated = $data;
            }
            
            return response()->json([
                'success' => true,
                'data' => $validated
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error validando paso del club: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Error validando datos'
            ], 500);
        }
    }
}