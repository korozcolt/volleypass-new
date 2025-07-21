<?php

namespace App\Jobs;

use App\Models\Player;
use App\Models\League;
use App\Models\User;
use App\Services\AutomaticCardGenerationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GeneratePlayerCardJob implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels;

    public $tries = 3;
    public $backoff = [30, 60, 120]; // seconds
    public $timeout = 300; // 5 minutes

    public function __construct(
        public Player $player,
        public League $league,
        public ?User $triggeredBy = null,
        public array $metadata = []
    ) {}

    /**
     * Execute the job.
     */
    public function handle(AutomaticCardGenerationService $cardGenerationService): void
    {
        try {
            Log::info("Iniciando job de generación de carnet", [
                'player_id' => $this->player->id,
                'league_id' => $this->league->id,
                'attempt' => $this->attempts(),
                'job_id' => $this->job?->getJobId()
            ]);

            $card = $cardGenerationService->generateCard(
                $this->player,
                $this->league,
                $this->triggeredBy
            );

            Log::info("Job de generación de carnet completado exitosamente", [
                'player_id' => $this->player->id,
                'league_id' => $this->league->id,
                'card_id' => $card->id,
                'card_number' => $card->card_number,
                'job_id' => $this->job?->getJobId()
            ]);

        } catch (\Exception $e) {
            Log::error("Error en job de generación de carnet", [
                'player_id' => $this->player->id,
                'league_id' => $this->league->id,
                'attempt' => $this->attempts(),
                'error' => $e->getMessage(),
                'job_id' => $this->job?->getJobId()
            ]);

            // Re-lanzar la excepción para que el sistema de colas maneje los reintentos
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("Job de generación de carnet falló definitivamente", [
            'player_id' => $this->player->id,
            'league_id' => $this->league->id,
            'attempts' => $this->attempts(),
            'exception' => $exception->getMessage(),
            'job_id' => $this->job?->getJobId()
        ]);

        // Aquí podríamos:
        // 1. Enviar notificación a administradores
        // 2. Crear un registro de error para revisión manual
        // 3. Programar reintento manual más tarde
    }

    /**
     * Get the tags that should be assigned to the job.
     */
    public function tags(): array
    {
        return [
            'card-generation',
            'player:' . $this->player->id,
            'league:' . $this->league->id
        ];
    }

    /**
     * Calculate the number of seconds to wait before retrying the job.
     */
    public function backoff(): array
    {
        return $this->backoff;
    }
}
