<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Enums\NotificationType;
use App\Enums\Priority;

abstract class BaseVolleyPassNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected NotificationType $type;
    protected Priority $priority = Priority::Medium;
    protected array $data = [];
    protected string $recipientRole = 'player';

    public function __construct(array $data = [], string $recipientRole = 'player')
    {
        $this->data = $data;
        $this->recipientRole = $recipientRole;
        $this->queue = $this->getQueue();
    }

    /**
     * Canales de notificación - SOLO EMAIL para empezar
     */
    public function via($notifiable): array
    {
        // Por ahora solo email - funcional garantizado
        return ['mail', 'database'];
    }

    /**
     * Email notification
     */
    public function toMail($notifiable): MailMessage
    {
        $message = (new MailMessage)
            ->subject($this->getSubject())
            ->greeting($this->getGreeting($notifiable))
            ->line($this->getMainMessage())
            ->line($this->getDetailMessage());

        // Agregar acción si existe
        if ($this->hasAction()) {
            $message->action($this->getActionText(), $this->getActionUrl($notifiable));
        }

        // Agregar datos adicionales
        foreach ($this->getAdditionalData() as $line) {
            $message->line($line);
        }

        return $message
            ->line('¡Gracias por usar VolleyPass!')
            ->salutation('Saludos, El equipo de VolleyPass Sucre');
    }

    /**
     * Database storage
     */
    public function toArray($notifiable): array
    {
        return [
            'type' => $this->type->value,
            'priority' => $this->priority->value,
            'recipient_role' => $this->recipientRole,
            'subject' => $this->getSubject(),
            'message' => $this->getMainMessage(),
            'action_url' => $this->hasAction() ? $this->getActionUrl($notifiable) : null,
            'data' => $this->data,
            'sent_at' => now()->toISOString(),
        ];
    }

    // ===============================================
    // MÉTODOS ABSTRACTOS
    // ===============================================

    abstract protected function getSubject(): string;
    abstract protected function getMainMessage(): string;
    abstract protected function getDetailMessage(): string;

    // ===============================================
    // MÉTODOS IMPLEMENTADOS - CORREGIDOS
    // ===============================================

    protected function getGreeting($notifiable): string
    {
        $time = now()->hour;
        $greeting = match(true) {
            $time < 12 => 'Buenos días',
            $time < 18 => 'Buenas tardes',
            default => 'Buenas noches'
        };

        // ✅ CORREGIDO: Usar operador ternario en lugar de null coalescing
        $name = $notifiable->first_name ? $notifiable->first_name : $notifiable->name;

        return "{$greeting}, {$name}";
    }

    protected function hasAction(): bool
    {
        return !empty($this->getActionText()) && !empty($this->getActionUrl(null));
    }

    protected function getActionText(): string
    {
        return '';
    }

    protected function getActionUrl($notifiable): string
    {
        return '';
    }

    protected function getAdditionalData(): array
    {
        return [];
    }

    protected function getQueue(): string
    {
        return match($this->priority) {
            Priority::Urgent => 'urgent',
            Priority::High => 'high',
            Priority::Medium => 'default',
            Priority::Low => 'low'
        };
    }
}
