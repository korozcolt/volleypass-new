<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Contracts\HasIcon;

enum MedicalStatus: string implements HasLabel, HasColor, HasIcon {
    case Fit = 'fit';
    case Restricted = 'restricted';
    case Unfit = 'unfit';
    case Under_Treatment = 'under_treatment';
    case Recovery = 'recovery';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Fit => 'Apta',
            self::Restricted => 'Restricción Parcial',
            self::Unfit => 'No Apta',
            self::Under_Treatment => 'En Tratamiento',
            self::Recovery => 'En Recuperación',
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::Fit => 'success',
            self::Restricted => 'warning',
            self::Unfit => 'danger',
            self::Under_Treatment => 'info',
            self::Recovery => 'purple',
        };
    }

    public function getIcon(): string | null
    {
        return match ($this) {
            self::Fit => 'heroicon-o-check-circle',
            self::Restricted => 'heroicon-o-exclamation-triangle',
            self::Unfit => 'heroicon-o-x-circle',
            self::Under_Treatment => 'heroicon-o-beaker',
            self::Recovery => 'heroicon-o-heart',
        };
    }

    public function getColorHtml(): ?string
    {
        return match ($this) {
            self::Fit => 'bg-green-100 text-green-800',
            self::Restricted => 'bg-yellow-100 text-yellow-800',
            self::Unfit => 'bg-red-100 text-red-800',
            self::Under_Treatment => 'bg-blue-100 text-blue-800',
            self::Recovery => 'bg-purple-100 text-purple-800',
        };
    }

    public function getLabelHtml(): ?string
    {
        return '<span class="py-1 px-3 rounded-full text-xs font-medium '.$this->getColorHtml().'">'.$this->getLabel().'</span>';
    }

    public function canPlay(): bool
    {
        return match($this) {
            self::Fit => true,
            self::Restricted => true, // Con limitaciones
            default => false,
        };
    }
}

