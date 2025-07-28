<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class MatchSet extends Model
{
    use SoftDeletes, LogsActivity;

    protected $fillable = [
        'match_id',
        'set_number',
        'home_score',
        'away_score',
        'status',
        'started_at',
        'ended_at',
        'duration_minutes',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'set_number' => 'integer',
        'home_score' => 'integer',
        'away_score' => 'integer',
        'duration_minutes' => 'integer',
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the match that owns this set.
     */
    public function match(): BelongsTo
    {
        return $this->belongsTo(VolleyMatch::class, 'match_id');
    }

    /**
     * Get the user who created this set record.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get activity log options.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['set_number', 'home_score', 'away_score', 'status'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * Scope a query to only include completed sets.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope a query to only include sets in progress.
     */
    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    /**
     * Get the winner of this set.
     */
    public function getWinnerAttribute()
    {
        if ($this->status !== 'completed') {
            return null;
        }

        if ($this->home_score > $this->away_score) {
            return 'home';
        } elseif ($this->away_score > $this->home_score) {
            return 'away';
        }

        return 'tie';
    }

    /**
     * Check if this set is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Check if this set is in progress.
     */
    public function isInProgress(): bool
    {
        return $this->status === 'in_progress';
    }
}