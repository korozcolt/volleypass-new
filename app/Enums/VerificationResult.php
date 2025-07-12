<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Contracts\HasIcon;

enum VerificationResult: string implements HasLabel, HasColor, HasIcon
{
    case Success = 'success';
    case Warning = 'warning';
    case Error = 'error';
    case Invalid_QR = 'invalid_qr';
    case Network_Error = 'network_error';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Success => 'Verificación Exitosa',
            self::Warning => 'Verificado con Advertencias',
            self::Error => 'Error en Verificación',
            self::Invalid_QR => 'Código QR Inválido',
            self::Network_Error => 'Error de Conexión',
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::Success => 'success',
            self::Warning => 'warning',
            self::Error => 'danger',
            self::Invalid_QR => 'purple',
            self::Network_Error => 'gray',
        };
    }

    public function getIcon(): string | null
    {
        return match ($this) {
            self::Success => 'heroicon-o-check-circle',
            self::Warning => 'heroicon-o-exclamation-triangle',
            self::Error => 'heroicon-o-x-circle',
            self::Invalid_QR => 'heroicon-o-qr-code',
            self::Network_Error => 'heroicon-o-wifi',
        };
    }

    public function getColorHtml(): ?string
    {
        return match ($this) {
            self::Success => 'bg-green-100 text-green-800',
            self::Warning => 'bg-yellow-100 text-yellow-800',
            self::Error => 'bg-red-100 text-red-800',
            self::Invalid_QR => 'bg-purple-100 text-purple-800',
            self::Network_Error => 'bg-gray-100 text-gray-800',
        };
    }
}
