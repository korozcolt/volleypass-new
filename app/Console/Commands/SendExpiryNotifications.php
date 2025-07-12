<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\ProcessCardExpiryNotifications;
use App\Jobs\ProcessMedicalCertificateExpiry;

class SendExpiryNotifications extends Command
{
    protected $signature = 'volleypass:send-expiry-notifications
                           {--days=30 : Días antes del vencimiento para notificar}
                           {--type=all : Tipo de notificación (cards, medical, all)}';

    protected $description = 'Enviar notificaciones de vencimiento de carnets y certificados médicos';

    public function handle(): int
    {
        $days = (int) $this->option('days');
        $type = $this->option('type');

        $this->info("Enviando notificaciones de vencimiento ({$days} días)...");

        try {
            if ($type === 'cards' || $type === 'all') {
                $this->info('Procesando carnets...');
                ProcessCardExpiryNotifications::dispatch($days);
            }

            if ($type === 'medical' || $type === 'all') {
                $this->info('Procesando certificados médicos...');
                ProcessMedicalCertificateExpiry::dispatch();
            }

            $this->info('Jobs de notificación encolados exitosamente');
            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Error enviando notificaciones: ' . $e->getMessage());
            return self::FAILURE;
        }
    }
}
