<?php

namespace App\Notifications;

use App\Models\PlayerTransfer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;

class TransferApprovedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public PlayerTransfer $transfer
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Traspaso Aprobado - ' . $this->transfer->player->user->name)
            ->greeting('¡Excelente noticia!')
            ->line('El traspaso ha sido **APROBADO** exitosamente.')
            ->line('**Jugadora:** ' . $this->transfer->player->user->name)
            ->line('**Documento:** ' . $this->transfer->player->user->document_number)
            ->line('**Club Origen:** ' . $this->transfer->fromClub->name)
            ->line('**Club Destino:** ' . $this->transfer->toClub->name)
            ->line('**Aprobado por:** ' . ($this->transfer->approvedBy->name ?? 'Sistema'))
            ->line('**Fecha de Aprobación:** ' . $this->transfer->approved_at->format('d/m/Y H:i'))
            ->line('**Fecha Efectiva:** ' . ($this->transfer->effective_date ? $this->transfer->effective_date->format('d/m/Y') : 'Inmediata'))
            ->action('Ver Detalles', url('/admin/transfers/' . $this->transfer->id))
            ->line('El traspaso ha sido procesado y la jugadora ya pertenece al nuevo club.');
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'transfer_approved',
            'transfer_id' => $this->transfer->id,
            'player_name' => $this->transfer->player->user->name,
            'player_document' => $this->transfer->player->user->document_number,
            'from_club' => $this->transfer->fromClub->name,
            'to_club' => $this->transfer->toClub->name,
            'approved_by' => $this->transfer->approvedBy->name ?? 'Sistema',
            'approved_at' => $this->transfer->approved_at,
            'effective_date' => $this->transfer->effective_date,
            'message' => "Traspaso aprobado: {$this->transfer->player->user->name} de {$this->transfer->fromClub->name} a {$this->transfer->toClub->name}",
        ];
    }
}