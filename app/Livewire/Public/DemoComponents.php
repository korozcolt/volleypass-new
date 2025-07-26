<?php

namespace App\Livewire\Public;

use App\Models\Tournament;
use App\Models\VolleyMatch;
use App\Models\Team;
use Livewire\Component;
use Illuminate\Support\Collection;

class DemoComponents extends Component
{
    public function render()
    {
        return view('livewire.public.demo-components', [
            'featuredTournaments' => $this->getFeaturedTournaments(),
            'liveMatches' => $this->getLiveMatches(),
            'upcomingMatches' => $this->getUpcomingMatches(),
            'standingsData' => $this->getStandingsData(),
        ]);
    }

    private function getFeaturedTournaments(): Collection
    {
        return Tournament::with(['teams', 'matches'])
            ->where('is_active', true)
            ->orderBy('start_date', 'desc')
            ->take(3)
            ->get()
            ->map(function ($tournament) {
                $totalMatches = $tournament->matches->count();
                $completedMatches = $tournament->matches->where('status', 'finished')->count();
                $progress = $totalMatches > 0 ? round(($completedMatches / $totalMatches) * 100) : 0;
                
                return [
                    'id' => $tournament->id,
                    'name' => $tournament->name,
                    'description' => $tournament->description ?? 'Torneo de voleibol competitivo',
                    'teams_count' => $tournament->teams->count(),
                    'cities_count' => $tournament->teams->pluck('city')->unique()->count(),
                    'progress' => $progress,
                    'status' => $this->getTournamentStatus($tournament),
                    'start_date' => $tournament->start_date,
                    'end_date' => $tournament->end_date,
                    'winner' => $this->getTournamentWinner($tournament),
                    'total_matches' => $totalMatches,
                ];
            });
    }

    private function getLiveMatches(): Collection
    {
        return VolleyMatch::with(['homeTeam', 'awayTeam', 'tournament'])
            ->where('status', 'in_progress')
            ->orderBy('match_date', 'desc')
            ->take(2)
            ->get()
            ->map(function ($match) {
                return [
                    'id' => $match->id,
                    'tournament_name' => $match->tournament->name ?? 'Torneo',
                    'phase' => $match->phase ?? 'Fase Regular',
                    'home_team' => [
                        'name' => $match->homeTeam->name,
                        'logo' => strtoupper(substr($match->homeTeam->name, 0, 2)),
                        'record' => $this->getTeamRecord($match->homeTeam),
                    ],
                    'away_team' => [
                        'name' => $match->awayTeam->name,
                        'logo' => strtoupper(substr($match->awayTeam->name, 0, 2)),
                        'record' => $this->getTeamRecord($match->awayTeam),
                    ],
                    'current_set' => $match->current_set ?? 1,
                    'home_score' => $match->home_score ?? 0,
                    'away_score' => $match->away_score ?? 0,
                    'sets' => $this->getMatchSets($match),
                    'time_elapsed' => $this->getTimeElapsed($match),
                ];
            });
    }

    private function getUpcomingMatches(): Collection
    {
        return VolleyMatch::with(['homeTeam', 'awayTeam', 'tournament'])
            ->where('status', 'scheduled')
            ->where('match_date', '>', now())
            ->orderBy('match_date', 'asc')
            ->take(2)
            ->get()
            ->map(function ($match) {
                return [
                    'id' => $match->id,
                    'tournament_name' => $match->tournament->name ?? 'Torneo',
                    'phase' => $match->phase ?? 'Fase Regular',
                    'home_team' => [
                        'name' => $match->homeTeam->name,
                        'logo' => strtoupper(substr($match->homeTeam->name, 0, 2)),
                        'record' => $this->getTeamRecord($match->homeTeam),
                    ],
                    'away_team' => [
                        'name' => $match->awayTeam->name,
                        'logo' => strtoupper(substr($match->awayTeam->name, 0, 2)),
                        'record' => $this->getTeamRecord($match->awayTeam),
                    ],
                    'match_date' => $match->match_date,
                    'venue' => $match->venue ?? 'Por definir',
                ];
            });
    }

