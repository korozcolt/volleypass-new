<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use App\Traits\HasSearch;
use App\Enums\CardStatus;
use App\Enums\MedicalStatus;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class PlayerCard extends Model
{
    use SoftDeletes, LogsActivity, HasSearch;

    protected $fillable = [
        'player_id',
        'card_number',
        'qr_code',
        'verification_token',
        'status',
        'issued_at',
        'expires_at',
        'season',
        'medical_status',
        'medical_check_date',
        'medical_approved_by',
        'issued_by',
        'approved_by',
        'approved_at',
        'restrictions',
        'card_design_data',
        'last_verified_at',
        'verification_count',
        'verification_locations',
        'version',
        'replaces_card_id',
        'replacement_reason',
    ];

    protected $casts = [
        'status' => CardStatus::class,
        'medical_status' => MedicalStatus::class,
        'issued_at' => 'date',
        'expires_at' => 'date',
        'medical_check_date' => 'date',
        'approved_at' => 'datetime',
        'last_verified_at' => 'datetime',
        'restrictions' => 'array',
        'card_design_data' => 'array',
        'verification_locations' => 'array',
        'season' => 'integer',
        'verification_count' => 'integer',
        'version' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $searchable = [
        'card_number',
        'player.user.name',
        'player.user.document_number'
    ];

    // =======================
    // SPATIE CONFIGURATION
    // =======================

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status', 'medical_status', 'expires_at', 'verification_count'])
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => match ($eventName) {
                'created' => 'Carnet digital generado',
                'updated' => 'Carnet actualizado',
                'deleted' => 'Carnet eliminado',
                default => "Carnet {$eventName}"
            });
    }

    // =======================
    // RELACIONES
    // =======================

    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }

    public function issuer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function medicalApprover(): BelongsTo
    {
        return $this->belongsTo(User::class, 'medical_approved_by');
    }

    public function replacedCard(): BelongsTo
    {
        return $this->belongsTo(self::class, 'replaces_card_id');
    }

    public function qrScanLogs(): HasMany
    {
        return $this->hasMany(QrScanLog::class);
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
        return $this->status->allowsPlay() &&
            !$this->is_expired;
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

    public function getQrImageAttribute(): string
    {
        return QrCode::size(200)
            ->format('png')
            ->generate($this->getQrVerificationUrl());
    }

    public function getStatusBadgeAttribute(): string
    {
        return $this->status->getLabelHtml();
    }

    // =======================
    // SCOPES
    // =======================

    public function scopeActive($query)
    {
        return $query->where('status', CardStatus::Active);
    }

    public function scopeValid($query)
    {
        return $query->whereIn('status', [CardStatus::Active, CardStatus::Medical_Restriction])
            ->where('expires_at', '>', now());
    }

    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<', now());
    }

    public function scopeExpiring($query, int $days = 30)
    {
        return $query->whereBetween('expires_at', [now(), now()->addDays($days)]);
    }

    public function scopeForSeason($query, int $season)
    {
        return $query->where('season', $season);
    }

    public function scopeByMedicalStatus($query, MedicalStatus $status)
    {
        return $query->where('medical_status', $status);
    }

    // =======================
    // MÉTODOS DE NEGOCIO
    // =======================

    public static function generateCardNumber(Player $player): string
    {
        $year = now()->year;
        $clubCode = str_pad($player->current_club_id, 3, '0', STR_PAD_LEFT);
        $sequence = self::where('season', $year)->count() + 1;

        return "VP-{$year}-{$clubCode}-" . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    public function generateQrCode(): void
    {
        $this->qr_code = hash(
            'sha256',
            $this->card_number .
                $this->player_id .
                now()->timestamp .
                config('app.key')
        );

        $this->verification_token = hash(
            'sha256',
            $this->qr_code .
                $this->player_id .
                'verification_token'
        );

        $this->save();
    }

    public function getQrVerificationUrl(): string
    {
        return route('api.verify-qr', [
            'qr' => $this->qr_code,
            'token' => $this->verification_token
        ]);
    }

    public function getVerificationStatus(): array
    {
        if ($this->is_expired) {
            return [
                'status' => 'error',
                'message' => 'Carnet vencido',
                'can_play' => false,
                'expires_at' => $this->expires_at->format('Y-m-d'),
                'color' => 'red'
            ];
        }

        return $this->status->getVerificationResult();
    }

    public function recordVerification(User $verifier, array $additionalData = []): void
    {
        $this->increment('verification_count');
        $this->update(['last_verified_at' => now()]);

        // Registrar ubicación si se proporciona
        if (isset($additionalData['location'])) {
            $locations = $this->verification_locations ?? [];
            $locations[] = [
                'location' => $additionalData['location'],
                'verified_at' => now()->toISOString(),
                'verifier' => $verifier->name
            ];

            // Mantener solo las últimas 10 ubicaciones
            $this->update([
                'verification_locations' => array_slice($locations, -10)
            ]);
        }
    }

    public function suspend(string $reason, User $suspendedBy): bool
    {
        $this->update([
            'status' => CardStatus::Suspended,
            'restrictions' => array_merge($this->restrictions ?? [], [
                'suspended_at' => now()->toISOString(),
                'suspended_by' => $suspendedBy->name,
                'suspension_reason' => $reason
            ])
        ]);

        $this->logActivity('card_suspended', "Carnet suspendido: {$reason}");
        return true;
    }

    public function renew(int $daysToAdd = 365): bool
    {
        return $this->update([
            'expires_at' => $this->expires_at->addDays($daysToAdd),
            'status' => CardStatus::Active
        ]);
    }

    public function replace(string $reason, User $replacedBy): self
    {
        // Marcar el carnet actual como reemplazado
        $this->update([
            'status' => CardStatus::Replaced,
            'replacement_reason' => $reason
        ]);

        // Crear nuevo carnet
        $newCardData = $this->toArray();
        unset($newCardData['id'], $newCardData['created_at'], $newCardData['updated_at']);

        $newCardData['card_number'] = self::generateCardNumber($this->player);
        $newCardData['version'] = $this->version + 1;
        $newCardData['replaces_card_id'] = $this->id;
        $newCardData['status'] = CardStatus::Active;
        $newCardData['issued_at'] = now();
        $newCardData['issued_by'] = $replacedBy->id;

        $newCard = self::create($newCardData);
        $newCard->generateQrCode();

        return $newCard;
    }
}
