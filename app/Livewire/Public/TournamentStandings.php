<?php

namespace App\Livewire\Public;

use App\Models\Tournament;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.public')]
class TournamentStandings extends Component
{
    public Tournament $tournament;
    public $standings;
    public $selectedGroup = 'all';

    public function mount(Tournament $tournament)
    {
        if (!$tournament->is_public) {
            abort(404);
        }

        $this->tournament = $tournament->load(['teams', 'groups']);
        $this->loadStandings();
    }

    public function updatedSelectedGroup()
    {
        $this->loadStandings();
    }

    private function loadStandings()
    {
        $query = $this->tournament->teams()->with(['matches']);

        if ($this->selectedGroup !== 'all') {
            $query->where('group', $this->selectedGroup);
        }

        $teams = $query->get();

        $this->standings = $teams->map(function ($team) {
            $matches = $team->matches()->where('tournament_id', $this->tournament->id)->get();

            $wins = $matches->where('winner_team_id', $team->id)->count();
            $losses = $matches->where('winner_team_id', '!=', $team->id)->where('winner_team_id', '!=', null)->count();
            $played = $wins + $losses;

            $setsWon = 0;
            $setsLost = 0;
            $pointsWon = 0;
            $pointsLost = 0;

            foreach ($matches as $match) {
                if ($match->home_team_id === $team->id) {
                    $setsWon += $match->home_sets ?? 0;
                    $setsLost += $match->away_sets ?? 0;
                    $pointsWon += $match->home_points ?? 0;
                    $pointsLost += $match->away_points ?? 0;
                } else {
                    $setsWon += $match->away_sets ?? 0;
                    $setsLost += $match->home_sets ?? 0;
                    $pointsWon += $match->away_points ?? 0;
                    $pointsLost += $match->home_points ?? 0;
                }
            }

            return [
                'team' => $team,
                'played' => $played,
                'wins' => $wins,
                'losses' => $losses,
                'sets_won' => $setsWon,
                'sets_lost' => $setsLost,
                'set_ratio' => $setsLost > 0 ? round($setsWon / $setsLost, 2) : $setsWon,
                'points_won' => $pointsWon,
                'points_lost' => $pointsLost,
                'point_ratio' => $pointsLost > 0 ? round($pointsWon / $pointsLost, 2) : $pointsWon,
                'points' => ($wins * 3) + ($losses * 1), // Ejemplo de sistema de puntos
            ];
        })->sortByDesc('points')->values();
    }

    public function render()
    {
        return view('livewire.public.tournament-standings');
    }
}
