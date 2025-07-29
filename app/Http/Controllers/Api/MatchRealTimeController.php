<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\MatchRealTimeService;
use App\Models\VolleyMatch;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class MatchRealTimeController extends Controller
{
    protected $matchRealTimeService;

    public function __construct(MatchRealTimeService $matchRealTimeService)
    {
        $this->matchRealTimeService = $matchRealTimeService;
    }

    /**
     * Obtener datos en tiempo real de un partido
     */
    public function getMatchData(int $matchId): JsonResponse
    {
        try {
            $data = $this->matchRealTimeService->getMatchRealTimeData($matchId);
            
            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener datos del partido',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Actualizar marcador de un set
     */
    public function updateSetScore(Request $request, int $matchId): JsonResponse
    {
        try {
            $validated = $request->validate([
                'set_number' => 'required|integer|min:1|max:5',
                'home_score' => 'required|integer|min:0',
                'away_score' => 'required|integer|min:0',
                'event_type' => 'sometimes|string|in:point,ace,block,error',
            ]);

            $result = $this->matchRealTimeService->updateSetScore(
                $matchId,
                $validated['set_number'],
                $validated['home_score'],
                $validated['away_score'],
                $validated['event_type'] ?? 'point'
            );

            return response()->json($result);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de entrada inválidos',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar marcador',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Cambiar estado del partido
     */
    public function changeStatus(Request $request, int $matchId): JsonResponse
    {
        try {
            $validated = $request->validate([
                'status' => 'required|string|in:scheduled,in_progress,finished,cancelled',
            ]);

            $result = $this->matchRealTimeService->changeMatchStatus(
                $matchId,
                $validated['status']
            );

            return response()->json($result);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Estado inválido',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cambiar estado del partido',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Actualizar rotación de jugadores
     */
    public function updateRotation(Request $request, int $matchId): JsonResponse
    {
        try {
            $validated = $request->validate([
                'team_id' => 'required|integer|exists:teams,id',
                'rotation_data' => 'required|array',
                'rotation_data.positions' => 'required|array|size:6',
                'rotation_data.libero_id' => 'sometimes|integer|nullable',
                'rotation_data.rotation_number' => 'required|integer|min:1',
            ]);

            $result = $this->matchRealTimeService->updatePlayerRotation(
                $matchId,
                $validated['team_id'],
                $validated['rotation_data']
            );

            return response()->json($result);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de rotación inválidos',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar rotación',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Iniciar un nuevo set
     */
    public function startNewSet(int $matchId): JsonResponse
    {
        try {
            $result = $this->matchRealTimeService->startNewSet($matchId);
            
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al iniciar nuevo set',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Obtener partidos en vivo
     */
    public function getLiveMatches(): JsonResponse
    {
        try {
            $liveMatches = VolleyMatch::with([
                'homeTeam',
                'awayTeam',
                'sets' => function ($query) {
                    $query->orderBy('set_number');
                },
                'tournament',
            ])
            ->where('status', 'in_progress')
            ->get()
            ->map(function ($match) {
                $currentSet = $match->sets()->where('status', 'in_progress')->first();
                
                return [
                    'match' => $match,
                    'current_set' => $currentSet,
                    'sets' => $match->sets,
                    'events' => $match->events ?? [],
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $liveMatches,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener partidos en vivo',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Agregar evento al partido (punto, ace, bloqueo, etc.)
     */
    public function addMatchEvent(Request $request, int $matchId): JsonResponse
    {
        try {
            $validated = $request->validate([
                'event_type' => 'required|string|in:point,ace,block,error,substitution,timeout,card',
                'team_id' => 'required|integer|exists:teams,id',
                'player_id' => 'sometimes|integer|exists:players,id',
                'set_number' => 'required|integer|min:1|max:5',
                'description' => 'sometimes|string|max:255',
                'metadata' => 'sometimes|array',
            ]);

            $match = VolleyMatch::findOrFail($matchId);
            
            // Agregar evento al historial
            $events = $match->events ?? [];
            $events[] = [
                'type' => $validated['event_type'],
                'team_id' => $validated['team_id'],
                'player_id' => $validated['player_id'] ?? null,
                'set_number' => $validated['set_number'],
                'description' => $validated['description'] ?? null,
                'metadata' => $validated['metadata'] ?? [],
                'timestamp' => now()->toISOString(),
            ];

            $match->update(['events' => $events]);

            return response()->json([
                'success' => true,
                'message' => 'Evento agregado correctamente',
                'event' => end($events),
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Datos del evento inválidos',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al agregar evento',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}