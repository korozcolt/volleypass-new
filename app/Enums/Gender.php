<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Contracts\HasIcon;

enum Gender: string implements HasLabel, HasColor, HasIcon {
    case Female = 'female';
    case Male = 'male';
    case Mixed = 'mixed';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Female => 'Femenino',
            self::Male => 'Masculino',
            self::Mixed => 'Mixto',
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::Female => 'pink',
            self::Male => 'info',
            self::Mixed => 'purple',
        };
    }

    public function getIcon(): string | null
    {
        return match ($this) {
            self::Female => 'heroicon-o-user',
            self::Male => 'heroicon-o-user',
            self::Mixed => 'heroicon-o-users',
        };
    }

    public function getColorHtml(): ?string
    {
        return match ($this) {
            self::Female => 'bg-pink-100 text-pink-800',
            self::Male => 'bg-blue-100 text-blue-800',
            self::Mixed => 'bg-purple-100 text-purple-800',
        };
    }

    public function getLabelHtml(): ?string
    {
        return '<span class="py-1 px-3 rounded-full text-xs font-medium '.$this->getColorHtml().'">'.$this->getLabel().'</span>';
    }
}
