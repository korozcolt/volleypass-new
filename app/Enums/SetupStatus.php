<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Contracts\HasIcon;

enum SetupStatus: string implements HasLabel, HasColor, HasIcon
{
    case NotStarted = 'not_started';
    case InProgress = 'in_progress';
    case Completed = 'completed';
    case RequiresUpdate = 'requires_update';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::NotStarted => 'No Iniciado',
            self::InProgress => 'En Progreso',
            self::Completed => 'Completado',
            self::RequiresUpdate => 'Requiere Actualización',
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::NotStarted => 'gray',
            self::InProgress => 'warning',
            self::Completed => 'success',
            self::RequiresUpdate => 'danger',
        };
    }

    public function getIcon(): string | null
    {
        return match ($this) {
            self::NotStarted => 'heroicon-o-clock',
            self::InProgress => 'heroicon-o-arrow-path',
            self::Completed => 'heroicon-o-check-circle',
            self::RequiresUpdate => 'heroicon-o-exclamation-triangle',
        };
    }

    // Legacy methods for backward compatibility
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
}