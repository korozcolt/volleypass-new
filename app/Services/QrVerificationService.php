<?php

namespace App\Services;

use App\Models\PlayerCard;
use App\Models\User;
use App\Models\Player;
use App\Models\MedicalCertificate;
use App\Models\QrScanLog;
use App\Models\MatchVerification;
use App\Enums\CardStatus;
use App\Enums\MedicalStatus;
use App\Enums\EventType;
use App\Enums\VerificationResult;
use App\Enums\UserStatus;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class QrVerificationService
{
    /**
     * Verificar código QR principal
     *
     * @param string $qrCode
     * @param string|null $verificationToken
     * @param int $scannerId
     * @param array $eventData
     * @return array
     */
    public function verifyQrCode(
        string $qrCode,
        ?string $verificationToken,
        int $scannerId,
        array $eventData = []
    ): array {
        try {
            // 1. Validar entrada básica
            if (empty($qrCode) || strlen($qrCode) !== 64) {
                return $this->buildErrorResponse(
                    'error',
                    'Código QR inválido',
                    'INVALID_QR_FORMAT'
                );
            }

            // 2. Buscar el carnet por código QR
            $card = $this->findPlayerCard($qrCode, $verificationToken);

            if (!$card) {
                return $this->buildErrorResponse(
                    'error',
                    'Código QR no válido o no encontrado',
                    'QR_NOT_FOUND'
                );
            }

            // 3. Validar el verificador
            $scanner = $this->validateScanner($scannerId);
            if (!$scanner) {
                return $this->buildErrorResponse(
                    'error',
                    'Verificador no autorizado',
                    'INVALID_SCANNER'
                );
            }

            // 4. Verificar estado del carnet
            $cardValidation = $this->validateCard($card);
            if (!$cardValidation['is_valid']) {
                return $this->buildErrorResponse(
                    'error',
                    $cardValidation['message'],
                    $cardValidation['error_code'],
                    $card
                );
            }

            // 5. Verificar estado de la jugadora
            $playerValidation = $this->validatePlayer($card->player);
            if (!$playerValidation['is_valid']) {
                return $this->buildErrorResponse(
                    'error',
                    $playerValidation['message'],
                    $playerValidation['error_code'],
                    $card
                );
            }

            // 6. Verificar documentos básicos
            $documentsValidation = $this->validatePlayerDocuments($card->player);
            if (!$documentsValidation['is_valid']) {
                return $this->buildErrorResponse(
                    'error',
                    $documentsValidation['message'],
                    $documentsValidation['error_code'],
                    $card
                );
            }

            // 7. Verificar certificados médicos
            $medicalValidation = $this->validateMedicalStatus($card->player);
            if (!$medicalValidation['is_valid']) {
                return $this->buildResponse(
                    $medicalValidation['status'],
                    $medicalValidation['message'],
                    $medicalValidation['can_play'],
                    $card,
                    $medicalValidation
                );
            }

            // 8. Verificar restricciones por tipo de evento
            $eventValidation = $this->validateForEvent($card, $eventData);
            if (!$eventValidation['is_valid']) {
                return $this->buildResponse(
                    'warning',
                    $eventValidation['message'],
                    $eventValidation['can_play'],
                    $card,
                    $eventValidation
                );
            }

            // 9. Verificar límites de uso (anti-fraude)
            $usageValidation = $this->validateUsageLimits($card, $eventData);
            if (!$usageValidation['is_valid']) {
                return $this->buildResponse(
                    'warning',
                    $usageValidation['message'],
                    $usageValidation['can_play'],
                    $card,
                    $usageValidation
                );
            }

            // 10. Registrar verificación exitosa
            $this->recordSuccessfulVerification($card, $scanner, $eventData);

            // 11. Respuesta exitosa
            return $this->buildSuccessResponse($card, $medicalValidation, $eventValidation);

        } catch (\Exception $e) {
            Log::error('Error en QrVerificationService', [
                'qr_code' => $qrCode,
                'scanner_id' => $scannerId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->buildErrorResponse(
                'error',
                'Error interno del sistema',
                'SYSTEM_ERROR'
            );
        }
    }

    /**
     * Buscar carnet por QR y token
     *
     * @param string $qrCode
     * @param string|null $verificationToken
     * @return PlayerCard|null
     */
    private function findPlayerCard(string $qrCode, ?string $verificationToken): ?PlayerCard
    {
        // Cache por 1 minuto para reducir consultas DB
        $cacheKey = "card_qr_{$qrCode}";

        return Cache::remember($cacheKey, 60, function () use ($qrCode, $verificationToken) {
            $query = PlayerCard::where('qr_code', $qrCode)
                ->with([
                    'player.user.country',
                    'player.user.department',
                    'player.user.city',
                    'player.currentClub.league',
                    'player.medicalCertificates' => function($q) {
                        $q->current()->orderBy('expires_at', 'desc');
                    }
                ]);

            // Si se proporciona token de verificación, validarlo también
            if ($verificationToken) {
                $query->where('verification_token', $verificationToken);
            }

            return $query->first();
        });
    }

    /**
     * Validar verificador
     *
     * @param int $scannerId
     * @return User|null
     */
    private function validateScanner(int $scannerId): ?User
    {
        return Cache::remember("scanner_{$scannerId}", 300, function () use ($scannerId) {
            return User::where('id', $scannerId)
                ->whereHas('roles', function($q) {
                    $q->whereIn('name', ['Verifier', 'LeagueAdmin', 'SuperAdmin', 'ClubDirector']);
                })
                ->where('status', UserStatus::Active)
                ->first();
        });
    }

    /**
     * Validar estado del carnet
     *
     * @param PlayerCard $card
     * @return array
     */
    private function validateCard(PlayerCard $card): array
    {
        // Verificar si está vencido
        if ($card->is_expired) {
            return [
                'is_valid' => false,
                'message' => 'Carnet vencido desde ' . $card->expires_at->format('d/m/Y'),
                'error_code' => 'CARD_EXPIRED',
                'expires_at' => $card->expires_at->format('Y-m-d')
            ];
        }

        // Verificar si está por vencer (warning)
        if ($card->is_expiring) {
            $daysLeft = $card->expires_at->diffInDays(now());
            // No es error, pero se incluirá como warning en la respuesta
        }

        // Verificar estado del carnet
        if (!$card->status->allowsPlay()) {
            $statusMessage = match($card->status) {
                CardStatus::Suspended => 'Carnet suspendido - Contactar administración',
                CardStatus::Cancelled => 'Carnet cancelado',
                CardStatus::Replaced => 'Carnet reemplazado - Usar el nuevo carnet',
                CardStatus::Pending_Approval => 'Carnet pendiente de aprobación',
                default => 'Carnet no válido'
            };

            return [
                'is_valid' => false,
                'message' => $statusMessage,
                'error_code' => 'CARD_INVALID_STATUS',
                'card_status' => $card->status->value
            ];
        }

        // Verificar temporada
        $currentSeason = now()->year;
        if ($card->season < $currentSeason - 1) {
            return [
                'is_valid' => false,
                'message' => "Carnet de temporada anterior ({$card->season})",
                'error_code' => 'CARD_OLD_SEASON',
                'card_season' => $card->season,
                'current_season' => $currentSeason
            ];
        }

        return ['is_valid' => true];
    }

    /**
     * Validar estado de la jugadora
     *
     * @param Player $player
     * @return array
     */
    private function validatePlayer(Player $player): array
    {
        // Verificar si la jugadora está activa
        if (!$player->is_active) {
            return [
                'is_valid' => false,
                'message' => 'Jugadora inactiva en el sistema',
                'error_code' => 'PLAYER_INACTIVE'
            ];
        }

        // Verificar estado del usuario
        if ($player->user->status !== UserStatus::Active) {
            $statusMessage = match($player->user->status) {
                UserStatus::Suspended => 'Jugadora suspendida del sistema',
                UserStatus::Blocked => 'Jugadora bloqueada - Contactar administración',
                UserStatus::Pending => 'Registro de jugadora pendiente',
                default => 'Jugadora no disponible'
            };

            return [
                'is_valid' => false,
                'message' => $statusMessage,
                'error_code' => 'PLAYER_USER_INACTIVE'
            ];
        }

        // Verificar elegibilidad
        if (!$player->is_eligible) {
            return [
                'is_valid' => false,
                'message' => 'Jugadora no elegible para competir',
                'error_code' => 'PLAYER_NOT_ELIGIBLE',
                'eligibility_date' => $player->eligibility_checked_at?->format('Y-m-d')
            ];
        }

        // Verificar si está retirada
        if ($player->retirement_date && $player->retirement_date->isPast()) {
            return [
                'is_valid' => false,
                'message' => 'Jugadora retirada del deporte activo',
                'error_code' => 'PLAYER_RETIRED',
                'retirement_date' => $player->retirement_date->format('Y-m-d')
            ];
        }

        // Verificar categoría por edad
        if (!$player->isInCorrectCategory()) {
            return [
                'is_valid' => false,
                'message' => 'Jugadora no está en la categoría correcta para su edad',
                'error_code' => 'PLAYER_WRONG_CATEGORY',
                'current_category' => $player->category->getLabel(),
                'player_age' => $player->user->age
            ];
        }

        return ['is_valid' => true];
    }

    /**
     * Validar documentos de la jugadora
     *
     * @param Player $player
     * @return array
     */
    private function validatePlayerDocuments(Player $player): array
    {
        // Verificar documentos básicos requeridos
        $requiredDocs = ['identity_card', 'birth_certificate', 'photo'];
        $missingDocs = [];

        foreach ($requiredDocs as $docType) {
            $hasValidDoc = $player->playerDocuments()
                ->where('document_type', $docType)
                ->where('status', 'approved')
                ->exists();

            if (!$hasValidDoc) {
                $missingDocs[] = $docType;
            }
        }

        if (!empty($missingDocs)) {
            return [
                'is_valid' => false,
                'message' => 'Documentos faltantes: ' . implode(', ', $missingDocs),
                'error_code' => 'MISSING_DOCUMENTS',
                'missing_documents' => $missingDocs
            ];
        }

        return ['is_valid' => true];
    }

    /**
     * Validar estado médico
     *
     * @param Player $player
     * @return array
     */
    private function validateMedicalStatus(Player $player): array
    {
        // Obtener certificado médico vigente
        $medicalCert = $player->medicalCertificates()
            ->current()
            ->where('status', 'approved')
            ->where('expires_at', '>', now())
            ->first();

        if (!$medicalCert) {
            // Buscar el más reciente para dar más información
            $lastCert = $player->medicalCertificates()
                ->orderBy('expires_at', 'desc')
                ->first();

            $message = 'Sin certificado médico válido';
            if ($lastCert) {
                if ($lastCert->expires_at->isPast()) {
                    $message = 'Certificado médico vencido desde ' . $lastCert->expires_at->format('d/m/Y');
                } else {
                    $message = 'Certificado médico pendiente de aprobación';
                }
            }

            return [
                'is_valid' => false,
                'status' => 'error',
                'message' => $message,
                'can_play' => false,
                'error_code' => 'NO_MEDICAL_CERTIFICATE',
                'last_certificate_date' => $lastCert?->expires_at?->format('Y-m-d')
            ];
        }

        // Verificar si el certificado está por vencer
        $warnings = [];
        if ($medicalCert->is_expiring) {
            $daysLeft = $medicalCert->days_until_expiry;
            $warnings[] = "Certificado médico vence en {$daysLeft} días";
        }

        // Verificar estado médico específico
        $medicalStatus = $medicalCert->medical_status;

        return match($medicalStatus) {
            MedicalStatus::Fit => [
                'is_valid' => true,
                'status' => 'success',
                'message' => 'Jugadora apta médicamente',
                'can_play' => true,
                'medical_status' => $medicalStatus,
                'expires_at' => $medicalCert->expires_at->format('Y-m-d'),
                'doctor' => $medicalCert->doctor_name,
                'warnings' => $warnings
            ],

            MedicalStatus::Restricted => [
                'is_valid' => true,
                'status' => 'warning',
                'message' => 'Jugadora con restricciones médicas',
                'can_play' => true,
                'medical_status' => $medicalStatus,
                'restrictions' => $medicalCert->restrictions ?? [],
                'expires_at' => $medicalCert->expires_at->format('Y-m-d'),
                'doctor' => $medicalCert->doctor_name,
                'warnings' => array_merge($warnings, ['Verificar restricciones médicas antes de jugar'])
            ],

            MedicalStatus::Under_Treatment => [
                'is_valid' => false,
                'status' => 'error',
                'message' => 'Jugadora en tratamiento médico - No puede participar',
                'can_play' => false,
                'medical_status' => $medicalStatus,
                'error_code' => 'MEDICAL_UNDER_TREATMENT'
            ],

            MedicalStatus::Recovery => [
                'is_valid' => false,
                'status' => 'error',
                'message' => 'Jugadora en proceso de recuperación - No puede participar',
                'can_play' => false,
                'medical_status' => $medicalStatus,
                'error_code' => 'MEDICAL_IN_RECOVERY'
            ],

            default => [
                'is_valid' => false,
                'status' => 'error',
                'message' => 'Estado médico no permite participación',
                'can_play' => false,
                'medical_status' => $medicalStatus,
                'error_code' => 'MEDICAL_STATUS_UNFIT'
            ]
        };
    }

    /**
     * Validar restricciones por tipo de evento
     *
     * @param PlayerCard $card
     * @param array $eventData
     * @return array
     */
    private function validateForEvent(PlayerCard $card, array $eventData): array
    {
        $eventType = EventType::tryFrom($eventData['event_type'] ?? 'match') ?? EventType::Match;
        $player = $card->player;
        $warnings = [];

        // Para eventos oficiales, aplicar restricciones más estrictas
        if ($eventType->requiresStrictMedicalCheck()) {

            // Verificar restricciones específicas del carnet
            if ($card->restrictions) {
                $applicableRestrictions = [];

                foreach ($card->restrictions as $restriction) {
                    if ($this->restrictionAppliesTo($restriction, $eventType, $player->position)) {
                        $applicableRestrictions[] = $restriction;
                    }
                }

                if (!empty($applicableRestrictions)) {
                    $canPlay = $eventType->allowsRestrictedPlayers();

                    return [
                        'is_valid' => $canPlay,
                        'message' => $canPlay
                            ? 'Jugadora con restricciones para este evento'
                            : 'Restricciones impiden participación en este evento',
                        'can_play' => $canPlay,
                        'restrictions' => $applicableRestrictions,
                        'event_type' => $eventType->getLabel(),
                        'warnings' => ['Verificar restricciones antes de permitir participación']
                    ];
                }
            }

            // Verificar edad para categoría en eventos oficiales
            if (!$player->isInCorrectCategory()) {
                return [
                    'is_valid' => false,
                    'message' => 'Edad no corresponde a la categoría en eventos oficiales',
                    'can_play' => false,
                    'player_age' => $player->user->age,
                    'category' => $player->category->getLabel()
                ];
            }
        }

        // Verificar si el carnet está próximo a vencer para eventos importantes
        if ($card->is_expiring && in_array($eventType, [EventType::Tournament, EventType::Match])) {
            $daysLeft = $card->expires_at->diffInDays(now());
            $warnings[] = "Carnet vence en {$daysLeft} días";
        }

        return [
            'is_valid' => true,
            'can_play' => true,
            'event_type' => $eventType->getLabel(),
            'warnings' => $warnings
        ];
    }

    /**
     * Validar límites de uso para detectar fraude
     *
     * @param PlayerCard $card
     * @param array $eventData
     * @return array
     */
    private function validateUsageLimits(PlayerCard $card, array $eventData): array
    {
        $warnings = [];

        // Verificar uso excesivo en las últimas horas
        $recentScans = QrScanLog::where('player_card_id', $card->id)
            ->where('scanned_at', '>=', now()->subHours(6))
            ->where('scan_result', 'success')
            ->count();

        if ($recentScans > 10) {
            $warnings[] = "Uso frecuente detectado ({$recentScans} verificaciones en 6 horas)";
        }

        // Verificar si ya se verificó en este evento específico
        if (isset($eventData['match_id'])) {
            $alreadyVerified = QrScanLog::where('player_card_id', $card->id)
                ->where('match_id', $eventData['match_id'])
                ->where('scan_result', 'success')
                ->exists();

            if ($alreadyVerified) {
                return [
                    'is_valid' => true,
                    'message' => 'Jugadora ya verificada para este evento',
                    'can_play' => true,
                    'warnings' => ['Ya verificada previamente en este evento']
                ];
            }
        }

        // Verificar ubicación sospechosa (opcional)
        if (isset($eventData['location'])) {
            $lastLocation = QrScanLog::where('player_card_id', $card->id)
                ->where('scanned_at', '>=', now()->subHours(1))
                ->whereNotNull('latitude')
                ->latest('scanned_at')
                ->first();

            if ($lastLocation && isset($eventData['latitude'], $eventData['longitude'])) {
                // Lógica de distancia geográfica aquí si es necesario
            }
        }

        return [
            'is_valid' => true,
            'can_play' => true,
            'warnings' => $warnings
        ];
    }

    /**
     * Verificar si una restricción aplica al evento/posición
     *
     * @param array $restriction
     * @param EventType $eventType
     * @param mixed $position
     * @return bool
     */
    private function restrictionAppliesTo(array $restriction, EventType $eventType, $position): bool
    {
        // Verificar restricciones por tipo de evento
        if (isset($restriction['events'])) {
            if (!in_array($eventType->value, $restriction['events'])) {
                return false;
            }
        }

        // Verificar restricciones por posición
        if (isset($restriction['positions'])) {
            if (!in_array($position->value, $restriction['positions'])) {
                return false;
            }
        }

        // Verificar restricciones por fecha
        if (isset($restriction['valid_until'])) {
            if (Carbon::parse($restriction['valid_until'])->isPast()) {
                return false;
            }
        }

        return true;
    }

    /**
     * Registrar verificación exitosa
     *
     * @param PlayerCard $card
     * @param User $scanner
     * @param array $eventData
     * @return void
     */
    private function recordSuccessfulVerification(PlayerCard $card, User $scanner, array $eventData): void
    {
        // Actualizar contador del carnet
        $card->recordVerification($scanner, [
            'location' => $eventData['location'] ?? null,
            'event_type' => $eventData['event_type'] ?? 'match',
            'match_id' => $eventData['match_id'] ?? null,
        ]);

        // Actualizar estadísticas del evento si existe
        if (isset($eventData['match_id'])) {
            $matchVerification = MatchVerification::find($eventData['match_id']);
            $matchVerification?->updateStats();
        }
    }

    /**
     * Construir respuesta de éxito
     *
     * @param PlayerCard $card
     * @param array $medicalData
     * @param array $eventData
     * @return array
     */
    private function buildSuccessResponse(PlayerCard $card, array $medicalData, array $eventData = []): array
    {
        $allWarnings = array_merge(
            $medicalData['warnings'] ?? [],
            $eventData['warnings'] ?? []
        );

        return [
            'status' => !empty($allWarnings) ? 'warning' : 'success',
            'message' => !empty($allWarnings)
                ? 'Jugadora APTA con observaciones'
                : 'Jugadora APTA para jugar',
            'can_play' => true,
            'verification_status' => 'apta',
            'card_id' => $card->id,
            'player_id' => $card->player_id,
            'player' => [
                'name' => $card->player->user->full_name,
                'document_number' => $card->player->user->document_number,
                'age' => $card->player->user->age,
                'position' => $card->player->position->getLabel(),
                'category' => $card->player->category->getLabel(),
                'jersey_number' => $card->player->jersey_number,
                'club' => [
                    'name' => $card->player->currentClub?->name,
                    'short_name' => $card->player->currentClub?->short_name,
                    'league' => $card->player->currentClub?->league?->name,
                ]
            ],
            'card' => [
                'number' => $card->card_number,
                'status' => $card->status->getLabel(),
                'expires_at' => $card->expires_at->format('Y-m-d'),
                'season' => $card->season,
                'verification_count' => $card->verification_count + 1,
                'last_verified' => now()->format('Y-m-d H:i:s')
            ],
            'medical' => [
                'status' => $medicalData['medical_status']->getLabel(),
                'expires_at' => $medicalData['expires_at'] ?? null,
                'doctor' => $medicalData['doctor'] ?? null,
                'restrictions' => $medicalData['restrictions'] ?? []
            ],
            'warnings' => $allWarnings,
            'verification_timestamp' => now()->toISOString(),
            'valid_for_events' => $this->getValidEvents($card),
        ];
    }

    /**
     * Construir respuesta de error
     *
     * @param string $status
     * @param string $message
     * @param string $errorCode
     * @param PlayerCard|null $card
     * @return array
     */
    private function buildErrorResponse(
        string $status,
        string $message,
        string $errorCode,
        ?PlayerCard $card = null
    ): array {
        $response = [
            'status' => $status,
            'message' => $message,
            'can_play' => false,
            'verification_status' => 'no_apta',
            'error_code' => $errorCode,
            'verification_timestamp' => now()->toISOString(),
        ];

        if ($card) {
            $response['card_id'] = $card->id;
            $response['player_id'] = $card->player_id;
            $response['player'] = [
                'name' => $card->player->user->full_name,
                'document_number' => $card->player->user->document_number,
            ];
        }

        return $response;
    }

    /**
     * Construir respuesta personalizada
     *
     * @param string $status
     * @param string $message
     * @param bool $canPlay
     * @param PlayerCard $card
     * @param array $additionalData
     * @return array
     */
    private function buildResponse(
        string $status,
        string $message,
        bool $canPlay,
        PlayerCard $card,
        array $additionalData = []
    ): array {
        $response = [
            'status' => $status,
            'message' => $message,
            'can_play' => $canPlay,
            'verification_status' => $canPlay ? 'restriccion' : 'no_apta',
            'card_id' => $card->id,
            'player_id' => $card->player_id,
            'player' => [
                'name' => $card->player->user->full_name,
                'document_number' => $card->player->user->document_number,
                'position' => $card->player->position->getLabel(),
                'category' => $card->player->category->getLabel(),
            ],
            'verification_timestamp' => now()->toISOString(),
        ];

        return array_merge($response, $additionalData);
    }

    /**
     * Obtener eventos válidos para el carnet
     *
     * @param PlayerCard $card
     * @return array
     */
    private function getValidEvents(PlayerCard $card): array
    {
        $validEvents = [];

        foreach (EventType::cases() as $eventType) {
            $validation = $this->validateForEvent($card, ['event_type' => $eventType->value]);
            if ($validation['can_play']) {
                $validEvents[] = $eventType->value;
            }
        }

        return $validEvents;
    }
}
