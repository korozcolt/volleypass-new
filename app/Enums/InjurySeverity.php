<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Contracts\HasIcon;

enum InjurySeverity: string implements HasLabel, HasColor, HasIcon {
    case Minor = 'minor';
    case Moderate = 'moderate';
    case Severe = 'severe';
    case Critical = 'critical';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Minor => 'Leve',
            self::Moderate => 'Moderada',
            self::Severe => 'Severa',
            self::Critical => 'Crítica',
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::Minor => 'success',
            self::Moderate => 'warning',
            self::Severe => 'danger',
            self::Critical => 'purple',
        };
    }

    public function getIcon(): string | null
    {
        return match ($this) {
            self::Minor => 'heroicon-o-face-smile',
            self::Moderate => 'heroicon-o-exclamation-triangle',
            self::Severe => 'heroicon-o-fire',
            self::Critical => 'heroicon-o-bolt',
        };
    }

    public function getColorHtml(): ?string
    {
        return match ($this) {
            self::Minor => 'bg-green-100 text-green-800',
            self::Moderate => 'bg-yellow-100 text-yellow-800',
            self::Severe => 'bg-red-100 text-red-800',
            self::Critical => 'bg-purple-100 text-purple-800',
        };
    }

    public function getLabelHtml(): ?string
    {
        return '<span class="py-1 px-3 rounded-full text-xs font-medium '.$this->getColorHtml().'">'.$this->getLabel().'</span>';
    }

    public function getRecoveryDays(): array
    {
        return match ($this) {
            self::Minor => [1, 7],
            self::Moderate => [7, 30],
            self::Severe => [30, 90],
            self::Critical => [90, 365],
        };
    }
}
