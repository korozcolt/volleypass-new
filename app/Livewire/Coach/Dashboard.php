<?php

namespace App\Livewire\Coach;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Dashboard extends Component
{
    public $coach;
    public $teams;
    public $teamStats;
    public $calendar;
    public $activeTournaments;
    public $alerts;
    public $playerPerformance;

    public function mount()
    {
        $this->coach = Auth::user()->coach;
        $this->loadDashboardData();
    }

    public function loadDashboardData()
    {
        $this->teams = [
            [
                'id' => 1,
                'name' => 'Halcones Juvenil',
                'category' => 'Sub-18',
                'players_count' => 14,
                'active_players' => 12,
                'next_match' => '2024-02-15 19:00',
                'tournament' => 'Liga Juvenil'
            ],
            [
                'id' => 2,
                'name' => 'Halcones Senior',
                'category' => 'Mayores',
                'players_count' => 16,
                'active_players' => 15,
                'next_match' => '2024-02-18 20:30',
                'tournament' => 'Liga Profesional'
            ]
        ];

        $this->teamStats = [
            'total_wins' => 18,
            'total_losses' => 7,
            'win_percentage' => 72.0,
            'points_for' => 1245,
            'points_against' => 987,
            'current_streak' => 'W-3'
        ];

        $this->calendar = [
            [
                'date' => '2024-02-15',
                'time' => '19:00',
                'type' => 'match',
                'title' => 'vs Águilas Doradas',
                'team' => 'Halcones Juvenil',
                'venue' => 'Coliseo Municipal'
            ],
            [
                'date' => '2024-02-16',
                'time' => '17:00',
                'type' => 'training',
                'title' => 'Entrenamiento Técnico',
                'team' => 'Halcones Senior',
                'venue' => 'Gimnasio Club'
            ]
        ];

        $this->alerts = [
            [
                'type' => 'warning',
                'message' => '3 jugadoras con documentación próxima a vencer',
                'action' => 'Revisar documentos'
            ],
            [
                'type' => 'info',
                'message' => 'Nueva convocatoria disponible para Liga Profesional',
                'action' => 'Ver detalles'
            ]
        ];
    }

    public function render()
    {
        return view('livewire.coach.dashboard')
            ->layout('layouts.app.coach');
    }
}
