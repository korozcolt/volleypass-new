<?php

namespace App\Livewire\Player;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.player-dashboard')]
class Dashboard extends Component
{
    public $player;
    public $personalStats;
    public $teamStats;
    public $upcomingMatches;
    public $availableTournaments;
    public $performanceHistory;

    public function mount()
    {
        $this->player = Auth::user()->player;
        
        // Verificar si el usuario tiene un perfil de jugador
        if (!$this->player) {
            session()->flash('error', 'No tienes un perfil de jugador asociado a tu cuenta.');
            $this->redirect(route('dashboard'));
            return;
        }
        
        $this->loadDashboardData();
    }

    public function loadDashboardData()
    {
        // Cargar estadísticas personales
        $this->personalStats = [
            'points' => rand(150, 300),
            'aces' => rand(20, 50),
            'blocks' => rand(15, 40),
            'attacks' => rand(80, 150),
            'matches_played' => rand(15, 25),
            'win_rate' => rand(60, 85)
        ];

        // Cargar estadísticas del equipo
        $this->teamStats = [
            'team_ranking' => rand(1, 8),
            'team_points' => rand(200, 400),
            'matches_won' => rand(10, 20),
            'matches_lost' => rand(3, 8),
            'next_match' => 'vs Águilas Doradas - 15 Feb 2024'
        ];

        // Próximos partidos
        $this->upcomingMatches = [
            [
                'id' => 1,
                'opponent' => 'Águilas Doradas',
                'date' => '2024-02-15',
                'time' => '19:00',
                'venue' => 'Coliseo Municipal',
                'tournament' => 'Liga Profesional Sucre'
            ],
            [
                'id' => 2,
                'opponent' => 'Tigres del Norte',
                'date' => '2024-02-22',
                'time' => '20:30',
                'venue' => 'Polideportivo Central',
                'tournament' => 'Copa Departamental'
            ]
        ];

        // Torneos disponibles
        $this->availableTournaments = [
            [
                'id' => 1,
                'name' => 'Liga Profesional Sucre 2024',
                'status' => 'active',
                'matches_today' => 3,
                'my_team_playing' => true
            ],
            [
                'id' => 2,
                'name' => 'Copa Departamental',
                'status' => 'active',
                'matches_today' => 1,
                'my_team_playing' => false
            ]
        ];
    }

    public function render()
    {
        return view('livewire.player.dashboard');
    }
}
