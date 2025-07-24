<?php

namespace App\Notifications;

use App\Models\PlayerTransfer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;

class TransferRequestNotification extends Notification implements ShouldQueue
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
            ->subject('Nueva Solicitud de Traspaso - ' . $this->transfer->player->user->name)
            ->greeting('¡Hola!')
            ->line('Se ha recibido una nueva solicitud de traspaso que requiere su atención.')
            ->line('**Jugadora:** ' . $this->transfer->player->user->name)
            ->line('**Documento:** ' . $this->transfer->player->user->document_number)
            ->line('**Club Origen:** ' . $this->transfer->fromClub->name)
            ->line('**Club Destino:** ' . $this->transfer->toClub->name)
            ->line('**Motivo:** ' . ($this->transfer->reason ?? 'No especificado'))
            ->line('**Fecha de Solicitud:** ' . $this->transfer->created_at->format('d/m/Y H:i'))
            ->action('Ver Solicitud', url('/admin/transfers/' . $this->transfer->id))
            ->line('Por favor, revise y procese esta solicitud lo antes posible.');
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'transfer_request',
            'transfer_id' => $this->transfer->id,
            'player_name' => $this->transfer->player->user->name,
            'player_document' => $this->transfer->player->user->document_number,
            'from_club' => $this->transfer->fromClub->name,
            'to_club' => $this->transfer->toClub->name,
            'reason' => $this->transfer->reason,
            'requested_at' => $this->transfer->created_at,
            'message' => "Nueva solicitud de traspaso de {$this->transfer->player->user->name} de {$this->transfer->fromClub->name} a {$this->transfer->toClub->name}",
        ];
    }
}