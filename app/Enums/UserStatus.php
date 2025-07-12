<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Contracts\HasIcon;

enum UserStatus: string implements HasLabel, HasColor, HasIcon {
    case Active = 'active';
    case Inactive = 'inactive';
    case Suspended = 'suspended';
    case Pending = 'pending';
    case Blocked = 'blocked';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Active => 'Activo',
            self::Inactive => 'Inactivo',
            self::Suspended => 'Suspendido',
            self::Pending => 'Pendiente',
            self::Blocked => 'Bloqueado',
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::Active => 'success',
            self::Inactive => 'gray',
            self::Suspended => 'warning',
            self::Pending => 'info',
            self::Blocked => 'danger',
        };
    }

    public function getIcon(): string | null
    {
        return match ($this) {
            self::Active => 'heroicon-o-check-circle',
            self::Inactive => 'heroicon-o-minus-circle',
            self::Suspended => 'heroicon-o-pause-circle',
            self::Pending => 'heroicon-o-clock',
            self::Blocked => 'heroicon-o-x-circle',
        };
    }

    public function getColorHtml(): ?string
    {
        return match ($this) {
            self::Active => 'bg-green-100 text-green-800',
            self::Inactive => 'bg-gray-100 text-gray-800',
            self::Suspended => 'bg-yellow-100 text-yellow-800',
            self::Pending => 'bg-blue-100 text-blue-800',
            self::Blocked => 'bg-red-100 text-red-800',
        };
    }

    public function getLabelHtml(): ?string
    {
        return '<span class="py-1 px-3 rounded-full text-xs font-medium '.$this->getColorHtml().'">'.$this->getLabel().'</span>';
    }

    public function canTransitionTo(self $newStatus): bool
    {
        return match($this) {
            self::Pending => in_array($newStatus, [self::Active, self::Blocked]),
            self::Active => in_array($newStatus, [self::Inactive, self::Suspended]),
            self::Inactive => $newStatus === self::Active,
            self::Suspended => in_array($newStatus, [self::Active, self::Blocked]),
            self::Blocked => false,
        };
    }
}
