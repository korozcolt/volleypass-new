<?php

namespace App\Notifications;

use App\Enums\NotificationType;
use App\Enums\Priority;
use App\Models\Player;

class PlayerCategoryReassignedNotification extends BaseVolleyPassNotification
{
    private Player $player;
    private string $oldCategory;
    private string $newCategory;

    public function __construct(Player $player, string $oldCategory, string $newCategory, string $reason, string $recipientRole = 'player')
    {
        $this->player = $player;
        $this->oldCategory = $oldCategory;
        $this->newCategory = $newCategory;
        $this->type = NotificationType::Document_Approved; // Usamos un tipo existente por ahora
        $this->priority = Priority::Medium;

        parent::__construct([
            'player_id' => $player->id,
            'player_name' => $player->user->full_name,
            'old_category' => $oldCategory,
            'new_category' => $newCategory,
            'reason' => $reason,
            'club_name' => $player->currentClub?->name,
            'league_name' => $player->currentClub?->league?->name,
            'changed_at' => now()->format('Y-m-d H:i'),
        ], $recipientRole);
    }

    protected function getSubject(): string
    {
        return "🔄 Cambio de categoría - VolleyPass";
    }

    protected function getMainMessage(): string
    {
        if ($this->recipientRole === 'player') {
            return "Tu categoría ha sido actualizada de '{$this->data['old_category']}' a '{$this->data['new_category']}'.";
        }
        
        return "La categoría de {$this->data['player_name']} ha sido actualizada de '{$this->data['old_category']}' a '{$this->data['new_category']}'.";
    }

    protected function getDetailMessage(): string
    {
        $details = "Detalles del cambio:\n";
        $details .= "- Jugadora: {$this->data['player_name']}\n";
        $details .= "- Categoría anterior: {$this->data['old_category']}\n";
        $details .= "- Nueva categoría: {$this->data['new_category']}\n";
        $details .= "- Motivo: {$this->data['reason']}\n";
        
        if ($this->recipientRole === 'director' || $this->recipientRole === 'admin') {
            $details .= "- Club: {$this->data['club_name']}\n";
            $details .= "- Liga: {$this->data['league_name']}\n";
        }
        
        return $details;
    }

    protected function getActionText(): string
    {
        return 'Ver Perfil';
    }

    protected function getActionUrl($notifiable): string
    {
        if ($this->recipientRole === 'admin' || $this->recipientRole === 'director') {
            return route('filament.admin.resources.players.edit', $this->player);
        }
        
        return route('dashboard');
    }
}