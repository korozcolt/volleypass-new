<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use App\Enums\MatchStatus;
use App\Enums\MatchPhase;

class VolleyMatch extends Model
{
    use SoftDeletes, LogsActivity;

    protected $table = 'matches';

    protected $fillable = [
        'tournament_id',
        'group_id',
        'home_team_id',
        'away_team_id',
        'league_id',
        'match_number',
        'scheduled_at',
        'started_at',
        'finished_at',
        'venue',
        'venue_address',
        'status',
        'phase',
        'round',
        'home_sets',
        'away_sets',
        'home_points',
        'away_points',
        'winner_team_id',
        'first_referee',
        'second_referee',
        'scorer',
        'duration_minutes',
        'events',
        'statistics',
        'notes',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
        'status' => MatchStatus::class,
        'phase' => MatchPhase::class,
        'events' => 'array',
        'statistics' => 'array',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['tournament_id', 'home_team_id', 'away_team_id', 'status', 'winner_team_id'])
            ->logOnlyDirty();
    }

    // =======================
    // RELATIONSHIPS
    // =======================

    public function tournament(): BelongsTo
    {
        return $this->belongsTo(Tournament::class);
    }

    public function homeTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'home_team_id');
    }

    public function awayTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'away_team_id');
    }

    public function winnerTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'winner_team_id');
    }

    public function league(): BelongsTo
    {
        return $this->belongsTo(League::class);
    }

    public function playerStatistics(): HasMany
    {
        return $this->hasMany(PlayerStatistic::class);
    }

    public function teamStatistics(): HasMany
    {
        return $this->hasMany(TeamStatistic::class);
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(TournamentGroup::class, 'group_id');
    }

    public function cards(): HasMany
    {
        return $this->hasMany(TournamentCard::class, 'match_id');
    }

    public function referee(): BelongsTo
    {
        return $this->belongsTo(Referee::class, 'first_referee');
    }

    public function sets(): HasMany
    {
        return $this->hasMany(MatchSet::class, 'match_id');
    }

    // Snake case aliases for compatibility
    public function home_team(): BelongsTo
    {
        return $this->homeTeam();
    }

    public function away_team(): BelongsTo
    {
        return $this->awayTeam();
    }

    // =======================
    // ACCESSORS & MUTATORS
    // =======================

    public function getIsFinishedAttribute(): bool
    {
        return $this->status === MatchStatus::Finished;
    }

    public function getIsLiveAttribute(): bool
    {
        return $this->status === MatchStatus::In_Progress;
    }

    public function getResultAttribute(): string
    {
        if (!$this->is_finished) {
            return 'Pendiente';
        }

        return "{$this->home_sets} - {$this->away_sets}";
    }

    public function getWinnerNameAttribute(): ?string
    {
        return $this->winnerTeam?->name;
    }

    // =======================
    // SCOPES
    // =======================

    public function scopeFinished($query)
    {
        return $query->where('status', MatchStatus::Finished);
    }

    public function scopeLive($query)
    {
        return $query->where('status', MatchStatus::In_Progress);
    }

    public function scopeScheduled($query)
    {
        return $query->where('status', MatchStatus::Scheduled);
    }

    public function scopeForTournament($query, $tournamentId)
    {
        return $query->where('tournament_id', $tournamentId);
    }

    public function scopeForTeam($query, $teamId)
    {
        return $query->where(function ($q) use ($teamId) {
            $q->where('home_team_id', $teamId)
              ->orWhere('away_team_id', $teamId);
        });
    }

    // =======================
    // METHODS
    // =======================

    public function startMatch(): void
    {
        $this->update([
            'status' => MatchStatus::In_Progress,
            'started_at' => now(),
        ]);
    }

    public function finishMatch(int $homeSets, int $awaySets, ?int $winnerTeamId = null): void
    {
        $this->update([
            'status' => MatchStatus::Finished,
            'finished_at' => now(),
            'home_sets' => $homeSets,
            'away_sets' => $awaySets,
            'winner_team_id' => $winnerTeamId ?? ($homeSets > $awaySets ? $this->home_team_id : $this->away_team_id),
            'duration_minutes' => $this->started_at ? now()->diffInMinutes($this->started_at) : null,
        ]);
    }

    public function getOpponentTeam(int $teamId): ?Team
    {
        if ($this->home_team_id === $teamId) {
            return $this->awayTeam;
        }
        
        if ($this->away_team_id === $teamId) {
            return $this->homeTeam;
        }
        
        return null;
    }

    public function isTeamParticipating(int $teamId): bool
    {
        return $this->home_team_id === $teamId || $this->away_team_id === $teamId;
    }

    public function getTeamStatistics(int $teamId): ?TeamStatistic
    {
        return $this->teamStatistics()->where('team_id', $teamId)->first();
    }

    public function getMatchCards(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->cards()->with(['player', 'team'])->orderBy('issued_at')->get();
    }

    public function getTeamCards(int $teamId): \Illuminate\Database\Eloquent\Collection
    {
        return $this->cards()->where('team_id', $teamId)->with(['player'])->orderBy('issued_at')->get();
    }

    public function hasActiveCards(): bool
    {
        return $this->cards()->active()->exists();
    }

    public function updateGroupStandings(): void
    {
        if ($this->group_id && $this->is_finished) {
            $this->group->updateStandings();
        }
    }

    public function isGroupMatch(): bool
    {
        return !is_null($this->group_id);
    }

    public function isKnockoutMatch(): bool
    {
        return is_null($this->group_id) && !is_null($this->tournament_id);
    }

    public function getMatchType(): string
    {
        if ($this->isGroupMatch()) {
            return 'Fase de Grupos';
        }
        
        if ($this->isKnockoutMatch()) {
            return match($this->phase) {
                MatchPhase::FINAL => 'Final',
                MatchPhase::SEMI_FINALS => 'Semifinal',
                MatchPhase::QUARTER_FINALS => 'Cuartos de Final',
                MatchPhase::ROUND_OF_16 => 'Octavos de Final',
                MatchPhase::THIRD_PLACE => 'Tercer Lugar',
                default => 'Eliminatoria'
            };
        }
        
        return 'Liga Regular';
    }
}