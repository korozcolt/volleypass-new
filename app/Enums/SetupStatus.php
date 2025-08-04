<?php

namespace App\Enums;

enum SetupStatus: string
{
    case NotStarted = 'not_started';
    case InProgress = 'in_progress';
    case Completed = 'completed';
    case RequiresUpdate = 'requires_update';

    public function label(): string
    {
        return match ($this) {
            self::NotStarted => 'No Iniciado',
            self::InProgress => 'En Progreso',
            self::Completed => 'Completado',
            self::RequiresUpdate => 'Requiere Actualización',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::NotStarted => 'gray',
            self::InProgress => 'warning',
            self::Completed => 'success',
            self::RequiresUpdate => 'danger',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::NotStarted => 'heroicon-o-clock',
            self::InProgress => 'heroicon-o-arrow-path',
            self::Completed => 'heroicon-o-check-circle',
            self::RequiresUpdate => 'heroicon-o-exclamation-triangle',
        };
    }
}