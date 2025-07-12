<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Contracts\HasIcon;

enum TournamentStatus: string implements HasLabel, HasColor, HasIcon {
    case Upcoming = 'upcoming';
    case Registration_Open = 'registration_open';
    case In_Progress = 'in_progress';
    case Finished = 'finished';
    case Cancelled = 'cancelled';
    case Postponed = 'postponed';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Upcoming => 'Próximo',
            self::Registration_Open => 'Inscripciones Abiertas',
            self::In_Progress => 'En Curso',
            self::Finished => 'Finalizado',
            self::Cancelled => 'Cancelado',
            self::Postponed => 'Pospuesto',
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::Upcoming => 'info',
            self::Registration_Open => 'success',
            self::In_Progress => 'warning',
            self::Finished => 'gray',
            self::Cancelled => 'danger',
            self::Postponed => 'purple',
        };
    }

    public function getIcon(): string | null
    {
        return match ($this) {
            self::Upcoming => 'heroicon-o-calendar-days',
            self::Registration_Open => 'heroicon-o-clipboard-document-list',
            self::In_Progress => 'heroicon-o-play-circle',
            self::Finished => 'heroicon-o-check-circle',
            self::Cancelled => 'heroicon-o-x-circle',
            self::Postponed => 'heroicon-o-pause-circle',
        };
    }

    public function getColorHtml(): ?string
    {
        return match ($this) {
            self::Upcoming => 'bg-blue-100 text-blue-800',
            self::Registration_Open => 'bg-green-100 text-green-800',
            self::In_Progress => 'bg-yellow-100 text-yellow-800',
            self::Finished => 'bg-gray-100 text-gray-800',
            self::Cancelled => 'bg-red-100 text-red-800',
            self::Postponed => 'bg-purple-100 text-purple-800',
        };
    }

    public function getLabelHtml(): ?string
    {
        return '<span class="py-1 px-3 rounded-full text-xs font-medium '.$this->getColorHtml().'">'.$this->getLabel().'</span>';
    }
}

