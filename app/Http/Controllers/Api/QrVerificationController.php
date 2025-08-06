<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\VerifyQrRequest;
use App\Http\Requests\VerifyBatchRequest;
use App\Services\QrVerificationService;
use App\Models\PlayerCard;
use App\Models\QrScanLog;
use App\Models\MatchVerification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class QrVerificationController extends Controller
{
    public function __construct(
        private QrVerificationService $verificationService
    ) {}

    /**
     * @OA\Post(
     *     path="/api/verify-qr",
     *     tags={"QR Verification"},
     *     summary="Verificación principal de código QR",
     *     description="Verifica un código QR de carnet de jugador para eventos deportivos",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"qr_code", "verification_token", "scanner_id"},
     *             @OA\Property(property="qr_code", type="string", example="QR123456789"),
     *             @OA\Property(property="verification_token", type="string", example="TOKEN_ABC123"),
     *             @OA\Property(property="scanner_id", type="string", example="SCANNER_001"),
     *             @OA\Property(
     *                 property="event_data",
     *                 type="object",
     *                 @OA\Property(property="match_id", type="integer", example=1),
     *                 @OA\Property(property="event_type", type="string", example="match_entry")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Verificación exitosa",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Carnet válido"),
     *             @OA\Property(property="can_play", type="boolean", example=true),
     *             @OA\Property(property="response_time_ms", type="integer", example=150),
     *             @OA\Property(
     *                 property="player_info",
     *                 type="object",
     *                 @OA\Property(property="name", type="string", example="Juan Pérez"),
     *                 @OA\Property(property="card_number", type="string", example="VP2024001"),
     *                 @OA\Property(property="club", type="string", example="Club Deportivo")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Carnet inválido o suspendido",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="invalid"),
     *             @OA\Property(property="message", type="string", example="Carnet suspendido"),
     *             @OA\Property(property="can_play", type="boolean", example=false),
     *             @OA\Property(property="error_code", type="string", example="CARD_SUSPENDED")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Errores de validación",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\AdditionalProperties(
     *                     type="array",
     *                     @OA\Items(type="string")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function verify(VerifyQrRequest $request): JsonResponse
    {
        try {
            $startTime = microtime(true);

            // Datos validados del request
            $qrCode = $request->validated('qr_code');
            $verificationToken = $request->validated('verification_token');
            $scannerId = $request->validated('scanner_id');
            $eventData = $request->validated('event_data', []);

            // Realizar verificación
            $result = $this->verificationService->verifyQrCode(
                $qrCode,
                $verificationToken,
                $scannerId,
                $eventData
            );

            // Calcular tiempo de respuesta
            $responseTime = round((microtime(true) - $startTime) * 1000);
            $result['response_time_ms'] = $responseTime;

            // Log de la verificación
            $this->logVerification($result, $request, $responseTime);

            // Respuesta según el resultado
            return response()->json($result, $this->getHttpStatusCode($result['status']));

        } catch (\Exception $e) {
            Log::error('Error en verificación QR', [
                'qr_code' => $request->input('qr_code'),
                'scanner_id' => $request->input('scanner_id'),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Error interno del sistema',
                'can_play' => false,
                'error_code' => 'SYSTEM_ERROR'
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/qr-info/{qrCode}",
     *     tags={"QR Verification"},
     *     summary="Información básica del carnet",
     *     description="Obtiene información básica de un carnet sin registrar verificación",
     *     @OA\Parameter(
     *         name="qrCode",
     *         in="path",
     *         required=true,
     *         description="Código QR del carnet",
     *         @OA\Schema(type="string", example="QR123456789")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Información del carnet",
     *         @OA\JsonContent(
     *             @OA\Property(property="card_number", type="string", example="VP2024001"),
     *             @OA\Property(property="player_name", type="string", example="Juan Pérez"),
     *             @OA\Property(property="club_name", type="string", example="Club Deportivo"),
     *             @OA\Property(property="status", type="string", example="Activo"),
     *             @OA\Property(property="photo_url", type="string", nullable=true),
     *             @OA\Property(property="valid_until", type="string", format="date", example="2024-12-31")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Carnet no encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Carnet no encontrado")
     *         )
     *     )
     * )
     */
    public function getCardInfo(string $qrCode): JsonResponse
    {
        try {
            // Cache por 5 minutos para reducir carga
            $cacheKey = "qr_info_{$qrCode}";

            $info = Cache::remember($cacheKey, 300, function () use ($qrCode) {
                $card = PlayerCard::where('qr_code', $qrCode)
                    ->with(['player.user', 'player.currentClub'])
                    ->first();

                if (!$card) {
                    return null;
                }

                return [
                    'card_number' => $card->card_number,
                    'player_name' => $card->player->user->full_name,
                    'club_name' => $card->player->currentClub?->name,
                    'status' => $card->status->getLabel(),
                    'expires_at' => $card->expires_at->format('Y-m-d'),
                    'season' => $card->season,
                    'position' => $card->player->position->getLabel(),
                    'category' => $card->player->category->getLabel(),
                ];
            });

            if (!$info) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Código QR no válido'
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'data' => $info
            ]);

        } catch (\Exception $e) {
            Log::error('Error obteniendo info de QR', [
                'qr_code' => $qrCode,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Error obteniendo información'
            ], 500);
        }
    }

    /**
     * Verificación en lote para múltiples carnets
     *
     * @param VerifyBatchRequest $request
     * @return JsonResponse
     */
    public function verifyBatch(VerifyBatchRequest $request): JsonResponse
    {
        try {
            $qrCodes = $request->validated('qr_codes');
            $scannerId = $request->validated('scanner_id');
            $eventData = $request->validated('event_data', []);

            $results = [];
            $summary = [
                'total' => count($qrCodes),
                'approved' => 0,
                'rejected' => 0,
                'warnings' => 0
            ];

            foreach ($qrCodes as $qrData) {
                $result = $this->verificationService->verifyQrCode(
                    $qrData['qr_code'],
                    $qrData['verification_token'] ?? null,
                    $scannerId,
                    $eventData
                );

                $results[] = $result;

                // Actualizar estadísticas
                match($result['status']) {
                    'success' => $summary['approved']++,
                    'warning' => $summary['warnings']++,
                    default => $summary['rejected']++
                };
            }

            return response()->json([
                'status' => 'success',
                'summary' => $summary,
                'results' => $results
            ]);

        } catch (\Exception $e) {
            Log::error('Error en verificación batch', [
                'scanner_id' => $request->input('scanner_id'),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Error en verificación en lote'
            ], 500);
        }
    }

    /**
     * Estadísticas para verificadores autenticados
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getStats(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $days = $request->query('days', 30);

            $stats = QrScanLog::getVerificationStats($days);

            // Estadísticas específicas del usuario si es verificador
            if ($user->hasRole('Verifier')) {
                $userStats = QrScanLog::where('scanned_by', $user->id)
                    ->where('scanned_at', '>=', now()->subDays($days))
                    ->selectRaw('
                        COUNT(*) as total_scans,
                        COUNT(CASE WHEN scan_result = "success" THEN 1 END) as successful_scans,
                        AVG(response_time_ms) as avg_response_time
                    ')
                    ->first();

                $stats['user_stats'] = $userStats;
            }

            return response()->json([
                'status' => 'success',
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            Log::error('Error obteniendo estadísticas', [
                'user_id' => $request->user()?->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Error obteniendo estadísticas'
            ], 500);
        }
    }

    /**
     * Log de verificación
     */
    private function logVerification(array $result, Request $request, int $responseTime): void
    {
        QrScanLog::create([
            'player_card_id' => $result['card_id'] ?? null,
            'player_id' => $result['player_id'] ?? null,
            'qr_code_scanned' => $request->input('qr_code'),
            'scan_result' => $result['status'],
            'verification_status' => $result['verification_status'] ?? 'unknown',
            'scanned_by' => $request->input('scanner_id'),
            'scan_location' => $request->input('event_data.location'),
            'event_type' => $request->input('event_data.event_type', 'match'),
            'match_id' => $request->input('event_data.match_id'),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'device_info' => $request->input('device_info'),
            'latitude' => $request->input('location.latitude'),
            'longitude' => $request->input('location.longitude'),
            'verification_response' => $result,
            'additional_notes' => $request->input('notes'),
            'response_time_ms' => $responseTime,
            'app_version' => $request->header('X-App-Version'),
            'scanned_at' => now(),
        ]);
    }

    /**
     * Mapear status a código HTTP
     */
    private function getHttpStatusCode(string $status): int
    {
        return match($status) {
            'success' => 200,
            'warning' => 200,
            'error' => 400,
            'not_found' => 404,
            default => 500
        };
    }
}
