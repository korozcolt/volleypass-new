<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Contracts\HasIcon;

enum BloodType: string implements HasLabel, HasColor, HasIcon
{
    case O = 'O';
    case A = 'A';
    case B = 'B';
    case AB = 'AB';

    public function getLabel(): string
    {
        return match($this) {
            self::O => 'O',
            self::A => 'A',
            self::B => 'B',
            self::AB => 'AB',
        };
    }

    public function getColor(): string | array | null
    {
        return match($this) {
            self::O => 'danger',
            self::A => 'warning',
            self::B => 'info',
            self::AB => 'success',
        };
    }

    public function getIcon(): string | null
    {
        return match($this) {
            self::O => 'heroicon-o-heart',
            self::A => 'heroicon-o-heart',
            self::B => 'heroicon-o-heart',
            self::AB => 'heroicon-o-heart',
        };
    }

    public static function getOptions(): array
    {
        return [
            self::O->value => self::O->getLabel(),
            self::A->value => self::A->getLabel(),
            self::B->value => self::B->getLabel(),
            self::AB->value => self::AB->getLabel(),
        ];
    }
}