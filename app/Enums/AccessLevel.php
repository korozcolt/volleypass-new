<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Contracts\HasIcon;

enum AccessLevel: string implements HasLabel, HasColor, HasIcon {
    case System = 'system';
    case League = 'league';
    case Club = 'club';
    case Personal = 'personal';
    case Medical = 'medical';
    case Verification = 'verification';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::System => 'Sistema Completo',
            self::League => 'Liga',
            self::Club => 'Club',
            self::Personal => 'Personal',
            self::Medical => 'Médico',
            self::Verification => 'Verificación',
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::System => 'danger',
            self::League => 'primary',
            self::Club => 'success',
            self::Personal => 'info',
            self::Medical => 'purple',
            self::Verification => 'gray',
        };
    }

    public function getIcon(): string | null
    {
        return match ($this) {
            self::System => 'heroicon-o-globe-alt',
            self::League => 'heroicon-o-building-office-2',
            self::Club => 'heroicon-o-building-office',
            self::Personal => 'heroicon-o-user',
            self::Medical => 'heroicon-o-heart',
            self::Verification => 'heroicon-o-qr-code',
        };
    }

    public function getColorHtml(): ?string
    {
        return match ($this) {
            self::System => 'bg-red-100 text-red-800',
            self::League => 'bg-blue-100 text-blue-800',
            self::Club => 'bg-green-100 text-green-800',
            self::Personal => 'bg-cyan-100 text-cyan-800',
            self::Medical => 'bg-purple-100 text-purple-800',
            self::Verification => 'bg-gray-100 text-gray-800',
        };
    }

    public function getLabelHtml(): ?string
    {
        return '<span class="py-1 px-3 rounded-full text-xs font-medium '.$this->getColorHtml().'">'.$this->getLabel().'</span>';
    }
}
