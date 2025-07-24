<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Contracts\HasIcon;

enum ViolationType: string implements HasLabel, HasColor, HasIcon
{
    case Misconduct = 'misconduct';
    case DelayOfGame = 'delay_of_game';
    case Unsportsmanlike = 'unsportsmanlike';
    case ViolentConduct = 'violent_conduct';
    case RefereeAbuse = 'referee_abuse';
    case IllegalSubstitution = 'illegal_substitution';
    case RotationFault = 'rotation_fault';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Misconduct => 'Mala Conducta',
            self::DelayOfGame => 'Retraso de Juego',
            self::Unsportsmanlike => 'Conducta Antideportiva',
            self::ViolentConduct => 'Conducta Violenta',
            self::RefereeAbuse => 'Irrespeto al Árbitro',
            self::IllegalSubstitution => 'Sustitución Ilegal',
            self::RotationFault => 'Falta de Rotación',
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::Misconduct => 'warning',
            self::DelayOfGame => 'info',
            self::Unsportsmanlike => 'warning',
            self::ViolentConduct => 'danger',
            self::RefereeAbuse => 'danger',
            self::IllegalSubstitution => 'primary',
            self::RotationFault => 'primary',
        };
    }

    public function getIcon(): string | null
    {
        return match ($this) {
            self::Misconduct => 'heroicon-o-exclamation-triangle',
            self::DelayOfGame => 'heroicon-o-clock',
            self::Unsportsmanlike => 'heroicon-o-hand-thumb-down',
            self::ViolentConduct => 'heroicon-o-fire',
            self::RefereeAbuse => 'heroicon-o-megaphone',
            self::IllegalSubstitution => 'heroicon-o-arrow-path',
            self::RotationFault => 'heroicon-o-arrow-path-rounded-square',
        };
    }

    public function getColorHtml(): ?string
    {
        return match ($this) {
            self::Misconduct => 'bg-yellow-100 text-yellow-800',
            self::DelayOfGame => 'bg-blue-100 text-blue-800',
            self::Unsportsmanlike => 'bg-orange-100 text-orange-800',
            self::ViolentConduct => 'bg-red-100 text-red-800',
            self::RefereeAbuse => 'bg-red-200 text-red-900',
            self::IllegalSubstitution => 'bg-purple-100 text-purple-800',
            self::RotationFault => 'bg-indigo-100 text-indigo-800',
        };
    }

    public function getLabelHtml(): ?string
    {
        return '<span class="py-1 px-3 rounded-full text-xs font-medium '.$this->getColorHtml().'">'.$this->getLabel().'</span>';
    }

    public function getDescription(): string
    {
        return match ($this) {
            self::Misconduct => 'Comportamiento inapropiado general',
            self::DelayOfGame => 'Acciones que retrasan el desarrollo del juego',
            self::Unsportsmanlike => 'Comportamiento contrario al espíritu deportivo',
            self::ViolentConduct => 'Agresión física o amenazas',
            self::RefereeAbuse => 'Insultos o faltas de respeto hacia los árbitros',
            self::IllegalSubstitution => 'Cambios no permitidos por el reglamento',
            self::RotationFault => 'Error en el orden de rotación de jugadoras',
        };
    }

    public function getRecommendedCard(): CardType
    {
        return match ($this) {
            self::Misconduct => CardType::Yellow,
            self::DelayOfGame => CardType::Yellow,
            self::Unsportsmanlike => CardType::Yellow,
            self::ViolentConduct => CardType::RedMatch,
            self::RefereeAbuse => CardType::Red,
            self::IllegalSubstitution => CardType::Yellow,
            self::RotationFault => CardType::Yellow,
        };
    }
}