<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class PlayerSeasonStatistic extends Model
{
    use SoftDeletes, LogsActivity;

    protected $fillable = [
        'player_id',
        'team_id',
        'league_id',
        'season',
        'total_attacks',
        'total_attack_kills',
        'total_attack_errors',
        'avg_attack_percentage',
        'total_serves',
        'total_service_aces',
        'total_service_errors',
        'avg_service_percentage',
        'total_receptions',
        'total_reception_errors',
        'avg_reception_percentage',
        'total_blocks',
        'total_block_kills',
        'total_block_errors',
        'total_digs',
        'total_dig_errors',
        'total_points_scored',
        'total_sets_played',
        'total_minutes_played',
        'matches_played',
        'avg_points_per_match',
        'avg_sets_per_match',
        'attack_ranking',
        'service_ranking',
        'reception_ranking',
        'block_ranking',
        'overall_ranking',
        'mvp_awards',
        'best_player_awards',
        'additional_stats',
        'notes',
    ];

    protected $casts = [
        'additional_stats' => 'array',
        'avg_attack_percentage' => 'decimal:2',
        'avg_service_percentage' => 'decimal:2',
        'avg_reception_percentage' => 'decimal:2',
        'avg_points_per_match' => 'decimal:2',
        'avg_sets_per_match' => 'decimal:2',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['player_id', 'season', 'total_points_scored', 'overall_ranking'])
            ->logOnlyDirty();
    }

    // =======================
    // RELATIONSHIPS
    // =======================

    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function league(): BelongsTo
    {
        return $this->belongsTo(League::class);
    }

    // =======================
    // ACCESSORS & MUTATORS
    // =======================

    public function getOverallEfficiencyAttribute(): float
    {
        $totalActions = $this->total_attacks + $this->total_serves + 
                       $this->total_receptions + $this->total_blocks + $this->total_digs;
        if ($totalActions === 0) return 0;
        
        $successfulActions = $this->total_attack_kills + $this->total_service_aces + 
                           ($this->total_receptions - $this->total_reception_errors) + 
                           $this->total_block_kills + $this->total_digs;
        
        return round(($successfulActions / $totalActions) * 100, 2);
    }

    public function getTotalErrorsAttribute(): int
    {
        return $this->total_attack_errors + $this->total_service_errors + 
               $this->total_reception_errors + $this->total_block_errors + $this->total_dig_errors;
    }

    public function getPerformanceScoreAttribute(): float
    {
        // Cálculo de puntuación de rendimiento basado en múltiples métricas
        $attackScore = $this->avg_attack_percentage * 0.3;
        $serviceScore = $this->avg_service_percentage * 0.25;
        $receptionScore = $this->avg_reception_percentage * 0.2;
        $pointsScore = ($this->avg_points_per_match / 20) * 100 * 0.15; // Normalizado a 20 puntos por partido
        $consistencyScore = ($this->matches_played > 10 ? 10 : $this->matches_played) * 0.1;
        
        return round($attackScore + $serviceScore + $receptionScore + $pointsScore + $consistencyScore, 2);
    }

    // =======================
    // SCOPES
    // =======================

    public function scopeForPlayer($query, $playerId)
    {
        return $query->where('player_id', $playerId);
    }

    public function scopeForSeason($query, $season = null)
    {
        $season = $season ?? now()->year;
        return $query->where('season', $season);
    }

    public function scopeForLeague($query, $leagueId)
    {
        return $query->where('league_id', $leagueId);
    }

    public function scopeByRanking($query, $metric = 'overall_ranking')
    {
        return $query->orderBy($metric, 'asc');
    }

    public function scopeTopPerformers($query, $limit = 10)
    {
        return $query->orderBy('total_points_scored', 'desc')
                    ->limit($limit);
    }

    // =======================
    // METHODS
    // =======================

    public function calculateAverages(): void
    {
        if ($this->matches_played > 0) {
            $this->avg_points_per_match = round($this->total_points_scored / $this->matches_played, 2);
            $this->avg_sets_per_match = round($this->total_sets_played / $this->matches_played, 2);
        }
        
        if ($this->total_attacks > 0) {
            $this->avg_attack_percentage = round((($this->total_attack_kills - $this->total_attack_errors) / $this->total_attacks) * 100, 2);
        }
        
        if ($this->total_serves > 0) {
            $this->avg_service_percentage = round((($this->total_serves - $this->total_service_errors) / $this->total_serves) * 100, 2);
        }
        
        if ($this->total_receptions > 0) {
            $this->avg_reception_percentage = round((($this->total_receptions - $this->total_reception_errors) / $this->total_receptions) * 100, 2);
        }
    }

    public function updateFromMatchStatistics(): void
    {
        $matchStats = PlayerStatistic::forPlayer($this->player_id)
            ->forSeason($this->season)
            ->get();

        $this->total_attacks = $matchStats->sum('attacks');
        $this->total_attack_kills = $matchStats->sum('attack_kills');
        $this->total_attack_errors = $matchStats->sum('attack_errors');
        $this->total_serves = $matchStats->sum('serves');
        $this->total_service_aces = $matchStats->sum('service_aces');
        $this->total_service_errors = $matchStats->sum('service_errors');
        $this->total_receptions = $matchStats->sum('receptions');
        $this->total_reception_errors = $matchStats->sum('reception_errors');
        $this->total_blocks = $matchStats->sum('blocks');
        $this->total_block_kills = $matchStats->sum('block_kills');
        $this->total_block_errors = $matchStats->sum('block_errors');
        $this->total_digs = $matchStats->sum('digs');
        $this->total_dig_errors = $matchStats->sum('dig_errors');
        $this->total_points_scored = $matchStats->sum('points_scored');
        $this->total_sets_played = $matchStats->sum('sets_played');
        $this->total_minutes_played = $matchStats->sum('minutes_played');
        $this->matches_played = $matchStats->count();
        
        $this->calculateAverages();
    }

    public static function getLeagueRankings($leagueId, $season = null, $metric = 'total_points_scored')
    {
        $season = $season ?? now()->year;
        
        return static::with('player.user')
            ->forLeague($leagueId)
            ->forSeason($season)
            ->orderBy($metric, 'desc')
            ->get()
            ->map(function ($stat, $index) {
                $stat->position = $index + 1;
                return $stat;
            });
    }

    public static function getSeasonMVP($season = null, $leagueId = null)
    {
        $query = static::with('player.user')
            ->forSeason($season ?? now()->year);
            
        if ($leagueId) {
            $query->forLeague($leagueId);
        }
        
        return $query->orderBy('total_points_scored', 'desc')
                    ->orderBy('avg_attack_percentage', 'desc')
                    ->first();
    }
}
