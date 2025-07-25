<?php

namespace App\Livewire\Public;

use Livewire\Component;
use App\Models\Tournament;
use App\Models\VolleyMatch;
use App\Models\Team;
use App\Enums\MatchStatus;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class Tournaments extends Component
{
    public $selectedTournament;
    public $tournaments;
    public $liveMatches;
    public $standings;
    public $upcomingMatches;

    public function mount()
    {
        // Inicializar propiedades como arrays/collections vacíos
        $this->tournaments = collect([]);
        $this->liveMatches = [];
        $this->standings = [];
        $this->upcomingMatches = [];
        
        $this->loadTournamentsData();
        // Seleccionar el primer torneo activo por defecto
        $this->selectedTournament = $this->tournaments->first()?->id ?? 1;
    }

    public function loadTournamentsData()
    {
        // Cargar torneos activos desde la base de datos con cache
        $this->tournaments = Cache::remember('public_tournaments', 300, function () {
            return Tournament::where('status', 'active')
                ->withCount(['teams', 'matches'])
                ->with(['category'])
                ->get()
                ->map(function ($tournament) {
                    $matchesPlayed = $tournament->matches()->where('status', 'completed')->count();
                    $matchesRemaining = $tournament->matches()->whereIn('status', ['scheduled', 'in_progress'])->count();
                    
                    return [
                        'id' => $tournament->id,
                        'name' => $tournament->name,
                        'status' => $tournament->status,
                        'category' => $tournament->category->name ?? 'General',
                        'teams_count' => $tournament->teams_count,
                        'matches_played' => $matchesPlayed,
                        'matches_remaining' => $matchesRemaining
                    ];
                });
        });

        // Si no hay torneos en la BD, usar datos de ejemplo
        if ($this->tournaments->isEmpty()) {
            $this->tournaments = collect([
                [
                    'id' => 1,
                    'name' => 'Liga Profesional Sucre 2024',
                    'status' => 'active',
                    'category' => 'Mayores',
                    'teams_count' => 8,
                    'matches_played' => 24,
                    'matches_remaining' => 12
                ],
                [
                    'id' => 2,
                    'name' => 'Copa Departamental',
                    'status' => 'active',
                    'category' => 'Juvenil',
                    'teams_count' => 12,
                    'matches_played' => 18,
                    'matches_remaining' => 8
                ]
            ]);
        }

        if ($this->selectedTournament) {
            $this->loadTournamentData($this->selectedTournament);
        }
    }

    public function selectTournament($tournamentId)
    {
        $this->selectedTournament = $tournamentId;
        $this->loadTournamentData($tournamentId);
    }

    public function refreshData()
    {
        // Simular actualización de datos en tiempo real
        $this->loadTournamentData($this->selectedTournament);

        // Dispatch event for UI feedback
        $this->dispatch('data-refreshed');
    }

    public function loadTournamentData($tournamentId)
    {
        // Inicializar arrays vacíos para evitar errores de count()
        $this->liveMatches = [];
        $this->standings = [];
        $this->upcomingMatches = [];
        
        try {
            // Cargar partidos en vivo desde la base de datos
            $liveMatchesData = Cache::remember("live_matches_{$tournamentId}", 60, function () use ($tournamentId) {
                return VolleyMatch::where('tournament_id', $tournamentId)
                     ->where('status', MatchStatus::In_Progress)
                     ->with(['homeTeam', 'awayTeam'])
                     ->get()
                    ->map(function ($match) {
                        return [
                            'id' => $match->id,
                            'team_a' => $match->homeTeam->name ?? 'Equipo A',
                            'team_b' => $match->awayTeam->name ?? 'Equipo B',
                            'score_a' => $match->home_points ?? 0,
                            'score_b' => $match->away_points ?? 0,
                            'sets_a' => $match->home_sets ?? 0,
                            'sets_b' => $match->away_sets ?? 0,
                            'current_set' => [
                                'number' => 1,
                                'score_a' => $match->home_points ?? 0,
                                'score_b' => $match->away_points ?? 0,
                            ],
                            'time_elapsed' => $match->started_at ? $match->started_at->diffForHumans() : '00:00:00',
                            'venue' => $match->venue ?? 'Cancha Principal',
                        ];
                    })->toArray();
            });
            
            if (!empty($liveMatchesData)) {
                $this->liveMatches = $liveMatchesData;
            }

            // Cargar tabla de posiciones desde la base de datos
            $standingsData = Cache::remember("standings_{$tournamentId}", 300, function () use ($tournamentId) {
                $tournament = Tournament::find($tournamentId);
                if (!$tournament) return [];
                
                return $tournament->teams()
                    ->withCount([
                        'matches as matches_played' => function ($query) use ($tournamentId) {
                            $query->where('tournament_id', $tournamentId)->where('status', 'completed');
                        },
                        'matchesAsTeamA as wins_a' => function ($query) use ($tournamentId) {
                            $query->where('tournament_id', $tournamentId)
                                  ->where('status', 'completed')
                                  ->whereColumn('team_a_sets', '>', 'team_b_sets');
                        },
                        'matchesAsTeamB as wins_b' => function ($query) use ($tournamentId) {
                            $query->where('tournament_id', $tournamentId)
                                  ->where('status', 'completed')
                                  ->whereColumn('team_b_sets', '>', 'team_a_sets');
                        }
                    ])
                    ->get()
                    ->map(function ($team, $index) {
                        $wins = $team->wins_a + $team->wins_b;
                        $losses = $team->matches_played - $wins;
                        $points = $wins * 3; // 3 puntos por victoria
                        
                        return [
                            'position' => $index + 1,
                            'team' => $team->name,
                            'matches' => $team->matches_played,
                            'wins' => $wins,
                            'losses' => $losses,
                            'points' => $points,
                            'sets_diff' => '+0' // Calcular diferencia de sets si es necesario
                        ];
                    })
                    ->sortByDesc('points')
                    ->values()
                    ->map(function ($team, $index) {
                        $team['position'] = $index + 1;
                        return $team;
                    })
                    ->toArray();
            });
            
            if (!empty($standingsData)) {
                $this->standings = $standingsData;
            }

            // Cargar próximos partidos desde la base de datos
            $upcomingMatchesData = Cache::remember("upcoming_matches_{$tournamentId}", 300, function () use ($tournamentId) {
                return VolleyMatch::where('tournament_id', $tournamentId)
                     ->where('status', MatchStatus::Scheduled)
                     ->where('scheduled_at', '>', now())
                     ->with(['homeTeam', 'awayTeam'])
                     ->orderBy('scheduled_at')
                     ->limit(6)
                     ->get()
                    ->map(function ($match) {
                        return [
                            'id' => $match->id,
                            'team_a' => $match->homeTeam->name ?? 'Equipo A',
                            'team_b' => $match->awayTeam->name ?? 'Equipo B',
                            'date' => $match->scheduled_at->format('d/m/Y'),
                            'time' => $match->scheduled_at->format('H:i'),
                            'venue' => $match->venue ?? 'Cancha Principal',
                            'address' => $match->venue_address ?? 'Dirección no disponible',
                        ];
                    })->toArray();
            });
            
            if (!empty($upcomingMatchesData)) {
                $this->upcomingMatches = $upcomingMatchesData;
            }
            
        } catch (\Exception $e) {
            // Fallback a datos de ejemplo si hay error
            $this->loadFallbackData($tournamentId);
        }
        
        // Si no hay datos reales, usar datos de ejemplo
        if (empty($this->liveMatches) && empty($this->standings) && empty($this->upcomingMatches)) {
            $this->loadFallbackData($tournamentId);
        }
    }
    
    private function loadFallbackData($tournamentId)
    {
        // Datos de ejemplo como fallback - Simulando un partido en vivo
        $this->liveMatches = [
            [
                'id' => 1,
                'team_a' => 'Halcones FC',
                'team_b' => 'Águilas Doradas',
                'score_a' => 2,
                'score_b' => 1,
                'sets_a' => 2,
                'sets_b' => 1,
                'current_set' => [
                    'number' => 4,
                    'score_a' => 18,
                    'score_b' => 15,
                ],
                'time_elapsed' => '1:45:30',
                'venue' => 'Coliseo Municipal Sucre'
            ],
            [
                'id' => 2,
                'team_a' => 'Tigres del Norte',
                'team_b' => 'Cóndores Azules',
                'score_a' => 1,
                'score_b' => 1,
                'sets_a' => 1,
                'sets_b' => 1,
                'current_set' => [
                    'number' => 3,
                    'score_a' => 12,
                    'score_b' => 8,
                ],
                'time_elapsed' => '58:22',
                'venue' => 'Polideportivo Central'
            ]
        ];

        $this->standings = [
            ['position' => 1, 'team' => 'Halcones FC', 'matches' => 8, 'wins' => 7, 'losses' => 1, 'points' => 21, 'sets_diff' => '+12'],
            ['position' => 2, 'team' => 'Águilas Doradas', 'matches' => 8, 'wins' => 6, 'losses' => 2, 'points' => 18, 'sets_diff' => '+8'],
            ['position' => 3, 'team' => 'Tigres del Norte', 'matches' => 7, 'wins' => 5, 'losses' => 2, 'points' => 15, 'sets_diff' => '+4']
        ];

        $this->upcomingMatches = [
            [
                'id' => 3,
                'team_a' => 'Pumas Dorados',
                'team_b' => 'Jaguares FC',
                'date' => now()->addDays(1)->format('d/m/Y'),
                'time' => '19:00',
                'venue' => 'Coliseo Municipal',
                'address' => 'Av. Principal #123, Sucre'
            ],
            [
                'id' => 4,
                'team_a' => 'Leones Rojos',
                'team_b' => 'Halcones FC',
                'date' => now()->addDays(2)->format('d/m/Y'),
                'time' => '20:30',
                'venue' => 'Polideportivo Central',
                'address' => 'Calle Deportiva #456, Sucre'
            ],
            [
                'id' => 5,
                'team_a' => 'Águilas Doradas',
                'team_b' => 'Tigres del Norte',
                'date' => now()->addDays(3)->format('d/m/Y'),
                'time' => '18:00',
                'venue' => 'Gimnasio Universitario',
                'address' => 'Campus Universitario, Sucre'
            ],
            [
                'id' => 6,
                'team_a' => 'Cóndores Azules',
                'team_b' => 'Pumas Dorados',
                'date' => now()->addDays(4)->format('d/m/Y'),
                'time' => '19:30',
                'venue' => 'Coliseo Municipal',
                'address' => 'Av. Principal #123, Sucre'
            ]
        ];
    }

    public function render()
    {
        return view('livewire.public.tournaments');
    }
}