    private function getStandingsData(): array
    {
        $tournament = Tournament::with(['teams', 'matches'])
            ->where('is_active', true)
            ->first();

        if (!$tournament) {
            return [
                'tournament' => null,
                'standings' => collect(),
            ];
        }

        $standings = $tournament->teams->map(function ($team) use ($tournament) {
            $matches = VolleyMatch::where('tournament_id', $tournament->id)
                ->where(function ($query) use ($team) {
                    $query->where('home_team_id', $team->id)
                          ->orWhere('away_team_id', $team->id);
                })
                ->where('status', 'finished')
                ->get();

            $wins = 0;
            $losses = 0;
            $setsWon = 0;
            $setsLost = 0;
            $recentResults = [];

            foreach ($matches as $match) {
                $isHome = $match->home_team_id === $team->id;
                $teamScore = $isHome ? $match->home_score : $match->away_score;
                $opponentScore = $isHome ? $match->away_score : $match->home_score;

                if ($teamScore > $opponentScore) {
                    $wins++;
                    $recentResults[] = 'G';
                } else {
                    $losses++;
                    $recentResults[] = 'P';
                }

                $setsWon += $teamScore;
                $setsLost += $opponentScore;
            }

            $points = ($wins * 3) + ($losses * 1); // 3 points for win, 1 for loss
            $recentResults = array_slice(array_reverse($recentResults), 0, 5);

            return [
                'team' => $team,
                'matches_played' => $matches->count(),
                'wins' => $wins,
                'losses' => $losses,
                'sets_won' => $setsWon,
                'sets_lost' => $setsLost,
                'points' => $points,
                'recent_results' => $recentResults,
                'logo' => strtoupper(substr($team->name, 0, 2)),
            ];
        })->sortByDesc('points')->values();

        return [
            'tournament' => $tournament,
            'standings' => $standings->take(8), // Show top 8 teams
        ];
    }

    private function getTournamentStatus($tournament): string
    {
        $now = now();
        
        if ($tournament->start_date > $now) {
            return 'upcoming';
        } elseif ($tournament->end_date < $now) {
            return 'finished';
        } else {
            return 'live';
        }
    }

    private function getTournamentWinner($tournament): ?string
    {
        if ($this->getTournamentStatus($tournament) !== 'finished') {
            return null;
        }

        // Get the team with most points in finished tournament
        $standings = $this->getStandingsData();
        return $standings['standings']->first()['team']->name ?? null;
    }

    private function getTeamRecord($team): string
    {
        $matches = VolleyMatch::where(function ($query) use ($team) {
            $query->where('home_team_id', $team->id)
                  ->orWhere('away_team_id', $team->id);
        })
        ->where('status', 'finished')
        ->get();

        $wins = 0;
        $losses = 0;

        foreach ($matches as $match) {
            $isHome = $match->home_team_id === $team->id;
            $teamScore = $isHome ? $match->home_score : $match->away_score;
            $opponentScore = $isHome ? $match->away_score : $match->home_score;

            if ($teamScore > $opponentScore) {
                $wins++;
            } else {
                $losses++;
            }
        }

        return "{$wins}-{$losses} (Temporada)";
    }

    private function getMatchSets($match): array
    {
        // This would typically come from a sets table or JSON field
        // For now, we'll simulate based on current scores
        $sets = [];
        $currentSet = $match->current_set ?? 1;
        
        for ($i = 1; $i <= 5; $i++) {
            if ($i < $currentSet) {
                // Finished sets (simulated)
                $sets[] = [
                    'set_number' => $i,
                    'home_score' => rand(20, 25),
                    'away_score' => rand(20, 25),
                    'status' => 'finished'
                ];
            } elseif ($i === $currentSet) {
                // Current set
                $sets[] = [
                    'set_number' => $i,
                    'home_score' => $match->home_score ?? 0,
                    'away_score' => $match->away_score ?? 0,
                    'status' => 'current'
                ];
            }
        }
        
        return $sets;
    }

    private function getTimeElapsed($match): string
    {
        if (!$match->match_date) {
            return '00:00';
        }
        
        $elapsed = now()->diffInMinutes($match->match_date);
        $hours = intval($elapsed / 60);
        $minutes = $elapsed % 60;
        
        return sprintf('%02d:%02d', $hours, $minutes);
    }
}