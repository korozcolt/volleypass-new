<?php

namespace App\Livewire\Public;

use App\Models\Tournament;
use App\Models\VolleyMatch;
use App\Models\Team;
use App\Enums\MatchStatus;
use App\Enums\UserRole;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Illuminate\Support\Collection;

#[Layout('layouts.public-dashboard')]
class TournamentsDashboard extends Component
{
    use WithPagination;

    public $activeTab = 'torneos';
    public $searchTerm = '';
    public $statusFilter = 'all';
    public $categoryFilter = 'all';
    public $cityFilter = 'all';
    
    // Data properties
    public $activeTournaments;
    public $liveMatches;
    public $recentResults;
    public $upcomingMatches;
    public $tournamentStats;
    public $topTeams;
    public $topPlayers;
    public $featuredMatch;
    
    protected $queryString = [
        'activeTab' => ['except' => 'torneos'],
        'searchTerm' => ['except' => ''],
        'statusFilter' => ['except' => 'all'],
        'categoryFilter' => ['except' => 'all'],
        'cityFilter' => ['except' => 'all']
    ];

    public function mount()
    {
        $this->loadDashboardData();
    }

    public function updatedSearchTerm()
    {
        $this->resetPage();
        $this->loadFilteredTournaments();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
        $this->loadFilteredTournaments();
    }

    public function updatedCategoryFilter()
    {
        $this->resetPage();
        $this->loadFilteredTournaments();
    }

    public function updatedCityFilter()
    {
        $this->resetPage();
        $this->loadFilteredTournaments();
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
        
        // Load specific data based on tab
        switch ($tab) {
            case 'en-vivo':
                $this->loadLiveData();
                break;
            case 'resultados':
                $this->loadResultsData();
                break;
            case 'estadisticas':
                $this->loadStatisticsData();
                break;
            default:
                $this->loadTournamentsData();
                break;
        }
    }

    public function toggleFavorite($tournamentId)
    {
        // Implementation for favorite tournaments
        // This would typically save to user preferences or local storage
        $this->dispatch('tournament-favorited', ['tournamentId' => $tournamentId]);
    }

    public function shareTournament($tournamentId)
    {
        $tournament = Tournament::find($tournamentId);
        if ($tournament) {
            $shareUrl = route('tournaments.public.show', $tournament->id);
            $this->dispatch('share-tournament', [
                'url' => $shareUrl,
                'title' => $tournament->name
            ]);
        }
    }

    private function loadDashboardData()
    {
        $this->loadTournamentStats();
        $this->loadActiveTournaments();
        $this->loadLiveMatches();
        $this->loadFeaturedMatch();
        $this->loadRecentResults();
        $this->loadUpcomingMatches();
    }

    private function loadTournamentStats()
    {
        $this->tournamentStats = [
            'active_tournaments' => Tournament::where('status', 'active')->count(),
            'live_matches' => VolleyMatch::where('status', MatchStatus::In_Progress)->count(),
            'upcoming_matches' => VolleyMatch::where('status', MatchStatus::Scheduled)
                ->where('scheduled_at', '>=', now())
                ->where('scheduled_at', '<=', now()->addDays(7))
                ->count(),
            'registered_teams' => Team::where('status', 'active')->count()
        ];
    }

    private function loadActiveTournaments()
    {
        $query = Tournament::with(['teams', 'matches'])
            ->where('status', 'active')
            ->orWhere('status', 'registration_open');

        if ($this->searchTerm) {
            $query->where('name', 'like', '%' . $this->searchTerm . '%');
        }

        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        if ($this->categoryFilter !== 'all') {
            $query->where('category', $this->categoryFilter);
        }

        if ($this->cityFilter !== 'all') {
            $query->where('city', $this->cityFilter);
        }

        $this->activeTournaments = $query->latest()->take(10)->get()->map(function ($tournament) {
            return [
                'id' => $tournament->id,
                'name' => $tournament->name,
                'city' => $tournament->city ?? 'No especificada',
                'start_date' => $tournament->start_date?->format('d M'),
                'end_date' => $tournament->end_date?->format('d M'),
                'status' => $tournament->status,
                'status_label' => $this->getStatusLabel($tournament->status),
                'status_color' => $this->getStatusColor($tournament->status),
                'teams_count' => $tournament->teams->count(),
                'matches_today' => $tournament->matches()->whereDate('scheduled_at', today())->count(),
                'active_teams' => $tournament->teams()->where('status', 'active')->count(),
                'current_phase' => $tournament->current_phase ?? 'Grupos',
                'prize_pool' => $tournament->prize_pool ?? 0,
                'registration_deadline' => $tournament->registration_deadline,
                'is_registration_open' => $tournament->status === 'registration_open'
            ];
        });
    }

