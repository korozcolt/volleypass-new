<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Models\PlayerCard;
use App\Models\User;
use Carbon\Carbon;

class ProcessCardExpiryNotifications implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 300; // 5 minutos

    public function __construct(
        private int $daysBeforeExpiry = 30
    ) {}

    public function handle(): void
    {
        Log::info('Iniciando procesamiento de notificaciones de vencimiento de carnets', [
            'days_before_expiry' => $this->daysBeforeExpiry
        ]);

        try {
            // Obtener carnets que vencen en X días
            $expiringCards = PlayerCard::with(['player.user', 'player.currentClub'])
                ->where('status', 'active')
                ->whereBetween('expires_at', [
                    now()->addDays($this->daysBeforeExpiry - 1),
                    now()->addDays($this->daysBeforeExpiry + 1)
                ])
                ->get();

            $notificationsSent = 0;
            $errors = 0;

            foreach ($expiringCards as $card) {
                try {
                    $this->sendExpiryNotification($card);
                    $notificationsSent++;
                } catch (\Exception $e) {
                    $errors++;
                    Log::error('Error enviando notificación de vencimiento', [
                        'card_id' => $card->id,
                        'player_name' => $card->player->user->full_name,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            // Marcar carnets vencidos
            $this->markExpiredCards();

            Log::info('Procesamiento de notificaciones completado', [
                'cards_expiring' => $expiringCards->count(),
                'notifications_sent' => $notificationsSent,
                'errors' => $errors
            ]);

        } catch (\Exception $e) {
            Log::error('Error en ProcessCardExpiryNotifications', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    private function sendExpiryNotification(PlayerCard $card): void
    {
        $daysLeft = $card->expires_at->diffInDays(now());
        $playerUser = $card->player->user;

        // Enviar email básico usando Mail facade
        Mail::send('emails.card-expiry', [
            'player_name' => $playerUser->full_name,
            'card_number' => $card->card_number,
            'expires_at' => $card->expires_at->format('d/m/Y'),
            'days_left' => $daysLeft,
            'club_name' => $card->player->currentClub?->name,
        ], function($message) use ($playerUser, $daysLeft) {
            $message->to($playerUser->email, $playerUser->full_name)
                    ->subject("🏐 Carnet VolleyPass vence en {$daysLeft} días");
        });

        // Notificar al director del club si existe
        if ($card->player->currentClub && $card->player->currentClub->director) {
            $director = $card->player->currentClub->director;

            Mail::send('emails.card-expiry-director', [
                'director_name' => $director->full_name,
                'player_name' => $playerUser->full_name,
                'card_number' => $card->card_number,
                'expires_at' => $card->expires_at->format('d/m/Y'),
                'days_left' => $daysLeft,
                'club_name' => $card->player->currentClub->name,
            ], function($message) use ($director, $daysLeft, $playerUser) {
                $message->to($director->email, $director->full_name)
                        ->subject("🏐 Carnet de {$playerUser->full_name} vence en {$daysLeft} días");
            });
        }

        Log::info('Notificación de vencimiento enviada', [
            'card_id' => $card->id,
            'player_email' => $playerUser->email,
            'days_left' => $daysLeft
        ]);
    }

    private function markExpiredCards(): void
    {
        $expiredCount = PlayerCard::where('status', 'active')
            ->where('expires_at', '<', now())
            ->update([
                'status' => 'expired',
                'updated_at' => now()
            ]);

        if ($expiredCount > 0) {
            Log::info("Marcados {$expiredCount} carnets como vencidos");
        }
    }
}
