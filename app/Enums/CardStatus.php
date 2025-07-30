<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Contracts\HasIcon;

enum CardStatus: string implements HasLabel, HasColor, HasIcon {
    case Active = 'active';
    case Expired = 'expired';
    case Suspended = 'suspended';
    case Medical_Restriction = 'medical_restriction';
    case Pending_Approval = 'pending_approval';
    case Cancelled = 'cancelled';
    case Replaced = 'replaced';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Active => 'Activo',
            self::Expired => 'Vencido',
            self::Suspended => 'Suspendido',
            self::Medical_Restriction => 'Restricción Médica',
            self::Pending_Approval => 'Pendiente de Aprobación',
            self::Cancelled => 'Cancelado',
            self::Replaced => 'Reemplazado',
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::Active => 'success',
            self::Expired => 'warning',
            self::Suspended => 'danger',
            self::Medical_Restriction => 'purple',
            self::Pending_Approval => 'info',
            self::Cancelled => 'gray',
            self::Replaced => 'gray',
        };
    }

    public function getIcon(): string | null
    {
        return match ($this) {
            self::Active => 'heroicon-o-check-badge',
            self::Expired => 'heroicon-o-clock',
            self::Suspended => 'heroicon-o-pause-circle',
            self::Medical_Restriction => 'heroicon-o-exclamation-triangle',
            self::Pending_Approval => 'heroicon-o-clock',
            self::Cancelled => 'heroicon-o-x-circle',
            self::Replaced => 'heroicon-o-arrow-right-circle',
        };
    }

    public function getColorHtml(): ?string
    {
        return match ($this) {
            self::Active => 'bg-green-100 text-green-800',
            self::Expired => 'bg-yellow-100 text-yellow-800',
            self::Suspended => 'bg-red-100 text-red-800',
            self::Medical_Restriction => 'bg-purple-100 text-purple-800',
            self::Pending_Approval => 'bg-blue-100 text-blue-800',
            self::Cancelled => 'bg-gray-100 text-gray-800',
            self::Replaced => 'bg-slate-100 text-slate-800',
        };
    }

    public function getLabelHtml(): ?string
    {
        return '<span class="py-1 px-3 rounded-full text-xs font-medium '.$this->getColorHtml().'">'.$this->getLabel().'</span>';
    }

    /**
     * Estados que permiten que la jugadora juegue
     */
    public function allowsPlay(): bool
    {
        return match($this) {
            self::Active => true,
            self::Medical_Restriction => true, // Con restricciones, pero puede jugar
            default => false,
        };
    }

    /**
     * Estados que requieren renovación
     */
    public function needsRenewal(): bool
    {
        return in_array($this, [
            self::Expired,
            self::Medical_Restriction,
            self::Pending_Approval
        ]);
    }

    /**
     * Estado para el verificador QR
     */
    public function getVerificationResult(): array
    {
        return match($this) {
            self::Active => [
                'status' => 'success',
                'message' => 'APTA - Carnet válido',
                'color' => 'green',
                'can_play' => true
            ],
            self::Medical_Restriction => [
                'status' => 'warning',
                'message' => 'RESTRICCIÓN - Verificar con médico',
                'color' => 'yellow',
                'can_play' => true
            ],
            self::Expired => [
                'status' => 'error',
                'message' => 'NO APTA - Carnet vencido',
                'color' => 'red',
                'can_play' => false
            ],
            self::Suspended => [
                'status' => 'error',
                'message' => 'NO APTA - Jugadora suspendida',
                'color' => 'red',
                'can_play' => false
            ],
            default => [
                'status' => 'error',
                'message' => 'NO APTA - Carnet inválido',
                'color' => 'red',
                'can_play' => false
            ]
        };
    }
}

