<?php

namespace App\Livewire\Public;

use Livewire\Component;
use App\Models\Tournament;
use App\Models\Team;
use App\Models\VolleyMatch;
use App\Enums\MatchStatus;
use Livewire\Attributes\On;
use Illuminate\Support\Collection;

class LeagueStats extends Component
{
    public $tournaments;
    public $selectedTournament = null;
    public $leagueStats = [];
    public $topTeams = [];
    public $isLoading = true;

    public function mount()
    {
        $this->loadTournaments();
        if ($this->tournaments->isNotEmpty()) {
            $this->selectedTournament = $this->tournaments->first()->id;
            $this->loadLeagueStats();
        }
        $this->isLoading = false;
    }

    public function updatedSelectedTournament()
    {
        $this->loadLeagueStats();
    }

    #[On('tournament-updated')]
    public function refreshStats()
    {
        $this->loadLeagueStats();
    }

    private function loadTournaments()
    {
        $this->tournaments = Tournament::where('is_public', true)
            ->where('status', 'active')
            ->orderBy('start_date', 'desc')
            ->get();
    }

    private function loadLeagueStats()
    {
        if (!$this->selectedTournament) {
            return;
        }

        $tournament = Tournament::find($this->selectedTournament);
        
        if (!$tournament) {
            return;
        }

        // Calculate general tournament stats
        $totalMatches = VolleyMatch::where('tournament_id', $this->selectedTournament)->count();
        $playedMatches = VolleyMatch::where('tournament_id', $this->selectedTournament)
            ->where('status', MatchStatus::Finished)
            ->count();
        $totalTeams = $tournament->teams()->count();
        $totalSets = VolleyMatch::where('tournament_id', $this->selectedTournament)
            ->where('status', MatchStatus::Finished)
            ->sum('total_sets');

        $this->leagueStats = [
            'tournament_name' => $tournament->name,
            'total_teams' => $totalTeams,
            'total_matches' => $totalMatches,
            'played_matches' => $playedMatches,
            'remaining_matches' => $totalMatches - $playedMatches,
            'total_sets' => $totalSets,
            'completion_percentage' => $totalMatches > 0 ? round(($playedMatches / $totalMatches) * 100, 1) : 0
        ];

        // Get top teams standings
        $this->topTeams = $this->calculateStandings($tournament);
    }

    private function calculateStandings($tournament)
    {
        $teams = $tournament->teams()->get();
        
        return $teams->map(function ($team) {
            $matches = VolleyMatch::where('tournament_id', $this->selectedTournament)
                ->where(function ($query) use ($team) {
                    $query->where('home_team_id', $team->id)
                          ->orWhere('away_team_id', $team->id);
                })
                ->where('status', MatchStatus::Finished)
                ->get();

            $wins = 0;
            $losses = 0;
            $setsWon = 0;
            $setsLost = 0;

            foreach ($matches as $match) {
                if ($match->home_team_id === $team->id) {
                    $setsWon += $match->home_sets ?? 0;
                    $setsLost += $match->away_sets ?? 0;
                    if (($match->home_sets ?? 0) > ($match->away_sets ?? 0)) {
                        $wins++;
                    } else {
                        $losses++;
                    }
                } else {
                    $setsWon += $match->away_sets ?? 0;
                    $setsLost += $match->home_sets ?? 0;
                    if (($match->away_sets ?? 0) > ($match->home_sets ?? 0)) {
                        $wins++;
                    } else {
                        $losses++;
                    }
                }
            }

            $points = ($wins * 3) + ($losses * 1); // 3 points for win, 1 for loss

            return [
                'team' => $team,
                'matches_played' => $matches->count(),
                'wins' => $wins,
                'losses' => $losses,
                'sets_won' => $setsWon,
                'sets_lost' => $setsLost,
                'points' => $points,
                'win_percentage' => $matches->count() > 0 ? round(($wins / $matches->count()) * 100, 1) : 0
            ];
        })->sortByDesc('points')->take(5)->values();
    }

    public function render()
    {
        return view('livewire.public.league-stats');
    }
}
