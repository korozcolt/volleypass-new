<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendBatchNotifications implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 120;

    public function __construct(
        private string $type,
        private array $recipients,
        private array $data
    ) {}

    public function handle(): void
    {
        Log::info('Procesando lote de notificaciones', [
            'type' => $this->type,
            'recipients_count' => count($this->recipients)
        ]);

        $sent = 0;
        $errors = 0;

        foreach ($this->recipients as $recipient) {
            try {
                $this->sendNotification($recipient);
                $sent++;
            } catch (\Exception $e) {
                $errors++;
                Log::error('Error enviando notificación en lote', [
                    'recipient' => $recipient['email'] ?? 'unknown',
                    'error' => $e->getMessage()
                ]);
            }
        }

        Log::info('Lote de notificaciones procesado', [
            'type' => $this->type,
            'sent' => $sent,
            'errors' => $errors
        ]);
    }

    private function sendNotification(array $recipient): void
    {
        $template = match($this->type) {
            'card_expiry' => 'emails.card-expiry',
            'medical_expiry' => 'emails.medical-expiry',
            default => 'emails.generic-notification'
        };

        Mail::send($template, $this->data, function($message) use ($recipient) {
            $message->to($recipient['email'], $recipient['name'])
                    ->subject($this->data['subject'] ?? 'Notificación VolleyPass');
        });
    }
}
