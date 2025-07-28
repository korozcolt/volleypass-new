<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use App\Traits\HasSearch;

class Referee extends Model
{
    use SoftDeletes, LogsActivity, HasSearch;

    protected $fillable = [
        'user_id',
        'license_number',
        'category',
        'experience_years',
        'status',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'experience_years' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $searchable = ['license_number', 'user.name', 'category'];

    /**
     * Get the user that owns the referee profile.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the matches assigned to this referee.
     */
    public function matches(): HasMany
    {
        return $this->hasMany(VolleyMatch::class);
    }

    /**
     * Get the user who created this referee profile.
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
            ->logOnly(['license_number', 'category', 'experience_years', 'status'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * Scope a query to only include active referees.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to filter by category.
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }
}