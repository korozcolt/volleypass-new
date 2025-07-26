<?php

namespace App\Livewire\Public;

use App\Models\Tournament;
use App\Models\Team;
use App\Models\VolleyMatch;
use App\Enums\MatchStatus;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Collection;

#[Layout('layouts.public')]
class Standings extends Component
{
    public $tournaments;
    public $selectedTournament = null;
    public $standings = [];
    public $tournamentStats = [];
    public $upcomingMatches = [];

    public function mount()
    {
        $this->loadTournaments();
        if ($this->tournaments->isNotEmpty()) {
            $this->selectedTournament = $this->tournaments->first()->id;
            $this->loadStandings();
        }
    }

    public function updatedSelectedTournament()
    {
        $this->loadStandings();
    }

    public function loadTournaments()
    {
        $this->tournaments = Tournament::where('is_public', true)
            ->whereIn('status', ['in_progress', 'registration_closed'])
            ->with(['teams', 'category'])
            ->orderBy('start_date', 'desc')
            ->get();
    }

    public function loadStandings()
    {
        if (!$this->selectedTournament) {
            return;
        }

        $tournament = Tournament::find($this->selectedTournament);
        if (!$tournament) {
            return;
        }

        $this->standings = $tournament->teams->map(function ($team) use ($tournament) {
            $matches = VolleyMatch::where('tournament_id', $tournament->id)
                ->where(function ($query) use ($team) {
                    $query->where('home_team_id', $team->id)
                          ->orWhere('away_team_id', $team->id);
                })
                ->where('status', MatchStatus::Finished)
                ->get();

            $matchesPlayed = $matches->count();
            $wins = 0;
            $losses = 0;
            $setsWon = 0;
            $setsLost = 0;
            $pointsWon = 0;
            $pointsLost = 0;

            foreach ($matches as $match) {
                $isHome = $match->home_team_id === $team->id;
                $teamScore = $isHome ? $match->home_score : $match->away_score;
                $opponentScore = $isHome ? $match->away_score : $match->home_score;

                if ($teamScore > $opponentScore) {
                    $wins++;
                } else {
                    $losses++;
                }

                $setsWon += $teamScore;
                $setsLost += $opponentScore;

                // Calcular puntos de sets individuales si están disponibles
                if ($match->sets_data) {
                    $setsData = json_decode($match->sets_data, true);
                    foreach ($setsData as $set) {
                        $pointsWon += $isHome ? $set['home_points'] : $set['away_points'];
                        $pointsLost += $isHome ? $set['away_points'] : $set['home_points'];
                    }
                }
            }

            return (object) [
                'team' => $team,
                'matches_played' => $matchesPlayed,
                'wins' => $wins,
                'losses' => $losses,
                'sets_won' => $setsWon,
                'sets_lost' => $setsLost,
                'set_ratio' => $setsLost > 0 ? round($setsWon / $setsLost, 2) : $setsWon,
                'points_won' => $pointsWon,
                'points_lost' => $pointsLost,
                'point_ratio' => $pointsLost > 0 ? round($pointsWon / $pointsLost, 2) : $pointsWon,
                'points' => ($wins * 3) + ($losses * 1), // Sistema de puntos: 3 por victoria, 1 por derrota
            ];
        })->sortByDesc('points')->values()->toArray();

        $this->loadTournamentStats($tournament);
        $this->loadUpcomingMatches($tournament);
    }

    private function loadTournamentStats($tournament)
    {
        $totalMatches = VolleyMatch::where('tournament_id', $tournament->id)->count();
        $playedMatches = VolleyMatch::where('tournament_id', $tournament->id)
            ->where('status', MatchStatus::Finished)
            ->count();
        $liveMatches = VolleyMatch::where('tournament_id', $tournament->id)
            ->where('status', MatchStatus::In_Progress)
            ->count();
        $totalTeams = $tournament->teams->count();
        $totalSets = VolleyMatch::where('tournament_id', $tournament->id)
            ->where('status', MatchStatus::Finished)
            ->sum('home_score') + VolleyMatch::where('tournament_id', $tournament->id)
            ->where('status', MatchStatus::Finished)
            ->sum('away_score');

        $this->tournamentStats = [
            'total_matches' => $totalMatches,
            'played_matches' => $playedMatches,
            'live_matches' => $liveMatches,
            'total_teams' => $totalTeams,
            'total_sets' => $totalSets,
            'completion_percentage' => $totalMatches > 0 ? round(($playedMatches / $totalMatches) * 100) : 0
        ];
    }

    private function loadUpcomingMatches($tournament)
    {
        $this->upcomingMatches = VolleyMatch::where('tournament_id', $tournament->id)
            ->whereIn('status', [MatchStatus::Scheduled])
            ->with(['homeTeam', 'awayTeam'])
            ->orderBy('scheduled_at')
            ->take(3)
            ->get();
    }

    public function getPositionColor($position)
    {
        if ($position <= 4) {
            return 'bg-green-500'; // Clasificado a playoffs
        } elseif ($position >= $this->standings->count() - 1) {
            return 'bg-red-500'; // Zona de descenso
        }
        return 'bg-gray-400'; // Posición normal
    }

    public function render()
    {
        return view('livewire.public.standings');
    }
}