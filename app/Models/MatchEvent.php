<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @OA\Schema(
 *     schema="MatchEvent",
 *     type="object",
 *     title="Match Event",
 *     description="Volleyball match event model",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="match_id", type="integer", example=1),
 *     @OA\Property(property="set_number", type="integer", example=1),
 *     @OA\Property(property="team_id", type="integer", example=1),
 *     @OA\Property(property="player_id", type="integer", example=1),
 *     @OA\Property(property="event_type", type="string", example="attack"),
 *     @OA\Property(property="event_subtype", type="string", example="spike"),
 *     @OA\Property(property="event_time", type="string", format="date-time"),
 *     @OA\Property(property="set_score_home", type="integer", example=15),
 *     @OA\Property(property="set_score_away", type="integer", example=12),
 *     @OA\Property(property="description", type="string", example="Successful attack"),
 *     @OA\Property(property="is_successful", type="boolean", example=true),
 *     @OA\Property(property="points_awarded", type="integer", example=1),
 *     @OA\Property(property="coordinates_x", type="number", format="float", nullable=true),
 *     @OA\Property(property="coordinates_y", type="number", format="float", nullable=true),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class MatchEvent extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'match_id',
        'set_number',
        'team_id',
        'player_id',
        'event_type',
        'event_subtype',
        'event_time',
        'set_score_home',
        'set_score_away',
        'total_score_home',
        'total_score_away',
        'position',
        'coordinates_x',
        'coordinates_y',
        'description',
        'is_successful',
        'points_awarded',
        'rotation_position',
        'serving_team_id',
        'receiving_team_id',
        'previous_event_id',
        'related_player_id',
        'metadata',
        'created_by',
        'verified_at',
        'verified_by'
    ];

    protected $casts = [
        'set_number' => 'integer',
        'event_time' => 'datetime',
        'set_score_home' => 'integer',
        'set_score_away' => 'integer',
        'total_score_home' => 'integer',
        'total_score_away' => 'integer',
        'coordinates_x' => 'decimal:2',
        'coordinates_y' => 'decimal:2',
        'is_successful' => 'boolean',
        'points_awarded' => 'integer',
        'rotation_position' => 'integer',
        'metadata' => 'array',
        'verified_at' => 'datetime'
    ];

    protected $dates = [
        'event_time',
        'verified_at',
        'deleted_at'
    ];

    // Event types constants
    const EVENT_SERVE = 'serve';
    const EVENT_ATTACK = 'attack';
    const EVENT_BLOCK = 'block';
    const EVENT_DIG = 'dig';
    const EVENT_SET = 'set';
    const EVENT_RECEPTION = 'reception';
    const EVENT_SUBSTITUTION = 'substitution';
    const EVENT_TIMEOUT = 'timeout';
    const EVENT_ROTATION = 'rotation';
    const EVENT_POINT = 'point';
    const EVENT_FAULT = 'fault';
    const EVENT_CARD = 'card';
    const EVENT_CHALLENGE = 'challenge';
    const EVENT_INJURY = 'injury';
    const EVENT_SET_START = 'set_start';
    const EVENT_SET_END = 'set_end';
    const EVENT_MATCH_START = 'match_start';
    const EVENT_MATCH_END = 'match_end';

    // Event subtypes
    const SUBTYPE_ACE = 'ace';
    const SUBTYPE_ERROR = 'error';
    const SUBTYPE_KILL = 'kill';
    const SUBTYPE_STUFF = 'stuff';
    const SUBTYPE_ASSIST = 'assist';
    const SUBTYPE_YELLOW_CARD = 'yellow';
    const SUBTYPE_RED_CARD = 'red';
    const SUBTYPE_TECHNICAL_TIMEOUT = 'technical';
    const SUBTYPE_TEAM_TIMEOUT = 'team';
    const SUBTYPE_LIBERO_REPLACEMENT = 'libero';
    const SUBTYPE_REGULAR_SUBSTITUTION = 'regular';

    /**
     * Get the match this event belongs to
     */
    public function match(): BelongsTo
    {
        return $this->belongsTo(VolleyMatch::class, 'match_id');
    }

    /**
     * Get the team associated with this event
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * Get the player who performed this event
     */
    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }

    /**
     * Get the serving team for this event
     */
    public function servingTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'serving_team_id');
    }

    /**
     * Get the receiving team for this event
     */
    public function receivingTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'receiving_team_id');
    }

    /**
     * Get the related player (for substitutions, etc.)
     */
    public function relatedPlayer(): BelongsTo
    {
        return $this->belongsTo(Player::class, 'related_player_id');
    }

    /**
     * Get the previous event in sequence
     */
    public function previousEvent(): BelongsTo
    {
        return $this->belongsTo(MatchEvent::class, 'previous_event_id');
    }

    /**
     * Get the user who created this event
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who verified this event
     */
    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Get the match set this event belongs to
     */
    public function matchSet(): BelongsTo
    {
        return $this->belongsTo(MatchSet::class, 'match_id', 'match_id')
                    ->where('set_number', $this->set_number);
    }

    /**
     * Check if this event was successful
     */
    public function isSuccessful(): bool
    {
        return $this->is_successful ?? false;
    }

    /**
     * Check if this event resulted in points
     */
    public function awardedPoints(): int
    {
        return $this->points_awarded ?? 0;
    }

    /**
     * Check if this event is verified
     */
    public function isVerified(): bool
    {
        return !is_null($this->verified_at) && !is_null($this->verified_by);
    }

    /**
     * Get event coordinates as array
     */
    public function getCoordinates(): array
    {
        return [
            'x' => $this->coordinates_x,
            'y' => $this->coordinates_y
        ];
    }

    /**
     * Get current score at the time of event
     */
    public function getScore(): array
    {
        return [
            'set' => [
                'home' => $this->set_score_home,
                'away' => $this->set_score_away
            ],
            'total' => [
                'home' => $this->total_score_home,
                'away' => $this->total_score_away
            ]
        ];
    }

    /**
     * Scope for events by match
     */
    public function scopeForMatch($query, int $matchId)
    {
        return $query->where('match_id', $matchId);
    }

    /**
     * Scope for events by set
     */
    public function scopeForSet($query, int $matchId, int $setNumber)
    {
        return $query->where('match_id', $matchId)
                    ->where('set_number', $setNumber);
    }

    /**
     * Scope for events by team
     */
    public function scopeForTeam($query, int $teamId)
    {
        return $query->where('team_id', $teamId);
    }

    /**
     * Scope for events by player
     */
    public function scopeForPlayer($query, int $playerId)
    {
        return $query->where('player_id', $playerId);
    }

    /**
     * Scope for events by type
     */
    public function scopeByType($query, string $eventType)
    {
        return $query->where('event_type', $eventType);
    }

    /**
     * Scope for events by subtype
     */
    public function scopeBySubtype($query, string $eventSubtype)
    {
        return $query->where('event_subtype', $eventSubtype);
    }

    /**
     * Scope for successful events
     */
    public function scopeSuccessful($query)
    {
        return $query->where('is_successful', true);
    }

    /**
     * Scope for verified events
     */
    public function scopeVerified($query)
    {
        return $query->whereNotNull('verified_at')
                    ->whereNotNull('verified_by');
    }

    /**
     * Scope for events that awarded points
     */
    public function scopeWithPoints($query)
    {
        return $query->where('points_awarded', '>', 0);
    }

    /**
     * Scope for events in chronological order
     */
    public function scopeChronological($query)
    {
        return $query->orderBy('event_time')
                    ->orderBy('id');
    }

    /**
     * Get all available event types
     */
    public static function getEventTypes(): array
    {
        return [
            self::EVENT_SERVE,
            self::EVENT_ATTACK,
            self::EVENT_BLOCK,
            self::EVENT_DIG,
            self::EVENT_SET,
            self::EVENT_RECEPTION,
            self::EVENT_SUBSTITUTION,
            self::EVENT_TIMEOUT,
            self::EVENT_ROTATION,
            self::EVENT_POINT,
            self::EVENT_FAULT,
            self::EVENT_CARD,
            self::EVENT_CHALLENGE,
            self::EVENT_INJURY,
            self::EVENT_SET_START,
            self::EVENT_SET_END,
            self::EVENT_MATCH_START,
            self::EVENT_MATCH_END
        ];
    }

    /**
     * Get all available event subtypes
     */
    public static function getEventSubtypes(): array
    {
        return [
            self::SUBTYPE_ACE,
            self::SUBTYPE_ERROR,
            self::SUBTYPE_KILL,
            self::SUBTYPE_STUFF,
            self::SUBTYPE_ASSIST,
            self::SUBTYPE_YELLOW_CARD,
            self::SUBTYPE_RED_CARD,
            self::SUBTYPE_TECHNICAL_TIMEOUT,
            self::SUBTYPE_TEAM_TIMEOUT,
            self::SUBTYPE_LIBERO_REPLACEMENT,
            self::SUBTYPE_REGULAR_SUBSTITUTION
        ];
    }
}