<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Contracts\HasIcon;

enum TournamentType: string implements HasLabel, HasColor, HasIcon {
    case League = 'league';
    case Championship = 'championship';
    case Cup = 'cup';
    case Friendly = 'friendly';
    case Regional = 'regional';
    case National = 'national';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::League => 'Liga Regular',
            self::Championship => 'Campeonato',
            self::Cup => 'Copa',
            self::Friendly => 'Amistoso',
            self::Regional => 'Regional',
            self::National => 'Nacional',
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::League => 'primary',
            self::Championship => 'warning',
            self::Cup => 'success',
            self::Friendly => 'gray',
            self::Regional => 'info',
            self::National => 'danger',
        };
    }

    public function getIcon(): string | null
    {
        return match ($this) {
            self::League => 'heroicon-o-trophy',
            self::Championship => 'heroicon-o-star',
            self::Cup => 'heroicon-o-gift',
            self::Friendly => 'heroicon-o-hand-raised',
            self::Regional => 'heroicon-o-map',
            self::National => 'heroicon-o-flag',
        };
    }

    public function getColorHtml(): ?string
    {
        return match ($this) {
            self::League => 'bg-blue-100 text-blue-800',
            self::Championship => 'bg-yellow-100 text-yellow-800',
            self::Cup => 'bg-green-100 text-green-800',
            self::Friendly => 'bg-gray-100 text-gray-800',
            self::Regional => 'bg-cyan-100 text-cyan-800',
            self::National => 'bg-red-100 text-red-800',
        };
    }

    public function getLabelHtml(): ?string
    {
        return '<span class="py-1 px-3 rounded-full text-xs font-medium '.$this->getColorHtml().'">'.$this->getLabel().'</span>';
    }
}
