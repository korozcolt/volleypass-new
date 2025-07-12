<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Contracts\HasIcon;

enum PlayerCategory: string implements HasLabel, HasColor, HasIcon {
    case Mini = 'mini';
    case Pre_Mini = 'pre_mini';
    case Infantil = 'infantil';
    case Cadete = 'cadete';
    case Juvenil = 'juvenil';
    case Mayores = 'mayores';
    case Masters = 'masters';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Mini => 'Mini (8-10 años)',
            self::Pre_Mini => 'Pre-Mini (11-12 años)',
            self::Infantil => 'Infantil (13-14 años)',
            self::Cadete => 'Cadete (15-16 años)',
            self::Juvenil => 'Juvenil (17-18 años)',
            self::Mayores => 'Mayores (19+ años)',
            self::Masters => 'Masters (35+ años)',
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::Mini => 'pink',
            self::Pre_Mini => 'purple',
            self::Infantil => 'info',
            self::Cadete => 'warning',
            self::Juvenil => 'success',
            self::Mayores => 'primary',
            self::Masters => 'gray',
        };
    }

    public function getIcon(): string | null
    {
        return match ($this) {
            self::Mini => 'heroicon-o-heart',
            self::Pre_Mini => 'heroicon-o-star',
            self::Infantil => 'heroicon-o-sparkles',
            self::Cadete => 'heroicon-o-fire',
            self::Juvenil => 'heroicon-o-bolt',
            self::Mayores => 'heroicon-o-trophy',
            self::Masters => 'heroicon-o-academic-cap',
        };
    }

    public function getColorHtml(): ?string
    {
        return match ($this) {
            self::Mini => 'bg-pink-100 text-pink-800',
            self::Pre_Mini => 'bg-purple-100 text-purple-800',
            self::Infantil => 'bg-blue-100 text-blue-800',
            self::Cadete => 'bg-yellow-100 text-yellow-800',
            self::Juvenil => 'bg-green-100 text-green-800',
            self::Mayores => 'bg-indigo-100 text-indigo-800',
            self::Masters => 'bg-gray-100 text-gray-800',
        };
    }

    public function getLabelHtml(): ?string
    {
        return '<span class="py-1 px-3 rounded-full text-xs font-medium '.$this->getColorHtml().'">'.$this->getLabel().'</span>';
    }

    public function getAgeRange(): array
    {
        return match ($this) {
            self::Mini => [8, 10],
            self::Pre_Mini => [11, 12],
            self::Infantil => [13, 14],
            self::Cadete => [15, 16],
            self::Juvenil => [17, 18],
            self::Mayores => [19, 34],
            self::Masters => [35, 100],
        };
    }
}
