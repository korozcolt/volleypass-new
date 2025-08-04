<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use App\Traits\HasSearch;
use App\Traits\HasStatus;
use App\Enums\PlayerPosition;
use App\Enums\PlayerCategory;
use App\Enums\MedicalStatus;
use App\Enums\UserStatus;
use App\Enums\FederationStatus;

class Player extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;
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
        // Campos de federación
        'federation_status',
        'federation_date',
        'federation_expires_at',
        'federation_payment_id',
        'federation_notes',
    ];

    protected $casts = [
        'height' => 'decimal:2',
        'weight' => 'decimal:2',
        'position' => PlayerPosition::class,
        'category' => PlayerCategory::class,
        'status' => UserStatus::class,
        'medical_status' => MedicalStatus::class,
        'federation_status' => FederationStatus::class,
        'debut_date' => 'date',
        'retirement_date' => 'date',
        'federation_date' => 'date',
        'federation_expires_at' => 'date',
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

    public function documents(): HasMany
    {
        return $this->hasMany(PlayerDocument::class);
    }

    public function federationPayment(): BelongsTo
    {
        return $this->belongsTo(Payment::class, 'federation_payment_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function sentPayments()
    {
        return $this->morphMany(Payment::class, 'payer');
    }

    public function league()
    {
        return $this->belongsTo(League::class);
    }

    public function club()
    {
        return $this->belongsTo(Club::class);
    }

    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class, 'team_players')
            ->withPivot(['jersey_number', 'position', 'is_captain', 'joined_at', 'left_at'])
            ->withTimestamps();
    }

    public function team()
    {
        // Get the current/active team (most recent team without left_at date)
        return $this->teams()->wherePivot('left_at', null)->latest('team_players.joined_at');
    }

    public function currentTeam()
    {
        // Alternative method to get current team as a single model
        return $this->teams()->wherePivot('left_at', null)->latest('team_players.joined_at')->first();
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

    public function scopeFederated($query)
    {
        return $query->where('federation_status', FederationStatus::Federated);
    }

    public function scopeNotFederated($query)
    {
        return $query->where('federation_status', FederationStatus::NotFederated);
    }

    public function scopeFederationExpired($query)
    {
        return $query->where('federation_expires_at', '<', now());
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
    // MÉTODOS DE FEDERACIÓN
    // =======================

    public function updateFederationStatus(FederationStatus $status, string $reason = null): bool
    {
        $oldStatus = $this->federation_status;

        $this->update(['federation_status' => $status]);

        if ($reason) {
            $this->update(['federation_notes' => ($this->federation_notes ? $this->federation_notes . "\n\n" : '') . now()->format('Y-m-d H:i') . ": {$reason}"]);
        }

        $this->logActivity(
            'federation_status_changed',
            "Estado de federación cambiado de {$oldStatus->getLabel()} a {$status->getLabel()}"
        );

        return true;
    }

    public function federateWithPayment(Payment $payment): bool
    {
        $this->update([
            'federation_status' => FederationStatus::Federated,
            'federation_date' => now(),
            'federation_expires_at' => now()->addYear(),
            'federation_payment_id' => $payment->id,
        ]);

        return true;
    }

    public function isFederated(): bool
    {
        return $this->federation_status === FederationStatus::Federated &&
            $this->federation_expires_at &&
            $this->federation_expires_at->isFuture();
    }

    public function canPlayInFederatedTournaments(): bool
    {
        return $this->isFederated() && $this->can_play;
    }

    public function getFederationStatusBadgeAttribute(): array
    {
        return [
            'label' => $this->federation_status->getLabel(),
            'color' => $this->federation_status->getColor(),
            'icon' => $this->federation_status->getIcon(),
        ];
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
