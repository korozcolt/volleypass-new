<?php

namespace App\Listeners;

use App\Events\DocumentsApproved;
use App\Services\AutomaticCardGenerationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class TriggerAutomaticCardGeneration implements ShouldQueue
{
    use InteractsWithQueue;

    public function __construct(
        private AutomaticCardGenerationService $cardGenerationService
    ) {}

    /**
     * Handle the event.
     */
    public function handle(DocumentsApproved $event): void
    {
        try {
            Log::info("Iniciando generación automática de carnet", [
                'player_id' => $event->player->id,
                'league_id' => $event->league->id,
                'approver_id' => $event->approver->id,
                'approved_documents' => $event->approvedDocuments
            ]);

            // Verificar si la jugadora puede tener carnet generado
            if (!$this->cardGenerationService->canGenerateCard($event->player, $event->league)) {
                Log::warning("Jugadora no elegible para generación automática de carnet", [
                    'player_id' => $event->player->id,
                    'league_id' => $event->league->id
                ]);
                return;
            }

            // Generar carnet automáticamente
            $card = $this->cardGenerationService->generateCard(
                $event->player,
                $event->league,
                $event->approver
            );

            Log::info("Carnet generado automáticamente exitosamente", [
                'player_id' => $event->player->id,
                'league_id' => $event->league->id,
                'card_id' => $card->id,
                'card_number' => $card->card_number
            ]);

        } catch (\Exception $e) {
            Log::error("Error en generación automática de carnet", [
                'player_id' => $event->player->id,
                'league_id' => $event->league->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // El error ya fue manejado en el servicio, no necesitamos re-lanzarlo
            // pero podríamos enviar una notificación adicional si es necesario
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(DocumentsApproved $event, \Throwable $exception): void
    {
        Log::error("Fallo crítico en listener de generación automática", [
            'player_id' => $event->player->id,
            'league_id' => $event->league->id,
            'exception' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);

        // Aquí podríamos enviar notificaciones de emergencia a administradores
    }
}
