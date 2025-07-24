<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Carbon\Carbon;

class PlayerAward extends Model
{
    use SoftDeletes, LogsActivity;

    protected $fillable = [
        'player_id',
        'award_id',
        'tournament_id',
        'match_id',
        'team_id',
        'awarded_date',
        'season',
        'category',
        'position',
        'competition_level',
        'description',
        'statistics_snapshot',
        'awarded_by',
        'certificate_number',
        'certificate_url',
        'verification_status',
        'points_earned',
        'notes',
    ];

    protected $casts = [
        'awarded_date' => 'date',
        'statistics_snapshot' => 'array',
        'verification_status' => 'boolean',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['player_id', 'award_id', 'awarded_date', 'points_earned'])
            ->logOnlyDirty();
    }

    // =======================
    // RELATIONSHIPS
    // =======================

    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }

    public function award(): BelongsTo
    {
        return $this->belongsTo(Award::class);
    }

    public function tournament(): BelongsTo
    {
        return $this->belongsTo(Tournament::class);
    }

    public function match(): BelongsTo
    {
        return $this->belongsTo(\App\Models\VolleyMatch::class, 'match_id');
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    // =======================
    // ACCESSORS & MUTATORS
    // =======================

    public function getIsRecentAttribute(): bool
    {
        return $this->awarded_date && $this->awarded_date->isAfter(now()->subDays(30));
    }

    public function getFormattedDateAttribute(): string
    {
        return $this->awarded_date ? $this->awarded_date->format('d/m/Y') : '';
    }

    public function getAwardTypeAttribute(): string
    {
        return $this->award?->type ?? 'individual';
    }

    public function getAwardNameAttribute(): string
    {
        return $this->award?->name ?? 'Premio';
    }

    public function getCompetitionLevelLabelAttribute(): string
    {
        return match($this->competition_level) {
            'local' => 'Local',
            'regional' => 'Regional',
            'national' => 'Nacional',
            'international' => 'Internacional',
            default => 'No especificado'
        };
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

    public function scopeForTournament($query, $tournamentId)
    {
        return $query->where('tournament_id', $tournamentId);
    }

    public function scopeByAwardType($query, $type)
    {
        return $query->whereHas('award', function ($q) use ($type) {
            $q->where('type', $type);
        });
    }

    public function scopeByCompetitionLevel($query, $level)
    {
        return $query->where('competition_level', $level);
    }

    public function scopeVerified($query)
    {
        return $query->where('verification_status', true);
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('awarded_date', '>=', now()->subDays($days));
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    // =======================
    // METHODS
    // =======================

    public function verify(): void
    {
        $this->update([
            'verification_status' => true,
            'certificate_number' => $this->certificate_number ?? $this->generateCertificateNumber(),
        ]);
    }

    public function generateCertificateNumber(): string
    {
        $prefix = strtoupper(substr($this->award_name, 0, 3));
        $year = $this->awarded_date ? $this->awarded_date->year : now()->year;
        $sequence = str_pad($this->id, 4, '0', STR_PAD_LEFT);
        
        return "{$prefix}-{$year}-{$sequence}";
    }

    public function generateCertificateUrl(): string
    {
        return route('certificates.show', [
            'player' => $this->player_id,
            'award' => $this->id
        ]);
    }

    public function updateStatisticsSnapshot(): void
    {
        if ($this->match_id) {
            $matchStats = PlayerStatistic::where('player_id', $this->player_id)
                ->where('match_id', $this->match_id)
                ->first();
                
            if ($matchStats) {
                $this->statistics_snapshot = [
                    'points_scored' => $matchStats->points_scored,
                    'attack_percentage' => $matchStats->attack_percentage,
                    'service_percentage' => $matchStats->service_percentage,
                    'reception_percentage' => $matchStats->reception_percentage,
                    'efficiency_rating' => $matchStats->efficiency_rating,
                ];
                $this->save();
            }
        }
    }

    public static function getPlayerAchievements($playerId, $season = null)
    {
        $query = static::with(['award', 'tournament', 'team'])
            ->forPlayer($playerId)
            ->verified()
            ->orderBy('awarded_date', 'desc');
            
        if ($season) {
            $query->forSeason($season);
        }
        
        return $query->get()->groupBy('award.type');
    }

    public static function getTopAwardedPlayers($limit = 10, $season = null)
    {
        $query = static::selectRaw('player_id, COUNT(*) as total_awards, SUM(points_earned) as total_points')
            ->with('player.user')
            ->verified()
            ->groupBy('player_id')
            ->orderBy('total_points', 'desc')
            ->orderBy('total_awards', 'desc')
            ->limit($limit);
            
        if ($season) {
            $query->forSeason($season);
        }
        
        return $query->get();
    }

    public static function getAwardStatistics($season = null)
    {
        $query = static::verified();
        
        if ($season) {
            $query->forSeason($season);
        }
        
        $awards = $query->with('award')->get();
        
        return [
            'total_awards' => $awards->count(),
            'by_type' => $awards->groupBy('award.type')->map->count(),
            'by_level' => $awards->groupBy('competition_level')->map->count(),
            'by_category' => $awards->groupBy('category')->map->count(),
            'total_points' => $awards->sum('points_earned'),
            'recent_awards' => $awards->where('is_recent', true)->count(),
        ];
    }
}
