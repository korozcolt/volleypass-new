<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\SanctionService;
use App\Models\Sanction;
use App\Models\Player;
use App\Models\VolleyMatch;
use App\Models\Tournament;
use App\Enums\SanctionType;
use App\Enums\SanctionSeverity;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use Exception;

/**
 * @OA\Tag(
 *     name="Sanctions",
 *     description="Gestión de sanciones disciplinarias"
 * )
 */
class SanctionController extends Controller
{
    protected SanctionService $sanctionService;

    public function __construct(SanctionService $sanctionService)
    {
        $this->sanctionService = $sanctionService;
    }

    /**
     * @OA\Get(
     *     path="/api/v1/sanctions",
     *     summary="Listar sanciones",
     *     tags={"Sanctions"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="player_id",
     *         in="query",
     *         description="ID del jugador",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="team_id",
     *         in="query",
     *         description="ID del equipo",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="tournament_id",
     *         in="query",
     *         description="ID del torneo",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Estado de la sanción",
     *         @OA\Schema(type="string", enum={"active", "completed", "appealed", "overturned"})
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de sanciones",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Sanction"))
     *         )
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Sanction::with(['player', 'team', 'match', 'tournament', 'referee']);

            // Filtros
            if ($request->has('player_id')) {
                $query->where('player_id', $request->player_id);
            }

            if ($request->has('team_id')) {
                $query->where('team_id', $request->team_id);
            }

            if ($request->has('tournament_id')) {
                $query->where('tournament_id', $request->tournament_id);
            }

            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            // Solo activas por defecto
            if (!$request->has('include_inactive')) {
                $query->where('is_active', true);
            }

            $sanctions = $query->orderBy('created_at', 'desc')->paginate(20);

            return response()->json([
                'success' => true,
                'data' => $sanctions
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener sanciones',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/v1/sanctions",
     *     summary="Aplicar nueva sanción",
     *     tags={"Sanctions"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"player_id", "type", "severity", "reason"},
     *             @OA\Property(property="player_id", type="integer", description="ID del jugador"),
     *             @OA\Property(property="type", type="string", enum={"yellow_card", "red_card", "suspension", "fine", "warning"}, description="Tipo de sanción"),
     *             @OA\Property(property="severity", type="string", enum={"minor", "moderate", "major", "severe"}, description="Severidad de la sanción"),
     *             @OA\Property(property="reason", type="string", description="Motivo de la sanción"),
     *             @OA\Property(property="match_id", type="integer", description="ID del partido (opcional)"),
     *             @OA\Property(property="tournament_id", type="integer", description="ID del torneo (opcional)"),
     *             @OA\Property(property="description", type="string", description="Descripción detallada"),
     *             @OA\Property(property="fine_amount", type="number", format="float", description="Monto de multa"),
     *             @OA\Property(property="suspension_matches", type="integer", description="Partidos de suspensión"),
     *             @OA\Property(property="suspension_days", type="integer", description="Días de suspensión")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Sanción aplicada exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/Sanction"),
     *             @OA\Property(property="message", type="string", example="Sanción aplicada exitosamente")
     *         )
     *     )
     * )
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'player_id' => 'required|exists:players,id',
                'type' => 'required|string|in:yellow_card,red_card,suspension,fine,warning',
                'severity' => 'required|string|in:minor,moderate,major,severe',
                'reason' => 'required|string|max:500',
                'match_id' => 'nullable|exists:volley_matches,id',
                'tournament_id' => 'nullable|exists:tournaments,id',
                'description' => 'nullable|string|max:1000',
                'fine_amount' => 'nullable|numeric|min:0',
                'suspension_matches' => 'nullable|integer|min:0',
                'suspension_days' => 'nullable|integer|min:0'
            ]);

            $player = Player::findOrFail($validated['player_id']);
            $type = SanctionType::from($validated['type']);
            $severity = SanctionSeverity::from($validated['severity']);

            $match = $validated['match_id'] ? VolleyMatch::find($validated['match_id']) : null;
            $tournament = $validated['tournament_id'] ? Tournament::find($validated['tournament_id']) : null;

            $additionalData = array_filter([
                'description' => $validated['description'] ?? null,
                'fine_amount' => $validated['fine_amount'] ?? null,
                'suspension_matches' => $validated['suspension_matches'] ?? null,
                'suspension_days' => $validated['suspension_days'] ?? null,
                'referee_id' => Auth::id()
            ]);

            $sanction = $this->sanctionService->applySanction(
                $player,
                $type,
                $severity,
                $validated['reason'],
                $match,
                $tournament,
                $additionalData
            );

            return response()->json([
                'success' => true,
                'data' => $sanction->load(['player', 'team', 'match', 'tournament']),
                'message' => 'Sanción aplicada exitosamente'
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
                'message' => 'Error al aplicar sanción',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/v1/sanctions/{id}",
     *     summary="Obtener detalles de una sanción",
     *     tags={"Sanctions"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la sanción",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Detalles de la sanción",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/Sanction")
     *         )
     *     )
     * )
     */
    public function show(int $id): JsonResponse
    {
        try {
            $sanction = Sanction::with([
                'player',
                'team',
                'match',
                'tournament',
                'referee',
                'reviewer'
            ])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $sanction
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Sanción no encontrada',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/v1/sanctions/{id}/revoke",
     *     summary="Revocar una sanción",
     *     tags={"Sanctions"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la sanción",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"reason"},
     *             @OA\Property(property="reason", type="string", description="Motivo de la revocación")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Sanción revocada exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Sanción revocada exitosamente")
     *         )
     *     )
     * )
     */
    public function revoke(Request $request, int $id): JsonResponse
    {
        try {
            $validated = $request->validate([
                'reason' => 'required|string|max:500'
            ]);

            $sanction = Sanction::findOrFail($id);

            $this->sanctionService->revokeSanction($sanction, $validated['reason'], Auth::id());

            return response()->json([
                'success' => true,
                'message' => 'Sanción revocada exitosamente'
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
                'message' => 'Error al revocar sanción',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/v1/sanctions/{id}/appeal",
     *     summary="Apelar una sanción",
     *     tags={"Sanctions"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la sanción",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"reason"},
     *             @OA\Property(property="reason", type="string", description="Motivo de la apelación")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Apelación registrada exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Apelación registrada exitosamente")
     *         )
     *     )
     * )
     */
    public function appeal(Request $request, int $id): JsonResponse
    {
        try {
            $validated = $request->validate([
                'reason' => 'required|string|max:1000'
            ]);

            $sanction = Sanction::findOrFail($id);

            $this->sanctionService->appealSanction($sanction, $validated['reason']);

            return response()->json([
                'success' => true,
                'message' => 'Apelación registrada exitosamente'
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
                'message' => 'Error al registrar apelación',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/v1/players/{playerId}/sanctions/active",
     *     summary="Obtener sanciones activas de un jugador",
     *     tags={"Sanctions"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="playerId",
     *         in="path",
     *         required=true,
     *         description="ID del jugador",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Sanciones activas del jugador",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="is_eligible", type="boolean"),
     *                 @OA\Property(property="active_sanctions", type="array", @OA\Items(ref="#/components/schemas/Sanction")),
     *                 @OA\Property(property="restrictions", type="array", @OA\Items(type="string"))
     *             )
     *         )
     *     )
     * )
     */
    public function getPlayerActiveSanctions(int $playerId): JsonResponse
    {
        try {
            $player = Player::findOrFail($playerId);
            $eligibility = $this->sanctionService->checkPlayerEligibility($player);

            return response()->json([
                'success' => true,
                'data' => $eligibility
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al verificar elegibilidad del jugador',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/v1/matches/{matchId}/sanctions",
     *     summary="Obtener sanciones de un partido",
     *     tags={"Sanctions"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="matchId",
     *         in="path",
     *         required=true,
     *         description="ID del partido",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Sanciones del partido",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Sanction"))
     *         )
     *     )
     * )
     */
    public function getMatchSanctions(int $matchId): JsonResponse
    {
        try {
            $match = VolleyMatch::findOrFail($matchId);
            $sanctions = $this->sanctionService->getMatchSanctions($match);

            return response()->json([
                'success' => true,
                'data' => $sanctions
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener sanciones del partido',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}