<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use App\Traits\HasSearch;
use App\Enums\Gender;

class Category extends Model
{
    use SoftDeletes, LogsActivity, HasSearch;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'gender',
        'min_age',
        'max_age',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'gender' => Gender::class,
        'min_age' => 'integer',
        'max_age' => 'integer',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    protected $searchable = [
        'name',
        'description',
        'slug',
    ];

    /**
     * Activity log options
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'name',
                'slug',
                'description',
                'gender',
                'min_age',
                'max_age',
                'is_active',
                'sort_order',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * Relationships
     */
    public function tournaments(): HasMany
    {
        return $this->hasMany(Tournament::class);
    }

    public function teams(): HasMany
    {
        return $this->hasMany(Team::class);
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByGender($query, $gender)
    {
        return $query->where('gender', $gender);
    }

    public function scopeByAgeRange($query, $minAge = null, $maxAge = null)
    {
        if ($minAge !== null) {
            $query->where(function ($q) use ($minAge) {
                $q->whereNull('min_age')
                  ->orWhere('min_age', '<=', $minAge);
            });
        }

        if ($maxAge !== null) {
            $query->where(function ($q) use ($maxAge) {
                $q->whereNull('max_age')
                  ->orWhere('max_age', '>=', $maxAge);
            });
        }

        return $query;
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Accessors & Mutators
     */
    public function getAgeRangeAttribute(): string
    {
        if ($this->min_age && $this->max_age) {
            return "{$this->min_age} - {$this->max_age} años";
        }
        
        if ($this->min_age) {
            return "Desde {$this->min_age} años";
        }
        
        if ($this->max_age) {
            return "Hasta {$this->max_age} años";
        }
        
        return 'Sin límite de edad';
    }

    public function getGenderLabelAttribute(): string
    {
        return match($this->gender) {
            Gender::Male => 'Masculino',
            Gender::Female => 'Femenino',
            Gender::Mixed => 'Mixto',
            default => 'No especificado',
        };
    }

    /**
     * Helper methods
     */
    public function isValidForAge(int $age): bool
    {
        if ($this->min_age && $age < $this->min_age) {
            return false;
        }
        
        if ($this->max_age && $age > $this->max_age) {
            return false;
        }
        
        return true;
    }

    public function isValidForGender(Gender $gender): bool
    {
        return $this->gender === Gender::Mixed || $this->gender === $gender;
    }
}