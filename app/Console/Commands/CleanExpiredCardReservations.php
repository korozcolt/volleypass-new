<?php

namespace App\Console\Commands;

use App\Services\CardNumberingService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CleanExpiredCardReservations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cards:clean-reservations
                            {--dry-run : Show what would be deleted without actually deleting}
                            {--force : Force the operation without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean expired card number reservations from the database';

    public function __construct(
        private CardNumberingService $numberingService
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🧹 Limpiando reservas de números de carnet expiradas...');

        try {
            if ($this->option('dry-run')) {
                $count = $this->dryRun();
                $this->info("🔍 Modo dry-run: Se eliminarían {$count} reservas expiradas");
                return self::SUCCESS;
            }

            if (!$this->option('force') && !$this->confirm('¿Continuar con la limpieza de reservas expiradas?')) {
                $this->info('❌ Operación cancelada');
                return self::SUCCESS;
            }

            $deletedCount = $this->numberingService->cleanExpiredReservations();

            if ($deletedCount > 0) {
                $this->info("✅ Se eliminaron {$deletedCount} reservas expiradas");

                Log::info("Reservas de carnets limpiadas", [
                    'deleted_count' => $deletedCount,
                    'command' => 'cards:clean-reservations'
                ]);
            } else {
                $this->info("ℹ️  No se encontraron reservas expiradas para limpiar");
            }

            return self::SUCCESS;

        } catch (\Exception $e) {
            $this->error("❌ Error limpiando reservas: {$e->getMessage()}");

            Log::error("Error en comando de limpieza de reservas", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return self::FAILURE;
        }
    }

    /**
     * Simulate the cleaning operation without actually deleting
     */
    private function dryRun(): int
    {
        return \DB::table('card_number_reservations')
            ->where('expires_at', '<', now())
            ->count();
    }
}
