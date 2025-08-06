<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\RotationTrackingService;
use App\Models\VolleyMatch;
use App\Models\PlayerRotation;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Exception;

/**
 * @OA\Tag(
 *     name="Rotation Tracking",
 *     description="Gestión de rotaciones de jugadores en partidos"
 * )
 */
class RotationTrackingController extends Controller
{
    protected RotationTrackingService $rotationService;

    public function __construct(RotationTrackingService $rotationService)
    {
        $this->rotationService = $rotationService;
    }

    /**
     * @OA\Post(
     *     path="/api/v1/matches/{id}/rotation/update",
     *     summary="Actualizar rotación de un equipo",
     *     tags={"Rotation Tracking"},
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
     *             required={"team_id", "rotation_data"},
     *             @OA\Property(property="team_id", type="integer", description="ID del equipo"),
     *             @OA\Property(property="rotation_data", type="object", description="Datos de rotación con posiciones y jugadores"),
     *             @OA\Property(property="set_number", type="integer", description="Número del set")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Rotación actualizada exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
                 @OA\Property(property="rotation", type="object",
                     @OA\Property(property="team_id", type="integer"),
                     @OA\Property(property="rotation_order", type="array", @OA\Items(type="integer"))
                 ),
                 @OA\Property(property="broadcast_sent", type="boolean")
             ),
     *             @OA\Property(property="message", type="string", example="Rotación actualizada exitosamente")
     *         )
     *     )
     * )
     */
    public function updateRotation(Request $request, int $id): JsonResponse
    {
        try {
            $validated = $request->validate([
                'team_id' => 'required|integer|exists:teams,id',
                'rotation_data' => 'required|array',
                'set_number' => 'nullable|integer|min:1'
            ]);

            $match = VolleyMatch::findOrFail($id);
            $result = $this->rotationService->updateTeamRotation(
                $match,
                $validated['team_id'],
                $validated['rotation_data'],
                $validated['set_number'] ?? null
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
     *     path="/api/v1/matches/{id}/rotation/rotate",
     *     summary="Rotar posiciones de un equipo",
     *     tags={"Rotation Tracking"},
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
     *             required={"team_id", "direction"},
     *             @OA\Property(property="team_id", type="integer", description="ID del equipo"),
     *             @OA\Property(property="direction", type="string", enum={"clockwise", "counterclockwise"}, description="Dirección de rotación"),
     *             @OA\Property(property="set_number", type="integer", description="Número del set")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Rotación realizada exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="rotation", type="object",
                     @OA\Property(property="team_id", type="integer"),
                     @OA\Property(property="rotation_order", type="array", @OA\Items(type="integer"))
                 ),
     *                 @OA\Property(property="previous_rotation", type="object"),
     *                 @OA\Property(property="broadcast_sent", type="boolean")
     *             ),
     *             @OA\Property(property="message", type="string", example="Rotación realizada exitosamente")
     *         )
     *     )
     * )
     */
    public function rotatePositions(Request $request, int $id): JsonResponse
    {
        try {
            $validated = $request->validate([
                'team_id' => 'required|integer|exists:teams,id',
                'direction' => 'required|string|in:clockwise,counterclockwise',
                'set_number' => 'nullable|integer|min:1'
            ]);

            $match = VolleyMatch::findOrFail($id);
            $team = $validated['team_id'] === $match->home_team_id ? 'home' : 'away';
            $result = $this->rotationService->rotatePositions(
                $match,
                $team,
                $validated['direction']
            );

            return response()->json([
                'success' => true,
                'data' => $result,
                'message' => 'Rotación realizada exitosamente'
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
                'message' => 'Error al realizar la rotación',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/v1/matches/{id}/rotation/current",
     *     summary="Obtener rotación actual de los equipos",
     *     tags={"Rotation Tracking"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del partido",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="team_id",
     *         in="query",
     *         description="ID del equipo (opcional, si no se especifica devuelve ambos equipos)",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="set_number",
     *         in="query",
     *         description="Número del set",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Rotación actual de los equipos",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="home_team", type="object"),
     *                 @OA\Property(property="away_team", type="object")
     *             )
     *         )
     *     )
     * )
     */
    public function getCurrentRotation(Request $request, int $id): JsonResponse
    {
        try {
            $match = VolleyMatch::findOrFail($id);
            $teamId = $request->query('team_id');
            $setNumber = $request->query('set_number');

            $result = $this->rotationService->getCurrentRotation(
                $match,
                $teamId,
                $setNumber
            );

            return response()->json([
                'success' => true,
                'data' => $result
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener la rotación actual',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/v1/matches/{id}/rotation/history",
     *     summary="Obtener historial de rotaciones",
     *     tags={"Rotation Tracking"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del partido",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="team_id",
     *         in="query",
     *         description="ID del equipo",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="set_number",
     *         in="query",
     *         description="Número del set",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Historial de rotaciones",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array", @OA\Items(type="object",
                 @OA\Property(property="team_id", type="integer"),
                 @OA\Property(property="rotation_order", type="array", @OA\Items(type="integer"))
             ))
     *         )
     *     )
     * )
     */
    public function getRotationHistory(Request $request, int $id): JsonResponse
    {
        try {
            $match = VolleyMatch::findOrFail($id);
            
            $query = PlayerRotation::where('match_id', $id)
                ->with(['team', 'players'])
                ->orderBy('created_at', 'desc');

            if ($request->has('team_id')) {
                $query->where('team_id', $request->team_id);
            }

            if ($request->has('set_number')) {
                $query->where('set_number', $request->set_number);
            }

            $rotations = $query->get();

            return response()->json([
                'success' => true,
                'data' => $rotations
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener el historial de rotaciones',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/v1/matches/{id}/rotation/substitute",
     *     summary="Realizar sustitución de jugador",
     *     tags={"Rotation Tracking"},
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
     *             required={"team_id", "player_out_id", "player_in_id", "position"},
     *             @OA\Property(property="team_id", type="integer", description="ID del equipo"),
     *             @OA\Property(property="player_out_id", type="integer", description="ID del jugador que sale"),
     *             @OA\Property(property="player_in_id", type="integer", description="ID del jugador que entra"),
     *             @OA\Property(property="position", type="string", description="Posición en la que se realiza la sustitución"),
     *             @OA\Property(property="set_number", type="integer", description="Número del set")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Sustitución realizada exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="rotation", type="object",
                     @OA\Property(property="team_id", type="integer"),
                     @OA\Property(property="rotation_order", type="array", @OA\Items(type="integer"))
                 ),
                 @OA\Property(property="substitution_event", ref="#/components/schemas/MatchEvent")
     *             ),
     *             @OA\Property(property="message", type="string", example="Sustitución realizada exitosamente")
     *         )
     *     )
     * )
     */
    public function substitutePlayer(Request $request, int $id): JsonResponse
    {
        try {
            $validated = $request->validate([
                'team_id' => 'required|integer|exists:teams,id',
                'player_out_id' => 'required|integer|exists:players,id',
                'player_in_id' => 'required|integer|exists:players,id',
                'position' => 'required|string',
                'set_number' => 'nullable|integer|min:1'
            ]);

            $match = VolleyMatch::findOrFail($id);
            $result = $this->rotationService->substitutePlayer(
                $match,
                $validated['team_id'],
                $validated['player_out_id'],
                $validated['player_in_id'],
                $validated['position'],
                $validated['set_number'] ?? null
            );

            return response()->json([
                'success' => true,
                'data' => $result,
                'message' => 'Sustitución realizada exitosamente'
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
                'message' => 'Error al realizar la sustitución',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/v1/matches/{id}/rotation/validate",
     *     summary="Validar configuración de rotación",
     *     tags={"Rotation Tracking"},
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
     *             required={"team_id", "rotation_data"},
     *             @OA\Property(property="team_id", type="integer", description="ID del equipo"),
     *             @OA\Property(property="rotation_data", type="object", description="Datos de rotación a validar")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Resultado de la validación",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="is_valid", type="boolean"),
     *                 @OA\Property(property="errors", type="array", @OA\Items(type="string")),
     *                 @OA\Property(property="warnings", type="array", @OA\Items(type="string"))
     *             )
     *         )
     *     )
     * )
     */
    public function validateRotation(Request $request, int $id): JsonResponse
    {
        try {
            $validated = $request->validate([
                'team_id' => 'required|integer|exists:teams,id',
                'rotation_data' => 'required|array'
            ]);

            $match = VolleyMatch::findOrFail($id);
            $team = $validated['team_id'] === $match->home_team_id ? 'home' : 'away';
            $currentRotation = $this->rotationService->getCurrentRotation($match, $team);
            
            // Validar que los datos de rotación sean válidos
            if (count($validated['rotation_data']) !== 6) {
                throw new \Exception('Se requieren exactamente 6 jugadores en la rotación');
            }
            
            $result = [
                'is_valid' => true,
                'current_rotation' => $currentRotation,
                'proposed_rotation' => $validated['rotation_data'],
                'validation_message' => 'Datos de rotación válidos'
            ];

            return response()->json([
                'success' => true,
                'data' => $result
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
                'message' => 'Error al validar la rotación',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/v1/matches/{id}/rotation/positions",
     *     summary="Obtener posiciones disponibles",
     *     tags={"Rotation Tracking"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del partido",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de posiciones disponibles",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="positions", type="array", @OA\Items(type="object")),
     *                 @OA\Property(property="rotation_order", type="array", @OA\Items(type="string"))
     *             )
     *         )
     *     )
     * )
     */
    public function getAvailablePositions(int $id): JsonResponse
    {
        try {
            $match = VolleyMatch::findOrFail($id);
            $homeRotation = $this->rotationService->getCurrentRotation($match, 'home');
            $awayRotation = $this->rotationService->getCurrentRotation($match, 'away');
            
            $result = [
                'positions' => [
                    ['id' => 1, 'name' => 'Colocador', 'zone' => 1],
                    ['id' => 2, 'name' => 'Opuesto', 'zone' => 2],
                    ['id' => 3, 'name' => 'Central', 'zone' => 3],
                    ['id' => 4, 'name' => 'Receptor', 'zone' => 4],
                    ['id' => 5, 'name' => 'Central', 'zone' => 5],
                    ['id' => 6, 'name' => 'Líbero', 'zone' => 6]
                ],
                'rotation_order' => ['Zona 1', 'Zona 2', 'Zona 3', 'Zona 4', 'Zona 5', 'Zona 6'],
                'current_rotations' => [
                    'home' => $homeRotation,
                    'away' => $awayRotation
                ]
            ];

            return response()->json([
                'success' => true,
                'data' => $result
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las posiciones disponibles',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}