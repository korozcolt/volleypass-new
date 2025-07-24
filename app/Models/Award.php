<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Support\Str;

class Award extends Model
{
    use SoftDeletes, LogsActivity;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'type',
        'category',
        'icon',
        'color',
        'points_value',
        'auto_generate_certificate',
        'criteria',
        'requires_approval',
        'is_active',
        'display_order',
        'metadata',
    ];

    protected $casts = [
        'criteria' => 'array',
        'metadata' => 'array',
        'auto_generate_certificate' => 'boolean',
        'requires_approval' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'type', 'points_value', 'is_active'])
            ->logOnlyDirty();
    }

    // =======================
    // RELATIONSHIPS
    // =======================

    public function playerAwards(): HasMany
    {
        return $this->hasMany(PlayerAward::class);
    }

    // =======================
    // ACCESSORS & MUTATORS
    // =======================

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = $value;
        $this->attributes['slug'] = Str::slug($value);
    }

    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'individual' => 'Individual',
            'team' => 'Equipo',
            'tournament' => 'Torneo',
            'season' => 'Temporada',
            'special' => 'Especial',
            default => 'No especificado'
        };
    }

    public function getCategoryLabelAttribute(): string
    {
        return match($this->category) {
            'performance' => 'Rendimiento',
            'achievement' => 'Logro',
            'participation' => 'Participación',
            'leadership' => 'Liderazgo',
            'sportsmanship' => 'Deportividad',
            'improvement' => 'Mejora',
            default => 'General'
        };
    }

    public function getIconHtmlAttribute(): string
    {
        $iconClass = $this->icon ?? 'heroicon-o-trophy';
        $colorClass = $this->getColorClass();
        
        return "<i class='{$iconClass} {$colorClass}'></i>";
    }

    public function getColorClass(): string
    {
        return match($this->color) {
            'gold' => 'text-yellow-500',
            'silver' => 'text-gray-400',
            'bronze' => 'text-orange-600',
            'blue' => 'text-blue-500',
            'green' => 'text-green-500',
            'red' => 'text-red-500',
            'purple' => 'text-purple-500',
            default => 'text-gray-500'
        };
    }

    // =======================
    // SCOPES
    // =======================

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeRequiresApproval($query)
    {
        return $query->where('requires_approval', true);
    }

    public function scopeAutoGenerate($query)
    {
        return $query->where('auto_generate_certificate', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order')->orderBy('name');
    }

    // =======================
    // METHODS
    // =======================

    public function checkCriteria($player, $context = []): bool
    {
        if (!$this->criteria || empty($this->criteria)) {
            return true;
        }

        foreach ($this->criteria as $criterion) {
            if (!$this->evaluateCriterion($criterion, $player, $context)) {
                return false;
            }
        }

        return true;
    }

    protected function evaluateCriterion($criterion, $player, $context): bool
    {
        $type = $criterion['type'] ?? 'manual';
        
        return match($type) {
            'points_threshold' => $this->checkPointsThreshold($criterion, $player, $context),
            'matches_played' => $this->checkMatchesPlayed($criterion, $player, $context),
            'performance_rating' => $this->checkPerformanceRating($criterion, $player, $context),
            'tournament_position' => $this->checkTournamentPosition($criterion, $context),
            'season_ranking' => $this->checkSeasonRanking($criterion, $player, $context),
            default => true // Manual awards always pass criteria check
        };
    }

    protected function checkPointsThreshold($criterion, $player, $context): bool
    {
        $threshold = $criterion['value'] ?? 0;
        $season = $context['season'] ?? now()->year;
        
        $seasonStats = PlayerSeasonStatistic::forPlayer($player->id)
            ->forSeason($season)
            ->first();
            
        return $seasonStats && $seasonStats->total_points_scored >= $threshold;
    }

    protected function checkMatchesPlayed($criterion, $player, $context): bool
    {
        $threshold = $criterion['value'] ?? 0;
        $season = $context['season'] ?? now()->year;
        
        $matchesPlayed = PlayerStatistic::forPlayer($player->id)
            ->forSeason($season)
            ->distinct('match_id')
            ->count();
            
        return $matchesPlayed >= $threshold;
    }

    protected function checkPerformanceRating($criterion, $player, $context): bool
    {
        $threshold = $criterion['value'] ?? 0;
        $season = $context['season'] ?? now()->year;
        
        $seasonStats = PlayerSeasonStatistic::forPlayer($player->id)
            ->forSeason($season)
            ->first();
            
        return $seasonStats && $seasonStats->performance_score >= $threshold;
    }

    protected function checkTournamentPosition($criterion, $context): bool
    {
        $requiredPosition = $criterion['value'] ?? 1;
        $actualPosition = $context['position'] ?? null;
        
        return $actualPosition && $actualPosition <= $requiredPosition;
    }

    protected function checkSeasonRanking($criterion, $player, $context): bool
    {
        $requiredRanking = $criterion['value'] ?? 1;
        $season = $context['season'] ?? now()->year;
        $leagueId = $context['league_id'] ?? null;
        
        if (!$leagueId) return false;
        
        $rankings = PlayerSeasonStatistic::getLeagueRankings($leagueId, $season);
        $playerRanking = $rankings->where('player_id', $player->id)->first();
        
        return $playerRanking && $playerRanking->position <= $requiredRanking;
    }

    public function awardToPlayer($playerId, $context = []): ?PlayerAward
    {
        $player = Player::find($playerId);
        if (!$player || !$this->checkCriteria($player, $context)) {
            return null;
        }

        $playerAward = PlayerAward::create([
            'player_id' => $playerId,
            'award_id' => $this->id,
            'tournament_id' => $context['tournament_id'] ?? null,
            'match_id' => $context['match_id'] ?? null,
            'team_id' => $context['team_id'] ?? $player->current_team_id,
            'awarded_date' => now(),
            'season' => $context['season'] ?? now()->year,
            'category' => $this->category,
            'position' => $context['position'] ?? null,
            'competition_level' => $context['competition_level'] ?? 'local',
            'description' => $context['description'] ?? $this->description,
            'awarded_by' => $context['awarded_by'] ?? 'Sistema',
            'points_earned' => $this->points_value,
            'verification_status' => !$this->requires_approval,
            'notes' => $context['notes'] ?? null,
        ]);

        if ($this->auto_generate_certificate && $playerAward) {
            $playerAward->certificate_url = $playerAward->generateCertificateUrl();
            $playerAward->save();
        }

        return $playerAward;
    }

    public static function getAvailableTypes(): array
    {
        return [
            'individual' => 'Individual',
            'team' => 'Equipo',
            'tournament' => 'Torneo',
            'season' => 'Temporada',
            'special' => 'Especial',
        ];
    }

    public static function getAvailableCategories(): array
    {
        return [
            'performance' => 'Rendimiento',
            'achievement' => 'Logro',
            'participation' => 'Participación',
            'leadership' => 'Liderazgo',
            'sportsmanship' => 'Deportividad',
            'improvement' => 'Mejora',
        ];
    }
}
