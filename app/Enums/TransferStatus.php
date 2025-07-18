<?php

namespace App\Enums;

enum TransferStatus: string
{
    case Pending = 'pending';
    case Approved = 'approved';
    case Rejected = 'rejected';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public function getLabel(): string
    {
        return match($this) {
            self::Pending => 'Pendiente',
            self::Approved => 'Aprobado',
            self::Rejected => 'Rechazado',
            self::Completed => 'Completado',
            self::Cancelled => 'Cancelado',
        };
    }

    public function getColor(): string
    {
        return match($this) {
            self::Pending => 'warning',
            self::Approved => 'info',
            self::Rejected => 'danger',
            self::Completed => 'success',
            self::Cancelled => 'gray',
        };
    }

    public function getIcon(): string
    {
        return match($this) {
            self::Pending => 'heroicon-o-clock',
            self::Approved => 'heroicon-o-check-circle',
            self::Rejected => 'heroicon-o-x-circle',
            self::Completed => 'heroicon-o-check-badge',
            self::Cancelled => 'heroicon-o-minus-circle',
        };
    }
}
