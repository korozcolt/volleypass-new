<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Contracts\HasIcon;

enum SanctionStatus: string implements HasLabel, HasColor, HasIcon
{
    case Pending = 'pending';
    case Active = 'active';
    case Served = 'served';
    case Suspended = 'suspended';
    case Appealed = 'appealed';
    case Overturned = 'overturned';
    case Reduced = 'reduced';
    case Expired = 'expired';
    case Cancelled = 'cancelled';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Pending => 'Pendiente',
            self::Active => 'Activa',
            self::Served => 'Cumplida',
            self::Suspended => 'Suspendida',
            self::Appealed => 'Apelada',
            self::Overturned => 'Revocada',
            self::Reduced => 'Reducida',
            self::Expired => 'Expirada',
            self::Cancelled => 'Cancelada',
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::Pending => 'warning',
            self::Active => 'danger',
            self::Served => 'success',
            self::Suspended => 'info',
            self::Appealed => 'warning',
            self::Overturned => 'success',
            self::Reduced => 'info',
            self::Expired => 'gray',
            self::Cancelled => 'gray',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::Pending => 'heroicon-o-clock',
            self::Active => 'heroicon-o-exclamation-circle',
            self::Served => 'heroicon-o-check-circle',
            self::Suspended => 'heroicon-o-pause-circle',
            self::Appealed => 'heroicon-o-scale',
            self::Overturned => 'heroicon-o-arrow-uturn-left',
            self::Reduced => 'heroicon-o-arrow-down-circle',
            self::Expired => 'heroicon-o-calendar-x',
            self::Cancelled => 'heroicon-o-x-circle',
        };
    }

    public function getDescription(): string
    {
        return match ($this) {
            self::Pending => 'Sanción pendiente de aplicación o revisión',
            self::Active => 'Sanción activa y en vigor',
            self::Served => 'Sanción completamente cumplida',
            self::Suspended => 'Sanción temporalmente suspendida',
            self::Appealed => 'Sanción bajo proceso de apelación',
            self::Overturned => 'Sanción revocada por apelación exitosa',
            self::Reduced => 'Sanción reducida tras revisión',
            self::Expired => 'Sanción expirada por tiempo',
            self::Cancelled => 'Sanción cancelada administrativamente',
        };
    }

    public function isActive(): bool
    {
        return in_array($this, [self::Pending, self::Active, self::Appealed]);
    }

    public function isCompleted(): bool
    {
        return in_array($this, [self::Served, self::Overturned, self::Expired, self::Cancelled]);
    }

    public function canBeAppealed(): bool
    {
        return in_array($this, [self::Active, self::Pending]);
    }

    public function canBeModified(): bool
    {
        return in_array($this, [self::Pending, self::Active, self::Appealed]);
    }

    public function affectsEligibility(): bool
    {
        return in_array($this, [self::Active, self::Pending]);
    }

    public function requiresAction(): bool
    {
        return in_array($this, [self::Pending, self::Appealed]);
    }

    public static function getActiveStatuses(): array
    {
        return [self::Pending, self::Active, self::Appealed];
    }

    public static function getCompletedStatuses(): array
    {
        return [self::Served, self::Overturned, self::Expired, self::Cancelled];
    }

    public static function getAppealableStatuses(): array
    {
        return [self::Active, self::Pending];
    }
}