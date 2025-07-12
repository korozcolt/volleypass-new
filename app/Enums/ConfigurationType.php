<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Contracts\HasIcon;

enum ConfigurationType: string implements HasLabel, HasColor, HasIcon {
    case System = 'system';
    case League = 'league';
    case Club = 'club';
    case Tournament = 'tournament';
    case Medical = 'medical';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::System => 'Sistema',
            self::League => 'Liga',
            self::Club => 'Club',
            self::Tournament => 'Torneo',
            self::Medical => 'Médico',
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::System => 'danger',
            self::League => 'primary',
            self::Club => 'success',
            self::Tournament => 'warning',
            self::Medical => 'purple',
        };
    }

    public function getIcon(): string | null
    {
        return match ($this) {
            self::System => 'heroicon-o-cog-6-tooth',
            self::League => 'heroicon-o-building-office-2',
            self::Club => 'heroicon-o-building-office',
            self::Tournament => 'heroicon-o-trophy',
            self::Medical => 'heroicon-o-heart',
        };
    }

    public function getColorHtml(): ?string
    {
        return match ($this) {
            self::System => 'bg-red-100 text-red-800',
            self::League => 'bg-blue-100 text-blue-800',
            self::Club => 'bg-green-100 text-green-800',
            self::Tournament => 'bg-yellow-100 text-yellow-800',
            self::Medical => 'bg-purple-100 text-purple-800',
        };
    }

    public function getLabelHtml(): ?string
    {
        return '<span class="py-1 px-3 rounded-full text-xs font-medium '.$this->getColorHtml().'">'.$this->getLabel().'</span>';
    }
}
