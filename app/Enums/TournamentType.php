<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Contracts\HasIcon;

enum TournamentType: string implements HasLabel, HasColor, HasIcon {
    case League = 'league';
    case Cup = 'cup';
    case Mixed = 'mixed';
    case Flash = 'flash';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::League => 'Liga Regular (Round Robin)',
            self::Cup => 'Copa/Eliminación Directa',
            self::Mixed => 'Torneo Mixto (Grupos + Llaves)',
            self::Flash => 'Torneo Relámpago',
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::League => 'primary',
            self::Cup => 'success',
            self::Mixed => 'warning',
            self::Flash => 'danger',
        };
    }

    public function getIcon(): string | null
    {
        return match ($this) {
            self::League => 'heroicon-o-trophy',
            self::Cup => 'heroicon-o-gift',
            self::Mixed => 'heroicon-o-squares-2x2',
            self::Flash => 'heroicon-o-bolt',
        };
    }

    public function getColorHtml(): ?string
    {
        return match ($this) {
            self::League => 'bg-blue-100 text-blue-800',
            self::Cup => 'bg-green-100 text-green-800',
            self::Mixed => 'bg-yellow-100 text-yellow-800',
            self::Flash => 'bg-red-100 text-red-800',
        };
    }

    public function getLabelHtml(): ?string
    {
        return '<span class="py-1 px-3 rounded-full text-xs font-medium '.$this->getColorHtml().'">'.$this->getLabel().'</span>';
    }
}
