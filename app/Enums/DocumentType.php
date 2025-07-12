<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Contracts\HasIcon;

enum DocumentType: string implements HasLabel, HasColor, HasIcon {
    case Identity_Card = 'identity_card';
    case Birth_Certificate = 'birth_certificate';
    case Medical_Certificate = 'medical_certificate';
    case Photo = 'photo';
    case Parent_Authorization = 'parent_authorization';
    case Transfer_Letter = 'transfer_letter';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Identity_Card => 'Cédula de Identidad',
            self::Birth_Certificate => 'Certificado de Nacimiento',
            self::Medical_Certificate => 'Certificado Médico',
            self::Photo => 'Fotografía',
            self::Parent_Authorization => 'Autorización de Padres',
            self::Transfer_Letter => 'Carta de Transferencia',
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::Identity_Card => 'primary',
            self::Birth_Certificate => 'info',
            self::Medical_Certificate => 'success',
            self::Photo => 'purple',
            self::Parent_Authorization => 'warning',
            self::Transfer_Letter => 'gray',
        };
    }

    public function getIcon(): string | null
    {
        return match ($this) {
            self::Identity_Card => 'heroicon-o-identification',
            self::Birth_Certificate => 'heroicon-o-document-text',
            self::Medical_Certificate => 'heroicon-o-heart',
            self::Photo => 'heroicon-o-photo',
            self::Parent_Authorization => 'heroicon-o-clipboard-document-check',
            self::Transfer_Letter => 'heroicon-o-arrow-right-circle',
        };
    }

    public function getColorHtml(): ?string
    {
        return match ($this) {
            self::Identity_Card => 'bg-blue-100 text-blue-800',
            self::Birth_Certificate => 'bg-cyan-100 text-cyan-800',
            self::Medical_Certificate => 'bg-green-100 text-green-800',
            self::Photo => 'bg-purple-100 text-purple-800',
            self::Parent_Authorization => 'bg-yellow-100 text-yellow-800',
            self::Transfer_Letter => 'bg-gray-100 text-gray-800',
        };
    }

    public function getLabelHtml(): ?string
    {
        return '<span class="py-1 px-3 rounded-full text-xs font-medium '.$this->getColorHtml().'">'.$this->getLabel().'</span>';
    }

    public function isRequired(): bool
    {
        return match ($this) {
            self::Identity_Card => true,
            self::Birth_Certificate => true,
            self::Medical_Certificate => true,
            self::Photo => true,
            self::Parent_Authorization => false, // Solo para menores
            self::Transfer_Letter => false, // Solo para transferencias
        };
    }

    public function getValidityMonths(): ?int
    {
        return match ($this) {
            self::Medical_Certificate => 12,
            self::Parent_Authorization => 12,
            default => null, // No vence
        };
    }
}
