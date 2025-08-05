<?php

namespace App\Enums;

enum TeamType: string
{
    case CLUB = 'club';
    case SELECTION = 'selection';

    public function label(): string
    {
        return match ($this) {
            self::CLUB => 'Equipo de Club',
            self::SELECTION => 'Selección Departamental',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::CLUB => 'Equipo formado por jugadores de un club específico',
            self::SELECTION => 'Selección departamental con jugadores de diferentes clubes',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn($case) => [$case->value => $case->label()])
            ->toArray();
    }
}
