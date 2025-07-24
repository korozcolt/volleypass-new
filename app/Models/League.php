<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
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
    use HasFactory, SoftDeletes, InteractsWithMedia, LogsActivity;
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
        $this->addMediaCollection('logo_light')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/svg+xml'])
            ->singleFile();

        $this->addMediaCollection('logo_dark')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/svg+xml'])
            ->singleFile();

        // Mantener compatibilidad con logo único (por defecto será light)
        $this->addMediaCollection('logo')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/svg+xml'])
            ->singleFile();

        $this->addMediaCollection('documents')
            ->acceptsMimeTypes(['application/pdf', 'image/jpeg', 'image/png']);
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(150)
            ->height(150)
            ->performOnCollections('logo', 'logo_light', 'logo_dark');

        $this->addMediaConversion('small')
            ->width(64)
            ->height(64)
            ->performOnCollections('logo', 'logo_light', 'logo_dark');
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

    public function configurations(): HasMany
    {
        return $this->hasMany(LeagueConfiguration::class);
    }

    public function categories(): HasMany
    {
        return $this->hasMany(LeagueCategory::class);
    }

    public function leagueCategories()
    {
        return $this->hasMany(LeagueCategory::class);
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
        // Por defecto devuelve el logo light, o el logo único si existe
        return $this->getLogoUrl('light');
    }

    public function getLogoUrl(string $mode = 'light'): ?string
    {
        $collection = match ($mode) {
            'dark' => 'logo_dark',
            'light' => 'logo_light',
            default => 'logo_light'
        };

        $url = $this->getFirstMediaUrl($collection);

        // Si no existe el logo específico, intentar con el logo genérico
        if (empty($url)) {
            $url = $this->getFirstMediaUrl('logo');
        }

        return $url ?: null;
    }

    public function getLogoLightUrlAttribute(): ?string
    {
        return $this->getLogoUrl('light');
    }

    public function getLogoDarkUrlAttribute(): ?string
    {
        return $this->getLogoUrl('dark');
    }

    public function hasLogo(string $mode = 'light'): bool
    {
        $collection = match ($mode) {
            'dark' => 'logo_dark',
            'light' => 'logo_light',
            default => 'logo_light'
        };

        return $this->hasMedia($collection) || $this->hasMedia('logo');
    }

    public function getAdaptiveLogoUrl(): string
    {
        // Devuelve un HTML que se adapta automáticamente al modo
        $lightLogo = $this->getLogoUrl('light');
        $darkLogo = $this->getLogoUrl('dark');

        if ($lightLogo && $darkLogo) {
            return "
                <img src='{$lightLogo}' class='block dark:hidden' alt='{$this->name}' />
                <img src='{$darkLogo}' class='hidden dark:block' alt='{$this->name}' />
            ";
        }

        // Si solo hay un logo, usarlo para ambos modos
        $logo = $lightLogo ?: $darkLogo;
        return $logo ? "<img src='{$logo}' alt='{$this->name}' />" : '';
    }

    public function getActiveClubsCountAttribute(): int
    {
        return $this->clubs()->where('is_active', true)->count();
    }

    public function getActivePlayersCountAttribute(): int
    {
        return $this->players()
            ->whereHas('user', function ($query) {
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
    // MÉTODOS DE CATEGORÍAS DINÁMICAS
    // =======================

    /**
     * Obtiene categorías activas ordenadas
     */
    public function getActiveCategories()
    {
        return $this->categories()->active()->ordered()->get();
    }

    /**
     * Encuentra la categoría apropiada para una jugadora
     */
    public function findCategoryForPlayer(int $age, string $gender): ?LeagueCategory
    {
        return $this->categories()
            ->active()
            ->forAge($age)
            ->forGender($gender)
            ->ordered()
            ->first();
    }

    /**
     * Verifica si tiene configuración de categorías personalizada
     */
    public function hasCustomCategories(): bool
    {
        return $this->categories()->exists();
    }

    /**
     * Crea categorías por defecto basadas en el enum PlayerCategory
     */
    public function createDefaultCategories(): void
    {
        if ($this->hasCustomCategories()) {
            return; // Ya tiene categorías configuradas
        }

        LeagueCategory::createDefaultCategoriesForLeague($this->id);
    }

    /**
     * Valida la configuración de categorías
     */
    public function validateCategoryConfiguration(): array
    {
        return LeagueCategory::validateLeagueConfiguration($this->id);
    }

    /**
     * Obtiene estadísticas de jugadoras por categorías configuradas
     */
    public function getCategoryStats(): array
    {
        if (!$this->hasCustomCategories()) {
            return $this->getPlayersStatsByCategory(); // Fallback al método existente
        }

        $stats = [];
        foreach ($this->getActiveCategories() as $category) {
            $stats[$category->name] = $category->getPlayerStats()['total'];
        }

        return $stats;
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
