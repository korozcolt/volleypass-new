<?php

namespace App\Livewire\Public;

use App\Models\Tournament;
use App\Enums\MatchStatus;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.public')]
class TournamentResults extends Component
{
    use WithPagination;

    public Tournament $tournament;
    public $selectedPhase = 'all';
    public $selectedTeam = 'all';
    public $availablePhases = [];
    public $availableTeams = [];

    public function mount(Tournament $tournament)
    {
        if (!$tournament->is_public) {
            abort(404);
        }

        $this->tournament = $tournament;
        $this->loadAvailableOptions();
    }

    public function updatedSelectedPhase()
    {
        $this->resetPage();
    }

    public function updatedSelectedTeam()
    {
        $this->resetPage();
    }

    private function loadAvailableOptions()
    {
        // Cargar fases disponibles
        $this->availablePhases = $this->tournament->matches()
            ->where('status', MatchStatus::Finished)
            ->whereNotNull('phase')
            ->distinct()
            ->pluck('phase')
            ->map(function ($phase) {
                return [
                    'value' => $phase,
                    'label' => $phase
                ];
            })
            ->toArray();

        // Cargar equipos disponibles
        $this->availableTeams = $this->tournament->teams()
            ->orderBy('name')
            ->get()
            ->map(function ($team) {
                return [
                    'value' => $team->id,
                    'label' => $team->name
                ];
            })
            ->toArray();
    }

    public function render()
    {
        $query = $this->tournament->matches()
            ->with(['homeTeam', 'awayTeam'])
            ->where('status', MatchStatus::Finished);

        if ($this->selectedPhase !== 'all') {
            $query->where('phase', $this->selectedPhase);
        }

        if ($this->selectedTeam !== 'all') {
            $query->where(function ($q) {
                $q->where('home_team_id', $this->selectedTeam)
                  ->orWhere('away_team_id', $this->selectedTeam);
            });
        }

        $matches = $query->latest('finished_at')->paginate(10);

        return view('livewire.public.tournament-results', [
            'matches' => $matches
        ]);
    }
}
