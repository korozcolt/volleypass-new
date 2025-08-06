<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tournament;
use App\Models\VolleyMatch;
use App\Enums\TournamentStatus;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

/**
 * @OA\Tag(
 *     name="Public Tournaments",
 *     description="Endpoints públicos para consultar torneos"
 * )
 */
class PublicTournamentController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/public/tournaments",
     *     summary="Listar torneos públicos",
     *     tags={"Public Tournaments"},
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Filtrar por estado del torneo",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="type",
     *         in="query",
     *         description="Filtrar por tipo de torneo",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="category",
     *         in="query",
     *         description="Filtrar por categoría",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de torneos públicos",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Tournament"))
     *         )
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Tournament::with(['league:id,name', 'tournamentTeams:id,tournament_id,team_id'])
                ->where('is_public', true)
                ->where('status', '!=', TournamentStatus::Draft)
                ->select([
                    'id',
                    'name',
                    'slug',
                    'description',
                    'league_id',
                    'type',
                    'format',
                    'category',
                    'gender',
                    'status',
                    'start_date',
                    'end_date',
                    'registration_start',
                    'registration_end',
                    'venue',
                    'venue_address',
                    'max_teams',
                    'total_teams'
                ]);

            // Filtros
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            if ($request->has('type')) {
                $query->where('type', $request->type);
            }

            if ($request->has('category')) {
                $query->where('category', $request->category);
            }

            if ($request->has('gender')) {
                $query->where('gender', $request->gender);
            }

            $tournaments = $query->orderBy('start_date', 'desc')
                ->paginate(20);

            // Agregar información adicional
            $tournaments->getCollection()->transform(function ($tournament) {
                $tournament->team_count = $tournament->tournamentTeams->count();
                $tournament->is_registration_open = $tournament->status === TournamentStatus::RegistrationOpen;
                unset($tournament->tournamentTeams);
                return $tournament;
            });

            return response()->json([
                'success' => true,
                'data' => $tournaments
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener torneos',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/public/tournaments/{id}",
     *     summary="Obtener detalles de un torneo",
     *     tags={"Public Tournaments"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del torneo",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Detalles del torneo",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/Tournament")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Torneo no encontrado"
     *     )
     * )
     */
    public function show(int $id): JsonResponse
    {
        try {
            $tournament = Tournament::with([
                'league:id,name',
                'teams:id,name,logo',
                'matches' => function ($query) {
                    $query->with(['homeTeam:id,name', 'awayTeam:id,name'])
                        ->select([
                            'id',
                            'tournament_id',
                            'home_team_id',
                            'away_team_id',
                            'scheduled_at',
                            'status',
                            'home_score',
                            'away_score',
                            'venue',
                            'round_number'
                        ]);
                },
                'rounds:id,tournament_id,round_number,name,status,matches_count'
            ])
                ->where('is_public', true)
                ->where('status', '!=', TournamentStatus::Draft)
                ->findOrFail($id);

            // Agregar estadísticas
            $tournament->statistics = [
                'total_teams' => $tournament->teams->count(),
                'total_matches' => $tournament->matches->count(),
                'completed_matches' => $tournament->matches->where('status', 'finished')->count(),
                'pending_matches' => $tournament->matches->where('status', 'scheduled')->count(),
                'in_progress_matches' => $tournament->matches->where('status', 'in_progress')->count(),
            ];

            return response()->json([
                'success' => true,
                'data' => $tournament
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener el torneo',
                'error' => $e->getMessage()
            ], 404);
        }
    }
}