<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use App\Enums\TransferStatus;

class PlayerTransfer extends Model
{
    use SoftDeletes, LogsActivity;

    protected $fillable = [
        'player_id',
        'from_club_id',
        'to_club_id',
        'league_id',
        'transfer_date',
        'effective_date',
        'status',
        'reason',
        'transfer_fee',
        'currency',
        'requested_by',
        'approved_by',
        'approved_at',
        'rejected_at',
        'rejection_reason',
        'notes',
        'metadata',
    ];

    protected $casts = [
        'status' => TransferStatus::class,
        'transfer_date' => 'date',
        'effective_date' => 'date',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'transfer_fee' => 'decimal:2',
        'metadata' => 'array',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status', 'approved_at', 'rejected_at'])
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => match($eventName) {
                'created' => 'Solicitud de traspaso creada',
                'updated' => 'Traspaso actualizado',
                default => "Traspaso {$eventName}"
            });
    }

    // Relaciones
    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }

    public function fromClub(): BelongsTo
    {
        return $this->belongsTo(Club::class, 'from_club_id');
    }

    public function toClub(): BelongsTo
    {
        return $this->belongsTo(Club::class, 'to_club_id');
    }

    public function league(): BelongsTo
    {
        return $this->belongsTo(League::class);
    }

    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', TransferStatus::Pending);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', TransferStatus::Approved);
    }

    public function scopeRejected($query)
    {
        return $query->where('status', TransferStatus::Rejected);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', TransferStatus::Completed);
    }

    // Métodos
    public function approve(User $approver): bool
    {
        $this->update([
            'status' => TransferStatus::Approved,
            'approved_by' => $approver->id,
            'approved_at' => now(),
        ]);

        // Ejecutar el traspaso
        $this->executeTransfer();

        return true;
    }

    public function reject(User $approver, string $reason): bool
    {
        $this->update([
            'status' => TransferStatus::Rejected,
            'approved_by' => $approver->id,
            'rejected_at' => now(),
            'rejection_reason' => $reason,
        ]);

        return true;
    }

    public function executeTransfer(): bool
    {
        if ($this->status !== TransferStatus::Approved) {
            return false;
        }

        // Actualizar el club de la jugadora
        $this->player->update(['current_club_id' => $this->to_club_id]);
        $this->player->user->update(['club_id' => $this->to_club_id]);

        // Marcar como completado
        $this->update([
            'status' => TransferStatus::Completed,
            'effective_date' => now(),
        ]);

        return true;
    }

    public function canBeApproved(): bool
    {
        return $this->status === TransferStatus::Pending;
    }

    public function requiresLeagueApproval(): bool
    {
        // Los traspasos entre clubes de diferentes ligas requieren aprobación
        return $this->fromClub->league_id !== $this->toClub->league_id;
    }
}
