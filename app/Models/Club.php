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
use App\Traits\HasStatus;
use App\Enums\UserStatus;

class Club extends Model implements HasMedia
{
    use SoftDeletes, InteractsWithMedia, LogsActivity;
    use HasSearch, HasStatus;

    protected $fillable = [
        'league_id',
        'name',
        'short_name',
        'description',
        'city_id',
        'address',
        'email',
        'phone',
        'website',
        'foundation_date',
        'colors',
        'history',
        'director_id',
        'status',
        'is_active',
        'configurations',
    ];

    protected $casts = [
        'foundation_date' => 'date',
        'is_active' => 'boolean',
        'status' => UserStatus::class,
        'configurations' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $searchable = ['name', 'short_name', 'email', 'city.name'];

    // =======================
    // SPATIE CONFIGURATION
    // =======================

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'status', 'is_active', 'director_id'])
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => match($eventName) {
                'created' => 'Club registrado en la liga',
                'updated' => 'Información del club actualizada',
                'deleted' => 'Club eliminado de la liga',
                default => "Club {$eventName}"
            });
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('logo')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/svg+xml'])
            ->singleFile();

        $this->addMediaCollection('photos')
            ->acceptsMimeTypes(['image/jpeg', 'image/png']);

        $this->addMediaCollection('documents')
            ->acceptsMimeTypes(['application/pdf', 'image/jpeg', 'image/png']);
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(150)
            ->height(150)
            ->performOnCollections('logo', 'photos');

        $this->addMediaConversion('card')
            ->width(400)
            ->height(300)
            ->performOnCollections('logo', 'photos');
    }

    // =======================
    // RELACIONES
    // =======================

    public function league(): BelongsTo
    {
        return $this->belongsTo(League::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function director(): BelongsTo
    {
        return $this->belongsTo(User::class, 'director_id');
    }

    public function players(): HasMany
    {
        return $this->hasMany(Player::class, 'current_club_id');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'club_id');
    }

    public function coaches(): HasMany
    {
        return $this->hasMany(Coach::class);
    }

    public function teams(): HasMany
    {
        return $this->hasMany(Team::class);
    }

    // =======================
    // ACCESSORS
    // =======================

    public function getLogoUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('logo');
    }

    public function getLogoThumbAttribute(): ?string
    {
        return $this->getFirstMediaUrl('logo', 'thumb');
    }

    public function getFullNameAttribute(): string
    {
        return $this->short_name ?
            "{$this->name} ({$this->short_name})" :
            $this->name;
    }

    public function getActivePlayersCountAttribute(): int
    {
        return $this->players()->whereHas('user', function($query) {
            $query->where('status', UserStatus::Active);
        })->count();
    }

    public function getCoachesCountAttribute(): int
    {
        return $this->coaches()->count();
    }

    public function getEstablishedYearsAttribute(): ?int
    {
        return $this->foundation_date ?
            $this->foundation_date->diffInYears(now()) : null;
    }

    public function getLocationAttribute(): string
    {
        $location = $this->city->name;
        if ($this->address) {
            $location .= " - {$this->address}";
        }
        return $location;
    }

    // =======================
    // SCOPES
    // =======================

    public function scopeInLeague($query, $leagueId)
    {
        return $query->where('league_id', $leagueId);
    }

    public function scopeInCity($query, $cityId)
    {
        return $query->where('city_id', $cityId);
    }

    public function scopeWithDirector($query)
    {
        return $query->whereNotNull('director_id');
    }

    public function scopeWithoutDirector($query)
    {
        return $query->whereNull('director_id');
    }

    // =======================
    // MÉTODOS DE GESTIÓN
    // =======================

    public function assignDirector(User $user): bool
    {
        // Verificar que el usuario pueda ser director
        if (!$user->hasRole('ClubDirector')) {
            throw new \Exception('El usuario debe tener el rol de Director de Club');
        }

        // Quitar director anterior si existe
        if ($this->director_id) {
            $oldDirector = User::find($this->director_id);
            if ($oldDirector) {
                $oldDirector->update(['club_id' => null]);
            }
        }

        // Asignar nuevo director
        $this->update(['director_id' => $user->id]);
        $user->update(['club_id' => $this->id]);

        return true;
    }

    public function removeDirector(): bool
    {
        if ($this->director_id) {
            $director = User::find($this->director_id);
            if ($director) {
                $director->update(['club_id' => null]);
            }
            $this->update(['director_id' => null]);
        }

        return true;
    }

    public function canRegisterPlayers(): bool
    {
        return $this->is_active &&
               $this->status === UserStatus::Active &&
               $this->director_id !== null;
    }

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
    // MÉTODOS DE ESTADÍSTICAS
    // =======================

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

    public function getPlayersStatsByPosition(): array
    {
        return $this->players()
            ->join('users', 'players.user_id', '=', 'users.id')
            ->where('users.status', UserStatus::Active)
            ->selectRaw('position, COUNT(*) as count')
            ->groupBy('position')
            ->pluck('count', 'position')
            ->toArray();
    }
}
