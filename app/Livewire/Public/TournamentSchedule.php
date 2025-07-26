<?php

namespace App\Livewire\Public;

use App\Models\Tournament;
use App\Enums\MatchStatus;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Carbon\Carbon;

#[Layout('layouts.public')]
class TournamentSchedule extends Component
{
    public Tournament $tournament;
    public $matches;
    public $selectedDate = null;
    public $selectedPhase = 'all';
    public $availableDates = [];
    public $availablePhases = [];

    public function mount(Tournament $tournament)
    {
        if (!$tournament->is_public) {
            abort(404);
        }

        $this->tournament = $tournament;
        $this->selectedDate = now()->format('Y-m-d');
        $this->loadAvailableOptions();
        $this->loadMatches();
    }

    public function updatedSelectedDate()
    {
        $this->loadMatches();
    }

    public function updatedSelectedPhase()
    {
        $this->loadMatches();
    }

    private function loadAvailableOptions()
    {
        // Cargar fechas disponibles
        $this->availableDates = $this->tournament->matches()
            ->whereNotNull('scheduled_at')
            ->selectRaw('DATE(scheduled_at) as match_date')
            ->distinct()
            ->orderBy('match_date')
            ->pluck('match_date')
            ->map(function ($date) {
                return [
                    'value' => $date,
                    'label' => Carbon::parse($date)->format('d M Y')
                ];
            })
            ->toArray();

        // Cargar fases disponibles
        $this->availablePhases = $this->tournament->matches()
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
    }

    private function loadMatches()
    {
        $query = $this->tournament->matches()
            ->with(['homeTeam', 'awayTeam']);

        if ($this->selectedDate) {
            $query->whereDate('scheduled_at', $this->selectedDate);
        }

        if ($this->selectedPhase !== 'all') {
            $query->where('phase', $this->selectedPhase);
        }

        $this->matches = $query->orderBy('scheduled_at')->get()->groupBy(function ($match) {
            return $match->scheduled_at ? $match->scheduled_at->format('H:i') : 'Sin hora';
        });
    }

    public function render()
    {
        return view('livewire.public.tournament-schedule');
    }
}
