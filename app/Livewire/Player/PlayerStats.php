<?php

namespace App\Livewire\Player;

use App\Models\User;
use App\Models\Player;
use App\Models\Tournament;
use App\Models\Match;
use App\Models\PlayerStatistic;
use App\Models\PlayerSeasonStatistic;
use App\Models\PlayerAward;
use App\Models\Team;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

#[Layout('layouts.player-dashboard')]
class PlayerStats extends Component
{
    public $player;
    public $selectedSeason;
    public $selectedPosition;
    public $selectedTournament;
    public $availableSeasons;
    public $availablePositions;
    public $availableTournaments;
    
    // Estadísticas generales
    public $totalTournaments;
    public $totalMatches;
    public $matchesWon;
    public $matchesLost;
    public $winPercentage;
    
    // Estadísticas técnicas
    public $technicalStats;
    public $seasonStats;
    public $positionStats;
    public $progressData;
    public $comparisonData;
    
    // Logros y certificaciones
    public $achievements;
    public $certifications;
    
    // Filtros
    public $dateFrom;
    public $dateTo;
    public $showAdvancedStats = false;
    
    public function mount()
    {
        $this->player = Auth::user()->player;
        
        // Verificar si el usuario tiene un perfil de jugador
        if (!$this->player) {
            session()->flash('error', 'No tienes un perfil de jugador asociado a tu cuenta.');
            $this->redirect(route('dashboard'));
            return;
        }
        
        $this->initializeFilters();
        $this->loadStatistics();
    }
    
    private function initializeFilters()
    {
        // Temporadas disponibles
        $this->availableSeasons = Tournament::whereHas('teams.players', function($query) {
                $query->where('player_id', $this->player->id);
            })
            ->selectRaw('YEAR(start_date) as season')
            ->distinct()
            ->orderBy('season', 'desc')
            ->pluck('season')
            ->toArray();
            
        $this->selectedSeason = $this->availableSeasons[0] ?? Carbon::now()->year;
        
        // Posiciones disponibles
        $this->availablePositions = [
            'libero' => 'Líbero',
            'setter' => 'Armadora',
            'outside_hitter' => 'Atacante Exterior',
            'middle_blocker' => 'Central',
            'opposite' => 'Opuesta',
            'defensive_specialist' => 'Especialista Defensiva'
        ];
        
        // Torneos disponibles
        $this->availableTournaments = Tournament::whereHas('teams.players', function($query) {
                $query->where('player_id', $this->player->id);
            })
            ->where('status', 'active')
            ->orderBy('start_date', 'desc')
            ->get(['id', 'name', 'start_date']);
            
        // Fechas por defecto (último año)
        $this->dateTo = Carbon::now()->format('Y-m-d');
        $this->dateFrom = Carbon::now()->subYear()->format('Y-m-d');
    }
    
    private function loadStatistics()
    {
        $this->loadGeneralStats();
        $this->loadTechnicalStats();
        $this->loadSeasonStats();
        $this->loadPositionStats();
        $this->loadProgressData();
        $this->loadComparisonData();
        $this->loadAchievements();
    }
    
