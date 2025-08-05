<?php

namespace App\Enums;

use App\Traits\EnumHelpers;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Contracts\HasIcon;

enum ConfigurationType: string implements HasLabel, HasColor, HasIcon
{
    use EnumHelpers;

    case STRING = 'string';
    case NUMBER = 'number';
    case BOOLEAN = 'boolean';
    case JSON = 'json';
    case DATE = 'date';
    case EMAIL = 'email';
    case URL = 'url';

    public function getLabel(): string
    {
        return match ($this) {
            self::STRING => 'Texto',
            self::NUMBER => 'Número',
            self::BOOLEAN => 'Booleano',
            self::JSON => 'JSON',
            self::DATE => 'Fecha',
            self::EMAIL => 'Email',
            self::URL => 'URL',
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::STRING => 'gray',
            self::NUMBER => 'blue',
            self::BOOLEAN => 'green',
            self::JSON => 'purple',
            self::DATE => 'orange',
            self::EMAIL => 'cyan',
            self::URL => 'indigo',
        };
    }

    public function getIcon(): string | null
    {
        return match ($this) {
            self::STRING => 'heroicon-o-document-text',
            self::NUMBER => 'heroicon-o-calculator',
            self::BOOLEAN => 'heroicon-o-check-circle',
            self::JSON => 'heroicon-o-code-bracket',
            self::DATE => 'heroicon-o-calendar',
            self::EMAIL => 'heroicon-o-envelope',
            self::URL => 'heroicon-o-link',
        };
    }

    // Backward compatibility methods
    public function label(): string
    {
        return $this->getLabel();
    }

    public function color(): string
    {
        return $this->getColor();
    }

    public function icon(): string
    {
        return $this->getIcon();
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn($case) => [$case->value => $case->getLabel()])
            ->toArray();
    }
}
