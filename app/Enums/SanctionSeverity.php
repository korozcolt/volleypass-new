<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Contracts\HasIcon;

enum SanctionSeverity: string implements HasLabel, HasColor, HasIcon
{
    case Minor = 'minor';
    case Moderate = 'moderate';
    case Major = 'major';
    case Severe = 'severe';
    case Critical = 'critical';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Minor => 'Menor',
            self::Moderate => 'Moderada',
            self::Major => 'Mayor',
            self::Severe => 'Severa',
            self::Critical => 'Crítica',
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::Minor => 'success',
            self::Moderate => 'warning',
            self::Major => 'danger',
            self::Severe => 'danger',
            self::Critical => 'danger',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::Minor => 'heroicon-o-information-circle',
            self::Moderate => 'heroicon-o-exclamation-triangle',
            self::Major => 'heroicon-o-exclamation-circle',
            self::Severe => 'heroicon-o-x-circle',
            self::Critical => 'heroicon-o-fire',
        };
    }

    public function getDescription(): string
    {
        return match ($this) {
            self::Minor => 'Infracción leve sin consecuencias graves',
            self::Moderate => 'Infracción moderada con consecuencias limitadas',
            self::Major => 'Infracción grave con consecuencias significativas',
            self::Severe => 'Infracción severa con consecuencias importantes',
            self::Critical => 'Infracción crítica con máximas consecuencias',
        };
    }

    public function getSuspensionDays(): int
    {
        return match ($this) {
            self::Minor => 0,
            self::Moderate => 1,
            self::Major => 3,
            self::Severe => 7,
            self::Critical => 30,
        };
    }

    public function getMatchesSuspended(): int
    {
        return match ($this) {
            self::Minor => 0,
            self::Moderate => 1,
            self::Major => 2,
            self::Severe => 4,
            self::Critical => 8,
        };
    }

    public function getFineAmount(): int
    {
        return match ($this) {
            self::Minor => 0,
            self::Moderate => 50000,
            self::Major => 100000,
            self::Severe => 250000,
            self::Critical => 500000,
        };
    }

    public function requiresHearing(): bool
    {
        return in_array($this, [self::Major, self::Severe, self::Critical]);
    }

    public function allowsAppeal(): bool
    {
        return in_array($this, [self::Moderate, self::Major, self::Severe, self::Critical]);
    }

    public function getEscalationLevel(): int
    {
        return match ($this) {
            self::Minor => 1,
            self::Moderate => 2,
            self::Major => 3,
            self::Severe => 4,
            self::Critical => 5,
        };
    }

    public static function getByEscalationLevel(int $level): self
    {
        return match ($level) {
            1 => self::Minor,
            2 => self::Moderate,
            3 => self::Major,
            4 => self::Severe,
            5 => self::Critical,
            default => self::Minor,
        };
    }
}