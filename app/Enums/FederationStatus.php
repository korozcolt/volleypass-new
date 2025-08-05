<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Contracts\HasIcon;

enum FederationStatus: string implements HasLabel, HasColor, HasIcon
{
    case NotFederated = 'not_federated';
    case PendingPayment = 'pending_payment';
    case PaymentSubmitted = 'payment_submitted';
    case Federated = 'federated';
    case Suspended = 'suspended';
    case Expired = 'expired';

    public function getLabel(): string
    {
        return match($this) {
            self::NotFederated => 'No Federado',
            self::PendingPayment => 'Pago Pendiente',
            self::PaymentSubmitted => 'Pago Enviado',
            self::Federated => 'Federado',
            self::Suspended => 'Suspendido',
            self::Expired => 'Vencido',
        };
    }

    public function getColor(): string | array | null
    {
        return match($this) {
            self::NotFederated => 'gray',
            self::PendingPayment => 'warning',
            self::PaymentSubmitted => 'info',
            self::Federated => 'success',
            self::Suspended => 'danger',
            self::Expired => 'danger',
        };
    }

    public function getIcon(): string | null
    {
        return match($this) {
            self::NotFederated => 'heroicon-o-minus-circle',
            self::PendingPayment => 'heroicon-o-clock',
            self::PaymentSubmitted => 'heroicon-o-document-arrow-up',
            self::Federated => 'heroicon-o-check-badge',
            self::Suspended => 'heroicon-o-exclamation-triangle',
            self::Expired => 'heroicon-o-x-circle',
        };
    }

    public function canPlay(): bool
    {
        return $this === self::Federated;
    }

    public function requiresPayment(): bool
    {
        return in_array($this, [self::NotFederated, self::PendingPayment, self::Expired]);
    }
}
