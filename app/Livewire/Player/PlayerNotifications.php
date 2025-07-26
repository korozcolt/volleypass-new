<?php

namespace App\Livewire\Player;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.player')]
class PlayerNotifications extends Component
{
    use WithPagination;

    public $user;
    public $filter = 'all'; // all, unread, match, tournament, team

    public function mount()
    {
        $this->user = Auth::user();

        if (!$this->user->player) {
            return redirect()->route('home');
        }
    }

    public function updatedFilter()
    {
        $this->resetPage();
    }

    public function markAsRead($notificationId)
    {
        // Implementar lógica para marcar como leída
        session()->flash('message', 'Notificación marcada como leída.');
    }

    public function markAllAsRead()
    {
        // Implementar lógica para marcar todas como leídas
        session()->flash('message', 'Todas las notificaciones marcadas como leídas.');
    }

    public function deleteNotification($notificationId)
    {
        // Implementar lógica para eliminar notificación
        session()->flash('message', 'Notificación eliminada.');
    }

    public function render()
    {
        // Por ahora, datos de ejemplo. En producción, esto vendría de la base de datos
        $notifications = collect([
            [
                'id' => 1,
                'type' => 'match',
                'title' => 'Próximo partido',
                'message' => 'Tienes un partido programado para mañana a las 15:00 contra Atlético Medellín',
                'created_at' => now()->subHours(2),
                'read_at' => null,
                'data' => ['match_id' => 123]
            ],
            [
                'id' => 2,
                'type' => 'tournament',
                'title' => 'Nuevo torneo disponible',
                'message' => 'Se ha abierto la inscripción para el Torneo Regional Femenino 2025',
                'created_at' => now()->subDay(),
                'read_at' => null,
                'data' => ['tournament_id' => 456]
            ],
            [
                'id' => 3,
                'type' => 'team',
                'title' => 'Convocatoria de equipo',
                'message' => 'Has sido convocada para el próximo entrenamiento del martes',
                'created_at' => now()->subDays(2),
                'read_at' => now()->subDay(),
                'data' => ['team_id' => 789]
            ],
            [
                'id' => 4,
                'type' => 'match',
                'title' => 'Resultado del partido',
                'message' => 'Tu equipo ganó 3-1 contra Deportivo Cali. ¡Felicitaciones!',
                'created_at' => now()->subDays(3),
                'read_at' => now()->subDays(2),
                'data' => ['match_id' => 321]
            ]
        ]);

        // Filtrar notificaciones
        if ($this->filter !== 'all') {
            if ($this->filter === 'unread') {
                $notifications = $notifications->whereNull('read_at');
            } else {
                $notifications = $notifications->where('type', $this->filter);
            }
        }

        // Simular paginación
        $perPage = 10;
        $currentPage = $this->getPage();
        $notifications = $notifications->forPage($currentPage, $perPage);

        return view('livewire.player.player-notifications', [
            'notifications' => $notifications
        ]);
    }
}
