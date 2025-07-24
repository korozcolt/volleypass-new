<?php

namespace App\Notifications;

use App\Enums\NotificationType;
use App\Enums\Priority;
use App\Models\League;

class CategoryConfigurationChangedNotification extends BaseVolleyPassNotification
{
    private League $league;
    private array $changedCategories;

    public function __construct(League $league, array $changedCategories, string $recipientRole = 'admin')
    {
        $this->league = $league;
        $this->changedCategories = $changedCategories;
        $this->type = NotificationType::Document_Approved; // Usamos un tipo existente por ahora
        $this->priority = Priority::Medium;

        parent::__construct([
            'league_id' => $league->id,
            'league_name' => $league->name,
            'changed_categories' => $changedCategories,
            'changed_at' => now()->format('Y-m-d H:i'),
        ], $recipientRole);
    }

    protected function getSubject(): string
    {
        return "🔄 Configuración de categorías actualizada - VolleyPass";
    }

    protected function getMainMessage(): string
    {
        return "La configuración de categorías para la liga '{$this->data['league_name']}' ha sido actualizada.";
    }

    protected function getDetailMessage(): string
    {
        $details = "Se han realizado los siguientes cambios en la configuración de categorías:\n";
        
        foreach ($this->data['changed_categories'] as $category) {
            $details .= "- {$category['name']}: {$category['change_description']}\n";
        }
        
        $details .= "\nEstos cambios pueden afectar la asignación de categorías de las jugadoras.";
        
        return $details;
    }

    protected function getActionText(): string
    {
        return 'Ver Configuración';
    }

    protected function getActionUrl($notifiable): string
    {
        return route('filament.admin.resources.leagues.edit', $this->league);
    }
}