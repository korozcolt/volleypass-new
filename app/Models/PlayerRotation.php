<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Enums\Position;

class PlayerRotation extends Model
{
    use HasFactory;

    protected $fillable = [
        'match_id',
        'set_number',
        'team_id',
        'player_id',
        'position',
        'rotation_order',
        'is_serving',
        'is_libero',
        'substituted_at',
        'substituted_by',
        'substitution_reason',
        'rotation_timestamp',
        'point_when_rotated',
        'is_active',
        'metadata'
    ];

    protected $casts = [
        'position' => Position::class,
        'set_number' => 'integer',
        'rotation_order' => 'integer',
        'is_serving' => 'boolean',
        'is_libero' => 'boolean',
        'substituted_at' => 'datetime',
        'rotation_timestamp' => 'datetime',
        'point_when_rotated' => 'integer',
        'is_active' => 'boolean',
        'metadata' => 'array'
    ];

    protected $dates = [
        'substituted_at',
        'rotation_timestamp'
    ];

    /**
     * Get the match this rotation belongs to
     */
    public function match(): BelongsTo
    {
        return $this->belongsTo(VolleyMatch::class, 'match_id');
    }

    /**
     * Get the team this rotation belongs to
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * Get the player in this rotation
     */
    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }

    /**
     * Get the player who substituted this player
     */
    public function substitute(): BelongsTo
    {
        return $this->belongsTo(Player::class, 'substituted_by');
    }

    /**
     * Get the match set this rotation belongs to
     */
    public function matchSet(): BelongsTo
    {
        return $this->belongsTo(MatchSet::class, 'match_id', 'match_id')
                    ->where('set_number', $this->set_number);
    }

    /**
     * Check if this player is currently serving
     */
    public function isServing(): bool
    {
        return $this->is_serving && $this->is_active;
    }

    /**
     * Check if this player is a libero
     */
    public function isLibero(): bool
    {
        return $this->is_libero;
    }

    /**
     * Check if this player has been substituted
     */
    public function isSubstituted(): bool
    {
        return !is_null($this->substituted_at) && !is_null($this->substituted_by);
    }

    /**
     * Check if this rotation is currently active
     */
    public function isActive(): bool
    {
        return $this->is_active && !$this->isSubstituted();
    }

    /**
     * Get the next position in rotation
     */
    public function getNextPosition(): Position
    {
        return $this->position->getNextPosition();
    }

    /**
     * Get the previous position in rotation
     */
    public function getPreviousPosition(): Position
    {
        return $this->position->getPreviousPosition();
    }

    /**
     * Check if player can serve from this position
     */
    public function canServe(): bool
    {
        return $this->position->canServe();
    }

    /**
     * Check if player can attack from this position
     */
    public function canAttack(): bool
    {
        return $this->position->canAttack();
    }

    /**
     * Check if player can block from this position
     */
    public function canBlock(): bool
    {
        return $this->position->canBlock();
    }

    /**
     * Get position zone (front row or back row)
     */
    public function getZone(): string
    {
        return $this->position->getZone();
    }

    /**
     * Get position coordinates on court
     */
    public function getCoordinates(): array
    {
        return $this->position->getCoordinates();
    }

    /**
     * Scope for active rotations
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                    ->whereNull('substituted_at');
    }

    /**
     * Scope for rotations by match
     */
    public function scopeForMatch($query, int $matchId)
    {
        return $query->where('match_id', $matchId);
    }

    /**
     * Scope for rotations by set
     */
    public function scopeForSet($query, int $matchId, int $setNumber)
    {
        return $query->where('match_id', $matchId)
                    ->where('set_number', $setNumber);
    }

    /**
     * Scope for rotations by team
     */
    public function scopeForTeam($query, int $teamId)
    {
        return $query->where('team_id', $teamId);
    }

    /**
     * Scope for rotations by player
     */
    public function scopeForPlayer($query, int $playerId)
    {
        return $query->where('player_id', $playerId);
    }

    /**
     * Scope for rotations by position
     */
    public function scopeByPosition($query, Position $position)
    {
        return $query->where('position', $position);
    }

    /**
     * Scope for serving rotations
     */
    public function scopeServing($query)
    {
        return $query->where('is_serving', true);
    }

    /**
     * Scope for libero rotations
     */
    public function scopeLibero($query)
    {
        return $query->where('is_libero', true);
    }

    /**
     * Scope for substituted rotations
     */
    public function scopeSubstituted($query)
    {
        return $query->whereNotNull('substituted_at')
                    ->whereNotNull('substituted_by');
    }

    /**
     * Scope for current rotation order
     */
    public function scopeByRotationOrder($query, int $order)
    {
        return $query->where('rotation_order', $order);
    }

    /**
     * Get rotation history for a specific match and team
     */
    public static function getRotationHistory(int $matchId, int $teamId, int $setNumber = null)
    {
        $query = static::forMatch($matchId)
                      ->forTeam($teamId)
                      ->orderBy('rotation_timestamp')
                      ->orderBy('rotation_order');

        if ($setNumber) {
            $query->forSet($matchId, $setNumber);
        }

        return $query->get();
    }

    /**
     * Get current rotation for a team in a specific set
     */
    public static function getCurrentRotation(int $matchId, int $teamId, int $setNumber)
    {
        return static::forSet($matchId, $setNumber)
                    ->forTeam($teamId)
                    ->active()
                    ->orderBy('rotation_order')
                    ->get();
    }
}