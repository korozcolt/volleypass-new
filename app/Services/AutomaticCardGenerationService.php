<?php

namespace App\Services;

use App\Models\Player;
use App\Models\League;
use App\Models\PlayerCard;
use App\Models\CardGenerationLog;
use App\Enums\CardStatus;
use App\Enums\CardGenerationStatus;
use App\Services\CardNumberingService;
use App\Services\CardValidationService;
use App\Services\QRCodeGenerationService;
use App\Services\CardNotificationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class AutomaticCardGenerationService
{
    public function __construct(
        private CardNumberingService $numberingService,
        private CardValidationService $validationService,
        private QRCodeGenerationService $qrService,
        private CardNotificationService $notificationService
    ) {}

    /**
     * Generar carnet automáticamente para una jugadora
     */
    public function generateCard(Player $player, League $league, $triggeredBy = null): PlayerCard
    {
        // Crear log de generación
        $log = CardGenerationLog::logGeneration([
            'player_id' => $player->id,
            'league_id' => $league->id,
            'status' => CardGenerationStatus::Pending,
            'triggered_by' => $triggeredBy?->id,
        ]);

        try {
            return $this->processGeneration($player, $league, $log);
        } catch (Exception $e) {
            $log->markAsFailed($e->getMessage(), [
                'exception' => get_class($e),
                'trace' => $e->getTraceAsString()
            ]);

            Log::error("Error en generación automática de carnet", [
                'player_id' => $player->id,
                'league_id' => $league->id,
                'error' => $e->getMessage(),
                'log_id' => $log->id
            ]);

            throw $e;
        }
    }

    /**
     * Procesar la generación completa del carnet
     */
    private function processGeneration(Player $player, League $league, CardGenerationLog $log): PlayerCard
    {
        return DB::transaction(function () use ($player, $league, $log) {
            // Fase 1: Validación
            $log->updateStatus(CardGenerationStatus::Validating);
            $validationResult = $this->validationService->validateForGeneration($player, $league);

            if (!$validationResult->isValid()) {
                throw new Exception("Validación fallida: " . $validationResult->getErrorMessage());
            }

            // Fase 2: Generación
            $log->updateStatus(CardGenerationStatus::Generating, [
                'validation_results' => $validationResult->toArray()
            ]);

            // Generar número único
            $cardNumber = $this->numberingService->generateCardNumber($league);

            // Crear el carnet
            $card = $this->createPlayerCard($player, $league, $cardNumber);

            // Generar QR code
            $this->qrService->generateQRCode($card);

            // Marcar como completado
            $log->markAsCompleted($card);

            // Enviar notificaciones
            $this->notificationService->notifyCardGenerated($card);

            Log::info("Carnet generado automáticamente", [
                'player_id' => $player->id,
                'league_id' => $league->id,
                'card_number' => $cardNumber,
                'processing_time' => $log->processing_time_seconds
            ]);

            return $card;
        });
    }

    /**
     * Crear el registro del carnet en la base de datos
     */
    private function createPlayerCard(Player $player, League $league, string $cardNumber): PlayerCard
    {
        return PlayerCard::create([
            'player_id' => $player->id,
            'league_id' => $league->id,
            'card_number' => $cardNumber,
            'status' => CardStatus::Active,
            'generation_status' => CardGenerationStatus::Completed,
            'season' => now()->year,
            'issued_at' => now(),
            'expires_at' => now()->addYear(),
            'generation_started_at' => now(),
            'generation_completed_at' => now(),
            'template_version' => '1.0',
            'version' => 1,
            'generation_metadata' => [
                'generated_automatically' => true,
                'generation_method' => 'automatic_on_approval',
                'generated_at' => now()->toISOString(),
            ]
        ]);
    }

    /**
     * Reintentar generación fallida
     */
    public function retryGeneration(CardGenerationLog $log, $triggeredBy = null): PlayerCard
    {
        if (!$log->status->canRetry()) {
            throw new Exception("El log de generación no puede ser reintentado en su estado actual: {$log->status->value}");
        }

        $log->incrementRetry();

        try {
            $player = $log->player;
            $league = $log->league;

            return $this->processGeneration($player, $league, $log);
        } catch (Exception $e) {
            $log->markAsFailed($e->getMessage(), [
                'retry_attempt' => $log->retry_count,
                'exception' => get_class($e)
            ]);

            throw $e;
        }
    }

    /**
     * Verificar si una jugadora puede tener carnet generado automáticamente
     */
    public function canGenerateCard(Player $player, League $league): bool
    {
        try {
            $validationResult = $this->validationService->validateForGeneration($player, $league);
            return $validationResult->isValid();
        } catch (Exception $e) {
            Log::warning("Error verificando elegibilidad para generación automática", [
                'player_id' => $player->id,
                'league_id' => $league->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Generar carnets para múltiples jugadoras (procesamiento en lote)
     */
    public function generateBatchCards(array $playerIds, League $league, $triggeredBy = null): array
    {
        $results = [];

        foreach ($playerIds as $playerId) {
            try {
                $player = Player::findOrFail($playerId);
                $card = $this->generateCard($player, $league, $triggeredBy);

                $results[] = [
                    'player_id' => $playerId,
                    'success' => true,
                    'card_id' => $card->id,
                    'card_number' => $card->card_number
                ];
            } catch (Exception $e) {
                $results[] = [
                    'player_id' => $playerId,
                    'success' => false,
                    'error' => $e->getMessage()
                ];
            }
        }

        return $results;
    }

    /**
     * Obtener estadísticas de generación automática
     */
    public function getGenerationStats(?League $league = null, int $days = 30): array
    {
        $query = CardGenerationLog::where('created_at', '>=', now()->subDays($days));

        if ($league) {
            $query->where('league_id', $league->id);
        }

        $stats = CardGenerationLog::getGenerationStats($days);

        if ($league) {
            $stats['league_name'] = $league->name;
            $stats['league_specific'] = true;
        }

        return $stats;
    }

    /**
     * Limpiar logs antiguos de generación
     */
    public function cleanOldLogs(int $daysToKeep = 90): int
    {
        return CardGenerationLog::where('created_at', '<', now()->subDays($daysToKeep))
            ->where('status', CardGenerationStatus::Completed)
            ->delete();
    }

    /**
     * Obtener carnets pendientes de generación
     */
    public function getPendingGenerations(?League $league = null): array
    {
        $query = CardGenerationLog::inProgress();

        if ($league) {
            $query->where('league_id', $league->id);
        }

        return $query->with(['player.user', 'league'])
            ->orderBy('created_at')
            ->get()
            ->toArray();
    }
}
