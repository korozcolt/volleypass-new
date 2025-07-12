<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use App\Traits\HasSearch;
use App\Traits\HasValidation;
use App\Enums\DocumentStatus;
use App\Enums\MedicalStatus;

class MedicalCertificate extends Model implements HasMedia
{
    use SoftDeletes, InteractsWithMedia, LogsActivity, HasSearch, HasValidation;

    protected $fillable = [
        'player_id',
        'certificate_type',
        'certificate_number',
        'issue_date',
        'expires_at',
        'doctor_name',
        'doctor_license',
        'medical_institution',
        'institution_address',
        'medical_status',
        'medical_observations',
        'restrictions',
        'recommendations',
        'blood_pressure_systolic',
        'blood_pressure_diastolic',
        'heart_rate',
        'weight',
        'height',
        'additional_tests',
        'status',
        'reviewed_by',
        'reviewed_at',
        'review_notes',
        'certificate_file_path',
        'file_hash',
        'version',
        'is_current',
        'replaces_certificate_id',
        'expiry_notification_sent',
        'expiry_notification_at',
        'uploaded_by',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'expires_at' => 'date',
        'medical_status' => MedicalStatus::class,
        'status' => DocumentStatus::class,
        'restrictions' => 'array',
        'recommendations' => 'array',
        'blood_pressure_systolic' => 'decimal:2',
        'blood_pressure_diastolic' => 'decimal:2',
        'heart_rate' => 'integer',
        'weight' => 'decimal:2',
        'height' => 'decimal:2',
        'additional_tests' => 'array',
        'reviewed_at' => 'datetime',
        'version' => 'integer',
        'is_current' => 'boolean',
        'expiry_notification_sent' => 'boolean',
        'expiry_notification_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $searchable = [
        'certificate_number',
        'doctor_name',
        'doctor_license',
        'medical_institution',
        'player.user.name'
    ];

