<?php

namespace App\Livewire\Player;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class PlayerStats extends Component
{
    public $player;
    public $seasonStats;
    public $teamComparison;
    public $evolutionData;
    public $positionStats;
    public $selectedPeriod = 'season';

    public function mount()
    {
        $this->player = Auth::user()->player;
        $this->loadStatsData();
    }

    public function loadStatsData()
    {
        $this->seasonStats = [
            'points' => ['current' => 245, 'previous' => 198, 'change' => '+23.7%'],
            'aces' => ['current' => 34, 'previous' => 28, 'change' => '+21.4%'],
            'blocks' => ['current' => 28, 'previous' => 31, 'change' => '-9.7%'],
            'attacks' => ['current' => 156, 'previous' => 142, 'change' => '+9.9%'],
            'reception' => ['current' => 87.5, 'previous' => 84.2, 'change' => '+3.9%'],
            'efficiency' => ['current' => 78.3, 'previous' => 75.1, 'change' => '+4.3%']
        ];

        $this->teamComparison = [
            'points' => ['player' => 245, 'team_avg' => 189, 'rank' => 2],
            'aces' => ['player' => 34, 'team_avg' => 24, 'rank' => 1],
            'blocks' => ['player' => 28, 'team_avg' => 32, 'rank' => 4],
            'attacks' => ['player' => 156, 'team_avg' => 134, 'rank' => 3]
        ];

        $this->evolutionData = [
            'labels' => ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun'],
            'points' => [35, 42, 38, 45, 41, 48],
            'aces' => [4, 6, 5, 7, 6, 8],
            'blocks' => [3, 4, 5, 4, 6, 5]
        ];

        $this->positionStats = [
            'libero' => ['matches' => 8, 'efficiency' => 85.2],
            'outside_hitter' => ['matches' => 12, 'efficiency' => 78.9],
            'setter' => ['matches' => 3, 'efficiency' => 72.1]
        ];
    }

    public function updatePeriod($period)
    {
        $this->selectedPeriod = $period;
        $this->loadStatsData(); // Recargar datos según el período
    }

    public function render()
    {
        return view('livewire.player.player-stats');
    }
}
