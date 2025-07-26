<?php

namespace App\Livewire\Public;

use Livewire\Component;
use App\Models\VolleyMatch;
use App\Enums\MatchStatus;
use Livewire\Attributes\On;

class RecentResults extends Component
{
    public $recentResults = [];
    public $isLoading = true;
    public $limit = 10;

    public function mount()
    {
        $this->loadRecentResults();
    }

    #[On('match-finished')]
    public function refreshResults()
    {
        $this->loadRecentResults();
    }

    public function loadRecentResults()
    {
        $this->recentResults = VolleyMatch::with(['homeTeam', 'awayTeam', 'tournament'])
            ->where('status', MatchStatus::Finished)
            ->latest('finished_at')
            ->take($this->limit)
            ->get()
            ->map(function ($match) {
                return [
                    'id' => $match->id,
                    'tournament' => [
                        'name' => $match->tournament->name ?? 'Torneo'
                    ],
                    'homeTeam' => [
                        'name' => $match->homeTeam->name ?? 'Equipo Local'
                    ],
                    'awayTeam' => [
                        'name' => $match->awayTeam->name ?? 'Equipo Visitante'
                    ],
                    'home_sets' => $match->home_sets ?? 0,
                    'away_sets' => $match->away_sets ?? 0,
                    'finished_at' => $match->finished_at,
                    'winner' => $match->winner_team_id === $match->home_team_id ? 'home' : 'away'
                ];
            })
            ->toArray();
        
        $this->isLoading = false;
    }

    public function render()
    {
        return view('livewire.public.recent-results');
    }
}