    private function loadLiveMatches()
    {
        $this->liveMatches = VolleyMatch::with(['homeTeam', 'awayTeam', 'tournament'])
            ->where('status', MatchStatus::In_Progress)
            ->latest('updated_at')
            ->take(5)
            ->get()
            ->map(function ($match) {
                return [
                    'id' => $match->id,
                    'tournament_name' => $match->tournament->name ?? 'Torneo',
                    'home_team' => [
                        'name' => $match->homeTeam->name ?? 'Equipo Local',
                        'short_name' => $this->getTeamShortName($match->homeTeam->name ?? 'EL'),
                        'logo' => $match->homeTeam->logo ?? null
                    ],
                    'away_team' => [
                        'name' => $match->awayTeam->name ?? 'Equipo Visitante',
                        'short_name' => $this->getTeamShortName($match->awayTeam->name ?? 'EV'),
                        'logo' => $match->awayTeam->logo ?? null
                    ],
                    'home_sets' => $match->home_sets ?? 0,
                    'away_sets' => $match->away_sets ?? 0,
                    'current_set_home' => $match->current_set_home ?? 0,
                    'current_set_away' => $match->current_set_away ?? 0,
                    'current_set' => $match->current_set ?? 1,
                    'phase' => $match->phase ?? 'Grupos',
                    'started_at' => $match->started_at,
                    'duration' => $match->started_at ? $match->started_at->diffForHumans() : null
                ];
            });
    }

    private function loadFeaturedMatch()
    {
        $featuredMatch = VolleyMatch::with(['homeTeam', 'awayTeam', 'tournament'])
            ->where('status', MatchStatus::In_Progress)
            ->where('is_featured', true)
            ->first();

        if (!$featuredMatch) {
            $featuredMatch = VolleyMatch::with(['homeTeam', 'awayTeam', 'tournament'])
                ->where('status', MatchStatus::In_Progress)
                ->latest('updated_at')
                ->first();
        }

        if ($featuredMatch) {
            $this->featuredMatch = [
                'id' => $featuredMatch->id,
                'tournament_name' => $featuredMatch->tournament->name ?? 'Torneo',
                'home_team' => [
                    'name' => $featuredMatch->homeTeam->name ?? 'Equipo Local',
                    'short_name' => $this->getTeamShortName($featuredMatch->homeTeam->name ?? 'EL'),
                    'group' => $featuredMatch->homeTeam->group ?? 'Grupo A'
                ],
                'away_team' => [
                    'name' => $featuredMatch->awayTeam->name ?? 'Equipo Visitante',
                    'short_name' => $this->getTeamShortName($featuredMatch->awayTeam->name ?? 'EV'),
                    'group' => $featuredMatch->awayTeam->group ?? 'Grupo A'
                ],
                'home_sets' => $featuredMatch->home_sets ?? 0,
                'away_sets' => $featuredMatch->away_sets ?? 0,
                'current_set_home' => $featuredMatch->current_set_home ?? 0,
                'current_set_away' => $featuredMatch->current_set_away ?? 0,
                'current_set' => $featuredMatch->current_set ?? 1,
                'phase' => $featuredMatch->phase ?? '2do Tiempo'
            ];
        } else {
            // Fallback data for demo
            $this->featuredMatch = [
                'id' => 1,
                'tournament_name' => 'Copa Nacional Femenina',
                'home_team' => [
                    'name' => 'Voleibol Bogotá',
                    'short_name' => 'VB',
                    'group' => 'Grupo A'
                ],
                'away_team' => [
                    'name' => 'Atlético Medellín',
                    'short_name' => 'AM',
                    'group' => 'Grupo A'
                ],
                'home_sets' => 2,
                'away_sets' => 1,
                'current_set_home' => 25,
                'current_set_away' => 23,
                'current_set' => 3,
                'phase' => '2do Tiempo'
            ];
        }
    }

