<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use App\Traits\HasSearch;

class Country extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, LogsActivity;
    use HasSearch; // SIN HasUuid trait

    protected $fillable = [
        'name',
        'code',
        'phone_code',
        'currency_code',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $searchable = ['name', 'code'];

    // =======================
    // SPATIE CONFIGURATION
    // =======================

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'code', 'is_active'])
            ->logOnlyDirty();
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('flag')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/svg+xml'])
            ->singleFile();
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(64)
            ->height(64)
            ->performOnCollections('flag');
    }

    // =======================
    // RELACIONES
    // =======================

    public function departments(): HasMany
    {
        return $this->hasMany(Department::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    // =======================
    // ACCESSORS
    // =======================

    public function getFlagUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('flag');
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->name} ({$this->code})";
    }

    // =======================
    // SCOPES
    // =======================

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
