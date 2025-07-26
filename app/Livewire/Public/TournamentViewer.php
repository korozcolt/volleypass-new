<?php

namespace App\Livewire\Public;

use App\Models\Tournament;
use App\Models\Match;
use App\Models\Team;
use App\Models\Category;
use App\Models\Venue;
use App\Models\TournamentGroup;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

class TournamentViewer extends Component
{
    use WithPagination;
    
    // Filtros públicos
    public $selectedCategory = null;
    public $selectedStatus = 'active'; // all, active, completed, upcoming
    public $selectedVenue = null;
    public $searchTerm = '';
    
    // Datos principales
    public $tournaments = [];
    public $categories = [];
    public $venues = [];
    
    // Torneo seleccionado para vista detallada
    public $selectedTournament = null;
    public $tournamentDetails = [];
    public $standings = [];
    public $recentMatches = [];
    public $upcomingMatches = [];
    public $tournamentStats = [];
    
    // Estado de carga
    public $loading = true;
    public $showDetails = false;
    
    protected $queryString = [
        'selectedCategory' => ['except' => null],
        'selectedStatus' => ['except' => 'active'],
        'selectedVenue' => ['except' => null],
        'searchTerm' => ['except' => ''],
        'selectedTournament' => ['except' => null]
    ];
    
    public function mount()
    {
        $this->loadFilters();
        $this->loadTournaments();
        
        if ($this->selectedTournament) {
            $this->loadTournamentDetails();
        }
        
        $this->loading = false;
    }
    
    public function render()
    {
        return view('livewire.public.tournament-viewer');
    }
    
    public function updatedSelectedCategory()
    {
        $this->resetPage();
        $this->loadTournaments();
    }
    
    public function updatedSelectedStatus()
    {
        $this->resetPage();
        $this->loadTournaments();
    }
    
    public function updatedSelectedVenue()
    {
        $this->resetPage();
        $this->loadTournaments();
    }
    
    public function updatedSearchTerm()
    {
        $this->resetPage();
        $this->loadTournaments();
    }
    
    public function selectTournament($tournamentId)
    {
        $this->selectedTournament = $tournamentId;
        $this->showDetails = true;
        $this->loadTournamentDetails();
    }
    
    public function backToList()
    {
        $this->selectedTournament = null;
        $this->showDetails = false;
        $this->tournamentDetails = [];
        $this->standings = [];
        $this->recentMatches = [];
        $this->upcomingMatches = [];
    }
    
    public function clearFilters()
    {
        $this->selectedCategory = null;
        $this->selectedStatus = 'active';
        $this->selectedVenue = null;
        $this->searchTerm = '';
        $this->resetPage();
        $this->loadTournaments();
    }
    
    private function loadFilters()
    {
        // Cargar categorías disponibles
        $this->categories = Category::whereHas('tournaments', function($q) {
            $q->where('is_public', true);
        })
        ->orderBy('name')
        ->get()
        ->map(function($category) {
            return [
                'id' => $category->id,
                'name' => $category->name,
                'tournaments_count' => $category->tournaments()->where('is_public', true)->count()
            ];
        });
        
        // Cargar sedes disponibles
        $this->venues = Venue::whereHas('tournaments', function($q) {
            $q->where('is_public', true);
        })
        ->orderBy('name')
        ->get()
        ->map(function($venue) {
            return [
                'id' => $venue->id,
                'name' => $venue->name,
                'city' => $venue->city,
                'tournaments_count' => $venue->tournaments()->where('is_public', true)->count()
            ];
        });
    }
    
