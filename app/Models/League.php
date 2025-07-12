<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use App\Traits\HasSearch;
use App\Enums\UserStatus;

class League extends Model implements HasMedia
{
    use SoftDeletes, InteractsWithMedia, LogsActivity;
    use HasSearch;

    protected $fillable = [
        'name',
        'short_name',
        'description',
        'country_id',
        'department_id',
        'city_id',
        'status',
        'foundation_date',
        'website',
        'email',
        'phone',
        'address',
        'configurations',
        'is_active',
    ];

    protected $casts = [
        'foundation_date' => 'date',
        'configurations' => 'array',
        'is_active' => 'boolean',
        'status' => UserStatus::class,
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $searchable = ['name', 'short_name', 'email'];

    // =======================
    // SPATIE CONFIGURATION
    // =======================

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'status', 'is_active'])
            ->logOnlyDirty();
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('logo')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/svg+xml'])
            ->singleFile();

        $this->addMediaCollection('documents')
            ->acceptsMimeTypes(['application/pdf', 'image/jpeg', 'image/png']);
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(150)
            ->height(150)
            ->performOnCollections('logo');
    }

    // =======================
    // RELACIONES
    // =======================

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function clubs(): HasMany
    {
        return $this->hasMany(Club::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function tournaments(): HasMany
    {
        return $this->hasMany(Tournament::class);
    }

    // Obtener todas las jugadoras de la liga
    public function players()
    {
        return $this->hasManyThrough(
            Player::class,
            Club::class,
            'league_id', // Foreign key en clubs
            'current_club_id', // Foreign key en players
            'id', // Local key en leagues
            'id' // Local key en clubs
        );
    }

    // Obtener todos los entrenadores de la liga
    public function coaches()
    {
        return $this->hasManyThrough(
            Coach::class,
            Club::class,
            'league_id',
            'club_id',
            'id',
            'id'
        );
    }

    // =======================
    // ACCESSORS
    // =======================

    public function getLogoUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('logo');
    }

    public function getActiveClubsCountAttribute(): int
    {
        return $this->clubs()->where('is_active', true)->count();
    }

    public function getActivePlayersCountAttribute(): int
    {
        return $this->players()
            ->whereHas('user', function($query) {
                $query->where('status', UserStatus::Active);
            })
            ->count();
    }

    public function getTotalUsersCountAttribute(): int
    {
        return $this->users()->where('status', UserStatus::Active)->count();
    }

    public function getEstablishedYearsAttribute(): ?int
    {
        return $this->foundation_date ?
            $this->foundation_date->diffInYears(now()) : null;
    }

    public function getFullLocationAttribute(): string
    {
        $parts = [];

        if ($this->city) {
            $parts[] = $this->city->name;
        }
        if ($this->department) {
            $parts[] = $this->department->name;
        }
        if ($this->country) {
            $parts[] = $this->country->name;
        }

        return implode(', ', $parts);
    }

    // =======================
    // SCOPES
    // =======================

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInDepartment($query, $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }

    public function scopeInCountry($query, $countryId)
    {
        return $query->where('country_id', $countryId);
    }

    // =======================
    // MÉTODOS DE CONFIGURACIÓN
    // =======================

    public function getConfiguration(string $key, $default = null)
    {
        return data_get($this->configurations, $key, $default);
    }

    public function setConfiguration(string $key, $value): void
    {
        $configurations = $this->configurations ?? [];
        data_set($configurations, $key, $value);
        $this->update(['configurations' => $configurations]);
    }

    // =======================
    // MÉTODOS DE GESTIÓN
    // =======================

    public function addClub(array $clubData): Club
    {
        $clubData['league_id'] = $this->id;
        return Club::create($clubData);
    }

    public function canRegisterClubs(): bool
    {
        return $this->is_active && $this->status === UserStatus::Active;
    }

    // =======================
    // MÉTODOS DE ESTADÍSTICAS
    // =======================

    public function getClubsStatsByStatus(): array
    {
        return $this->clubs()
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();
    }

    public function getPlayersStatsByCategory(): array
    {
        return $this->players()
            ->join('users', 'players.user_id', '=', 'users.id')
            ->where('users.status', UserStatus::Active)
            ->selectRaw('category, COUNT(*) as count')
            ->groupBy('category')
            ->pluck('count', 'category')
            ->toArray();
    }

    public function getPlayersStatsByGender(): array
    {
        return $this->players()
            ->join('users', 'players.user_id', '=', 'users.id')
            ->where('users.status', UserStatus::Active)
            ->join('users as u', 'players.user_id', '=', 'u.id')
            ->selectRaw('u.gender, COUNT(*) as count')
            ->groupBy('u.gender')
            ->pluck('count', 'u.gender')
            ->toArray();
    }

    public function getRegistrationTrends(int $months = 12): array
    {
        return $this->players()
            ->join('users', 'players.user_id', '=', 'users.id')
            ->where('players.created_at', '>=', now()->subMonths($months))
            ->selectRaw('DATE_FORMAT(players.created_at, "%Y-%m") as month, COUNT(*) as count')
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month')
            ->toArray();
    }
}
