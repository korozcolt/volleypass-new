<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Limpiar reservas de números de carnet expiradas cada hora
        $schedule->command('cards:clean-reservations --force')
                 ->hourly()
                 ->withoutOverlapping()
                 ->runInBackground();

        // Limpiar logs antiguos de generación de carnets (mantener 90 días)
        $schedule->call(function () {
            app(\App\Services\AutomaticCardGenerationService::class)->cleanOldLogs(90);
        })->daily()->at('02:00');

        // Generar estadísticas diarias y enviar reporte (opcional)
        $schedule->command('cards:stats --days=1 --format=json')
                 ->daily()
                 ->at('06:00')
                 ->appendOutputTo(storage_path('logs/card-stats.log'));

        // Limpiar archivos de log antiguos
        $schedule->command('log:clear --keep=30')
                 ->weekly()
                 ->sundays()
                 ->at('03:00');

        // Sincronizar estadísticas de clubes diariamente
        $schedule->command('clubs:sync-stats --force')
                 ->daily()
                 ->at('05:00')
                 ->withoutOverlapping()
                 ->runInBackground();

        // Generar pagos mensuales automáticamente el primer día de cada mes
        $schedule->command('payments:generate-monthly')
                 ->monthlyOn(1, '08:00')
                 ->withoutOverlapping()
                 ->runInBackground();

        // Marcar pagos vencidos diariamente
        $schedule->call(function () {
            app(\App\Services\PaymentService::class)->markOverduePayments();
        })->daily()->at('07:00');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
