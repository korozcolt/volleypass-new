<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Contracts\HasIcon;

enum PaymentType: string implements HasLabel, HasColor, HasIcon
{
    case Federation = 'federation';
    case Registration = 'registration';
    case Tournament = 'tournament';
    case Transfer = 'transfer';
    case Fine = 'fine';
    case MonthlyFee = 'monthly_fee';
    case ClubToLeague = 'club_to_league';
    case PlayerToClub = 'player_to_club';
    case Other = 'other';

    public function getLabel(): string
    {
        return match($this) {
            self::Federation => 'Federación',
            self::Registration => 'Inscripción',
            self::Tournament => 'Torneo',
            self::Transfer => 'Traspaso',
            self::Fine => 'Multa',
            self::MonthlyFee => 'Mensualidad',
            self::ClubToLeague => 'Club a Liga',
            self::PlayerToClub => 'Jugador a Club',
            self::Other => 'Otro',
        };
    }

    public function getColor(): string
    {
        return match($this) {
            self::Federation => 'primary',
            self::Registration => 'success',
            self::Tournament => 'warning',
            self::Transfer => 'info',
            self::Fine => 'danger',
            self::MonthlyFee => 'info',
            self::ClubToLeague => 'primary',
            self::PlayerToClub => 'success',
            self::Other => 'gray',
        };
    }

    public function getIcon(): string
    {
        return match($this) {
            self::Federation => 'heroicon-o-building-office',
            self::Registration => 'heroicon-o-user-plus',
            self::Tournament => 'heroicon-o-trophy',
            self::Transfer => 'heroicon-o-arrow-right-circle',
            self::Fine => 'heroicon-o-exclamation-triangle',
            self::MonthlyFee => 'heroicon-o-calendar',
            self::ClubToLeague => 'heroicon-o-arrow-up',
            self::PlayerToClub => 'heroicon-o-arrow-down',
            self::Other => 'heroicon-o-ellipsis-horizontal',
        };
    }
}
