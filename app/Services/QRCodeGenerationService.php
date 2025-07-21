<?php

namespace App\Services;

use App\Models\PlayerCard;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class QRCodeGenerationService
{
    /**
     * Generar código QR seguro para un carnet
     */
    public function generateQRCode(PlayerCard $card): void
    {
        // Generar token de verificación seguro
        $verificationToken = $this->createVerificationToken($card);

        // Crear datos para el QR
        $qrData = $this->buildQRData($card, $verificationToken);

        // Generar imagen QR
        $qrImage = $this->generateQRImage($qrData);

        // Actualizar el carnet con la información del QR
        $card->update([
            'qr_code' => $qrImage,
            'qr_token' => $verificationToken,
            'verification_token' => $verificationToken, // Mantener compatibilidad
        ]);
    }

    /**
     * Crear token de verificación JWT seguro
     */
    public function createVerificationToken(PlayerCard $card): string
    {
        $payload = [
            'card_id' => $card->id,
            'card_number' => $card->card_number,
            'player_id' => $card->player_id,
            'league_id' => $card->league_id,
            'issued_at' => $card->issued_at->timestamp,
            'expires_at' => $card->expires_at->timestamp,
            'generated_at' => now()->timestamp,
            'iat' => now()->timestamp,
            'exp' => $card->expires_at->timestamp,
        ];

        return JWT::encode($payload, config('app.key'), 'HS256');
    }

    /**
     * Construir datos para el código QR
     */
    private function buildQRData(PlayerCard $card, string $token): array
    {
        return [
            'card_id' => $card->id,
            'card_number' => $card->card_number,
            'verification_url' => $this->getVerificationUrl($token),
            'generated_at' => now()->toISOString(),
            'expires_at' => $card->expires_at->toISOString(),
            'version' => '1.0'
        ];
    }

    /**
     * Generar imagen QR con configuración optimizada
     */
    private function generateQRImage(array $data): string
    {
        $jsonData = json_encode($data);

        return QrCode::format('png')
            ->size(200)
            ->errorCorrection('H') // Nivel de corrección alto (30% redundancia)
            ->margin(2)
            ->generate($jsonData);
    }

    /**
     * Obtener URL de verificación
     */
    private function getVerificationUrl(string $token): string
    {
        return route('api.card.verify', ['token' => $token]);
    }

    /**
     * Verificar token de carnet
     */
    public function verifyToken(string $token): array
    {
        try {
            $payload = JWT::decode($token, new Key(config('app.key'), 'HS256'));

            // Verificar que el carnet existe y está activo
            $card = PlayerCard::find($payload->card_id);

            if (!$card) {
                return [
                    'valid' => false,
                    'error' => 'Carnet no encontrado',
                    'code' => 'CARD_NOT_FOUND'
                ];
            }

            // Verificar que el token corresponde al carnet
            if ($card->qr_token !== $token) {
                return [
                    'valid' => false,
                    'error' => 'Token inválido',
                    'code' => 'INVALID_TOKEN'
                ];
            }

            // Verificar estado del carnet
            $verificationResult = $card->getVerificationStatus();

            return [
                'valid' => $verificationResult['can_play'],
                'player_name' => $card->player->user->full_name,
                'card_number' => $card->card_number,
                'league' => $card->league->name,
                'club' => $card->player->currentClub->name,
                'status' => $card->status->value,
                'expires_at' => $card->expires_at->toISOString(),
                'verification_result' => $verificationResult,
                'verified_at' => now()->toISOString()
            ];

        } catch (\Firebase\JWT\ExpiredException $e) {
            return [
                'valid' => false,
                'error' => 'Token expirado',
                'code' => 'TOKEN_EXPIRED'
            ];
        } catch (\Firebase\JWT\SignatureInvalidException $e) {
            return [
                'valid' => false,
                'error' => 'Firma inválida',
                'code' => 'INVALID_SIGNATURE'
            ];
        } catch (\Exception $e) {
            return [
                'valid' => false,
                'error' => 'Error de verificación',
                'code' => 'VERIFICATION_ERROR'
            ];
        }
    }

    /**
     * Regenerar QR code para un carnet existente
     */
    public function regenerateQRCode(PlayerCard $card): void
    {
        $this->generateQRCode($card);
    }

    /**
     * Validar formato de datos QR
     */
    public function validateQRData(string $qrData): bool
    {
        try {
            $data = json_decode($qrData, true);

            if (!$data) {
                return false;
            }

            $requiredFields = ['card_id', 'card_number', 'verification_url', 'generated_at', 'expires_at'];

            foreach ($requiredFields as $field) {
                if (!isset($data[$field])) {
                    return false;
                }
            }

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Extraer información de datos QR
     */
    public function parseQRData(string $qrData): ?array
    {
        try {
            $data = json_decode($qrData, true);

            if (!$this->validateQRData($qrData)) {
                return null;
            }

            return $data;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Generar QR code para verificación offline
     */
    public function generateOfflineQR(PlayerCard $card): string
    {
        $offlineData = [
            'card_number' => $card->card_number,
            'player_name' => $card->player->user->full_name,
            'club' => $card->player->currentClub->name,
            'league' => $card->league->name,
            'expires_at' => $card->expires_at->format('Y-m-d'),
            'status' => $card->status->value,
            'offline' => true,
            'generated_at' => now()->format('Y-m-d H:i:s')
        ];

        return QrCode::format('png')
            ->size(150)
            ->errorCorrection('M')
            ->generate(json_encode($offlineData));
    }

    /**
     * Obtener estadísticas de verificación QR
     */
    public function getQRStats(PlayerCard $card): array
    {
        return [
            'card_number' => $card->card_number,
            'total_verifications' => $card->verification_count,
            'last_verified_at' => $card->last_verified_at?->toISOString(),
            'qr_generated_at' => $card->created_at->toISOString(),
            'verification_locations' => $card->verification_locations ?? [],
            'is_active' => $card->status->allowsPlay(),
            'expires_at' => $card->expires_at->toISOString()
        ];
    }
}
