<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TestEmailNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Prueba de Configuración - ' . app_name())
            ->greeting('¡Hola!')
            ->line('Este es un email de prueba para verificar que las notificaciones por correo están funcionando correctamente.')
            ->line('**Sistema:** ' . app_name())
            ->line('**Versión:** ' . app_version())
            ->line('**Fecha:** ' . now()->format('d/m/Y H:i:s'))
            ->action('Ir al Panel Administrativo', url('/admin'))
            ->line('Si recibes este email, significa que las notificaciones están configuradas correctamente.')
            ->salutation('Saludos, ' . app_name());
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
