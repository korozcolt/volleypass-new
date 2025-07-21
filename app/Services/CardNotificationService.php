<?php

namespace App\Services;

use App\Models\PlayerCard;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class CardNotificationService
{
    /**
     * Enviar todas las notificaciones cuando se genera un carnet
     */
    public function notifyCardGenerated(PlayerCard $card): void
    {
        try {
            // Notificar a la jugadora
            $this->notifyPlayer($card);

            // Notificar al director del club
            $this->notifyClubDirector($card);

            // Notificar al administrador de la liga
            $this->notifyLeagueAdmin($card);

            // Log del sistema
            $this->logCardGeneration($card);

        } catch (\Exception $e) {
            Log::error("Error enviando notificaciones de carnet generado", [
                'card_id' => $card->id,
                'card_number' => $card->card_number,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Notificar a la jugadora
     */
    public function notifyPlayer(PlayerCard $card): void
    {
        $player = $card->player;
        $user = $player->user;

        if (!$user->email) {
            Log::warning("No se puede notificar a jugadora sin email", [
                'player_id' => $player->id,
                'card_number' => $card->card_number
            ]);
            return;
        }

        $data = [
            'player_name' => $user->full_name,
            'card_number' => $card->card_number,
            'league_name' => $card->league->name,
            'club_name' => $player->currentClub->name,
            'expires_at' => $card->expires_at->format('d/m/Y'),
            'download_url' => route('player.card.download', $card->id),
            'card' => $card
        ];

        // Email principal
        Mail::send('emails.card-generated-player', $data, function ($message) use ($user, $card) {
            $message->to($user->email, $user->full_name)
                    ->subject('¡Tu carnet VolleyPass está listo!')
                    ->priority(1);
        });

        // SMS si tiene teléfono (opcional)
        if ($user->phone) {
            $this->sendSMS($user->phone, $this->buildPlayerSMSMessage($card));
        }

        Log::info("Notificación enviada a jugadora", [
            'player_id' => $player->id,
            'email' => $user->email,
            'card_number' => $card->card_number
        ]);
    }

    /**
     * Notificar al director del club
     */
    public function notifyClubDirector(PlayerCard $card): void
    {
        $club = $card->player->currentClub;
        $director = $club->director;

        if (!$director || !$director->email) {
            Log::warning("No se puede notificar a director de club", [
                'club_id' => $club->id,
                'card_number' => $card->card_number
            ]);
            return;
        }

        $data = [
            'director_name' => $director->full_name,
            'player_name' => $card->player->user->full_name,
            'card_number' => $card->card_number,
            'club_name' => $club->name,
            'league_name' => $card->league->name,
            'generated_at' => $card->created_at->format('d/m/Y H:i'),
            'card' => $card
        ];

        Mail::send('emails.card-generated-director', $data, function ($message) use ($director, $card) {
            $message->to($director->email, $director->full_name)
                    ->subject('Nueva carnetización completada - ' . $card->card_number)
                    ->priority(2);
        });

        Log::info("Notificación enviada a director de club", [
            'director_id' => $director->id,
            'club_id' => $club->id,
            'card_number' => $card->card_number
        ]);
    }

    /**
     * Notificar al administrador de la liga
     */
    public function notifyLeagueAdmin(PlayerCard $card): void
    {
        $league = $card->league;

        // Obtener administradores de la liga
        $admins = User::whereHas('roles', function ($query) {
            $query->where('name', 'LeagueAdmin');
        })->where('league_id', $league->id)->get();

        if ($admins->isEmpty()) {
            Log::warning("No hay administradores de liga para notificar", [
                'league_id' => $league->id,
                'card_number' => $card->card_number
            ]);
            return;
        }

        $data = [
            'league_name' => $league->name,
            'player_name' => $card->player->user->full_name,
            'club_name' => $card->player->currentClub->name,
            'card_number' => $card->card_number,
            'generated_at' => $card->created_at->format('d/m/Y H:i'),
            'processing_time' => $this->getProcessingTime($card),
            'card' => $card
        ];

        foreach ($admins as $admin) {
            Mail::send('emails.card-generated-admin', $data, function ($message) use ($admin, $card) {
                $message->to($admin->email, $admin->full_name)
                        ->subject('Carnet generado automáticamente - ' . $card->card_number)
                        ->priority(3);
            });
        }

        Log::info("Notificación enviada a administradores de liga", [
            'league_id' => $league->id,
            'admin_count' => $admins->count(),
            'card_number' => $card->card_number
        ]);
    }

    /**
     * Notificar error en generación
     */
    public function notifyGenerationError(Player $player, League $league, string $error): void
    {
        // Notificar a administradores de liga sobre el error
        $admins = User::whereHas('roles', function ($query) {
            $query->where('name', 'LeagueAdmin');
        })->where('league_id', $league->id)->get();

        $data = [
            'league_name' => $league->name,
            'player_name' => $player->user->full_name,
            'club_name' => $player->currentClub->name,
            'error_message' => $error,
            'occurred_at' => now()->format('d/m/Y H:i'),
        ];

        foreach ($admins as $admin) {
            Mail::send('emails.card-generation-error', $data, function ($message) use ($admin, $player) {
                $message->to($admin->email, $admin->full_name)
                        ->subject('Error en generación automática de carnet - ' . $player->user->full_name)
                        ->priority(1);
            });
        }

        Log::error("Notificación de error enviada", [
            'player_id' => $player->id,
            'league_id' => $league->id,
            'error' => $error
        ]);
    }

    /**
     * Enviar SMS (implementación básica)
     */
    private function sendSMS(string $phone, string $message): void
    {
        // TODO: Implementar integración con proveedor de SMS
        // Por ahora solo registrar en log
        Log::info("SMS enviado", [
            'phone' => $phone,
            'message' => $message
        ]);
    }

    /**
     * Construir mensaje SMS para jugadora
     */
    private function buildPlayerSMSMessage(PlayerCard $card): string
    {
        return "¡Hola {$card->player->user->first_name}! Tu carnet VolleyPass #{$card->card_number} está listo. " .
               "Válido hasta {$card->expires_at->format('d/m/Y')}. " .
               "Descárgalo desde tu perfil en la app.";
    }

    /**
     * Registrar generación en log del sistema
     */
    private function logCardGeneration(PlayerCard $card): void
    {
        Log::info("Carnet generado automáticamente", [
            'event_type' => 'card_generation',
            'card_id' => $card->id,
            'card_number' => $card->card_number,
            'player_id' => $card->player_id,
            'league_id' => $card->league_id,
            'club_id' => $card->player->current_club_id,
            'generated_at' => $card->created_at->toISOString(),
            'expires_at' => $card->expires_at->toISOString(),
            'processing_time' => $this->getProcessingTime($card),
            'automatic' => true
        ]);
    }

    /**
     * Calcular tiempo de procesamiento
     */
    private function getProcessingTime(PlayerCard $card): ?string
    {
        if ($card->generation_started_at && $card->generation_completed_at) {
            $seconds = $card->generation_completed_at->diffInSeconds($card->generation_started_at);
            return "{$seconds} segundos";
        }

        return null;
    }

    /**
     * Notificar renovación próxima
     */
    public function notifyUpcomingRenewal(PlayerCard $card, int $daysUntilExpiry): void
    {
        $player = $card->player;
        $user = $player->user;

        if (!$user->email) {
            return;
        }

        $data = [
            'player_name' => $user->full_name,
            'card_number' => $card->card_number,
            'expires_at' => $card->expires_at->format('d/m/Y'),
            'days_until_expiry' => $daysUntilExpiry,
            'renewal_url' => route('player.card.renew', $card->id)
        ];

        Mail::send('emails.card-renewal-reminder', $data, function ($message) use ($user, $daysUntilExpiry) {
            $message->to($user->email, $user->full_name)
                    ->subject("Tu carnet VolleyPass vence en {$daysUntilExpiry} días")
                    ->priority(2);
        });
    }

    /**
     * Obtener estadísticas de notificaciones
     */
    public function getNotificationStats(int $days = 30): array
    {
        // TODO: Implementar tracking de notificaciones enviadas
        return [
            'period_days' => $days,
            'cards_generated' => PlayerCard::where('created_at', '>=', now()->subDays($days))->count(),
            'notifications_sent' => 0, // Placeholder
            'email_success_rate' => 0, // Placeholder
            'sms_sent' => 0, // Placeholder
        ];
    }
}
