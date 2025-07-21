<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Contracts\HasIcon;

enum CardGenerationStatus: string implements HasLabel, HasColor, HasIcon
{
    case NotGenerated = 'not_generated';
    case Pending = 'pending';
    case Validating = 'validating';
    case Generating = 'generating';
    case Completed = 'completed';
    case Failed = 'failed';
    case Retrying = 'retrying';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::NotGenerated => 'No Generado',
            self::Pending => 'Pendiente',
            self::Validating => 'Validando',
            self::Generating => 'Generando',
            self::Completed => 'Completado',
            self::Failed => 'Fallido',
            self::Retrying => 'Reintentando',
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::NotGenerated => 'gray',
            self::Pending => 'info',
            self::Validating => 'warning',
            self::Generating => 'primary',
            self::Completed => 'success',
            self::Failed => 'danger',
            self::Retrying => 'warning',
        };
    }

    public function getIcon(): string | null
    {
        return match ($this) {
            self::NotGenerated => 'heroicon-o-minus-circle',
            self::Pending => 'heroicon-o-clock',
            self::Validating => 'heroicon-o-magnifying-glass',
            self::Generating => 'heroicon-o-cog-6-tooth',
            self::Completed => 'heroicon-o-check-circle',
            self::Failed => 'heroicon-o-x-circle',
            self::Retrying => 'heroicon-o-arrow-path',
        };
    }

    public function getColorHtml(): ?string
    {
        return match ($this) {
            self::NotGenerated => 'bg-gray-100 text-gray-800',
            self::Pending => 'bg-blue-100 text-blue-800',
            self::Validating => 'bg-yellow-100 text-yellow-800',
            self::Generating => 'bg-indigo-100 text-indigo-800',
            self::Completed => 'bg-green-100 text-green-800',
            self::Failed => 'bg-red-100 text-red-800',
            self::Retrying => 'bg-orange-100 text-orange-800',
        };
    }

    public function getLabelHtml(): ?string
    {
        return '<span class="py-1 px-3 rounded-full text-xs font-medium '.$this->getColorHtml().'">'.$this->getLabel().'</span>';
    }

    public function isInProgress(): bool
    {
        return in_array($this, [
            self::Pending,
            self::Validating,
            self::Generating,
            self::Retrying
        ]);
    }

    public function isCompleted(): bool
    {
        return $this === self::Completed;
    }

    public function isFailed(): bool
    {
        return $this === self::Failed;
    }

    public function canRetry(): bool
    {
        return in_array($this, [
            self::Failed,
            self::Validating,
            self::Generating
        ]);
    }
}
