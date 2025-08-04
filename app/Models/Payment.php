<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use App\Enums\PaymentStatus;
use App\Enums\PaymentType;

class Payment extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, LogsActivity, InteractsWithMedia;

    protected $fillable = [
        'club_id',
        'league_id',
        'user_id',
        'player_id',
        'payer_type',
        'payer_id',
        'receiver_type',
        'receiver_id',
        'type',
        'amount',
        'currency',
        'reference_number',
        'payment_method',
        'status',
        'paid_at',
        'verified_at',
        'verified_by',
        'notes',
        'metadata',
        'payment_date',
        'due_date',
        'confirmed_at',
        'transaction_id',
        'gateway',
        'description',
        'receipt',
        'month_year',
        'is_recurring',
        'receipt_url',
        'payment_proof',
    ];

    protected $casts = [
        'type' => PaymentType::class,
        'status' => PaymentStatus::class,
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'verified_at' => 'datetime',
        'payment_date' => 'datetime',
        'due_date' => 'datetime',
        'confirmed_at' => 'datetime',
        'metadata' => 'array',
        'receipt' => 'array',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status', 'amount', 'verified_at'])
            ->logOnlyDirty();
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('receipts')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'application/pdf']);
    }

    // Relaciones
    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }

    public function league(): BelongsTo
    {
        return $this->belongsTo(League::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }

    public function payer()
    {
        return $this->morphTo();
    }

    public function receiver()
    {
        return $this->morphTo();
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', PaymentStatus::Pending);
    }

    public function scopeVerified($query)
    {
        return $query->where('status', PaymentStatus::Verified);
    }

    public function scopeForFederation($query)
    {
        return $query->where('type', PaymentType::Federation);
    }

    public function scopeMonthlyFees($query)
    {
        return $query->where('type', PaymentType::MonthlyFee);
    }

    public function scopeClubToLeague($query)
    {
        return $query->where('type', PaymentType::ClubToLeague);
    }

    public function scopePlayerToClub($query)
    {
        return $query->where('type', PaymentType::PlayerToClub);
    }

    public function scopeForMonth($query, $month, $year)
    {
        return $query->where('month_year', $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT));
    }

    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now())
                    ->whereIn('status', [PaymentStatus::Pending, PaymentStatus::UnderVerification]);
    }

    // Métodos
    public function verify(User $verifier): bool
    {
        $this->update([
            'status' => PaymentStatus::Verified,
            'verified_at' => now(),
            'verified_by' => $verifier->id,
        ]);

        return true;
    }

    public function reject(User $verifier, string $reason): bool
    {
        $this->update([
            'status' => PaymentStatus::Rejected,
            'verified_at' => now(),
            'verified_by' => $verifier->id,
            'notes' => $reason,
        ]);

        return true;
    }

    public function markAsUnderVerification(): bool
    {
        $this->update([
            'status' => PaymentStatus::UnderVerification,
        ]);

        return true;
    }

    public function complete(User $verifier): bool
    {
        $this->update([
            'status' => PaymentStatus::Completed,
            'verified_at' => now(),
            'verified_by' => $verifier->id,
            'confirmed_at' => now(),
        ]);

        return true;
    }

    public function isPlayerPayment(): bool
    {
        return $this->type === PaymentType::PlayerToClub || $this->type === PaymentType::MonthlyFee;
    }

    public function isClubPayment(): bool
    {
        return $this->type === PaymentType::ClubToLeague;
    }

    public function canBeViewedBy(User $user): bool
    {
        // Superadmin puede ver todos
        if ($user->hasRole('superadmin')) {
            return true;
        }

        // Para pagos de jugador a club
        if ($this->isPlayerPayment()) {
            // El jugador puede ver sus propios pagos
            if ($this->player && $this->player->user_id === $user->id) {
                return true;
            }
            
            // El club puede ver pagos de sus jugadores
            if ($this->club && $user->hasRole('club_admin') && $user->club_id === $this->club_id) {
                return true;
            }
            
            // La liga puede ver si hay un traspaso pendiente
            if ($user->hasRole('league_admin') && $this->status !== PaymentStatus::Completed) {
                return true;
            }
        }

        // Para pagos de club a liga
        if ($this->isClubPayment()) {
            // El club puede ver sus propios pagos
            if ($user->hasRole('club_admin') && $user->club_id === $this->club_id) {
                return true;
            }
            
            // La liga puede ver pagos hacia ella
            if ($user->hasRole('league_admin') && $user->league_id === $this->league_id) {
                return true;
            }
        }

        return false;
    }
}
