<?php

namespace App\Models;

use App\Enums\RegistrationStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TournamentTeam extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tournament_id',
        'team_id',
        'registration_status',
        'registered_at',
        'approved_at',
        'group_number',
        'seed_position',
        'roster_players',
        'coaching_staff',
        'registration_notes',
        'registration_fee',
        'fee_paid',
        'fee_paid_at',
        'metadata',
    ];

    protected $casts = [
        'registration_status' => RegistrationStatus::class,
        'registered_at' => 'datetime',
        'approved_at' => 'datetime',
        'roster_players' => 'array',
        'coaching_staff' => 'array',
        'registration_fee' => 'decimal:2',
        'fee_paid' => 'boolean',
        'fee_paid_at' => 'datetime',
        'metadata' => 'array',
    ];

    // Relationships
    public function tournament(): BelongsTo
    {
        return $this->belongsTo(Tournament::class);
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    // Scopes
    public function scopeApproved($query)
    {
        return $query->where('registration_status', RegistrationStatus::Approved);
    }

    public function scopePending($query)
    {
        return $query->where('registration_status', RegistrationStatus::Pending);
    }

    public function scopeByGroup($query, int $groupNumber)
    {
        return $query->where('group_number', $groupNumber);
    }

    public function scopeFeePaid($query)
    {
        return $query->where('fee_paid', true);
    }

    // Helper methods
    public function approve(): bool
    {
        if ($this->registration_status !== RegistrationStatus::Pending) {
            return false;
        }

        $this->update([
            'registration_status' => RegistrationStatus::Approved,
            'approved_at' => now(),
        ]);

        // Update tournament registered teams count
        $this->tournament->updateRegisteredTeamsCount();

        return true;
    }

    public function reject(): bool
    {
        if ($this->registration_status !== RegistrationStatus::Pending) {
            return false;
        }

        $this->update([
            'registration_status' => RegistrationStatus::Rejected,
        ]);

        return true;
    }

    public function markFeePaid(): bool
    {
        $this->update([
            'fee_paid' => true,
            'fee_paid_at' => now(),
        ]);

        return true;
    }

    public function assignToGroup(int $groupNumber, ?int $seedPosition = null): bool
    {
        $this->update([
            'group_number' => $groupNumber,
            'seed_position' => $seedPosition,
        ]);

        return true;
    }

    public function isApproved(): bool
    {
        return $this->registration_status === RegistrationStatus::Approved;
    }

    public function isPending(): bool
    {
        return $this->registration_status === RegistrationStatus::Pending;
    }

    public function isRejected(): bool
    {
        return $this->registration_status === RegistrationStatus::Rejected;
    }

    public function hasFeePaid(): bool
    {
        return $this->fee_paid === true;
    }

    public function getRegistrationDaysAgo(): int
    {
        return $this->registered_at->diffInDays(now());
    }

    public function getRosterPlayersCount(): int
    {
        return count($this->roster_players ?? []);
    }

    public function getCoachingStaffCount(): int
    {
        return count($this->coaching_staff ?? []);
    }

    public function canBeApproved(): bool
    {
        return $this->isPending() && 
               $this->tournament->canRegisterTeams() &&
               $this->getRosterPlayersCount() >= 6; // Minimum players required
    }
}
