<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Contracts\HasIcon;

enum BloodRh: string implements HasLabel, HasColor, HasIcon
{
    case Positive = 'positive';
    case Negative = 'negative';

    public function getLabel(): string
    {
        return match($this) {
            self::Positive => 'Positivo (+)',
            self::Negative => 'Negativo (-)',
        };
    }

    public function getColor(): string | array | null
    {
        return match($this) {
            self::Positive => 'success',
            self::Negative => 'danger',
        };
    }

    public function getIcon(): string | null
    {
        return match($this) {
            self::Positive => 'heroicon-o-plus-circle',
            self::Negative => 'heroicon-o-minus-circle',
        };
    }

    public function getSymbol(): string
    {
        return match($this) {
            self::Positive => '+',
            self::Negative => '-',
        };
    }

    public static function getOptions(): array
    {
        return [
            self::Positive->value => self::Positive->getLabel(),
            self::Negative->value => self::Negative->getLabel(),
        ];
    }
}