<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\CleanupOldLogs;

class CleanupLogsCommand extends Command
{
    protected $signature = 'volleypass:cleanup-logs
                           {--qr-days=365 : Días a mantener logs QR}
                           {--activity-days=180 : Días a mantener activity logs}
                           {--force : No pedir confirmación}';

    protected $description = 'Limpiar logs antiguos del sistema';

    public function handle(): int
    {
        $qrDays = (int) $this->option('qr-days');
        $activityDays = (int) $this->option('activity-days');
        $force = $this->option('force');

        if (!$force) {
            $this->warn("Se eliminarán:");
            $this->line("- Logs QR anteriores a {$qrDays} días");
            $this->line("- Activity logs anteriores a {$activityDays} días");

            if (!$this->confirm('¿Continuar con la limpieza?')) {
                $this->info('Operación cancelada');
                return self::SUCCESS;
            }
        }

        $this->info('Iniciando limpieza de logs...');

        try {
            CleanupOldLogs::dispatch($qrDays, $activityDays);

            $this->info('Job de limpieza encolado exitosamente');
            return self::SUCCESS;

        } catch (\Exception $e) {
            $this->error('Error en limpieza: ' . $e->getMessage());
            return self::FAILURE;
        }
    }
}