    // =======================
    // SPATIE CONFIGURATION
    // =======================

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'medical_status',
                'status',
                'expires_at',
                'is_current',
                'reviewed_by'
            ])
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => match ($eventName) {
                'created' => 'Certificado médico registrado',
                'updated' => 'Certificado médico actualizado',
                'deleted' => 'Certificado médico eliminado',
                default => "Certificado médico {$eventName}"
            });
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('certificate')
            ->acceptsMimeTypes(['application/pdf', 'image/jpeg', 'image/png'])
            ->singleFile();

        $this->addMediaCollection('additional_documents')
            ->acceptsMimeTypes(['application/pdf', 'image/jpeg', 'image/png']);
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(200)
            ->height(200)
            ->performOnCollections('certificate', 'additional_documents');
    }

    // =======================
    // RELACIONES
    // =======================

    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function replacedCertificate(): BelongsTo
    {
        return $this->belongsTo(self::class, 'replaces_certificate_id');
    }

    // =======================
    // ACCESSORS
    // =======================

    public function getPlayerNameAttribute(): string
    {
        return $this->player->user->full_name;
    }

    public function getIsValidAttribute(): bool
    {
        return $this->status === DocumentStatus::Approved &&
            !$this->is_expired &&
            $this->is_current;
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->expires_at->isPast();
    }

    public function getIsExpiringAttribute(): bool
    {
        return $this->expires_at->diffInDays(now()) <= 30 &&
            !$this->is_expired;
    }

    public function getBmiAttribute(): ?float
    {
        if (!$this->height || !$this->weight) {
            return null;
        }
        return round($this->weight / ($this->height * $this->height), 2);
    }

    public function getBloodPressureAttribute(): ?string
    {
        if (!$this->blood_pressure_systolic || !$this->blood_pressure_diastolic) {
            return null;
        }
        return $this->blood_pressure_systolic . '/' . $this->blood_pressure_diastolic;
    }

    public function getBloodPressureStatusAttribute(): string
    {
        if (!$this->blood_pressure_systolic || !$this->blood_pressure_diastolic) {
            return 'No registrado';
        }

        $systolic = $this->blood_pressure_systolic;
        $diastolic = $this->blood_pressure_diastolic;

        return match (true) {
            $systolic < 90 || $diastolic < 60 => 'Hipotensión',
            $systolic <= 120 && $diastolic <= 80 => 'Normal',
            $systolic <= 129 && $diastolic <= 80 => 'Elevada',
            $systolic <= 139 || $diastolic <= 89 => 'Hipertensión Grado 1',
            $systolic >= 140 || $diastolic >= 90 => 'Hipertensión Grado 2',
            default => 'No clasificado'
        };
    }

    public function getCertificateUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('certificate');
    }

    public function getDaysUntilExpiryAttribute(): int
    {
        return $this->expires_at->diffInDays(now(), false);
    }

    // =======================
    // SCOPES
    // =======================

    public function scopeCurrent($query)
    {
        return $query->where('is_current', true);
    }

    public function scopeValid($query)
    {
        return $query->where('status', DocumentStatus::Approved)
            ->where('is_current', true)
            ->where('expires_at', '>', now());
    }

    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<', now());
    }

    public function scopeExpiring($query, int $days = 30)
    {
        return $query->whereBetween('expires_at', [now(), now()->addDays($days)])
            ->where('is_current', true);
    }

    public function scopeByMedicalStatus($query, MedicalStatus $status)
    {
        return $query->where('medical_status', $status);
    }

    public function scopeByDoctor($query, string $doctorLicense)
    {
        return $query->where('doctor_license', $doctorLicense);
    }

    public function scopeNeedingNotification($query)
    {
        return $query->expiring(30)
            ->where('expiry_notification_sent', false);
    }

    public function scopeForPlayer($query, $playerId)
    {
        return $query->where('player_id', $playerId);
    }

    // =======================
    // MÉTODOS DE NEGOCIO
    // =======================

    public function approve(User $reviewer, ?string $notes = null): bool
    {
        if (!$this->status->canTransitionTo(DocumentStatus::Approved)) {
            throw new \Exception("No se puede aprobar un certificado en estado {$this->status->getLabel()}");
        }

        // Marcar otros certificados como no vigentes
        self::where('player_id', $this->player_id)
            ->where('id', '!=', $this->id)
            ->update(['is_current' => false]);

        return $this->update([
            'status' => DocumentStatus::Approved,
            'reviewed_by' => $reviewer->id,
            'reviewed_at' => now(),
            'review_notes' => $notes,
            'is_current' => true,
        ]);
    }

    public function reject(User $reviewer, string $reason, ?string $notes = null): bool
    {
        return $this->update([
            'status' => DocumentStatus::Rejected,
            'reviewed_by' => $reviewer->id,
            'reviewed_at' => now(),
            'review_notes' => $notes,
            'medical_observations' => $reason,
            'is_current' => false,
        ]);
    }

    public function markAsExpired(): bool
    {
        return $this->update([
            'status' => DocumentStatus::Expired,
            'is_current' => false
        ]);
    }

    public function sendExpiryNotification(): void
    {
        // Aquí se implementaría la lógica de notificación
        $this->update([
            'expiry_notification_sent' => true,
            'expiry_notification_at' => now(),
        ]);
    }

    public function isEligibleForSports(): bool
    {
        return $this->is_valid &&
            $this->medical_status->canPlay();
    }

    public function getRestrictionsForPosition(string $position): array
    {
        $restrictions = $this->restrictions ?? [];

        return array_filter($restrictions, function ($restriction) use ($position) {
            return !isset($restriction['positions']) ||
                in_array($position, $restriction['positions']);
        });
    }

    public function validateDoctorLicense(): bool
    {
        // Implementar validación específica de licencia médica colombiana
        return $this->validateColombianId($this->doctor_license);
    }

    public static function generateCertificateNumber(): string
    {
        $year = now()->year;
        $sequence = self::whereYear('created_at', $year)->count() + 1;

        return "CM-{$year}-" . str_pad($sequence, 6, '0', STR_PAD_LEFT);
    }
}
