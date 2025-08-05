<?php

namespace App\Enums;

enum SelectionStatus: string
{
    case NONE = 'NONE';
    case PRESELECCION = 'PRESELECCION';
    case SELECCION = 'SELECCION';

    public function label(): string
    {
        return match ($this) {
            self::NONE => 'Sin Selección',
            self::PRESELECCION => 'Preselección',
            self::SELECCION => 'Selección',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::NONE => 'gray',
            self::PRESELECCION => 'warning',
            self::SELECCION => 'success',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn($case) => [$case->value => $case->label()])
            ->toArray();
    }
}
