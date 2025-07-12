<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use App\Models\QrScanLog;
use App\Models\Activity;
use Illuminate\Support\Facades\DB;

class CleanupOldLogs implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;
    public int $timeout = 1800; // 30 minutos

    public function __construct(
        private int $qrLogDays = 365,     // 1 año de logs QR
        private int $activityDays = 180   // 6 meses de activity logs
    ) {}

    public function handle(): void
    {
        Log::info('Iniciando limpieza de logs antiguos', [
            'qr_log_days' => $this->qrLogDays,
            'activity_days' => $this->activityDays
        ]);

        try {
            $this->cleanupQrScanLogs();
            $this->cleanupActivityLogs();
            $this->optimizeTables();

        } catch (\Exception $e) {
            Log::error('Error en CleanupOldLogs', [
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    private function cleanupQrScanLogs(): void
    {
        $cutoffDate = now()->subDays($this->qrLogDays);

        $deletedCount = QrScanLog::where('scanned_at', '<', $cutoffDate)
            ->delete();

        Log::info('Logs QR eliminados', [
            'deleted_count' => $deletedCount,
            'cutoff_date' => $cutoffDate->toDateString()
        ]);
    }

    private function cleanupActivityLogs(): void
    {
        $cutoffDate = now()->subDays($this->activityDays);

        $deletedCount = Activity::where('created_at', '<', $cutoffDate)
            ->delete();

        Log::info('Activity logs eliminados', [
            'deleted_count' => $deletedCount,
            'cutoff_date' => $cutoffDate->toDateString()
        ]);
    }

    private function optimizeTables(): void
    {
        // Optimizar tablas después de eliminaciones masivas
        $tables = ['qr_scan_logs', 'activity_log'];

        foreach ($tables as $table) {
            try {
                DB::statement("OPTIMIZE TABLE {$table}");
                Log::info("Tabla {$table} optimizada");
            } catch (\Exception $e) {
                Log::warning("No se pudo optimizar tabla {$table}", [
                    'error' => $e->getMessage()
                ]);
            }
        }
    }
}
