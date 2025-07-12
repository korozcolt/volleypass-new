<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Contracts\HasIcon;

enum StatisticType: string implements HasLabel, HasColor, HasIcon {
    case Attack = 'attack';
    case Block = 'block';
    case Serve = 'serve';
    case Reception = 'reception';
    case Set = 'set';
    case Dig = 'dig';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Attack => 'Ataque',
            self::Block => 'Bloqueo',
            self::Serve => 'Saque',
            self::Reception => 'Recepción',
            self::Set => 'Armado',
            self::Dig => 'Defensa',
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::Attack => 'danger',
            self::Block => 'warning',
            self::Serve => 'success',
            self::Reception => 'info',
            self::Set => 'purple',
            self::Dig => 'gray',
        };
    }

    public function getIcon(): string | null
    {
        return match ($this) {
            self::Attack => 'heroicon-o-bolt',
            self::Block => 'heroicon-o-shield-check',
            self::Serve => 'heroicon-o-arrow-up-circle',
            self::Reception => 'heroicon-o-arrow-down-circle',
            self::Set => 'heroicon-o-hand-raised',
            self::Dig => 'heroicon-o-shield-exclamation',
        };
    }

    public function getColorHtml(): ?string
    {
        return match ($this) {
            self::Attack => 'bg-red-100 text-red-800',
            self::Block => 'bg-yellow-100 text-yellow-800',
            self::Serve => 'bg-green-100 text-green-800',
            self::Reception => 'bg-blue-100 text-blue-800',
            self::Set => 'bg-purple-100 text-purple-800',
            self::Dig => 'bg-gray-100 text-gray-800',
        };
    }

    public function getLabelHtml(): ?string
    {
        return '<span class="py-1 px-3 rounded-full text-xs font-medium '.$this->getColorHtml().'">'.$this->getLabel().'</span>';
    }

    public function getUnit(): string
    {
        return match ($this) {
            self::Attack => 'puntos',
            self::Block => 'bloqueos',
            self::Serve => 'aces',
            self::Reception => 'recepciones',
            self::Set => 'armadas',
            self::Dig => 'defensas',
        };
    }
}
