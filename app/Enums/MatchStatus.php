<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Contracts\HasIcon;

enum MatchStatus: string implements HasLabel, HasColor, HasIcon {
    case Scheduled = 'scheduled';
    case In_Progress = 'in_progress';
    case Paused = 'paused';
    case Finished = 'finished';
    case Cancelled = 'cancelled';
    case Postponed = 'postponed';
    case Walkover = 'walkover';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Scheduled => 'Programado',
            self::In_Progress => 'En Curso',
            self::Paused => 'Pausado',
            self::Finished => 'Finalizado',
            self::Cancelled => 'Cancelado',
            self::Postponed => 'Pospuesto',
            self::Walkover => 'Walkover',
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::Scheduled => 'info',
            self::In_Progress => 'warning',
            self::Paused => 'orange',
            self::Finished => 'success',
            self::Cancelled => 'danger',
            self::Postponed => 'purple',
            self::Walkover => 'gray',
        };
    }

    public function getIcon(): string | null
    {
        return match ($this) {
            self::Scheduled => 'heroicon-o-clock',
            self::In_Progress => 'heroicon-o-play-circle',
            self::Paused => 'heroicon-o-pause',
            self::Finished => 'heroicon-o-check-circle',
            self::Cancelled => 'heroicon-o-x-circle',
            self::Postponed => 'heroicon-o-pause-circle',
            self::Walkover => 'heroicon-o-forward',
        };
    }

    public function getColorHtml(): ?string
    {
        return match ($this) {
            self::Scheduled => 'bg-blue-100 text-blue-800',
            self::In_Progress => 'bg-yellow-100 text-yellow-800',
            self::Finished => 'bg-green-100 text-green-800',
            self::Cancelled => 'bg-red-100 text-red-800',
            self::Postponed => 'bg-purple-100 text-purple-800',
            self::Walkover => 'bg-gray-100 text-gray-800',
        };
    }

    public function getLabelHtml(): ?string
    {
        return '<span class="py-1 px-3 rounded-full text-xs font-medium '.$this->getColorHtml().'">'.$this->getLabel().'</span>';
    }
}
