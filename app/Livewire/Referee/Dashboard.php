<?php

namespace App\Livewire\Referee;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Dashboard extends Component
{
    public $referee;
    public $assignedMatches;
    public $activeTournaments;
    public $arbitrageStats;
    public $liveMatch;
    public $evaluations;

    public function mount()
    {
        $this->referee = Auth::user()->referee;
        $this->loadDashboardData();
    }

    public function loadDashboardData()
    {
        $this->assignedMatches = [
            [
                'id' => 1,
                'date' => '2024-02-15',
                'time' => '19:00',
                'team_a' => 'Halcones FC',
                'team_b' => 'Águilas Doradas',
                'venue' => 'Coliseo Municipal',
                'tournament' => 'Liga Profesional Sucre',
                'status' => 'scheduled',
                'type' => 'main_referee'
            ],
            [
                'id' => 2,
                'date' => '2024-02-16',
                'time' => '20:30',
                'team_a' => 'Tigres del Norte',
                'team_b' => 'Leones FC',
                'venue' => 'Polideportivo Central',
                'tournament' => 'Copa Departamental',
                'status' => 'scheduled',
                'type' => 'line_judge'
            ]
        ];

        $this->activeTournaments = [
            [
                'id' => 1,
                'name' => 'Liga Profesional Sucre 2024',
                'matches_today' => 3,
                'my_matches' => 1
            ],
            [
                'id' => 2,
                'name' => 'Copa Departamental',
                'matches_today' => 2,
                'my_matches' => 1
            ]
        ];

        $this->arbitrageStats = [
            'total_matches' => 45,
            'this_season' => 18,
            'average_rating' => 4.7,
            'yellow_cards' => 12,
            'red_cards' => 3,
            'disputed_calls' => 2
        ];

        $this->liveMatch = null; // No hay partido en vivo actualmente

        $this->evaluations = [
            [
                'match' => 'Halcones vs Águilas',
                'date' => '2024-02-08',
                'rating' => 4.8,
                'comments' => 'Excelente control del partido, decisiones acertadas'
            ],
            [
                'match' => 'Tigres vs Leones',
                'date' => '2024-02-01',
                'rating' => 4.5,
                'comments' => 'Buen arbitraje, algunas dudas en el tercer set'
            ]
        ];
    }

    public function startMatch($matchId)
    {
        // Iniciar control del partido
        $this->liveMatch = $matchId;
        $this->dispatch('match-started', ['matchId' => $matchId]);
    }

    public function render()
    {
        return view('livewire.referee.dashboard')
            ->layout('layouts.app.referee');
    }
}
