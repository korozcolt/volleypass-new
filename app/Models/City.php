<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use App\Traits\HasSearch;

class City extends Model
{
    use HasFactory, LogsActivity;
    use HasSearch; // SIN HasUuid trait

    protected $fillable = [
        'department_id',
        'name',
        'code',
        'postal_code',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $searchable = ['name', 'code', 'department.name'];

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

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function country()
    {
        return $this->hasOneThrough(Country::class, Department::class, 'id', 'id', 'department_id', 'country_id');
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

    public function scopeForDepartment($query, $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->name}, {$this->department->name}";
    }

    public function getFullLocationAttribute(): string
    {
        return "{$this->name}, {$this->department->name}, {$this->country->name}";
    }
}
