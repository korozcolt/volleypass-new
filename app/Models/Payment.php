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
}