    private function loadGeneralStats()
    {
        $query = $this->getBaseQuery();
        
        // Torneos participados
        $this->totalTournaments = Tournament::whereHas('teams.players', function($q) {
                $q->where('player_id', $this->player->id);
            })
            ->when($this->selectedSeason, function($q) {
                $q->whereYear('start_date', $this->selectedSeason);
            })
            ->count();
            
        // Partidos jugados
        $matchStats = $query->selectRaw('
                COUNT(*) as total_matches,
                SUM(CASE WHEN result = "won" THEN 1 ELSE 0 END) as matches_won,
                SUM(CASE WHEN result = "lost" THEN 1 ELSE 0 END) as matches_lost
            ')
            ->first();
            
        $this->totalMatches = $matchStats->total_matches ?? 0;
        $this->matchesWon = $matchStats->matches_won ?? 0;
        $this->matchesLost = $matchStats->matches_lost ?? 0;
        $this->winPercentage = $this->totalMatches > 0 ? 
            round(($this->matchesWon / $this->totalMatches) * 100, 1) : 0;
    }
    
    private function loadTechnicalStats()
    {
        $query = $this->getBaseQuery();
        
        $this->technicalStats = $query->selectRaw('
                AVG(attacks_attempted) as avg_attacks,
                AVG(attacks_successful) as avg_successful_attacks,
                AVG(blocks_attempted) as avg_blocks,
                AVG(blocks_successful) as avg_successful_blocks,
                AVG(serves_attempted) as avg_serves,
                AVG(serves_successful) as avg_successful_serves,
                AVG(receptions_attempted) as avg_receptions,
                AVG(receptions_successful) as avg_successful_receptions,
                AVG(digs) as avg_digs,
                AVG(assists) as avg_assists,
                AVG(errors) as avg_errors,
                SUM(points_scored) as total_points
            ')
            ->first();
            
        // Calcular porcentajes de efectividad
        if ($this->technicalStats) {
            $this->technicalStats->attack_percentage = $this->technicalStats->avg_attacks > 0 ?
                round(($this->technicalStats->avg_successful_attacks / $this->technicalStats->avg_attacks) * 100, 1) : 0;
                
            $this->technicalStats->block_percentage = $this->technicalStats->avg_blocks > 0 ?
                round(($this->technicalStats->avg_successful_blocks / $this->technicalStats->avg_blocks) * 100, 1) : 0;
                
            $this->technicalStats->serve_percentage = $this->technicalStats->avg_serves > 0 ?
                round(($this->technicalStats->avg_successful_serves / $this->technicalStats->avg_serves) * 100, 1) : 0;
                
            $this->technicalStats->reception_percentage = $this->technicalStats->avg_receptions > 0 ?
                round(($this->technicalStats->avg_successful_receptions / $this->technicalStats->avg_receptions) * 100, 1) : 0;
        }
    }
    
    private function loadSeasonStats()
    {
        $this->seasonStats = PlayerStatistic::where('player_id', $this->player->id)
            ->whereHas('match.tournament', function($query) {
                $query->when($this->selectedSeason, function($q) {
                    $q->whereYear('start_date', $this->selectedSeason);
                });
            })
            ->selectRaw('
                DATE_FORMAT(created_at, "%Y-%m") as month,
                AVG(attacks_successful) as avg_attacks,
                AVG(blocks_successful) as avg_blocks,
                AVG(serves_successful) as avg_serves,
                AVG(receptions_successful) as avg_receptions,
                COUNT(*) as matches_played
            ')
            ->groupBy('month')
            ->orderBy('month')
            ->get();
    }
    
    private function loadPositionStats()
    {
        $this->positionStats = PlayerStatistic::where('player_id', $this->player->id)
            ->when($this->selectedPosition, function($query) {
                $query->where('position_played', $this->selectedPosition);
            })
            ->selectRaw('
                position_played,
                COUNT(*) as matches_played,
                AVG(attacks_successful) as avg_attacks,
                AVG(blocks_successful) as avg_blocks,
                AVG(serves_successful) as avg_serves,
                AVG(receptions_successful) as avg_receptions,
                AVG(points_scored) as avg_points
            ')
            ->groupBy('position_played')
            ->get();
    }
    
    private function loadProgressData()
    {
        // Datos para gráfico de progreso temporal
        $this->progressData = PlayerStatistic::where('player_id', $this->player->id)
            ->whereBetween('created_at', [$this->dateFrom, $this->dateTo])
            ->selectRaw('
                DATE(created_at) as date,
                AVG(attacks_successful + blocks_successful + serves_successful) as performance_score,
                COUNT(*) as matches
            ')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }
    
    private function loadComparisonData()
    {
        // Comparación con promedio de la liga
        $leagueAverage = PlayerStatistic::whereHas('match.tournament', function($query) {
                $query->when($this->selectedSeason, function($q) {
                    $q->whereYear('start_date', $this->selectedSeason);
                });
            })
            ->selectRaw('
                AVG(attacks_successful) as league_avg_attacks,
                AVG(blocks_successful) as league_avg_blocks,
                AVG(serves_successful) as league_avg_serves,
                AVG(receptions_successful) as league_avg_receptions
            ')
            ->first();
            
        $playerAverage = $this->getBaseQuery()
            ->selectRaw('
                AVG(attacks_successful) as player_avg_attacks,
                AVG(blocks_successful) as player_avg_blocks,
                AVG(serves_successful) as player_avg_serves,
                AVG(receptions_successful) as player_avg_receptions
            ')
            ->first();
            
        $this->comparisonData = [
            'league' => $leagueAverage,
            'player' => $playerAverage
        ];
    }
    
    private function loadAchievements()
    {
        $this->achievements = PlayerAward::where('player_id', $this->player->id)
            ->with(['tournament', 'achievement_type'])
            ->orderBy('achieved_at', 'desc')
            ->get();
            
        // Certificaciones (simuladas - podrían venir de otra tabla)
        $this->certifications = [
            [
                'name' => 'Certificación Nivel Básico',
                'issued_by' => 'Federación Nacional de Voleibol',
                'date' => '2023-06-15',
                'status' => 'active'
            ],
            [
                'name' => 'Curso de Arbitraje',
                'issued_by' => 'Asociación de Árbitros',
                'date' => '2023-03-20',
                'status' => 'active'
            ]
        ];
    }
    
    private function getBaseQuery()
    {
        return PlayerStatistic::where('player_id', $this->player->id)
            ->when($this->selectedSeason, function($query) {
                $query->whereHas('match.tournament', function($q) {
                    $q->whereYear('start_date', $this->selectedSeason);
                });
            })
            ->when($this->selectedTournament, function($query) {
                $query->whereHas('match', function($q) {
                    $q->where('tournament_id', $this->selectedTournament);
                });
            })
            ->when($this->selectedPosition, function($query) {
                $query->where('position_played', $this->selectedPosition);
            })
            ->whereBetween('created_at', [$this->dateFrom, $this->dateTo]);
    }
    
    public function updatedSelectedSeason()
    {
        $this->loadStatistics();
    }
    
    public function updatedSelectedPosition()
    {
        $this->loadStatistics();
    }
    
    public function updatedSelectedTournament()
    {
        $this->loadStatistics();
    }
    
    public function updateDateRange()
    {
        $this->validate([
            'dateFrom' => 'required|date',
            'dateTo' => 'required|date|after_or_equal:dateFrom'
        ]);
        
        $this->loadStatistics();
    }
    
    public function toggleAdvancedStats()
    {
        $this->showAdvancedStats = !$this->showAdvancedStats;
    }
    
    public function exportStats()
    {
        // Exportar estadísticas a Excel/PDF
        $data = [
            'player' => $this->player,
            'general_stats' => [
                'tournaments' => $this->totalTournaments,
                'matches' => $this->totalMatches,
                'wins' => $this->matchesWon,
                'losses' => $this->matchesLost,
                'win_percentage' => $this->winPercentage
            ],
            'technical_stats' => $this->technicalStats,
            'season_stats' => $this->seasonStats,
            'achievements' => $this->achievements,
            'period' => [
                'from' => $this->dateFrom,
                'to' => $this->dateTo,
                'season' => $this->selectedSeason
            ]
        ];
        
        $this->dispatch('export-stats', $data);
    }
    
    public function render()
    {
        return view('livewire.player.player-stats');
    }
}
