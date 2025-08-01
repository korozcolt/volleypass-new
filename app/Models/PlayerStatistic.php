<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class PlayerStatistic extends Model
{
    use SoftDeletes, LogsActivity;

    protected $fillable = [
        'player_id',
        'match_id',
        'tournament_id',
        'team_id',
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
        'points_scored',
        'sets_played',
        'minutes_played',
        'match_date',
        'additional_stats',
        'notes',
    ];

    protected $casts = [
        'match_date' => 'date',
        'additional_stats' => 'array',
        'attack_percentage' => 'decimal:2',
        'service_percentage' => 'decimal:2',
        'reception_percentage' => 'decimal:2',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['player_id', 'match_id', 'points_scored', 'attack_percentage'])
            ->logOnlyDirty();
    }

    // =======================
    // RELATIONSHIPS
    // =======================

    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }

    public function match(): BelongsTo
    {
        return $this->belongsTo(\App\Models\VolleyMatch::class);
    }

    public function tournament(): BelongsTo
    {
        return $this->belongsTo(Tournament::class);
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    // =======================
    // ACCESSORS & MUTATORS
    // =======================

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

    public function scopeForPlayer($query, $playerId)
    {
        return $query->where('player_id', $playerId);
    }

    public function scopeForTournament($query, $tournamentId)
    {
        return $query->where('tournament_id', $tournamentId);
    }

    public function scopeForSeason($query, $season = null)
    {
        $season = $season ?? now()->year;
        return $query->whereYear('match_date', $season);
    }

    public function scopeByPosition($query, $position)
    {
        return $query->whereHas('player', function ($q) use ($position) {
            $q->where('position', $position);
        });
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
    }

    public static function getTopPerformers($metric = 'points_scored', $limit = 10, $season = null)
    {
        $query = static::with('player.user')
            ->selectRaw('player_id, SUM(' . $metric . ') as total_' . $metric)
            ->groupBy('player_id')
            ->orderBy('total_' . $metric, 'desc')
            ->limit($limit);

        if ($season) {
            $query->forSeason($season);
        }

        return $query->get();
    }
}
