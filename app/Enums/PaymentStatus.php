<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Contracts\HasIcon;

enum PaymentStatus: string implements HasLabel, HasColor, HasIcon {
    case Pending = 'pending';
    case Verified = 'verified';
    case Rejected = 'rejected';
    case Paid = 'paid';
    case Overdue = 'overdue';
    case Cancelled = 'cancelled';
    case Refunded = 'refunded';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Pending => 'Pendiente',
            self::Verified => 'Verificado',
            self::Rejected => 'Rechazado',
            self::Paid => 'Pagado',
            self::Overdue => 'Vencido',
            self::Cancelled => 'Cancelado',
            self::Refunded => 'Reembolsado',
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::Pending => 'warning',
            self::Verified => 'success',
            self::Rejected => 'danger',
            self::Paid => 'success',
            self::Overdue => 'danger',
            self::Cancelled => 'gray',
            self::Refunded => 'info',
        };
    }

    public function getIcon(): string | null
    {
        return match ($this) {
            self::Pending => 'heroicon-o-clock',
            self::Verified => 'heroicon-o-check-badge',
            self::Rejected => 'heroicon-o-x-circle',
            self::Paid => 'heroicon-o-check-circle',
            self::Overdue => 'heroicon-o-exclamation-triangle',
            self::Cancelled => 'heroicon-o-x-circle',
            self::Refunded => 'heroicon-o-arrow-path',
        };
    }

    public function getColorHtml(): ?string
    {
        return match ($this) {
            self::Pending => 'bg-yellow-100 text-yellow-800',
            self::Verified => 'bg-green-100 text-green-800',
            self::Rejected => 'bg-red-100 text-red-800',
            self::Paid => 'bg-green-100 text-green-800',
            self::Overdue => 'bg-red-100 text-red-800',
            self::Cancelled => 'bg-gray-100 text-gray-800',
            self::Refunded => 'bg-blue-100 text-blue-800',
        };
    }

    public function getLabelHtml(): ?string
    {
        return '<span class="py-1 px-3 rounded-full text-xs font-medium '.$this->getColorHtml().'">'.$this->getLabel().'</span>';
    }
}
