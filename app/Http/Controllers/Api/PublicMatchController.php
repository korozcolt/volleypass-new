<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\VolleyMatch;
use App\Models\MatchSet;
use App\Models\MatchEvent;
use App\Enums\MatchStatus;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

/**
 * @OA\Tag(
 *     name="Public Matches",
 *     description="Endpoints públicos para consultar partidos"
 * )
 */
class PublicMatchController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/public/matches/scheduled",
     *     summary="Listar partidos programados",
     *     tags={"Public Matches"},
     *     @OA\Parameter(
     *         name="tournament_id",
     *         in="query",
     *         description="Filtrar por torneo",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="team_id",
     *         in="query",
     *         description="Filtrar por equipo",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="date",
     *         in="query",
     *         description="Filtrar por fecha (YYYY-MM-DD)",
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de partidos programados",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/VolleyMatch"))
     *         )
     *     )
     * )
     */
    public function scheduled(Request $request): JsonResponse
    {
        try {
            $query = VolleyMatch::with([
                'homeTeam:id,name,logo',
                'awayTeam:id,name,logo',
                'tournament:id,name,type,status',
                'referee:id,name'
            ])
                ->whereIn('status', [MatchStatus::Scheduled, MatchStatus::In_Progress])
                ->select([
                    'id',
                    'tournament_id',
                    'home_team_id',
                    'away_team_id',
                    'referee_id',
                    'scheduled_at',
                    'status',
                    'home_score',
                    'away_score',
                    'venue',
                    'venue_address',
                    'round_number',
                    'group_name',
                    'match_type'
                ]);

            // Filtros
            if ($request->has('tournament_id')) {
                $query->where('tournament_id', $request->tournament_id);
            }

            if ($request->has('team_id')) {
                $query->where(function (Builder $q) use ($request) {
                    $q->where('home_team_id', $request->team_id)
                        ->orWhere('away_team_id', $request->team_id);
                });
            }

            if ($request->has('date')) {
                $query->whereDate('scheduled_at', $request->date);
            }

            $matches = $query->orderBy('scheduled_at', 'asc')
                ->paginate(20);

            return response()->json([
                'success' => true,
                'data' => $matches
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener partidos programados',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/public/matches/live",
     *     summary="Listar partidos en vivo",
     *     tags={"Public Matches"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de partidos en vivo",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/VolleyMatch"))
     *         )
     *     )
     * )
     */
    public function live(): JsonResponse
    {
        try {
            $matches = VolleyMatch::with([
                'homeTeam:id,name,logo',
                'awayTeam:id,name,logo',
                'tournament:id,name,type',
                'currentSet:id,match_id,set_number,home_score,away_score,status',
                'sets:id,match_id,set_number,home_score,away_score,status'
            ])
                ->where('status', MatchStatus::In_Progress)
                ->select([
                    'id',
                    'tournament_id',
                    'home_team_id',
                    'away_team_id',
                    'scheduled_at',
                    'started_at',
                    'status',
                    'home_score',
                    'away_score',
                    'venue',
                    'round_number',
                    'group_name',
                    'current_set'
                ])
                ->orderBy('started_at', 'desc')
                ->get();

            // Agregar información adicional para partidos en vivo
            $matches->transform(function ($match) {
                $match->live_stats = [
                    'duration' => $match->started_at ? now()->diffInMinutes($match->started_at) : 0,
                    'sets_played' => $match->sets->count(),
                    'current_set_number' => $match->current_set,
                    'home_sets_won' => $match->sets->where('home_score', '>', 'away_score')->count(),
                    'away_sets_won' => $match->sets->where('away_score', '>', 'home_score')->count()
                ];
                return $match;
            });

            return response()->json([
                'success' => true,
                'data' => $matches
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener partidos en vivo',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/public/matches/{id}",
     *     summary="Obtener detalles de un partido",
     *     tags={"Public Matches"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del partido",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Detalles del partido",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/VolleyMatch")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Partido no encontrado"
     *     )
     * )
     */
    public function show(int $id): JsonResponse
    {
        try {
            $match = VolleyMatch::with([
                'homeTeam:id,name,logo',
                'awayTeam:id,name,logo',
                'tournament:id,name,type,status',
                'referee:id,name',
                'sets' => function ($query) {
                    $query->orderBy('set_number');
                },
                'events' => function ($query) {
                    $query->with(['player:id,name', 'team:id,name'])
                        ->orderBy('event_time');
                },
                'rotations' => function ($query) {
                    $query->with(['player:id,name,jersey_number'])
                        ->orderBy('created_at', 'desc');
                }
            ])->findOrFail($id);

            // Agregar estadísticas del partido
            $match->statistics = [
                'duration' => $match->started_at && $match->finished_at 
                    ? $match->started_at->diffInMinutes($match->finished_at)
                    : ($match->started_at ? now()->diffInMinutes($match->started_at) : 0),
                'sets_played' => $match->sets->count(),
                'home_sets_won' => $match->sets->where('home_score', '>', 'away_score')->count(),
                'away_sets_won' => $match->sets->where('away_score', '>', 'home_score')->count(),
                'total_events' => $match->events->count(),
                'points_by_set' => $match->sets->map(function ($set) {
                    return [
                        'set_number' => $set->set_number,
                        'home_score' => $set->home_score,
                        'away_score' => $set->away_score,
                        'duration' => $set->started_at && $set->finished_at 
                            ? $set->started_at->diffInMinutes($set->finished_at)
                            : null
                    ];
                })
            ];

            return response()->json([
                'success' => true,
                'data' => $match
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener el partido',
                'error' => $e->getMessage()
            ], 404);
        }
    }
}