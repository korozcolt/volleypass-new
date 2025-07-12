<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Models\MedicalCertificate;

class ProcessMedicalCertificateExpiry implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 300;

    public function __construct(
        private array $notificationDays = [30, 15, 7, 3, 1]
    ) {}

    public function handle(): void
    {
        Log::info('Iniciando procesamiento de certificados médicos por vencer');

        try {
            foreach ($this->notificationDays as $days) {
                $this->processExpiringCertificates($days);
            }

            // Marcar certificados vencidos
            $this->markExpiredCertificates();

        } catch (\Exception $e) {
            Log::error('Error en ProcessMedicalCertificateExpiry', [
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    private function processExpiringCertificates(int $days): void
    {
        $certificates = MedicalCertificate::with(['player.user', 'player.currentClub'])
            ->where('status', 'approved')
            ->where('is_current', true)
            ->whereDate('expires_at', now()->addDays($days))
            ->where('expiry_notification_sent', false)
            ->get();

        foreach ($certificates as $certificate) {
            try {
                $playerUser = $certificate->player->user;

                // Enviar email usando Mail facade
                Mail::send('emails.medical-expiry', [
                    'player_name' => $playerUser->full_name,
                    'doctor_name' => $certificate->doctor_name,
                    'expires_at' => $certificate->expires_at->format('d/m/Y'),
                    'days_left' => $days,
                    'medical_status' => $certificate->medical_status->getLabel(),
                ], function($message) use ($playerUser, $days) {
                    $message->to($playerUser->email, $playerUser->full_name)
                            ->subject("🏥 Certificado médico vence en {$days} días");
                });

                // Notificar al director del club
                if ($certificate->player->currentClub?->director) {
                    $director = $certificate->player->currentClub->director;

                    Mail::send('emails.medical-expiry-director', [
                        'director_name' => $director->full_name,
                        'player_name' => $playerUser->full_name,
                        'doctor_name' => $certificate->doctor_name,
                        'expires_at' => $certificate->expires_at->format('d/m/Y'),
                        'days_left' => $days,
                        'club_name' => $certificate->player->currentClub->name,
                    ], function($message) use ($director, $days, $playerUser) {
                        $message->to($director->email, $director->full_name)
                                ->subject("🏥 Certificado médico de {$playerUser->full_name} vence en {$days} días");
                    });
                }

                // Marcar como notificado
                $certificate->update([
                    'expiry_notification_sent' => true,
                    'expiry_notification_at' => now(),
                ]);

                Log::info('Notificación médica enviada', [
                    'certificate_id' => $certificate->id,
                    'player_name' => $playerUser->full_name,
                    'days_left' => $days
                ]);

            } catch (\Exception $e) {
                Log::error('Error enviando notificación médica', [
                    'certificate_id' => $certificate->id,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }

    private function markExpiredCertificates(): void
    {
        $expiredCount = MedicalCertificate::where('status', 'approved')
            ->where('is_current', true)
            ->where('expires_at', '<', now())
            ->update([
                'status' => 'expired',
                'is_current' => false,
                'updated_at' => now()
            ]);

        if ($expiredCount > 0) {
            Log::info("Marcados {$expiredCount} certificados médicos como vencidos");
        }
    }
}
