<?php

namespace App\Livewire\Player;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.player')]
class PlayerSettings extends Component
{
    public $user;
    public $notificationPreferences = [
        'match_reminders' => true,
        'tournament_updates' => true,
        'team_announcements' => true,
        'email_notifications' => true,
        'push_notifications' => false,
    ];

    public $privacySettings = [
        'profile_visibility' => 'public',
        'stats_visibility' => 'public',
        'contact_visibility' => 'team_only',
    ];

    public function mount()
    {
        $this->user = Auth::user();

        if (!$this->user->player) {
            return redirect()->route('home');
        }

        $this->loadSettings();
    }

    private function loadSettings()
    {
        // Cargar preferencias desde la base de datos o usar valores por defecto
        $preferences = $this->user->preferences ?? [];

        $this->notificationPreferences = array_merge(
            $this->notificationPreferences,
            $preferences['notifications'] ?? []
        );

        $this->privacySettings = array_merge(
            $this->privacySettings,
            $preferences['privacy'] ?? []
        );
    }

    public function updateNotificationPreferences()
    {
        $preferences = $this->user->preferences ?? [];
        $preferences['notifications'] = $this->notificationPreferences;

        $this->user->update(['preferences' => $preferences]);

        session()->flash('message', 'Preferencias de notificación actualizadas correctamente.');
    }

    public function updatePrivacySettings()
    {
        $preferences = $this->user->preferences ?? [];
        $preferences['privacy'] = $this->privacySettings;

        $this->user->update(['preferences' => $preferences]);

        session()->flash('message', 'Configuración de privacidad actualizada correctamente.');
    }

    public function render()
    {
        return view('livewire.player.player-settings');
    }
}
