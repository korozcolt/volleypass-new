<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Contracts\HasIcon;

enum RegistrationStatus: string implements HasLabel, HasColor, HasIcon
{
    case Pending = 'pending';
    case Approved = 'approved';
    case Rejected = 'rejected';
    case Cancelled = 'cancelled';
    case Withdrawn = 'withdrawn';

    public function getLabel(): string
    {
        return match($this) {
            self::Pending => 'Pendiente',
            self::Approved => 'Aprobado',
            self::Rejected => 'Rechazado',
            self::Cancelled => 'Cancelado',
            self::Withdrawn => 'Retirado',
        };
    }

    public function getColor(): string | array | null
    {
        return match($this) {
            self::Pending => 'warning',
            self::Approved => 'success',
            self::Rejected => 'danger',
            self::Cancelled => 'secondary',
            self::Withdrawn => 'info',
        };
    }

    public function getIcon(): string | null
    {
        return match($this) {
            self::Pending => 'heroicon-o-clock',
            self::Approved => 'heroicon-o-check-circle',
            self::Rejected => 'heroicon-o-x-circle',
            self::Cancelled => 'heroicon-o-no-symbol',
            self::Withdrawn => 'heroicon-o-arrow-left-circle',
        };
    }

    public function getColorHtml(): string
    {
        return match($this) {
            self::Pending => '#ffc107',
            self::Approved => '#28a745',
            self::Rejected => '#dc3545',
            self::Cancelled => '#6c757d',
            self::Withdrawn => '#17a2b8',
        };
    }

    public function getLabelHtml(): string
    {
        return '<span class="badge bg-' . $this->getColor() . '">' . $this->getLabel() . '</span>';
    }

    public function getDescription(): string
    {
        return match($this) {
            self::Pending => 'La inscripción está pendiente de revisión',
            self::Approved => 'La inscripción ha sido aprobada',
            self::Rejected => 'La inscripción ha sido rechazada',
            self::Cancelled => 'La inscripción ha sido cancelada',
            self::Withdrawn => 'El equipo se ha retirado del torneo',
        };
    }

    public function canTransitionTo(RegistrationStatus $status): bool
    {
        return match($this) {
            self::Pending => in_array($status, [self::Approved, self::Rejected, self::Cancelled]),
            self::Approved => in_array($status, [self::Withdrawn, self::Cancelled]),
            self::Rejected => in_array($status, [self::Pending]),
            self::Cancelled => false,
            self::Withdrawn => false,
        };
    }

    public function isFinal(): bool
    {
        return in_array($this, [self::Cancelled, self::Withdrawn]);
    }

    public function isActive(): bool
    {
        return $this === self::Approved;
    }

    public static function getOptions(): array
    {
        return collect(self::cases())->mapWithKeys(function ($status) {
            return [$status->value => $status->getLabel()];
        })->toArray();
    }
}