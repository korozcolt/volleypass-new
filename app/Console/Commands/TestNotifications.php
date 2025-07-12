<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\PlayerCard;
use App\Notifications\CardExpiryNotification;

class TestNotifications extends Command
{
    protected $signature = 'volleypass:test-notifications
                           {user_id : ID del usuario}
                           {--type=simple : Tipo de prueba (simple, notification)}';

    protected $description = 'Probar envío de notificaciones';

    public function handle(): int
    {
        $userId = $this->argument('user_id');
        $type = $this->option('type');

        $user = User::find($userId);

        if (!$user) {
            $this->error("Usuario {$userId} no encontrado");
            return 1;
        }

        try {
            if ($type === 'simple') {
                // Envío simple con Mail::send
                $this->sendSimpleEmail($user);
            } else {
                // Envío con Notification (si CardExpiryNotification existe)
                $this->sendNotificationEmail($user);
            }

            $this->info("Notificación enviada a {$user->email}");
            return 0;

        } catch (\Exception $e) {
            $this->error("Error: {$e->getMessage()}");
            return 1;
        }
    }

    private function sendSimpleEmail(User $user): void
    {
        \Illuminate\Support\Facades\Mail::send('emails.generic-notification', [
            'subject' => 'Prueba de notificación VolleyPass',
            'title' => 'Email de Prueba',
            'recipient_name' => $user->full_name,
            'message' => 'Este es un email de prueba del sistema VolleyPass.',
            'details' => [
                'usuario_id' => $user->id,
                'email' => $user->email,
                'fecha_prueba' => now()->format('d/m/Y H:i:s'),
            ],
            'action_url' => route('dashboard'),
            'action_text' => 'Ir a Dashboard',
        ], function($message) use ($user) {
            $message->to($user->email, $user->full_name)
                    ->subject('🏐 Prueba VolleyPass - Sistema Funcionando');
        });
    }

    private function sendNotificationEmail(User $user): void
    {
        // Solo si existe la notificación CardExpiryNotification
        if (!class_exists(CardExpiryNotification::class)) {
            $this->info('CardExpiryNotification no existe, enviando email simple...');
            $this->sendSimpleEmail($user);
            return;
        }

        $card = PlayerCard::where('player_id', $user->player?->id)
            ->first();

        if (!$card) {
            $this->error("Usuario no tiene carnet asociado");
            return;
        }

        $user->notify(new CardExpiryNotification($card, 7));
    }
}
