<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Contracts\HasIcon;

enum CardType: string implements HasLabel, HasColor, HasIcon
{
    case Yellow = 'yellow';
    case Red = 'red';
    case RedMatch = 'red_match';
    case RedTournament = 'red_tournament';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Yellow => 'Tarjeta Amarilla',
            self::Red => 'Tarjeta Roja (Set)',
            self::RedMatch => 'Tarjeta Roja (Partido)',
            self::RedTournament => 'Tarjeta Roja (Torneo)',
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::Yellow => 'warning',
            self::Red => 'danger',
            self::RedMatch => 'danger',
            self::RedTournament => 'danger',
        };
    }

    public function getIcon(): string | null
    {
        return match ($this) {
            self::Yellow => 'heroicon-o-exclamation-triangle',
            self::Red => 'heroicon-o-x-circle',
            self::RedMatch => 'heroicon-o-no-symbol',
            self::RedTournament => 'heroicon-o-shield-exclamation',
        };
    }

    public function getColorHtml(): ?string
    {
        return match ($this) {
            self::Yellow => 'bg-yellow-100 text-yellow-800',
            self::Red => 'bg-red-100 text-red-800',
            self::RedMatch => 'bg-red-200 text-red-900',
            self::RedTournament => 'bg-red-300 text-red-900',
        };
    }

    public function getLabelHtml(): ?string
    {
        return '<span class="py-1 px-3 rounded-full text-xs font-medium '.$this->getColorHtml().'">'.$this->getLabel().'</span>';
    }

    public function getDescription(): string
    {
        return match ($this) {
            self::Yellow => 'Advertencia - Solo registro',
            self::Red => 'Expulsión del set actual',
            self::RedMatch => 'Expulsión del partido completo',
            self::RedTournament => 'Expulsión del torneo',
        };
    }

    public function getSeverityLevel(): int
    {
        return match ($this) {
            self::Yellow => 1,
            self::Red => 2,
            self::RedMatch => 3,
            self::RedTournament => 4,
        };
    }
}