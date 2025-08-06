<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\MatchLiveService;
use App\Models\VolleyMatch;
use App\Models\MatchEvent;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use Exception;

/**
 * @OA\Tag(
 *     name="Match Live",
 *     description="Gestión de partidos en vivo y eventos"
 * )
 */
class MatchLiveController extends Controller
{
    protected MatchLiveService $matchLiveService;

    public function __construct(MatchLiveService $matchLiveService)
    {
        $this->matchLiveService = $matchLiveService;
    }

    /**
     * @OA\Post(
     *     path="/api/v1/matches/{id}/start",
     *     summary="Iniciar un partido",
     *     tags={"Match Live"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del partido",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Partido iniciado exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="match", ref="#/components/schemas/VolleyMatch"),
     *                 @OA\Property(property="first_set", ref="#/components/schemas/MatchSet")
     *             ),
     *             @OA\Property(property="message", type="string", example="Partido iniciado exitosamente")
     *         )
     *     )
     * )
     */
    public function startMatch(int $id): JsonResponse
    {
        try {
            $match = VolleyMatch::findOrFail($id);
            $result = $this->matchLiveService->startMatch($match);

            return response()->json([
                'success' => true,
                'data' => $result,
                'message' => 'Partido iniciado exitosamente'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al iniciar el partido',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/v1/matches/{id}/finish",
     *     summary="Finalizar un partido",
     *     tags={"Match Live"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del partido",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Partido finalizado exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/VolleyMatch"),
     *             @OA\Property(property="message", type="string", example="Partido finalizado exitosamente")
     *         )
     *     )
     * )
     */
    public function finishMatch(int $id): JsonResponse
    {
        try {
            $match = VolleyMatch::findOrFail($id);
            $result = $this->matchLiveService->finishMatch($match);

            return response()->json([
                'success' => true,
                'data' => $result,
                'message' => 'Partido finalizado exitosamente'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al finalizar el partido',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/v1/matches/{id}/sets/start",
     *     summary="Iniciar un nuevo set",
     *     tags={"Match Live"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del partido",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Set iniciado exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/MatchSet"),
     *             @OA\Property(property="message", type="string", example="Set iniciado exitosamente")
     *         )
     *     )
     * )
     */
    public function startNewSet(int $id): JsonResponse
    {
        try {
            $match = VolleyMatch::findOrFail($id);
            $result = $this->matchLiveService->startNewSet($match);

            return response()->json([
                'success' => true,
                'data' => $result,
                'message' => 'Set iniciado exitosamente'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al iniciar el set',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/v1/matches/{id}/sets/finish",
     *     summary="Finalizar el set actual",
     *     tags={"Match Live"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del partido",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"home_score", "away_score"},
     *             @OA\Property(property="home_score", type="integer", description="Puntos del equipo local"),
     *             @OA\Property(property="away_score", type="integer", description="Puntos del equipo visitante")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Set finalizado exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/MatchSet"),
     *             @OA\Property(property="message", type="string", example="Set finalizado exitosamente")
     *         )
     *     )
     * )
     */
    public function finishSet(Request $request, int $id): JsonResponse
    {
        try {
            $validated = $request->validate([
                'home_score' => 'required|integer|min:0',
                'away_score' => 'required|integer|min:0'
            ]);

            $match = VolleyMatch::findOrFail($id);
            $result = $this->matchLiveService->finishCurrentSet(
                $match,
                $validated['home_score'],
                $validated['away_score']
            );

            return response()->json([
                'success' => true,
                'data' => $result,
                'message' => 'Set finalizado exitosamente'
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de entrada inválidos',
                'errors' => $e->errors()
            ], 422);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al finalizar el set',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/v1/matches/{id}/score",
     *     summary="Actualizar marcador del set actual",
     *     tags={"Match Live"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del partido",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"home_score", "away_score"},
     *             @OA\Property(property="home_score", type="integer", description="Puntos del equipo local"),
     *             @OA\Property(property="away_score", type="integer", description="Puntos del equipo visitante")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Marcador actualizado exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="set", ref="#/components/schemas/MatchSet"),
     *                 @OA\Property(property="match", ref="#/components/schemas/VolleyMatch")
     *             ),
     *             @OA\Property(property="message", type="string", example="Marcador actualizado exitosamente")
     *         )
     *     )
     * )
     */
    public function updateScore(Request $request, int $id): JsonResponse
    {
        try {
            $validated = $request->validate([
                'home_score' => 'required|integer|min:0',
                'away_score' => 'required|integer|min:0'
            ]);

            $match = VolleyMatch::findOrFail($id);
            $result = $this->matchLiveService->updateSetScore(
                $match,
                $validated['home_score'],
                $validated['away_score']
            );

            return response()->json([
                'success' => true,
                'data' => $result,
                'message' => 'Marcador actualizado exitosamente'
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de entrada inválidos',
                'errors' => $e->errors()
            ], 422);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el marcador',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/v1/matches/{id}/rotation",
     *     summary="Actualizar rotación de jugadores",
     *     tags={"Match Live"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del partido",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"team", "rotation"},
     *             @OA\Property(property="team", type="string", enum={"home", "away"}, description="Equipo (home o away)"),
     *             @OA\Property(property="rotation", type="array", @OA\Items(type="integer"), description="Array de IDs de jugadores en orden de rotación")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Rotación actualizada exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="rotation", type="array", @OA\Items(type="object"))
     *             ),
     *             @OA\Property(property="message", type="string", example="Rotación actualizada exitosamente")
     *         )
     *     )
     * )
     */
    public function updateRotation(Request $request, int $id): JsonResponse
    {
        try {
            $validated = $request->validate([
                'team' => 'required|string|in:home,away',
                'rotation' => 'required|array|size:6',
                'rotation.*' => 'required|integer|exists:players,id'
            ]);

            $match = VolleyMatch::findOrFail($id);
            $result = $this->matchLiveService->updateRotation(
                $match,
                $validated['team'],
                $validated['rotation']
            );

            return response()->json([
                'success' => true,
                'data' => $result,
                'message' => 'Rotación actualizada exitosamente'
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de entrada inválidos',
                'errors' => $e->errors()
            ], 422);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar la rotación',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/v1/matches/{id}/events",
     *     summary="Registrar evento del partido",
     *     tags={"Match Live"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del partido",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"event_type", "team_id"},
     *             @OA\Property(property="event_type", type="string", description="Tipo de evento"),
     *             @OA\Property(property="event_subtype", type="string", description="Subtipo de evento"),
     *             @OA\Property(property="team_id", type="integer", description="ID del equipo"),
     *             @OA\Property(property="player_id", type="integer", description="ID del jugador"),
     *             @OA\Property(property="description", type="string", description="Descripción del evento"),
     *             @OA\Property(property="is_successful", type="boolean", description="Si el evento fue exitoso"),
     *             @OA\Property(property="points_awarded", type="integer", description="Puntos otorgados"),
     *             @OA\Property(property="coordinates_x", type="number", format="float", description="Coordenada X"),
     *             @OA\Property(property="coordinates_y", type="number", format="float", description="Coordenada Y")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Evento registrado exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/MatchEvent"),
     *             @OA\Property(property="message", type="string", example="Evento registrado exitosamente")
     *         )
     *     )
     * )
     */
    public function addEvent(Request $request, int $id): JsonResponse
    {
        try {
            $validated = $request->validate([
                'event_type' => 'required|string',
                'event_subtype' => 'nullable|string',
                'team_id' => 'required|integer|exists:teams,id',
                'player_id' => 'nullable|integer|exists:players,id',
                'description' => 'nullable|string|max:500',
                'is_successful' => 'nullable|boolean',
                'points_awarded' => 'nullable|integer|min:0',
                'coordinates_x' => 'nullable|numeric',
                'coordinates_y' => 'nullable|numeric'
            ]);

            $match = VolleyMatch::findOrFail($id);
            $result = $this->matchLiveService->addMatchEvent($match, $validated);

            return response()->json([
                'success' => true,
                'data' => $result,
                'message' => 'Evento registrado exitosamente'
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de entrada inválidos',
                'errors' => $e->errors()
            ], 422);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar el evento',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/v1/matches/{id}/events",
     *     summary="Obtener eventos del partido",
     *     tags={"Match Live"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del partido",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="set_number",
     *         in="query",
     *         description="Número del set",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="event_type",
     *         in="query",
     *         description="Tipo de evento",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de eventos del partido",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/MatchEvent"))
     *         )
     *     )
     * )
     */
    public function getEvents(Request $request, int $id): JsonResponse
    {
        try {
            $match = VolleyMatch::findOrFail($id);
            
            $query = MatchEvent::where('match_id', $id)
                ->with(['player', 'team'])
                ->orderBy('event_time');

            if ($request->has('set_number')) {
                $query->where('set_number', $request->set_number);
            }

            if ($request->has('event_type')) {
                $query->where('event_type', $request->event_type);
            }

            $events = $query->get();

            return response()->json([
                'success' => true,
                'data' => $events
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener eventos del partido',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/v1/matches/{id}/status",
     *     summary="Obtener estado completo del partido",
     *     tags={"Match Live"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del partido",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Estado completo del partido",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="match", ref="#/components/schemas/VolleyMatch"),
     *                 @OA\Property(property="sets", type="array", @OA\Items(ref="#/components/schemas/MatchSet")),
     *                 @OA\Property(property="current_rotations", type="object"),
     *                 @OA\Property(property="recent_events", type="array", @OA\Items(ref="#/components/schemas/MatchEvent"))
     *             )
     *         )
     *     )
     * )
     */
    public function getMatchStatus(int $id): JsonResponse
    {
        try {
            $match = VolleyMatch::with([
                'homeTeam',
                'awayTeam',
                'sets' => function ($query) {
                    $query->orderBy('set_number');
                },
                'tournament'
            ])->findOrFail($id);

            $result = $this->matchLiveService->getMatchStatus($match);

            return response()->json([
                'success' => true,
                'data' => $result
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener estado del partido',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}