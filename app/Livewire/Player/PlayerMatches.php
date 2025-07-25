<?php

namespace App\Livewire\Player;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class PlayerMatches extends Component
{
    public $player;
    public $upcomingMatches;
    public $matchHistory;
    public $convocationStatus;
    public $selectedTab = 'upcoming';

    public function mount()
    {
        $this->player = Auth::user()->player;
        $this->loadMatchesData();
    }

    public function loadMatchesData()
    {
        $this->upcomingMatches = [
            [
                'id' => 1,
                'opponent' => 'Águilas Doradas',
                'date' => '2024-02-15',
                'time' => '19:00',
                'venue' => 'Coliseo Municipal',
                'tournament' => 'Liga Profesional Sucre',
                'convocation_status' => 'confirmed',
                'team_list' => 'Titular'
            ],
            [
                'id' => 2,
                'opponent' => 'Tigres del Norte',
                'date' => '2024-02-22',
                'time' => '20:30',
                'venue' => 'Polideportivo Central',
                'tournament' => 'Copa Departamental',
                'convocation_status' => 'pending',
                'team_list' => 'Por definir'
            ]
        ];

        $this->matchHistory = [
            [
                'id' => 10,
                'opponent' => 'Leones FC',
                'date' => '2024-02-08',
                'result' => 'W 3-1',
                'points' => 18,
                'aces' => 3,
                'blocks' => 2,
                'efficiency' => 82.5,
                'playing_time' => '3 sets'
            ],
            [
                'id' => 9,
                'opponent' => 'Cóndores',
                'date' => '2024-02-01',
                'result' => 'L 1-3',
                'points' => 12,
                'aces' => 1,
                'blocks' => 4,
                'efficiency' => 75.3,
                'playing_time' => '4 sets'
            ]
        ];
    }

    public function setTab($tab)
    {
        $this->selectedTab = $tab;
    }

    public function render()
    {
        return view('livewire.player.player-matches');
    }
}
