<?php

namespace App\Notifications;

namespace App\Notifications;

use App\Models\PlayerCard;
use App\Enums\NotificationType;
use App\Enums\Priority;

class CardExpiryNotification extends BaseVolleyPassNotification
{
    private PlayerCard $card;
    private int $daysLeft;

    public function __construct(PlayerCard $card, int $daysLeft, string $recipientRole = 'player')
    {
        $this->card = $card;
        $this->daysLeft = $daysLeft;
        $this->type = NotificationType::Card_Expiry;

        $this->priority = match(true) {
            $daysLeft <= 3 => Priority::Urgent,
            $daysLeft <= 7 => Priority::High,
            $daysLeft <= 15 => Priority::Medium,
            default => Priority::Low
        };

        parent::__construct([
            'card_id' => $card->id,
            'card_number' => $card->card_number,
            'expires_at' => $card->expires_at->format('Y-m-d'),
            'days_left' => $daysLeft,
            'player_name' => $card->player->user->full_name,
            'club_name' => $card->player->currentClub?->name,
        ], $recipientRole);
    }

    protected function getSubject(): string
    {
        if ($this->daysLeft <= 0) {
            return '🚨 Carnet VolleyPass VENCIDO';
        }

        return "🏐 Carnet VolleyPass vence en {$this->daysLeft} días";
    }

    protected function getMainMessage(): string
    {
        if ($this->daysLeft <= 0) {
            return "Tu carnet VolleyPass ha VENCIDO. No podrás participar en eventos hasta renovarlo.";
        }

        $urgency = match(true) {
            $this->daysLeft <= 3 => '🚨 URGENTE: ',
            $this->daysLeft <= 7 => '⚠️ ',
            default => ''
        };

        return "{$urgency}Tu carnet VolleyPass vence en {$this->daysLeft} días.";
    }

    protected function getDetailMessage(): string
    {
        return sprintf(
            "Jugadora: %s\nNúmero de carnet: %s\nFecha de vencimiento: %s",
            $this->data['player_name'],
            $this->data['card_number'],
            $this->data['expires_at']
        );
    }

    protected function getActionText(): string
    {
        return 'Ver Dashboard VolleyPass';
    }

    protected function getActionUrl($notifiable): string
    {
        // Usar ruta que SÍ existe
        return route('dashboard');
    }

    protected function getAdditionalData(): array
    {
        $data = [];

        if ($this->recipientRole === 'director') {
            $data[] = "Club: {$this->data['club_name']}";
            $data[] = "Para renovar, debe verificar que todos los documentos estén vigentes.";
        } else {
            $data[] = "Contacta a tu director de club para iniciar el proceso de renovación.";
        }

        if ($this->daysLeft <= 7) {
            $data[] = "⚠️ Sin carnet válido no podrás participar en partidos oficiales.";
        }

        return $data;
    }
}
