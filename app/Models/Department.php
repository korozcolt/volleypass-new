<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use App\Traits\HasSearch;

class Department extends Model
{
    use LogsActivity;
    use HasSearch; // SIN HasUuid trait

    protected $fillable = [
        'country_id',
        'name',
        'code',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $searchable = ['name', 'code', 'country.name'];

    // =======================
    // SPATIE CONFIGURATION
    // =======================

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'code', 'is_active'])
            ->logOnlyDirty();
    }

    // =======================
    // RELACIONES
    // =======================

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function cities(): HasMany
    {
        return $this->hasMany(City::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    // =======================
    // SCOPES Y MÉTODOS
    // =======================

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForCountry($query, $countryId)
    {
        return $query->where('country_id', $countryId);
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->name}, {$this->country->name}";
    }
}
