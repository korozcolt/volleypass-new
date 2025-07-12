<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Contracts\HasIcon;

enum LogLevel: string implements HasLabel, HasColor, HasIcon {
    case Info = 'info';
    case Warning = 'warning';
    case Error = 'error';
    case Critical = 'critical';
    case Debug = 'debug';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Info => 'Información',
            self::Warning => 'Advertencia',
            self::Error => 'Error',
            self::Critical => 'Crítico',
            self::Debug => 'Debug',
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::Info => 'info',
            self::Warning => 'warning',
            self::Error => 'danger',
            self::Critical => 'purple',
            self::Debug => 'gray',
        };
    }

    public function getIcon(): string | null
    {
        return match ($this) {
            self::Info => 'heroicon-o-information-circle',
            self::Warning => 'heroicon-o-exclamation-triangle',
            self::Error => 'heroicon-o-x-circle',
            self::Critical => 'heroicon-o-fire',
            self::Debug => 'heroicon-o-bug-ant',
        };
    }

    public function getColorHtml(): ?string
    {
        return match ($this) {
            self::Info => 'bg-blue-100 text-blue-800',
            self::Warning => 'bg-yellow-100 text-yellow-800',
            self::Error => 'bg-red-100 text-red-800',
            self::Critical => 'bg-purple-100 text-purple-800',
            self::Debug => 'bg-gray-100 text-gray-800',
        };
    }

    public function getLabelHtml(): ?string
    {
        return '<span class="py-1 px-3 rounded-full text-xs font-medium '.$this->getColorHtml().'">'.$this->getLabel().'</span>';
    }
}

