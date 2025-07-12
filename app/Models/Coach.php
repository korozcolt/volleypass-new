<?php

// ======================
// app/Models/Coach.php
// ======================

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use App\Traits\HasSearch;

class Coach extends Model
{
    use SoftDeletes, LogsActivity, HasSearch;

    protected $fillable = [
        'user_id',
        'club_id',
        'license_number',
        'license_level',
        'specialization',
        'experience_years',
        'status',
        'notes',
    ];

    protected $casts = [
        'experience_years' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $searchable = ['user.name', 'license_number', 'specialization'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['license_number', 'license_level', 'status'])
            ->logOnlyDirty();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }

    public function getFullNameAttribute(): string
    {
        return $this->user->full_name;
    }
}