    private function loadRecentResults()
    {
        $this->recentResults = VolleyMatch::with(['homeTeam', 'awayTeam', 'tournament'])
            ->where('status', MatchStatus::Finished)
            ->latest('finished_at')
            ->take(10)
            ->get()
            ->map(function ($match) {
                return [
                    'id' => $match->id,
                    'home_team' => $match->homeTeam->name ?? 'Equipo Local',
                    'away_team' => $match->awayTeam->name ?? 'Equipo Visitante',
                    'home_sets' => $match->home_sets ?? 0,
                    'away_sets' => $match->away_sets ?? 0,
                    'finished_at' => $match->finished_at,
                    'tournament_name' => $match->tournament->name ?? 'Torneo'
                ];
            });
    }

    private function loadUpcomingMatches()
    {
        $this->upcomingMatches = VolleyMatch::with(['homeTeam', 'awayTeam', 'tournament'])
            ->where('status', MatchStatus::Scheduled)
            ->where('scheduled_at', '>=', now())
            ->orderBy('scheduled_at')
            ->take(10)
            ->get()
            ->map(function ($match) {
                return [
                    'id' => $match->id,
                    'home_team' => $match->homeTeam->name ?? 'Equipo Local',
                    'away_team' => $match->awayTeam->name ?? 'Equipo Visitante',
                    'scheduled_at' => $match->scheduled_at,
                    'tournament_name' => $match->tournament->name ?? 'Torneo',
                    'venue' => $match->venue ?? 'Por definir'
                ];
            });
    }

    private function loadFilteredTournaments()
    {
        $this->loadActiveTournaments();
    }

    private function loadLiveData()
    {
        $this->loadLiveMatches();
    }

    private function loadResultsData()
    {
        $this->loadRecentResults();
    }

    private function loadStatisticsData()
    {
        $this->loadTopTeams();
        $this->loadTopPlayers();
    }

    private function loadTournamentsData()
    {
        $this->loadActiveTournaments();
    }

    private function loadTopTeams()
    {
        // This would typically calculate based on wins, points, etc.
        $this->topTeams = collect([
            ['name' => 'Voleibol Bogotá', 'win_rate' => 95.2, 'position' => 1],
            ['name' => 'Atlético Medellín', 'win_rate' => 89.7, 'position' => 2],
            ['name' => 'Deportivo Cali', 'win_rate' => 84.3, 'position' => 3],
            ['name' => 'Once Caldas', 'win_rate' => 78.9, 'position' => 4],
            ['name' => 'Millonarios FC', 'win_rate' => 72.1, 'position' => 5]
        ]);
    }

    private function loadTopPlayers()
    {
        // This would typically get from player statistics
        $this->topPlayers = collect([
            ['name' => 'María González', 'team' => 'Voleibol Bogotá', 'points' => 24],
            ['name' => 'Ana Rodríguez', 'team' => 'Atlético Medellín', 'points' => 22],
            ['name' => 'Carmen López', 'team' => 'Deportivo Cali', 'points' => 20],
            ['name' => 'Sofia Martínez', 'team' => 'Once Caldas', 'points' => 18],
            ['name' => 'Laura Pérez', 'team' => 'Millonarios FC', 'points' => 16]
        ]);
    }

    private function getStatusLabel($status)
    {
        return match($status) {
            'registration_open' => 'Inscripciones Abiertas',
            'active' => 'En Curso',
            'finished' => 'Finalizado',
            'suspended' => 'Suspendido',
            default => 'Desconocido'
        };
    }

    private function getStatusColor($status)
    {
        return match($status) {
            'registration_open' => 'upcoming',
            'active' => 'live',
            'finished' => 'finished',
            'suspended' => 'finished',
            default => 'finished'
        };
    }

    private function getTeamShortName($teamName)
    {
        $words = explode(' ', $teamName);
        if (count($words) >= 2) {
            return strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
        }
        return strtoupper(substr($teamName, 0, 2));
    }

    public function render()
    {
        return view('livewire.public.tournaments-dashboard');
    }
}