<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Contracts\HasIcon;
use App\Traits\EnumHelpers;

enum EventType: string implements HasLabel, HasColor, HasIcon
{
    use EnumHelpers;

    case Match = 'match';
    case Tournament = 'tournament';
    case Training = 'training';
    case Friendly = 'friendly';
    case Medical_Check = 'medical_check';
    case Registration = 'registration';
    case Verification = 'verification';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Match => 'Partido Oficial',
            self::Tournament => 'Torneo',
            self::Training => 'Entrenamiento',
            self::Friendly => 'Partido Amistoso',
            self::Medical_Check => 'Chequeo Médico',
            self::Registration => 'Registro/Inscripción',
            self::Verification => 'Verificación de Carnet',
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::Match => 'primary',
            self::Tournament => 'warning',
            self::Training => 'success',
            self::Friendly => 'info',
            self::Medical_Check => 'purple',
            self::Registration => 'gray',
            self::Verification => 'info',
        };
    }

    public function getIcon(): string | null
    {
        return match ($this) {
            self::Match => 'heroicon-o-trophy',
            self::Tournament => 'heroicon-o-star',
            self::Training => 'heroicon-o-academic-cap',
            self::Friendly => 'heroicon-o-hand-raised',
            self::Medical_Check => 'heroicon-o-heart',
            self::Registration => 'heroicon-o-clipboard-document-list',
            self::Verification => 'heroicon-o-qr-code',
        };
    }

    public function getColorHtml(): ?string
    {
        return match ($this) {
            self::Match => 'bg-blue-100 text-blue-800',
            self::Tournament => 'bg-yellow-100 text-yellow-800',
            self::Training => 'bg-green-100 text-green-800',
            self::Friendly => 'bg-cyan-100 text-cyan-800',
            self::Medical_Check => 'bg-purple-100 text-purple-800',
            self::Registration => 'bg-gray-100 text-gray-800',
            self::Verification => 'bg-blue-100 text-blue-800',
        };
    }

    /**
     * Eventos que requieren verificación médica estricta
     */
    public function requiresStrictMedicalCheck(): bool
    {
        return in_array($this, [
            self::Match,
            self::Tournament
        ]);
    }

    /**
     * Eventos que permiten jugadoras con restricciones
     */
    public function allowsRestrictedPlayers(): bool
    {
        return in_array($this, [
            self::Training,
            self::Friendly
        ]);
    }
}
