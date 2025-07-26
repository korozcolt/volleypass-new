<?php

namespace App\Livewire\Public;

use Livewire\Component;
use App\Models\VolleyMatch;
use App\Enums\MatchStatus;
use Livewire\Attributes\On;

class LiveMatches extends Component
{
    public $liveMatches = [];
    public $isLoading = true;

    public function mount()
    {
        $this->loadLiveMatches();
    }

    #[On('match-updated')]
    public function refreshMatch($matchId)
    {
        $this->loadLiveMatches();
        $this->dispatch('match-score-updated', matchId: $matchId);
    }

    public function loadLiveMatches()
    {
        $this->liveMatches = VolleyMatch::where('status', MatchStatus::In_Progress)
            ->with(['homeTeam', 'awayTeam', 'tournament'])
            ->orderBy('started_at', 'desc')
            ->limit(6)
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
                    'home_score' => $match->home_sets ?? 0,
                    'away_score' => $match->away_sets ?? 0,
                    'current_set' => $match->current_set ?? 1,
                    'match_time' => $match->started_at ? $match->started_at->diffForHumans() : '0'
                ];
            })
            ->toArray();
        
        $this->isLoading = false;
    }

    public function render()
    {
        return view('livewire.public.live-matches');
    }
}
