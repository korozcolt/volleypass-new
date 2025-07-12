<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Contracts\HasIcon;

enum NotificationType: string implements HasLabel, HasColor, HasIcon {
    case Card_Expiry = 'card_expiry';
    case Medical_Expiry = 'medical_expiry';
    case Document_Approved = 'document_approved';
    case Document_Rejected = 'document_rejected';
    case Registration_Welcome = 'registration_welcome';
    case Tournament_Registration = 'tournament_registration';
    case Match_Reminder = 'match_reminder';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Card_Expiry => 'Vencimiento de Carnet',
            self::Medical_Expiry => 'Vencimiento Médico',
            self::Document_Approved => 'Documento Aprobado',
            self::Document_Rejected => 'Documento Rechazado',
            self::Registration_Welcome => 'Bienvenida',
            self::Tournament_Registration => 'Inscripción a Torneo',
            self::Match_Reminder => 'Recordatorio de Partido',
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::Card_Expiry => 'warning',
            self::Medical_Expiry => 'danger',
            self::Document_Approved => 'success',
            self::Document_Rejected => 'danger',
            self::Registration_Welcome => 'info',
            self::Tournament_Registration => 'primary',
            self::Match_Reminder => 'info',
        };
    }

    public function getIcon(): string | null
    {
        return match ($this) {
            self::Card_Expiry => 'heroicon-o-identification',
            self::Medical_Expiry => 'heroicon-o-heart',
            self::Document_Approved => 'heroicon-o-check-circle',
            self::Document_Rejected => 'heroicon-o-x-circle',
            self::Registration_Welcome => 'heroicon-o-hand-raised',
            self::Tournament_Registration => 'heroicon-o-trophy',
            self::Match_Reminder => 'heroicon-o-bell-alert',
        };
    }

    public function getColorHtml(): ?string
    {
        return match ($this) {
            self::Card_Expiry => 'bg-yellow-100 text-yellow-800',
            self::Medical_Expiry => 'bg-red-100 text-red-800',
            self::Document_Approved => 'bg-green-100 text-green-800',
            self::Document_Rejected => 'bg-red-100 text-red-800',
            self::Registration_Welcome => 'bg-blue-100 text-blue-800',
            self::Tournament_Registration => 'bg-indigo-100 text-indigo-800',
            self::Match_Reminder => 'bg-blue-100 text-blue-800',
        };
    }

    public function getLabelHtml(): ?string
    {
        return '<span class="py-1 px-3 rounded-full text-xs font-medium '.$this->getColorHtml().'">'.$this->getLabel().'</span>';
    }
}
