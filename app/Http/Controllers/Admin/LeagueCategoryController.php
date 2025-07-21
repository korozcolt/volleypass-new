<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\League;
use App\Models\LeagueCategory;
use App\Services\LeagueConfigurationService;
use App\Services\CategoryValidationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class LeagueCategoryController extends Controller
{
    protected LeagueConfigurationService $configService;
    protected CategoryValidationService $validationService;

    public function __construct(
        LeagueConfigurationService $configService,
        CategoryValidationService $validationService
    ) {
        $this->configService = $configService;
        $this->validationService = $validationService;
    }

    /**
     * Crea categorías por defecto para una liga
     */
    public function createDefault(League $league): JsonResponse
    {
        try {
            Log::info('Creando categorías por defecto via API', ['league_id' => $league->id]);

            $result = $this->configService->createDefaultCategories($league);

            if ($result['success']) {
                Log::info('Categorías por defecto creadas exitosamente', [
                    'league_id' => $league->id,
                    'categories_created' => $result['categories_created']
                ]);

                return response()->json([
                    'success' => true,
                    'message' => $result['message'],
                    'categories_created' => $result['categories_created'],
                    'categories' => $result['categories']
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $result['message']
            ], 400);

        } catch (\Exception $e) {
            Log::error('Error creando categorías por defecto via API', [
                'league_id' => $league->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor'
            ], 500);
        }
    }

    /**
     * Valida la configuración de categorías de una liga
     */
    public function validate(League $league): JsonResponse
    {
        try {
            Log::info('Validando configuración via API', ['league_id' => $league->id]);

            $validation = $this->configService->validateCategoryConfiguration($league);

            return response()->json([
                'valid' => $validation['valid'],
                'errors' => $validation['errors'],
                'warnings' => $validation['warnings'],
                'suggestions' => $validation['suggestions'],
                'statistics' => $validation['statistics'],
                'message' => $validation['valid'] ?
                    'Configuración válida' :
                    'Se encontraron ' . count($validation['errors']) . ' errores'
            ]);

        } catch (\Exception $e) {
            Log::error('Error validando configuración via API', [
                'league_id' => $league->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'valid' => false,
                'errors' => ['Error interno validando la configuración'],
                'warnings' => [],
                'suggestions' => [],
                'statistics' => [],
                'message' => 'Error interno del servidor'
            ], 500);
        }
    }

    /**
     * Exporta la configuración de categorías
     */
    public function export(League $league)
    {
        try {
            Log::info('Exportando configuración via API', ['league_id' => $league->id]);

            $result = $this->configService->exportCategoriesConfiguration($league);

            if (!$result['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $result['message']
                ], 400);
            }

            $filename = "categorias-{$league->short_name}-" . now()->format('Y-m-d') . ".json";

            return response()->json($result['data'])
                ->header('Content-Disposition', "attachment; filename=\"{$filename}\"")
                ->header('Content-Type', 'application/json');

        } catch (\Exception $e) {
            Log::error('Error exportando configuración via API', [
                'league_id' => $league->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor'
            ], 500);
        }
    }

    /**
     * Crea una nueva categoría
     */
    public function store(League $league, Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:100',
                'code' => 'nullable|string|max:20',
                'description' => 'nullable|string|max:500',
                'gender' => 'required|in:male,female,mixed',
                'min_age' => 'required|integer|min:5|max:100',
                'max_age' => 'required|integer|min:5|max:100|gte:min_age',
                'color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
                'icon' => 'nullable|string|max:50',
                'sort_order' => 'nullable|integer|min:0',
                'special_rules' => 'nullable|array',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Datos de validación incorrectos',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Verificar unicidad de nombre y código en la liga
            $existingName = $league->categories()
                ->where('name', $request->name)
                ->exists();

            if ($existingName) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ya existe una categoría con ese nombre en esta liga'
                ], 422);
            }

            if ($request->code) {
                $existingCode = $league->categories()
                    ->where('code', $request->code)
                    ->exists();

                if ($existingCode) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Ya existe una categoría con ese código en esta liga'
                    ], 422);
                }
            }

            $categoryData = $request->validated();
            $categoryData['league_id'] = $league->id;
            $categoryData['is_active'] = true;

            // Asignar sort_order automático si no se especifica
            if (!isset($categoryData['sort_order'])) {
                $categoryData['sort_order'] = $league->categories()->max('sort_order') + 1;
            }

            $category = LeagueCategory::create($categoryData);

            Log::info('Categoría creada via API', [
                'league_id' => $league->id,
                'category_id' => $category->id,
                'category_name' => $category->name
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Categoría creada exitosamente',
                'category' => [
                    'id' => $category->id,
                    'name' => $category->name,
                    'code' => $category->code,
                    'age_range' => $category->age_range_label,
                    'gender' => $category->gender_label,
                    'color' => $category->color,
                    'is_active' => $category->is_active
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error creando categoría via API', [
                'league_id' => $league->id,
                'error' => $e->getMessage(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor'
            ], 500);
        }
    }

    /**
     * Actualiza una categoría existente
     */
    public function update(LeagueCategory $category, Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:100',
                'code' => 'nullable|string|max:20',
                'description' => 'nullable|string|max:500',
                'gender' => 'required|in:male,female,mixed',
                'min_age' => 'required|integer|min:5|max:100',
                'max_age' => 'required|integer|min:5|max:100|gte:min_age',
                'color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
                'icon' => 'nullable|string|max:50',
                'sort_order' => 'nullable|integer|min:0',
                'special_rules' => 'nullable|array',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Datos de validación incorrectos',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Verificar unicidad de nombre y código (excluyendo la categoría actual)
            $existingName = $category->league->categories()
                ->where('name', $request->name)
                ->where('id', '!=', $category->id)
                ->exists();

            if ($existingName) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ya existe otra categoría con ese nombre en esta liga'
                ], 422);
            }

            if ($request->code) {
                $existingCode = $category->league->categories()
                    ->where('code', $request->code)
                    ->where('id', '!=', $category->id)
                    ->exists();

                if ($existingCode) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Ya existe otra categoría con ese código en esta liga'
                    ], 422);
                }
            }

            $category->update($request->validated());

            Log::info('Categoría actualizada via API', [
                'league_id' => $category->league_id,
                'category_id' => $category->id,
                'category_name' => $category->name
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Categoría actualizada exitosamente',
                'category' => [
                    'id' => $category->id,
                    'name' => $category->name,
                    'code' => $category->code,
                    'age_range' => $category->age_range_label,
                    'gender' => $category->gender_label,
                    'color' => $category->color,
                    'is_active' => $category->is_active
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error actualizando categoría via API', [
                'category_id' => $category->id,
                'error' => $e->getMessage(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor'
            ], 500);
        }
    }

    /**
     * Cambia el estado activo/inactivo de una categoría
     */
    public function toggleStatus(LeagueCategory $category): JsonResponse
    {
        try {
            $oldStatus = $category->is_active;
            $category->update(['is_active' => !$oldStatus]);

            Log::info('Estado de categoría cambiado via API', [
                'category_id' => $category->id,
                'old_status' => $oldStatus,
                'new_status' => $category->is_active
            ]);

            return response()->json([
                'success' => true,
                'message' => $category->is_active ?
                    'Categoría activada exitosamente' :
                    'Categoría desactivada exitosamente',
                'is_active' => $category->is_active
            ]);

        } catch (\Exception $e) {
            Log::error('Error cambiando estado de categoría via API', [
                'category_id' => $category->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor'
            ], 500);
        }
    }

    /**
     * Elimina una categoría
     */
    public function destroy(LeagueCategory $category): JsonResponse
    {
        try {
            // Verificar si hay jugadoras asignadas a esta categoría
            $playerStats = $category->getPlayerStats();
            if ($playerStats['total'] > 0) {
                return response()->json([
                    'success' => false,
                    'message' => "No se puede eliminar la categoría porque tiene {$playerStats['total']} jugadora(s) asignada(s)"
                ], 422);
            }

            $categoryName = $category->name;
            $leagueId = $category->league_id;

            $category->delete();

            Log::info('Categoría eliminada via API', [
                'league_id' => $leagueId,
                'category_name' => $categoryName
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Categoría eliminada exitosamente'
            ]);

        } catch (\Exception $e) {
            Log::error('Error eliminando categoría via API', [
                'category_id' => $category->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor'
            ], 500);
        }
    }

    /**
     * Obtiene información detallada de una categoría
     */
    public function show(LeagueCategory $category): JsonResponse
    {
        try {
            $stats = $category->getPlayerStats();

            return response()->json([
                'success' => true,
                'category' => [
                    'id' => $category->id,
                    'name' => $category->name,
                    'code' => $category->code,
                    'description' => $category->description,
                    'gender' => $category->gender,
                    'gender_label' => $category->gender_label,
                    'min_age' => $category->min_age,
                    'max_age' => $category->max_age,
                    'age_range_label' => $category->age_range_label,
                    'color' => $category->color,
                    'icon' => $category->icon,
                    'sort_order' => $category->sort_order,
                    'special_rules' => $category->special_rules,
                    'is_active' => $category->is_active,
                    'created_at' => $category->created_at,
                    'updated_at' => $category->updated_at,
                    'stats' => $stats
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error obteniendo información de categoría via API', [
                'category_id' => $category->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor'
            ], 500);
        }
    }
}
