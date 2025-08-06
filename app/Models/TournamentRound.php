<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class TournamentRound extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tournament_id',
        'round_number',
        'name',
        'matches_count',
        'status',
        'is_elimination',
        'started_at',
        'finished_at',
        'metadata'
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
        'is_elimination' => 'boolean',
        'metadata' => 'array'
    ];

    protected $dates = [
        'started_at',
        'finished_at',
        'deleted_at'
    ];

    /**
     * Get the tournament that owns the round
     */
    public function tournament(): BelongsTo
    {
        return $this->belongsTo(Tournament::class);
    }

    /**
     * Get the matches for this round
     */
    public function matches(): HasMany
    {
        return $this->hasMany(VolleyMatch::class, 'round_id');
    }

    /**
     * Check if the round is completed
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Check if the round is in progress
     */
    public function isInProgress(): bool
    {
        return $this->status === 'in_progress';
    }

    /**
     * Check if the round is scheduled
     */
    public function isScheduled(): bool
    {
        return $this->status === 'scheduled';
    }

    /**
     * Get the completion percentage of the round
     */
    public function getCompletionPercentage(): float
    {
        $totalMatches = $this->matches()->count();
        if ($totalMatches === 0) {
            return 0;
        }

        $completedMatches = $this->matches()->where('status', 'finished')->count();
        return ($completedMatches / $totalMatches) * 100;
    }

    /**
     * Start the round
     */
    public function start(): void
    {
        $this->update([
            'status' => 'in_progress',
            'started_at' => now()
        ]);
    }

    /**
     * Complete the round
     */
    public function complete(): void
    {
        $this->update([
            'status' => 'completed',
            'finished_at' => now()
        ]);
    }

    /**
     * Scope for rounds by status
     */
    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for elimination rounds
     */
    public function scopeElimination($query)
    {
        return $query->where('is_elimination', true);
    }

    /**
     * Scope for group stage rounds
     */
    public function scopeGroupStage($query)
    {
        return $query->where('is_elimination', false);
    }
}