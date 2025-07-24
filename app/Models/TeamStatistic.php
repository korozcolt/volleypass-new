<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class TeamStatistic extends Model
{
    use SoftDeletes, LogsActivity;

    protected $fillable = [
        'team_id',
        'match_id',
        'tournament_id',
        'season',
        'sets_won',
        'sets_lost',
        'points_scored',
        'points_conceded',
        'attacks',
        'attack_kills',
        'attack_errors',
        'attack_percentage',
        'serves',
        'service_aces',
        'service_errors',
        'service_percentage',
        'receptions',
        'reception_errors',
        'reception_percentage',
        'blocks',
        'block_kills',
        'block_errors',
        'digs',
        'dig_errors',
        'matches_played',
        'matches_won',
        'matches_lost',
        'win_percentage',
        'performance_rating',
        'additional_stats',
        'notes',
    ];

    protected $casts = [
        'additional_stats' => 'array',
        'attack_percentage' => 'decimal:2',
        'service_percentage' => 'decimal:2',
        'reception_percentage' => 'decimal:2',
        'win_percentage' => 'decimal:2',
        'performance_rating' => 'decimal:2',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['team_id', 'match_id', 'points_scored', 'win_percentage'])
            ->logOnlyDirty();
    }

    // =======================
    // RELATIONSHIPS
    // =======================

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function match(): BelongsTo
    {
        return $this->belongsTo(\App\Models\VolleyMatch::class, 'match_id');
    }

    public function tournament(): BelongsTo
    {
        return $this->belongsTo(Tournament::class);
    }

    // =======================
    // ACCESSORS & MUTATORS
    // =======================

    public function getPointsDifferenceAttribute(): int
    {
        return $this->points_scored - $this->points_conceded;
    }

    public function getSetsDifferenceAttribute(): int
    {
        return $this->sets_won - $this->sets_lost;
    }

    public function getEfficiencyRatingAttribute(): float
    {
        $totalActions = $this->attacks + $this->serves + $this->receptions + $this->blocks + $this->digs;
        if ($totalActions === 0) return 0;
        
        $successfulActions = $this->attack_kills + $this->service_aces + 
                           ($this->receptions - $this->reception_errors) + 
                           $this->block_kills + $this->digs;
        
        return round(($successfulActions / $totalActions) * 100, 2);
    }

    public function getTotalErrorsAttribute(): int
    {
        return $this->attack_errors + $this->service_errors + 
               $this->reception_errors + $this->block_errors + $this->dig_errors;
    }

    // =======================
    // SCOPES
    // =======================

    public function scopeForTeam($query, $teamId)
    {
        return $query->where('team_id', $teamId);
    }

    public function scopeForTournament($query, $tournamentId)
    {
        return $query->where('tournament_id', $tournamentId);
    }

    public function scopeForSeason($query, $season = null)
    {
        $season = $season ?? now()->year;
        return $query->where('season', $season);
    }

    public function scopeByPerformance($query, $order = 'desc')
    {
        return $query->orderBy('performance_rating', $order);
    }

    // =======================
    // METHODS
    // =======================

    public function calculatePercentages(): void
    {
        if ($this->attacks > 0) {
            $this->attack_percentage = round((($this->attack_kills - $this->attack_errors) / $this->attacks) * 100, 2);
        }
        
        if ($this->serves > 0) {
            $this->service_percentage = round((($this->serves - $this->service_errors) / $this->serves) * 100, 2);
        }
        
        if ($this->receptions > 0) {
            $this->reception_percentage = round((($this->receptions - $this->reception_errors) / $this->receptions) * 100, 2);
        }
        
        if ($this->matches_played > 0) {
            $this->win_percentage = round(($this->matches_won / $this->matches_played) * 100, 2);
        }
    }

    public function calculatePerformanceRating(): void
    {
        // Fórmula de rating basada en múltiples factores
        $winFactor = $this->win_percentage * 0.3;
        $attackFactor = $this->attack_percentage * 0.25;
        $serviceFactor = $this->service_percentage * 0.2;
        $receptionFactor = $this->reception_percentage * 0.15;
        $pointsFactor = ($this->points_difference > 0 ? min($this->points_difference / 100, 10) : 0) * 0.1;
        
        $this->performance_rating = round($winFactor + $attackFactor + $serviceFactor + $receptionFactor + $pointsFactor, 2);
    }

    public static function getTopTeams($metric = 'performance_rating', $limit = 10, $season = null)
    {
        $query = static::with('team')
            ->orderBy($metric, 'desc')
            ->limit($limit);

        if ($season) {
            $query->forSeason($season);
        }

        return $query->get();
    }
}