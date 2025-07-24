<?php

namespace App\Notifications;

use App\Models\PlayerTransfer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;

class TransferRejectedNotification extends Notification implements ShouldQueue
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
            ->subject('Traspaso Rechazado - ' . $this->transfer->player->user->name)
            ->greeting('Información importante')
            ->line('Lamentamos informarle que el traspaso ha sido **RECHAZADO**.')
            ->line('**Jugadora:** ' . $this->transfer->player->user->name)
            ->line('**Documento:** ' . $this->transfer->player->user->document_number)
            ->line('**Club Origen:** ' . $this->transfer->fromClub->name)
            ->line('**Club Destino:** ' . $this->transfer->toClub->name)
            ->line('**Rechazado por:** ' . ($this->transfer->approvedBy->name ?? 'Sistema'))
            ->line('**Fecha de Rechazo:** ' . $this->transfer->rejected_at->format('d/m/Y H:i'))
            ->line('**Motivo del Rechazo:**')
            ->line($this->transfer->rejection_reason ?? 'No se especificó un motivo')
            ->action('Ver Detalles', url('/admin/transfers/' . $this->transfer->id))
            ->line('Si tiene dudas sobre esta decisión, puede contactar al administrador de la liga.');
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'transfer_rejected',
            'transfer_id' => $this->transfer->id,
            'player_name' => $this->transfer->player->user->name,
            'player_document' => $this->transfer->player->user->document_number,
            'from_club' => $this->transfer->fromClub->name,
            'to_club' => $this->transfer->toClub->name,
            'rejected_by' => $this->transfer->approvedBy->name ?? 'Sistema',
            'rejected_at' => $this->transfer->rejected_at,
            'rejection_reason' => $this->transfer->rejection_reason,
            'message' => "Traspaso rechazado: {$this->transfer->player->user->name} de {$this->transfer->fromClub->name} a {$this->transfer->toClub->name}",
        ];
    }
}