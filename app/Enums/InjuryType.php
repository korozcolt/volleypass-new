<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Contracts\HasIcon;

enum InjuryType: string implements HasLabel, HasColor, HasIcon {
    case Ankle = 'ankle';
    case Knee = 'knee';
    case Shoulder = 'shoulder';
    case Wrist = 'wrist';
    case Back = 'back';
    case Finger = 'finger';
    case Other = 'other';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Ankle => 'Tobillo',
            self::Knee => 'Rodilla',
            self::Shoulder => 'Hombro',
            self::Wrist => 'Muñeca',
            self::Back => 'Espalda',
            self::Finger => 'Dedo',
            self::Other => 'Otra',
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::Ankle => 'warning',
            self::Knee => 'danger',
            self::Shoulder => 'info',
            self::Wrist => 'purple',
            self::Back => 'gray',
            self::Finger => 'success',
            self::Other => 'secondary',
        };
    }

    public function getIcon(): string | null
    {
        return match ($this) {
            self::Ankle => 'heroicon-o-bolt',
            self::Knee => 'heroicon-o-exclamation-triangle',
            self::Shoulder => 'heroicon-o-hand-raised',
            self::Wrist => 'heroicon-o-hand-thumb-up',
            self::Back => 'heroicon-o-user',
            self::Finger => 'heroicon-o-finger-print',
            self::Other => 'heroicon-o-question-mark-circle',
        };
    }

    public function getColorHtml(): ?string
    {
        return match ($this) {
            self::Ankle => 'bg-yellow-100 text-yellow-800',
            self::Knee => 'bg-red-100 text-red-800',
            self::Shoulder => 'bg-blue-100 text-blue-800',
            self::Wrist => 'bg-purple-100 text-purple-800',
            self::Back => 'bg-gray-100 text-gray-800',
            self::Finger => 'bg-green-100 text-green-800',
            self::Other => 'bg-slate-100 text-slate-800',
        };
    }

    public function getLabelHtml(): ?string
    {
        return '<span class="py-1 px-3 rounded-full text-xs font-medium '.$this->getColorHtml().'">'.$this->getLabel().'</span>';
    }
}