    private function loadTournaments()
    {
        $query = Tournament::where('is_public', true)
            ->with(['category', 'venue', 'teams'])
            ->when($this->selectedCategory, function($q) {
                $q->where('category_id', $this->selectedCategory);
            })
            ->when($this->selectedVenue, function($q) {
                $q->where('venue_id', $this->selectedVenue);
            })
            ->when($this->searchTerm, function($q) {
                $q->where(function($query) {
                    $query->where('name', 'like', '%' . $this->searchTerm . '%')
                          ->orWhere('description', 'like', '%' . $this->searchTerm . '%')
                          ->orWhereHas('venue', function($q) {
                              $q->where('name', 'like', '%' . $this->searchTerm . '%');
                          });
                });
            });
            
        // Filtrar por estado
        if ($this->selectedStatus === 'active') {
            $query->where('status', 'active')
                  ->where('start_date', '<=', now())
                  ->where('end_date', '>=', now());
        } elseif ($this->selectedStatus === 'completed') {
            $query->where('status', 'completed')
                  ->orWhere('end_date', '<', now());
        } elseif ($this->selectedStatus === 'upcoming') {
            $query->where('status', 'upcoming')
                  ->where('start_date', '>', now());
        }
        
        $this->tournaments = $query->orderBy('start_date', 'desc')
            ->paginate(12)
            ->through(function($tournament) {
                return [
                    'id' => $tournament->id,
                    'name' => $tournament->name,
                    'description' => $tournament->description,
                    'category' => $tournament->category->name,
                    'venue' => $tournament->venue->name,
                    'venue_city' => $tournament->venue->city,
                    'start_date' => $tournament->start_date,
                    'end_date' => $tournament->end_date,
                    'status' => $tournament->status,
                    'status_label' => $this->getStatusLabel($tournament->status),
                    'status_color' => $this->getStatusColor($tournament->status),
                    'teams_count' => $tournament->teams->count(),
                    'matches_played' => $this->getMatchesPlayedCount($tournament),
                    'total_matches' => $this->getTotalMatchesCount($tournament),
                    'progress_percentage' => $this->getProgressPercentage($tournament),
                    'next_match_date' => $this->getNextMatchDate($tournament),
                    'image_url' => $tournament->image_url,
                    'registration_deadline' => $tournament->registration_deadline,
                    'is_registration_open' => $tournament->registration_deadline > now()
                ];
            });
    }
    
    private function loadTournamentDetails()
    {
        $tournament = Tournament::where('id', $this->selectedTournament)
            ->where('is_public', true)
            ->with(['category', 'venue', 'teams.players', 'groups'])
            ->first();
            
        if (!$tournament) {
            $this->backToList();
            return;
        }
        
        $this->tournamentDetails = [
            'id' => $tournament->id,
            'name' => $tournament->name,
            'description' => $tournament->description,
            'category' => $tournament->category->name,
            'venue' => [
                'name' => $tournament->venue->name,
                'address' => $tournament->venue->address,
                'city' => $tournament->venue->city,
                'capacity' => $tournament->venue->capacity
            ],
            'start_date' => $tournament->start_date,
            'end_date' => $tournament->end_date,
            'status' => $tournament->status,
            'status_label' => $this->getStatusLabel($tournament->status),
            'status_color' => $this->getStatusColor($tournament->status),
            'teams_count' => $tournament->teams->count(),
            'total_players' => $tournament->teams->sum(function($team) {
                return $team->players->count();
            }),
            'groups' => $tournament->groups->map(function($group) {
                return [
                    'id' => $group->id,
                    'name' => $group->name,
                    'teams_count' => $group->teams->count()
                ];
            }),
            'organizer' => [
                'name' => $tournament->organizer_name,
                'contact' => $tournament->organizer_contact
            ],
            'rules' => $tournament->rules,
            'prizes' => $tournament->prizes,
            'image_url' => $tournament->image_url
        ];
        
        $this->loadStandings();
        $this->loadRecentMatches();
        $this->loadUpcomingMatches();
        $this->loadTournamentStats();
    }
    
    private function loadStandings()
    {
        $tournament = Tournament::find($this->selectedTournament);
        if (!$tournament) return;
        
        // Agrupar por grupos del torneo
        $this->standings = $tournament->groups->map(function($group) use ($tournament) {
            $teams = $group->teams()->with(['matches_as_home', 'matches_as_away'])
                ->get()
                ->map(function($team) use ($tournament) {
                    $stats = $this->calculatePublicTeamStats($team, $tournament->id);
                    return [
                        'team_name' => $team->name,
                        'matches_played' => $stats['matches_played'],
                        'wins' => $stats['wins'],
                        'losses' => $stats['losses'],
                        'sets_won' => $stats['sets_won'],
                        'sets_lost' => $stats['sets_lost'],
                        'points_for' => $stats['points_for'],
                        'points_against' => $stats['points_against'],
                        'points' => $stats['points'],
                        'win_percentage' => $stats['win_percentage'],
                        'set_ratio' => $stats['set_ratio']
                    ];
                })
                ->sortByDesc('points')
                ->values();
                
            return [
                'group_name' => $group->name,
                'teams' => $teams
            ];
        });
    }
    
