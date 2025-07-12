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
     * Verificación principal de código QR
     *
     * @param VerifyQrRequest $request
     * @return JsonResponse
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
     * Información básica del carnet (sin logging)
     *
     * @param string $qrCode
     * @return JsonResponse
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
