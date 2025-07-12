<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Contracts\HasIcon;

enum PlayerPosition: string implements HasLabel, HasColor, HasIcon {
    case Setter = 'setter';
    case Outside_Hitter = 'outside_hitter';
    case Middle_Blocker = 'middle_blocker';
    case Opposite = 'opposite';
    case Libero = 'libero';
    case Defensive_Specialist = 'defensive_specialist';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Setter => 'Armadora/Colocadora',
            self::Outside_Hitter => 'Atacante Exterior',
            self::Middle_Blocker => 'Bloqueadora Central',
            self::Opposite => 'Opuesta',
            self::Libero => 'Líbero',
            self::Defensive_Specialist => 'Especialista Defensiva',
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::Setter => 'purple',
            self::Outside_Hitter => 'danger',
            self::Middle_Blocker => 'warning',
            self::Opposite => 'success',
            self::Libero => 'info',
            self::Defensive_Specialist => 'gray',
        };
    }

    public function getIcon(): string | null
    {
        return match ($this) {
            self::Setter => 'heroicon-o-hand-raised',
            self::Outside_Hitter => 'heroicon-o-bolt',
            self::Middle_Blocker => 'heroicon-o-shield-check',
            self::Opposite => 'heroicon-o-fire',
            self::Libero => 'heroicon-o-shield-exclamation',
            self::Defensive_Specialist => 'heroicon-o-arrow-down-circle',
        };
    }

    public function getColorHtml(): ?string
    {
        return match ($this) {
            self::Setter => 'bg-purple-100 text-purple-800',
            self::Outside_Hitter => 'bg-red-100 text-red-800',
            self::Middle_Blocker => 'bg-yellow-100 text-yellow-800',
            self::Opposite => 'bg-green-100 text-green-800',
            self::Libero => 'bg-blue-100 text-blue-800',
            self::Defensive_Specialist => 'bg-gray-100 text-gray-800',
        };
    }

    public function getLabelHtml(): ?string
    {
        return '<span class="py-1 px-3 rounded-full text-xs font-medium '.$this->getColorHtml().'">'.$this->getLabel().'</span>';
    }

    public function getShortCode(): string
    {
        return match ($this) {
            self::Setter => 'ARM',
            self::Outside_Hitter => 'AE',
            self::Middle_Blocker => 'BC',
            self::Opposite => 'OP',
            self::Libero => 'LIB',
            self::Defensive_Specialist => 'ED',
        };
    }
}
