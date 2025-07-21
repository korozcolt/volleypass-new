<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PlayerCard;
use App\Models\QrScanLog;
use App\Services\QRCodeGenerationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class CardVerificationController extends Controller
{
    public function __construct(
        private QRCodeGenerationService $qrService
    ) {}

    /**
     * Verificar carnet mediante token QR
     */
    public function verify(Request $request, string $token): JsonResponse
    {
        try {
            // Verificar el token
            $verificationResult = $this->qrService->verifyToken($token);

            // Registrar el escaneo
            $this->logScan($request, $token, $verificationResult);

            // Respuesta para verificación pública
            return response()->json([
                'valid' => $verificationResult['valid'],
                'message' => $verificationResult['valid'] ? 'Carnet válido' : 'Carnet inválido',
                'data' => $verificationResult['valid'] ? [
                    'player_name' => $verificationResult['player_name'] ?? null,
                    'card_number' => $verificationResult['card_number'] ?? null,
                    'league' => $verificationResult['league'] ?? null,
                    'club' => $verificationResult['club'] ?? null,
                    'status' => $verificationResult['verification_result']['message'] ?? null,
                    'can_play' => $verificationResult['verification_result']['can_play'] ?? false,
                    'expires_at' => $verificationResult['expires_at'] ?? null,
                ] : null,
                'verified_at' => now()->toISOString()
            ]);
        } catch (\Exception $e) {
            Log::error("Error en verificación de carnet", [
                'token' => substr($token, 0, 20) . '...',
                'error' => $e->getMessage(),
                'ip' => $request->ip()
            ]);

            return response()->json([
                'valid' => false,
                'message' => 'Error en la verificación',
                'error' => 'VERIFICATION_ERROR'
            ], 500);
        }
    }

    /**
     * Verificar carnet por número (para uso interno)
     */
    public function verifyByNumber(Request $request, string $cardNumber): JsonResponse
    {
        try {
            $card = PlayerCard::where('card_number', $cardNumber)->first();

            if (!$card) {
                return response()->json([
                    'valid' => false,
                    'message' => 'Carnet no encontrado',
                    'error' => 'CARD_NOT_FOUND'
                ], 404);
            }

            $verificationResult = $card->getVerificationStatus();

            // Registrar verificación
            $card->recordVerification(
                $request->user() ?? new \App\Models\User(['name' => 'Sistema']),
                ['verification_method' => 'card_number']
            );

            return response()->json([
                'valid' => $verificationResult['can_play'],
                'player_name' => $card->player->user->full_name,
                'card_number' => $card->card_number,
                'league' => $card->league->name,
                'club' => $card->player->currentClub->name,
                'status' => $card->status->value,
                'verification_result' => $verificationResult,
                'expires_at' => $card->expires_at->toISOString(),
                'verified_at' => now()->toISOString()
            ]);
        } catch (\Exception $e) {
            Log::error("Error en verificación por número", [
                'card_number' => $cardNumber,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'valid' => false,
                'message' => 'Error en la verificación',
                'error' => 'VERIFICATION_ERROR'
            ], 500);
        }
    }

    /**
     * Obtener información detallada del carnet (requiere autenticación)
     */
    public function details(Request $request, string $token): JsonResponse
    {
        try {
            $verificationResult = $this->qrService->verifyToken($token);

            if (!$verificationResult['valid']) {
                return response()->json([
                    'valid' => false,
                    'message' => $verificationResult['error'] ?? 'Token inválido'
                ], 400);
            }

            // Información completa para usuarios autenticados
            return response()->json([
                'valid' => true,
                'card' => $verificationResult,
                'verification_stats' => $this->qrService->getQRStats(
                    PlayerCard::find($verificationResult['card_id'] ?? null)
                )
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'valid' => false,
                'message' => 'Error obteniendo detalles',
                'error' => 'DETAILS_ERROR'
            ], 500);
        }
    }

    /**
     * Registrar escaneo de QR
     */
    private function logScan(Request $request, string $token, array $verificationResult): void
    {
        try {
            // Extraer información básica del token sin decodificar completamente
            $cardId = $verificationResult['card_id'] ?? null;
            $playerId = null;

            if ($cardId) {
                $card = PlayerCard::find($cardId);
                $playerId = $card?->player_id;
            }

            QrScanLog::logScan([
                'player_card_id' => $cardId,
                'player_id' => $playerId,
                'qr_code_scanned' => substr($token, 0, 50), // Solo primeros 50 caracteres
                'scan_result' => $verificationResult['valid'] ?
                    \App\Enums\VerificationResult::Success :
                    \App\Enums\VerificationResult::Error,
                'verification_status' => $verificationResult['verification_result']['message'] ?? 'Unknown',
                'scanned_by' => $request->user()?->id,
                'scan_location' => $request->input('location'),
                'event_type' => \App\Enums\EventType::Verification,
                'verification_response' => $verificationResult,
                'response_time_ms' => 0, // Se calculará en el modelo
            ]);
        } catch (\Exception $e) {
            Log::warning("Error registrando escaneo QR", [
                'error' => $e->getMessage(),
                'token' => substr($token, 0, 20) . '...'
            ]);
        }
    }

    /**
     * Obtener estadísticas de verificaciones
     */
    public function stats(Request $request): JsonResponse
    {
        $days = $request->input('days', 30);

        return response()->json([
            'verification_stats' => QrScanLog::getVerificationStats($days),
            'period_days' => $days,
            'generated_at' => now()->toISOString()
        ]);
    }
}