    private function loadRecentMatches()
    {
        $this->recentMatches = Match::where('tournament_id', $this->selectedTournament)
            ->where('status', 'completed')
            ->with(['homeTeam', 'awayTeam', 'venue'])
            ->orderBy('match_date', 'desc')
            ->limit(10)
            ->get()
            ->map(function($match) {
                return [
                    'id' => $match->id,
                    'home_team' => $match->homeTeam->name,
                    'away_team' => $match->awayTeam->name,
                    'home_score' => $match->home_score,
                    'away_score' => $match->away_score,
                    'match_date' => $match->match_date,
                    'venue' => $match->venue ? $match->venue->name : 'Por definir',
                    'round' => $match->round,
                    'group' => $match->group,
                    'sets_detail' => $match->sets_detail ? $match->sets_detail : [],
                    'duration' => $match->duration,
                    'winner' => $match->home_score > $match->away_score ? $match->homeTeam->name : $match->awayTeam->name
                ];
            });
    }
    
    private function loadUpcomingMatches()
    {
        $this->upcomingMatches = Match::where('tournament_id', $this->selectedTournament)
            ->where('match_date', '>', now())
            ->where('status', '!=', 'cancelled')
            ->with(['homeTeam', 'awayTeam', 'venue'])
            ->orderBy('match_date', 'asc')
            ->limit(10)
            ->get()
            ->map(function($match) {
                return [
                    'id' => $match->id,
                    'home_team' => $match->homeTeam->name,
                    'away_team' => $match->awayTeam->name,
                    'match_date' => $match->match_date,
                    'venue' => $match->venue ? $match->venue->name : 'Por definir',
                    'round' => $match->round,
                    'group' => $match->group,
                    'days_until' => Carbon::parse($match->match_date)->diffInDays(now()),
                    'formatted_date' => Carbon::parse($match->match_date)->format('d/m/Y H:i'),
                    'is_today' => Carbon::parse($match->match_date)->isToday(),
                    'is_tomorrow' => Carbon::parse($match->match_date)->isTomorrow()
                ];
            });
    }
    
    private function loadTournamentStats()
    {
        $tournament = Tournament::find($this->selectedTournament);
        if (!$tournament) return;
        
        $totalMatches = Match::where('tournament_id', $this->selectedTournament)->count();
        $completedMatches = Match::where('tournament_id', $this->selectedTournament)
            ->where('status', 'completed')
            ->count();
        $upcomingMatches = Match::where('tournament_id', $this->selectedTournament)
            ->where('match_date', '>', now())
            ->where('status', '!=', 'cancelled')
            ->count();
            
        $this->tournamentStats = [
            'total_teams' => $tournament->teams->count(),
            'total_players' => $tournament->teams->sum(function($team) {
                return $team->players->count();
            }),
            'total_matches' => $totalMatches,
            'completed_matches' => $completedMatches,
            'upcoming_matches' => $upcomingMatches,
            'progress_percentage' => $totalMatches > 0 ? round(($completedMatches / $totalMatches) * 100, 1) : 0,
            'avg_match_duration' => $this->getAverageMatchDuration(),
            'total_sets_played' => $this->getTotalSetsPlayed(),
            'most_active_venue' => $this->getMostActiveVenue()
        ];
    }
    
