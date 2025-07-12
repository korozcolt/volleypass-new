<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use App\Traits\HasSearch;
use App\Traits\HasStatus;
use App\Enums\PlayerPosition;
use App\Enums\PlayerCategory;
use App\Enums\MedicalStatus;
use App\Enums\UserStatus;

class Player extends Model
{
    use SoftDeletes, LogsActivity;
    use HasSearch, HasStatus;

    protected $fillable = [
        'user_id',
        'current_club_id',
        'jersey_number',
        'position',
        'category',
        'height',
        'weight',
        'dominant_hand',
        'status',
        'medical_status',
        'debut_date',
        'retirement_date',
        'notes',
        'achievements',
        'preferences',
        'is_eligible',
        'eligibility_checked_at',
        'eligibility_checked_by',
    ];

    protected $casts = [
        'height' => 'decimal:2',
        'weight' => 'decimal:2',
        'position' => PlayerPosition::class,
        'category' => PlayerCategory::class,
        'status' => UserStatus::class,
        'medical_status' => MedicalStatus::class,
        'debut_date' => 'date',
        'retirement_date' => 'date',
        'achievements' => 'array',
        'preferences' => 'array',
        'is_eligible' => 'boolean',
        'eligibility_checked_at' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $searchable = [
        'user.name',
        'user.first_name',
        'user.last_name',
        'user.document_number',
        'jersey_number'
    ];

    // =======================
    // SPATIE ACTIVITY LOG
    // =======================

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'current_club_id',
                'position',
                'category',
                'status',
                'medical_status',
                'is_eligible',
                'jersey_number'
            ])
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => match ($eventName) {
                'created' => 'Perfil deportivo de jugadora creado',
                'updated' => 'Perfil deportivo actualizado',
                'deleted' => 'Perfil deportivo eliminado',
                default => "Jugadora {$eventName}"
            });
    }

    // =======================
    // RELACIONES
    // =======================

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function currentClub(): BelongsTo
    {
        return $this->belongsTo(Club::class, 'current_club_id');
    }

    public function eligibilityChecker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'eligibility_checked_by');
    }

    public function playerCards(): HasMany
    {
        return $this->hasMany(PlayerCard::class);
    }

    public function statistics(): HasMany
    {
        return $this->hasMany(PlayerStatistic::class);
    }

    public function seasonStatistics(): HasMany
    {
        return $this->hasMany(PlayerSeasonStatistic::class);
    }

    public function awards(): HasMany
    {
        return $this->hasMany(PlayerAward::class);
    }

    public function transfers(): HasMany
    {
        return $this->hasMany(PlayerTransfer::class);
    }

    public function injuries(): HasMany
    {
        return $this->hasMany(Injury::class);
    }

    // =======================
    // ACCESSORS
    // =======================

    public function getFullNameAttribute(): string
    {
        return $this->user->full_name;
    }

    public function getDisplayNameAttribute(): string
    {
        $name = $this->user->full_name;
        if ($this->jersey_number) {
            $name .= " (#{$this->jersey_number})";
        }
        return $name;
    }

    public function getAgeAttribute(): ?int
    {
        return $this->user->age;
    }

    public function getYearsPlayingAttribute(): ?int
    {
        return $this->debut_date ? $this->debut_date->diffInYears(now()) : null;
    }

    public function getIsActiveAttribute(): bool
    {
        return $this->status === UserStatus::Active &&
            $this->user->status === UserStatus::Active;
    }

    public function getCanPlayAttribute(): bool
    {
        return $this->is_active &&
            $this->is_eligible &&
            $this->medical_status->canPlay();
    }

    public function getCurrentCardAttribute(): ?PlayerCard
    {
        return $this->playerCards()
            ->where('status', 'active')
            ->where('expires_at', '>', now())
            ->first();
    }

    public function getBmiAttribute(): ?float
    {
        if (!$this->height || !$this->weight) {
            return null;
        }

        return round($this->weight / ($this->height * $this->height), 2);
    }

    // =======================
    // SCOPES
    // =======================

    public function scopeInClub($query, $clubId)
    {
        return $query->where('current_club_id', $clubId);
    }

    public function scopeByPosition($query, $position)
    {
        return $query->where('position', $position);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeEligible($query)
    {
        return $query->where('is_eligible', true);
    }

    public function scopeMedicallyFit($query)
    {
        return $query->where('medical_status', MedicalStatus::Fit);
    }

    public function scopeCanPlay($query)
    {
        return $query->active()
            ->eligible()
            ->whereIn('medical_status', [MedicalStatus::Fit, MedicalStatus::Restricted]);
    }

    public function scopeWithCard($query)
    {
        return $query->whereHas('playerCards', function ($q) {
            $q->where('status', 'active')
                ->where('expires_at', '>', now());
        });
    }

    // =======================
    // MÉTODOS DE GESTIÓN
    // =======================

    public function transferToClub(Club $newClub, string $reason = null): bool
    {
        $oldClub = $this->currentClub;

        // Crear registro de transferencia
        PlayerTransfer::create([
            'player_id' => $this->id,
            'from_club_id' => $this->current_club_id,
            'to_club_id' => $newClub->id,
            'transfer_date' => now(),
            'reason' => $reason,
            'status' => 'completed'
        ]);

        // Actualizar club actual
        $this->update(['current_club_id' => $newClub->id]);

        // Actualizar usuario
        $this->user->update(['club_id' => $newClub->id]);

        return true;
    }

    public function updateEligibility(bool $eligible, User $checker, string $reason = null): bool
    {
        $this->update([
            'is_eligible' => $eligible,
            'eligibility_checked_at' => now(),
            'eligibility_checked_by' => $checker->id,
        ]);

        if ($reason) {
            $this->logActivity(
                'eligibility_changed',
                "Elegibilidad cambiada a " . ($eligible ? 'APTA' : 'NO APTA') . ": {$reason}"
            );
        }

        return true;
    }

    public function updateMedicalStatus(MedicalStatus $status, string $reason = null): bool
    {
        $oldStatus = $this->medical_status;

        $this->update(['medical_status' => $status]);

        if ($reason) {
            $this->logActivity(
                'medical_status_changed',
                "Estado médico cambiado de {$oldStatus->getLabel()} a {$status->getLabel()}: {$reason}"
            );
        }

        return true;
    }

    public function retire(string $reason = null): bool
    {
        $this->update([
            'status' => UserStatus::Inactive,
            'retirement_date' => now(),
            'is_eligible' => false,
        ]);

        if ($reason) {
            $this->update(['notes' => ($this->notes ? $this->notes . "\n\n" : '') . "RETIRO: {$reason}"]);
        }

        return true;
    }

    // =======================
    // MÉTODOS DE VALIDACIÓN
    // =======================

    public function canWearJerseyNumber(string $number): bool
    {
        if (!$this->current_club_id) {
            return true;
        }

        return !Player::where('current_club_id', $this->current_club_id)
            ->where('jersey_number', $number)
            ->where('id', '!=', $this->id)
            ->exists();
    }

    public function isInCorrectCategory(): bool
    {
        if (!$this->user->age) {
            return false;
        }

        $ageRange = $this->category->getAgeRange();
        return $this->user->age >= $ageRange[0] && $this->user->age <= $ageRange[1];
    }

    // =======================
    // MÉTODOS DE ESTADÍSTICAS
    // =======================

    public function getCareerStats(): array
    {
        return [
            'years_playing' => $this->years_playing,
            'clubs_played' => $this->transfers()->distinct('to_club_id')->count() + 1,
            'total_awards' => $this->awards()->count(),
            'injuries' => $this->injuries()->count(),
        ];
    }

    public function getCurrentSeasonStats(): ?PlayerSeasonStatistic
    {
        return $this->seasonStatistics()
            ->where('season', now()->year)
            ->first();
    }
}
