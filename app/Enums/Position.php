<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Contracts\HasIcon;

enum Position: string implements HasLabel, HasColor, HasIcon
{
    case Position1 = 'position_1';
    case Position2 = 'position_2';
    case Position3 = 'position_3';
    case Position4 = 'position_4';
    case Position5 = 'position_5';
    case Position6 = 'position_6';
    case Libero = 'libero';
    case Substitute = 'substitute';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Position1 => 'Posición 1 (Zaguero Derecho)',
            self::Position2 => 'Posición 2 (Delantero Derecho)',
            self::Position3 => 'Posición 3 (Delantero Centro)',
            self::Position4 => 'Posición 4 (Delantero Izquierdo)',
            self::Position5 => 'Posición 5 (Zaguero Izquierdo)',
            self::Position6 => 'Posición 6 (Zaguero Centro)',
            self::Libero => 'Líbero',
            self::Substitute => 'Suplente',
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::Position1 => 'blue',
            self::Position2 => 'green',
            self::Position3 => 'yellow',
            self::Position4 => 'red',
            self::Position5 => 'purple',
            self::Position6 => 'orange',
            self::Libero => 'indigo',
            self::Substitute => 'gray',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::Position1 => 'heroicon-o-1',
            self::Position2 => 'heroicon-o-2',
            self::Position3 => 'heroicon-o-3',
            self::Position4 => 'heroicon-o-4',
            self::Position5 => 'heroicon-o-5',
            self::Position6 => 'heroicon-o-6',
            self::Libero => 'heroicon-o-shield-check',
            self::Substitute => 'heroicon-o-user-plus',
        };
    }

    public function getShortName(): string
    {
        return match ($this) {
            self::Position1 => 'P1',
            self::Position2 => 'P2',
            self::Position3 => 'P3',
            self::Position4 => 'P4',
            self::Position5 => 'P5',
            self::Position6 => 'P6',
            self::Libero => 'L',
            self::Substitute => 'S',
        };
    }

    public function getZone(): string
    {
        return match ($this) {
            self::Position1, self::Position5, self::Position6 => 'Zaguero',
            self::Position2, self::Position3, self::Position4 => 'Delantero',
            self::Libero => 'Líbero',
            self::Substitute => 'Banco',
        };
    }

    public function getCoordinates(): array
    {
        return match ($this) {
            self::Position1 => ['x' => 3, 'y' => 1], // Zaguero derecho
            self::Position2 => ['x' => 3, 'y' => 3], // Delantero derecho
            self::Position3 => ['x' => 2, 'y' => 3], // Delantero centro
            self::Position4 => ['x' => 1, 'y' => 3], // Delantero izquierdo
            self::Position5 => ['x' => 1, 'y' => 1], // Zaguero izquierdo
            self::Position6 => ['x' => 2, 'y' => 1], // Zaguero centro
            self::Libero => ['x' => 2, 'y' => 0], // Posición especial
            self::Substitute => ['x' => 0, 'y' => 0], // Fuera de cancha
        };
    }

    public function isBackRow(): bool
    {
        return in_array($this, [self::Position1, self::Position5, self::Position6, self::Libero]);
    }

    public function isFrontRow(): bool
    {
        return in_array($this, [self::Position2, self::Position3, self::Position4]);
    }

    public function canServe(): bool
    {
        return $this !== self::Libero && $this !== self::Substitute;
    }

    public function canAttack(): bool
    {
        return $this !== self::Libero && $this !== self::Substitute;
    }

    public function canBlock(): bool
    {
        return $this->isFrontRow();
    }

    public function getRotationOrder(): int
    {
        return match ($this) {
            self::Position1 => 1,
            self::Position2 => 2,
            self::Position3 => 3,
            self::Position4 => 4,
            self::Position5 => 5,
            self::Position6 => 6,
            self::Libero => 0, // No rota
            self::Substitute => 0, // No rota
        };
    }

    public function getNextPosition(): ?self
    {
        return match ($this) {
            self::Position1 => self::Position6,
            self::Position2 => self::Position1,
            self::Position3 => self::Position2,
            self::Position4 => self::Position3,
            self::Position5 => self::Position4,
            self::Position6 => self::Position5,
            default => null,
        };
    }

    public function getPreviousPosition(): ?self
    {
        return match ($this) {
            self::Position1 => self::Position2,
            self::Position2 => self::Position3,
            self::Position3 => self::Position4,
            self::Position4 => self::Position5,
            self::Position5 => self::Position6,
            self::Position6 => self::Position1,
            default => null,
        };
    }

    public static function getRotationPositions(): array
    {
        return [
            self::Position1,
            self::Position2,
            self::Position3,
            self::Position4,
            self::Position5,
            self::Position6,
        ];
    }

    public static function getCourtPositions(): array
    {
        return [
            self::Position1,
            self::Position2,
            self::Position3,
            self::Position4,
            self::Position5,
            self::Position6,
            self::Libero,
        ];
    }
}