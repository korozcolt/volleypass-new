<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @OA\Schema(
 *     schema="Sanction",
 *     type="object",
 *     title="Sanction",
 *     description="Player sanction model",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="player_id", type="integer", example=1),
 *     @OA\Property(property="team_id", type="integer", example=1),
 *     @OA\Property(property="match_id", type="integer", nullable=true),
 *     @OA\Property(property="tournament_id", type="integer", nullable=true),
 *     @OA\Property(property="type", type="string", example="yellow_card"),
 *     @OA\Property(property="severity", type="string", example="minor"),
 *     @OA\Property(property="status", type="string", example="active"),
 *     @OA\Property(property="reason", type="string", example="Unsportsmanlike conduct"),
 *     @OA\Property(property="description", type="string", example="Player argued with referee"),
 *     @OA\Property(property="incident_time", type="string", format="date-time"),
 *     @OA\Property(property="applied_at", type="string", format="date-time"),
 *     @OA\Property(property="expires_at", type="string", format="date-time", nullable=true),
 *     @OA\Property(property="fine_amount", type="number", format="float", nullable=true),
 *     @OA\Property(property="suspension_matches", type="integer", nullable=true),
 *     @OA\Property(property="suspension_days", type="integer", nullable=true),
 *     @OA\Property(property="is_active", type="boolean", example=true),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Enums\SanctionType;
use App\Enums\SanctionSeverity;
use App\Enums\SanctionStatus;

class Sanction extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'player_id',
        'team_id',
        'match_id',
        'tournament_id',
        'referee_id',
        'type',
        'severity',
        'status',
        'reason',
        'description',
        'incident_time',
        'applied_at',
        'expires_at',
        'fine_amount',
        'suspension_matches',
        'suspension_days',
        'appeal_deadline',
        'appeal_reason',
        'appeal_status',
        'reviewed_by',
        'reviewed_at',
        'review_notes',
        'is_active',
        'metadata'
    ];

    protected $casts = [
        'type' => SanctionType::class,
        'severity' => SanctionSeverity::class,
        'status' => SanctionStatus::class,
        'incident_time' => 'datetime',
        'applied_at' => 'datetime',
        'expires_at' => 'datetime',
        'appeal_deadline' => 'datetime',
        'reviewed_at' => 'datetime',
        'fine_amount' => 'decimal:2',
        'suspension_matches' => 'integer',
        'suspension_days' => 'integer',
        'is_active' => 'boolean',
        'metadata' => 'array'
    ];

    protected $dates = [
        'incident_time',
        'applied_at',
        'expires_at',
        'appeal_deadline',
        'reviewed_at',
        'deleted_at'
    ];

    /**
     * Get the player that received the sanction
     */
    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }

    /**
     * Get the team associated with the sanction
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * Get the match where the sanction occurred
     */
    public function match(): BelongsTo
    {
        return $this->belongsTo(VolleyMatch::class, 'match_id');
    }

    /**
     * Get the tournament where the sanction occurred
     */
    public function tournament(): BelongsTo
    {
        return $this->belongsTo(Tournament::class);
    }

    /**
     * Get the referee who applied the sanction
     */
    public function referee(): BelongsTo
    {
        return $this->belongsTo(Referee::class);
    }

    /**
     * Get the user who reviewed the sanction
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Check if the sanction is currently active
     */
    public function isActive(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }

        return $this->status === SanctionStatus::ACTIVE;
    }

    /**
     * Check if the sanction can be appealed
     */
    public function canBeAppealed(): bool
    {
        if ($this->appeal_deadline && $this->appeal_deadline->isPast()) {
            return false;
        }

        return $this->status->canBeAppealed();
    }

    /**
     * Check if the sanction affects player eligibility
     */
    public function affectsEligibility(): bool
    {
        return $this->isActive() && $this->severity->affectsEligibility();
    }

    /**
     * Get the remaining suspension matches
     */
    public function getRemainingMatches(): int
    {
        if (!$this->isActive() || !$this->suspension_matches) {
            return 0;
        }

        // This would need to be calculated based on matches played since sanction
        // For now, return the original suspension matches
        return $this->suspension_matches;
    }

    /**
     * Get the remaining suspension days
     */
    public function getRemainingDays(): int
    {
        if (!$this->isActive() || !$this->expires_at) {
            return 0;
        }

        return max(0, now()->diffInDays($this->expires_at, false));
    }

    /**
     * Scope for active sanctions
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                    ->where('status', SanctionStatus::ACTIVE)
                    ->where(function ($q) {
                        $q->whereNull('expires_at')
                          ->orWhere('expires_at', '>', now());
                    });
    }

    /**
     * Scope for sanctions by type
     */
    public function scopeByType($query, SanctionType $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope for sanctions by severity
     */
    public function scopeBySeverity($query, SanctionSeverity $severity)
    {
        return $query->where('severity', $severity);
    }

    /**
     * Scope for player sanctions
     */
    public function scopeForPlayer($query, int $playerId)
    {
        return $query->where('player_id', $playerId);
    }

    /**
     * Scope for team sanctions
     */
    public function scopeForTeam($query, int $teamId)
    {
        return $query->where('team_id', $teamId);
    }

    /**
     * Scope for match sanctions
     */
    public function scopeForMatch($query, int $matchId)
    {
        return $query->where('match_id', $matchId);
    }

    /**
     * Scope for tournament sanctions
     */
    public function scopeForTournament($query, int $tournamentId)
    {
        return $query->where('tournament_id', $tournamentId);
    }
}