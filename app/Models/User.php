<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Traits\HasRoles;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use App\Traits\HasSearch;
use App\Traits\HasValidation;
use App\Enums\UserStatus;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements HasMedia, FilamentUser
{
    use HasFactory, Notifiable, SoftDeletes;
    use HasRoles, InteractsWithMedia, LogsActivity; // Spatie traits
    use HasSearch, HasValidation; // Nuestros traits personalizados
    use HasApiTokens;

    protected $fillable = [
        'name',
        'email',
        'password',
        'document_type',
        'document_number',
        'first_name',
        'last_name',
        'birth_date',
        'gender',
        'phone',
        'phone_secondary',
        'address',
        'country_id',
        'department_id',
        'city_id',
        'status',
        'league_id',
        'club_id',
        'preferences',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'birth_date' => 'date',
            'status' => UserStatus::class,
            'preferences' => 'array',
            'last_login_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    protected $searchable = ['name', 'email', 'first_name', 'last_name', 'document_number'];

    // =======================
    // EVENTOS DEL MODELO
    // =======================

    protected static function boot()
    {
        parent::boot();

        // Crear perfil automáticamente al crear usuario
        static::created(function ($user) {
            $user->profile()->create([]);
        });
    }

    // =======================
    // SPATIE ACTIVITY LOG CONFIGURATION
    // =======================

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'name',
                'email',
                'first_name',
                'last_name',
                'document_number',
                'status',
                'league_id',
                'club_id'
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => match ($eventName) {
                'created' => 'Usuario registrado en el sistema',
                'updated' => 'Información de usuario actualizada',
                'deleted' => 'Usuario eliminado del sistema',
                default => "Usuario {$eventName}"
            });
    }

    // =======================
    // SPATIE MEDIA LIBRARY CONFIGURATION
    // =======================

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('avatar')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp'])
            ->singleFile();

        $this->addMediaCollection('documents')
            ->acceptsMimeTypes(['application/pdf', 'image/jpeg', 'image/png']);
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(150)
            ->height(150)
            ->sharpen(10)
            ->performOnCollections('avatar');

        $this->addMediaConversion('profile')
            ->width(300)
            ->height(300)
            ->quality(90)
            ->performOnCollections('avatar');
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

    public function league(): BelongsTo
    {
        return $this->belongsTo(League::class);
    }

    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }

    // NUEVA RELACIÓN: Perfil extendido
    public function profile(): HasOne
    {
        return $this->hasOne(UserProfile::class);
    }

    // Relación jugadora (si es jugadora)
    public function player(): HasOne
    {
        return $this->hasOne(Player::class);
    }

    // Relación entrenador (si es entrenador)
    public function coach(): HasOne
    {
        return $this->hasOne(Coach::class);
    }

    // Relación médico deportivo (si es médico)
    public function sportsDoctor(): HasOne
    {
        return $this->hasOne(SportsDoctor::class);
    }

    // Clubes que dirige (si es director)
    public function directedClubs()
    {
        return $this->hasMany(Club::class, 'director_id');
    }

    // =======================
    // ACCESSORS MEJORADOS
    // =======================

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}") ?: $this->name;
    }

    public function getDisplayNameAttribute(): string
    {
        // Si tiene perfil con nickname, usar ese
        if ($this->profile && $this->profile->nickname) {
            return $this->profile->nickname;
        }
        return $this->full_name;
    }

    public function getAgeAttribute(): ?int
    {
        return $this->birth_date ? $this->calculateAge($this->birth_date->format('Y-m-d')) : null;
    }

    public function getStatusBadgeAttribute(): string
    {
        return $this->status->getLabelHtml();
    }

    public function getAvatarUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('avatar', 'profile');
    }

    public function getAvatarThumbAttribute(): ?string
    {
        return $this->getFirstMediaUrl('avatar', 'thumb');
    }

    public function getLocationAttribute(): string
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
    // MÉTODOS DE ROLES ESPECÍFICOS
    // =======================

    public function isPlayer(): bool
    {
        return $this->hasRole('Player');
    }

    public function isCoach(): bool
    {
        return $this->hasRole('Coach');
    }

    public function isClubDirector(): bool
    {
        return $this->hasRole('ClubDirector');
    }

    public function isLeagueAdmin(): bool
    {
        return $this->hasRole('LeagueAdmin');
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasRole('SuperAdmin');
    }

    public function isSportsDoctor(): bool
    {
        return $this->hasRole('SportsDoctor');
    }

    public function isVerifier(): bool
    {
        return $this->hasRole('Verifier');
    }

    // =======================
    // MÉTODOS DE AUTORIZACIÓN
    // =======================

    public function canAccessClub($club): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        if ($this->isLeagueAdmin() && $this->league_id === $club->league_id) {
            return true;
        }

        return $this->club_id === $club->id;
    }

    public function canManagePlayer(Player $player): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        if ($this->isLeagueAdmin() && $this->league_id === $player->currentClub?->league_id) {
            return true;
        }

        if ($this->isClubDirector() && $this->club_id === $player->current_club_id) {
            return true;
        }

        // La jugadora puede gestionar su propio perfil
        return $this->id === $player->user_id;
    }

    public function canVerifyCards(): bool
    {
        return $this->hasAnyRole(['SuperAdmin', 'LeagueAdmin', 'Verifier']);
    }

    // =======================
    // SCOPES
    // =======================

    public function scopeActive($query)
    {
        return $query->where('status', UserStatus::Active);
    }

    public function scopeInLeague($query, $leagueId)
    {
        return $query->where('league_id', $leagueId);
    }

    public function scopeInClub($query, $clubId)
    {
        return $query->where('club_id', $clubId);
    }

    public function scopePlayers($query)
    {
        return $query->whereHas('roles', function ($q) {
            $q->where('name', 'Player');
        });
    }

    public function scopeCoaches($query)
    {
        return $query->whereHas('roles', function ($q) {
            $q->where('name', 'Coach');
        });
    }

    public function scopeClubDirectors($query)
    {
        return $query->whereHas('roles', function ($q) {
            $q->where('name', 'ClubDirector');
        });
    }

    public function scopeWithProfile($query)
    {
        return $query->with('profile');
    }

    // =======================
    // MÉTODOS DE GESTIÓN DE PERFIL
    // =======================

    public function updateLastLogin(): void
    {
        $this->update([
            'last_login_at' => now(),
            'last_login_ip' => request()->ip(),
        ]);
    }

    public function uploadAvatar($file): void
    {
        $this->addMediaFromRequest('avatar')
            ->toMediaCollection('avatar');
    }

    public function createPlayerProfile(array $data): Player
    {
        // Asignar rol de jugadora si no lo tiene
        if (!$this->hasRole('Player')) {
            $this->assignRole('Player');
        }

        return $this->player()->create($data);
    }

    public function createCoachProfile(array $data): Coach
    {
        if (!$this->hasRole('Coach')) {
            $this->assignRole('Coach');
        }

        return $this->coach()->create($data);
    }

    // =======================
    // MÉTODOS DE VALIDACIÓN
    // =======================

    public function hasCompleteProfile(): bool
    {
        return !empty($this->first_name) &&
            !empty($this->last_name) &&
            !empty($this->document_number) &&
            !empty($this->birth_date) &&
            !empty($this->phone) &&
            $this->profile !== null;
    }

    public function canBeActivated(): bool
    {
        return $this->hasCompleteProfile() &&
            $this->email_verified_at !== null &&
            $this->status !== UserStatus::Blocked;
    }

    public function needsProfileCompletion(): array
    {
        $missing = [];

        if (empty($this->first_name)) $missing[] = 'Nombre';
        if (empty($this->last_name)) $missing[] = 'Apellido';
        if (empty($this->document_number)) $missing[] = 'Número de documento';
        if (empty($this->birth_date)) $missing[] = 'Fecha de nacimiento';
        if (empty($this->phone)) $missing[] = 'Teléfono';
        if (!$this->profile) $missing[] = 'Perfil extendido';
        if (!$this->email_verified_at) $missing[] = 'Verificación de email';

        return $missing;
    }

    // =======================
    // MÉTODOS DE ESTADÍSTICAS
    // =======================

    public function getActivitySummary(): array
    {
        $activities = $this->activities()
            ->where('created_at', '>=', now()->subDays(30))
            ->get();

        return [
            'total_activities' => $activities->count(),
            'recent_logins' => $activities->where('description', 'like', '%login%')->count(),
            'profile_updates' => $activities->where('description', 'like', '%updated%')->count(),
            'last_activity' => $activities->first()?->created_at,
        ];
    }

    // =======================
    // MÉTODOS DE UTILIDAD
    // =======================

    public function getRoleDisplayName(): string
    {
        $roleNames = $this->roles->pluck('name')->toArray();

        return match (true) {
            in_array('SuperAdmin', $roleNames) => 'Super Administrador',
            in_array('LeagueAdmin', $roleNames) => 'Administrador de Liga',
            in_array('ClubDirector', $roleNames) => 'Director de Club',
            in_array('SportsDoctor', $roleNames) => 'Médico Deportivo',
            in_array('Coach', $roleNames) => 'Entrenador',
            in_array('Player', $roleNames) => 'Jugadora',
            in_array('Verifier', $roleNames) => 'Verificador',
            default => 'Usuario'
        };
    }

    public function getPermissionLevel(): int
    {
        $roles = $this->roles->pluck('name')->toArray();

        if (in_array('SuperAdmin', $roles)) return 10;
        if (in_array('LeagueAdmin', $roles)) return 8;
        if (in_array('ClubDirector', $roles)) return 6;
        if (in_array('SportsDoctor', $roles)) return 5;
        if (in_array('Coach', $roles)) return 4;
        if (in_array('Verifier', $roles)) return 3;
        if (in_array('Player', $roles)) return 1;

        return 0;
    }

    public function hasAppInstalled(): bool
    {
        // Verificar si tiene token FCM o dispositivo registrado
        return !empty($this->fcm_token) ||
            $this->profile?->metadata['has_mobile_app'] ?? false;
    }

    public function prefersWhatsApp(): bool
    {
        // Verificar preferencias o si tiene WhatsApp configurado
        return !empty($this->whatsapp_number) ||
            $this->notificationPreferences()
            ->where('channel', 'whatsapp')
            ->where('is_enabled', true)
            ->exists();
    }

    public function getWhatsappNumberAttribute(): ?string
    {
        // Buscar número de WhatsApp en preferencias o profile
        return $this->profile?->metadata['whatsapp_number'] ??
            $this->phone;
    }

    public function getFcmTokenAttribute(): ?string
    {
        // Token FCM almacenado en preferencias
        return $this->profile?->metadata['fcm_token'] ?? null;
    }

    /**
     * Relación con preferencias de notificación
     */
    public function notificationPreferences()
    {
        return $this->hasMany(NotificationPreference::class);
    }

    /**
     * Obtener canales preferidos para un tipo de notificación
     */
    public function getPreferredChannels(string $notificationType): array
    {
        $preferences = $this->notificationPreferences()
            ->where('notification_type', $notificationType)
            ->where('is_enabled', true)
            ->pluck('channel')
            ->toArray();

        // Siempre incluir email como fallback
        if (empty($preferences)) {
            return ['mail'];
        }

        return $preferences;
    }

    public function createApiToken(string $name, array $abilities = ['*']): string
    {
        // Solo usuarios con roles específicos pueden crear tokens API
        if (!$this->hasAnyRole(['Verifier', 'LeagueAdmin', 'SuperAdmin'])) {
            throw new \Exception('Usuario no autorizado para tokens API');
        }

        return $this->createToken($name, $abilities)->plainTextToken;
    }

    // =======================
    // FILAMENT AUTHORIZATION
    // =======================

    public function canAccessPanel(Panel $panel): bool
    {
        // Para el panel admin, permitir acceso a usuarios activos
        if ($panel->getId() === 'admin') {
            // Verificar roles administrativos
            $adminRoles = [
                'admin',
                'super_admin',
                'league_director',
                'club_director',
                'coach',
                'referee'
            ];

            foreach ($adminRoles as $role) {
                if ($this->hasRole($role)) {
                    return true;
                }
            }
        }

        return false;
    }
}
