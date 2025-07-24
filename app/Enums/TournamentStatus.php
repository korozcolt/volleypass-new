<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Contracts\HasIcon;

enum TournamentStatus: string implements HasLabel, HasColor, HasIcon {
    case Draft = 'draft';
    case RegistrationOpen = 'registration_open';
    case RegistrationClosed = 'registration_closed';
    case InProgress = 'in_progress';
    case Finished = 'finished';
    case Cancelled = 'cancelled';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Draft => 'Borrador',
            self::RegistrationOpen => 'Inscripciones Abiertas',
            self::RegistrationClosed => 'Inscripciones Cerradas',
            self::InProgress => 'En Progreso',
            self::Finished => 'Finalizado',
            self::Cancelled => 'Cancelado',
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::Draft => 'gray',
            self::RegistrationOpen => 'success',
            self::RegistrationClosed => 'warning',
            self::InProgress => 'primary',
            self::Finished => 'info',
            self::Cancelled => 'danger',
        };
    }

    public function getIcon(): string | null
    {
        return match ($this) {
            self::Draft => 'heroicon-o-document',
            self::RegistrationOpen => 'heroicon-o-user-plus',
            self::RegistrationClosed => 'heroicon-o-lock-closed',
            self::InProgress => 'heroicon-o-play',
            self::Finished => 'heroicon-o-check-circle',
            self::Cancelled => 'heroicon-o-x-circle',
        };
    }

    public function getColorHtml(): ?string
    {
        return match ($this) {
            self::Draft => 'bg-gray-100 text-gray-800',
            self::RegistrationOpen => 'bg-green-100 text-green-800',
            self::RegistrationClosed => 'bg-yellow-100 text-yellow-800',
            self::InProgress => 'bg-blue-100 text-blue-800',
            self::Finished => 'bg-cyan-100 text-cyan-800',
            self::Cancelled => 'bg-red-100 text-red-800',
        };
    }

    public function getLabelHtml(): ?string
    {
        return '<span class="py-1 px-3 rounded-full text-xs font-medium '.$this->getColorHtml().'">'.$this->getLabel().'</span>';
    }
}

