<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
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
    use HasFactory, SoftDeletes, InteractsWithMedia, LogsActivity;
    use HasSearch, HasStatus;

    protected $fillable = [
        'league_id',
        'nombre',
        'nombre_corto',
        'description',
        'ciudad_id',
        'country_id',
        'departamento_id',
        'direccion',
        'email',
        'telefono',
        'website',
        'fundacion',
        'colors',
        'history',
        'director_id',
        'status',
        'is_active',
        'es_federado',
        'tipo_federacion',
        'codigo_federacion',
        'vencimiento_federacion',
        'observaciones_federacion',
        'created_by',
        'updated_by',
        'configurations',
        'settings',
        'notes',
    ];

    protected $casts = [
        'fundacion' => 'date',
        'vencimiento_federacion' => 'date',
        'es_federado' => 'boolean',
        'is_active' => 'boolean',
        'status' => UserStatus::class,
        'configurations' => 'array',
        'settings' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $searchable = ['nombre', 'nombre_corto', 'email', 'ciudad.name', 'departamento.name'];

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

    public function ciudad(): BelongsTo
    {
        return $this->belongsTo(City::class, 'ciudad_id');
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function departamento(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'departamento_id');
    }

    public function director(): BelongsTo
    {
        return $this->belongsTo(User::class, 'director_id');
    }

    public function jugadoras(): HasMany
    {
        return $this->hasMany(Player::class, 'club_id');
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

    public function directivos()
    {
        return $this->belongsToMany(User::class, 'club_directivos')
            ->withPivot(['rol', 'activo', 'fecha_inicio', 'fecha_fin'])
            ->withTimestamps();
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
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
        return $this->nombre_corto ?
            "{$this->nombre} ({$this->nombre_corto})" :
            $this->nombre;
    }

    public function getAnosFuncionamientoAttribute(): int
    {
        return $this->fundacion ? $this->fundacion->diffInYears(now()) : 0;
    }

    public function getTipoFederacionFormattedAttribute(): string
    {
        return $this->tipo_federacion ? ucfirst($this->tipo_federacion) : '';
    }

    public function getFederacionExpiradaAttribute(): bool
    {
        return $this->es_federado && $this->vencimiento_federacion && $this->vencimiento_federacion->isPast();
    }

    public function getActivePlayersCountAttribute(): int
    {
        return $this->players()->whereHas('user', function($query) {
            $query->where('status', UserStatus::Active);
        })->count();
    }

    public function getJugadorasActivasCountAttribute(): int
    {
        return $this->jugadoras()->where('activa', true)->count();
    }

    public function getJugadorasFederadasCountAttribute(): int
    {
        return $this->jugadoras()->where('activa', true)->where('es_federada', true)->count();
    }

    public function getDirectivosActivosCountAttribute(): int
    {
        return $this->directivos()->wherePivot('activo', true)->count();
    }

    public function getCoachesCountAttribute(): int
    {
        return $this->coaches()->count();
    }

    public function getEstablishedYearsAttribute(): ?int
    {
        return $this->fundacion ?
            $this->fundacion->diffInYears(now()) : null;
    }

    public function getLocationAttribute(): string
    {
        $location = $this->ciudad->nombre;
        if ($this->direccion) {
            $location .= " - {$this->direccion}";
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
        return $query->where('ciudad_id', $cityId);
    }

    public function scopeWithDirector($query)
    {
        return $query->whereNotNull('director_id');
    }

    public function scopeWithoutDirector($query)
    {
        return $query->whereNull('director_id');
    }

    public function scopeFederados($query)
    {
        return $query->where('es_federado', true);
    }

    public function scopeNoFederados($query)
    {
        return $query->where('es_federado', false);
    }

    public function scopePorDepartamento($query, $departamentoId)
    {
        return $query->where('departamento_id', $departamentoId);
    }

    public function scopePorTipoFederacion($query, $tipo)
    {
        return $query->where('tipo_federacion', $tipo);
    }

    public function scopeFederacionVigente($query)
    {
        return $query->where('es_federado', true)
            ->where(function($q) {
                $q->whereNull('vencimiento_federacion')
                  ->orWhere('vencimiento_federacion', '>=', now());
            });
    }

    public function scopeFederacionExpirada($query)
    {
        return $query->where('es_federado', true)
            ->where('vencimiento_federacion', '<', now());
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
