<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Contracts\HasIcon;

enum TournamentFormat: string implements HasLabel, HasColor, HasIcon
{
    case RoundRobin = 'round_robin';
    case Elimination = 'elimination';
    case Mixed = 'mixed';
    case Swiss = 'swiss';
    case League = 'league';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::RoundRobin => 'Todos contra Todos',
            self::Elimination => 'Eliminación Directa',
            self::Mixed => 'Formato Mixto',
            self::Swiss => 'Sistema Suizo',
            self::League => 'Liga Regular',
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::RoundRobin => 'success',
            self::Elimination => 'danger',
            self::Mixed => 'warning',
            self::Swiss => 'info',
            self::League => 'primary',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::RoundRobin => 'heroicon-o-arrow-path',
            self::Elimination => 'heroicon-o-trophy',
            self::Mixed => 'heroicon-o-squares-plus',
            self::Swiss => 'heroicon-o-puzzle-piece',
            self::League => 'heroicon-o-calendar-days',
        };
    }

    public function getDescription(): string
    {
        return match ($this) {
            self::RoundRobin => 'Cada equipo juega contra todos los demás equipos',
            self::Elimination => 'Los equipos son eliminados tras perder un partido',
            self::Mixed => 'Combinación de fase de grupos y eliminación directa',
            self::Swiss => 'Emparejamientos basados en puntuación acumulada',
            self::League => 'Competición regular de temporada completa',
        };
    }

    public static function getOptions(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn($case) => [$case->value => $case->getLabel()])
            ->toArray();
    }

    public function getMinimumTeams(): int
    {
        return match ($this) {
            self::RoundRobin => 2,
            self::Elimination => 4,
            self::Mixed => 4,
            self::Swiss => 4,
            self::League => 6,
        };
    }

    public function getMaximumTeams(): ?int
    {
        return match ($this) {
            self::RoundRobin => 20,
            self::Elimination => 64,
            self::Mixed => 32,
            self::Swiss => 100,
            self::League => null,
        };
    }

    public function requiresPowerOfTwo(): bool
    {
        return $this === self::Elimination;
    }

    public function supportsGroups(): bool
    {
        return in_array($this, [self::Mixed, self::League]);
    }

    public function hasPlayoffs(): bool
    {
        return in_array($this, [self::Mixed, self::League]);
    }
}