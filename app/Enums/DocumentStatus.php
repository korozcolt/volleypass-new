<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Contracts\HasIcon;

enum DocumentStatus: string implements HasLabel, HasColor, HasIcon {
    case Pending = 'pending';
    case Under_Review = 'under_review';
    case Approved = 'approved';
    case Rejected = 'rejected';
    case Expired = 'expired';
    case Requires_Update = 'requires_update';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Pending => 'Pendiente',
            self::Under_Review => 'En Revisión',
            self::Approved => 'Aprobado',
            self::Rejected => 'Rechazado',
            self::Expired => 'Vencido',
            self::Requires_Update => 'Requiere Actualización',
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::Pending => 'gray',
            self::Under_Review => 'info',
            self::Approved => 'success',
            self::Rejected => 'danger',
            self::Expired => 'warning',
            self::Requires_Update => 'purple',
        };
    }

    public function getIcon(): string | null
    {
        return match ($this) {
            self::Pending => 'heroicon-o-clock',
            self::Under_Review => 'heroicon-o-eye',
            self::Approved => 'heroicon-o-check-circle',
            self::Rejected => 'heroicon-o-x-circle',
            self::Expired => 'heroicon-o-exclamation-triangle',
            self::Requires_Update => 'heroicon-o-arrow-path',
        };
    }

    public function getColorHtml(): ?string
    {
        return match ($this) {
            self::Pending => 'bg-gray-100 text-gray-800',
            self::Under_Review => 'bg-blue-100 text-blue-800',
            self::Approved => 'bg-green-100 text-green-800',
            self::Rejected => 'bg-red-100 text-red-800',
            self::Expired => 'bg-yellow-100 text-yellow-800',
            self::Requires_Update => 'bg-purple-100 text-purple-800',
        };
    }

    public function getLabelHtml(): ?string
    {
        return '<span class="py-1 px-3 rounded-full text-xs font-medium '.$this->getColorHtml().'">'.$this->getLabel().'</span>';
    }

    /**
     * Estados que permiten que el documento sea válido
     */
    public function isValid(): bool
    {
        return $this === self::Approved;
    }

    /**
     * Estados que requieren acción
     */
    public function requiresAction(): bool
    {
        return in_array($this, [
            self::Pending,
            self::Requires_Update,
            self::Expired
        ]);
    }

    /**
     * Transiciones válidas desde el estado actual
     */
    public function canTransitionTo(self $newStatus): bool
    {
        return match($this) {
            self::Pending => in_array($newStatus, [self::Under_Review, self::Approved, self::Rejected]),
            self::Under_Review => in_array($newStatus, [self::Approved, self::Rejected, self::Requires_Update]),
            self::Approved => in_array($newStatus, [self::Expired, self::Requires_Update]),
            self::Rejected => in_array($newStatus, [self::Pending, self::Under_Review]),
            self::Expired => in_array($newStatus, [self::Pending, self::Under_Review]),
            self::Requires_Update => in_array($newStatus, [self::Pending, self::Under_Review]),
        };
    }
}

