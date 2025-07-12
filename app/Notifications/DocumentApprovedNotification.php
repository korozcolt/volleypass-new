<?php

namespace App\Notifications;

use App\Models\PlayerDocument;
use App\Enums\NotificationType;
use App\Enums\Priority;

class DocumentApprovedNotification extends BaseVolleyPassNotification
{
    private PlayerDocument $document;

    public function __construct(PlayerDocument $document, string $recipientRole = 'player')
    {
        $this->document = $document;
        $this->type = NotificationType::Document_Approved;
        $this->priority = Priority::Medium;

        parent::__construct([
            'document_id' => $document->id,
            'document_type' => $document->document_type->getLabel(),
            'player_name' => $document->player->user->full_name,
            'approved_at' => now()->format('Y-m-d H:i'),
        ], $recipientRole);
    }

    protected function getSubject(): string
    {
        return "✅ Documento aprobado - VolleyPass";
    }

    protected function getMainMessage(): string
    {
        return "Tu documento '{$this->data['document_type']}' ha sido aprobado exitosamente.";
    }

    protected function getDetailMessage(): string
    {
        return "Ya puedes proceder con el siguiente paso en tu proceso de registro.";
    }

    protected function getActionText(): string
    {
        return 'Ver Dashboard';
    }

    protected function getActionUrl($notifiable): string
    {
        return route('dashboard');
    }
}