    // Métodos auxiliares
    private function calculatePublicTeamStats($team, $tournamentId)
    {
        $homeMatches = Match::where('tournament_id', $tournamentId)
            ->where('home_team_id', $team->id)
            ->where('status', 'completed')
            ->get();
            
        $awayMatches = Match::where('tournament_id', $tournamentId)
            ->where('away_team_id', $team->id)
            ->where('status', 'completed')
            ->get();
            
        $allMatches = $homeMatches->concat($awayMatches);
        
        $wins = 0;
        $losses = 0;
        $setsWon = 0;
        $setsLost = 0;
        $pointsFor = 0;
        $pointsAgainst = 0;
        
        foreach ($allMatches as $match) {
            $isHome = $match->home_team_id === $team->id;
            $myScore = $isHome ? $match->home_score : $match->away_score;
            $opponentScore = $isHome ? $match->away_score : $match->home_score;
            
            if ($myScore > $opponentScore) {
                $wins++;
            } else {
                $losses++;
            }
            
            $setsWon += $myScore;
            $setsLost += $opponentScore;
            
            // Calcular puntos si están disponibles en sets_detail
            if ($match->sets_detail) {
                foreach ($match->sets_detail as $set) {
                    if (isset($set['home_points']) && isset($set['away_points'])) {
                        $pointsFor += $isHome ? $set['home_points'] : $set['away_points'];
                        $pointsAgainst += $isHome ? $set['away_points'] : $set['home_points'];
                    }
                }
            }
        }
        
        $matchesPlayed = $allMatches->count();
        $points = ($wins * 3) + ($losses * 0); // Sistema de puntos estándar
        
        return [
            'matches_played' => $matchesPlayed,
            'wins' => $wins,
            'losses' => $losses,
            'sets_won' => $setsWon,
            'sets_lost' => $setsLost,
            'points_for' => $pointsFor,
            'points_against' => $pointsAgainst,
            'points' => $points,
            'win_percentage' => $matchesPlayed > 0 ? round(($wins / $matchesPlayed) * 100, 1) : 0,
            'set_ratio' => $setsLost > 0 ? round($setsWon / $setsLost, 2) : $setsWon
        ];
    }
    
    private function getMatchesPlayedCount($tournament)
    {
        return Match::where('tournament_id', $tournament->id)
            ->where('status', 'completed')
            ->count();
    }
    
    private function getTotalMatchesCount($tournament)
    {
        return Match::where('tournament_id', $tournament->id)->count();
    }
    
    private function getProgressPercentage($tournament)
    {
        $total = $this->getTotalMatchesCount($tournament);
        $completed = $this->getMatchesPlayedCount($tournament);
        
        return $total > 0 ? round(($completed / $total) * 100, 1) : 0;
    }
    
    private function getNextMatchDate($tournament)
    {
        $match = Match::where('tournament_id', $tournament->id)
            ->where('match_date', '>', now())
            ->where('status', '!=', 'cancelled')
            ->orderBy('match_date', 'asc')
            ->first();
            
        if ($match) {
            return Carbon::parse($match->match_date)->format('d/m/Y H:i');
        }
        return null;
    }
    
    private function getAverageMatchDuration()
    {
        $avg = Match::where('tournament_id', $this->selectedTournament)
            ->where('status', 'completed')
            ->whereNotNull('duration')
            ->avg('duration');
            
        if ($avg) {
            return round($avg, 0) . ' min';
        }
        return 'N/A';
    }
    
    private function getTotalSetsPlayed()
    {
        $homeScore = Match::where('tournament_id', $this->selectedTournament)
            ->where('status', 'completed')
            ->sum('home_score');
            
        $awayScore = Match::where('tournament_id', $this->selectedTournament)
            ->where('status', 'completed')
            ->sum('away_score');
            
        return $homeScore + $awayScore;
    }
    
    private function getMostActiveVenue()
    {
        $venue = Match::where('tournament_id', $this->selectedTournament)
            ->with('venue')
            ->get()
            ->groupBy('venue_id')
            ->sortByDesc(function($matches) {
                return $matches->count();
            })
            ->first();
            
        if ($venue && $venue->first() && $venue->first()->venue) {
            return $venue->first()->venue->name;
        }
        return 'N/A';
    }
    
    private function getStatusLabel($status)
    {
        switch ($status) {
            case 'upcoming':
                return 'Próximo';
            case 'active':
                return 'En curso';
            case 'completed':
                return 'Finalizado';
            case 'cancelled':
                return 'Cancelado';
            default:
                return 'Desconocido';
        }
    }
    
    private function getStatusColor($status)
    {
        switch ($status) {
            case 'upcoming':
                return 'blue';
            case 'active':
                return 'green';
            case 'completed':
                return 'gray';
            case 'cancelled':
                return 'red';
            default:
                return 'gray';
        }
    }
}