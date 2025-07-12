<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Contracts\HasIcon;

enum UserRole: string implements HasLabel, HasColor, HasIcon {
    case SuperAdmin = 'superadmin';
    case LeagueAdmin = 'league_admin';
    case ClubDirector = 'club_director';
    case Player = 'player';
    case Coach = 'coach';
    case SportsDoctor = 'sports_doctor';
    case Verifier = 'verifier';
    case Referee = 'referee';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::SuperAdmin => 'Super Administrador',
            self::LeagueAdmin => 'Administrador de Liga',
            self::ClubDirector => 'Director de Club',
            self::Player => 'Jugadora',
            self::Coach => 'Entrenador',
            self::SportsDoctor => 'Médico Deportivo',
            self::Verifier => 'Verificador',
            self::Referee => 'Árbitro',
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::SuperAdmin => 'danger',
            self::LeagueAdmin => 'primary',
            self::ClubDirector => 'success',
            self::Player => 'info',
            self::Coach => 'warning',
            self::SportsDoctor => 'purple',
            self::Verifier => 'gray',
            self::Referee => 'yellow',
        };
    }

    public function getIcon(): string | null
    {
        return match ($this) {
            self::SuperAdmin => 'heroicon-o-shield-check',
            self::LeagueAdmin => 'heroicon-o-user-circle',
            self::ClubDirector => 'heroicon-o-building-office',
            self::Player => 'heroicon-o-user',
            self::Coach => 'heroicon-o-academic-cap',
            self::SportsDoctor => 'heroicon-o-heart',
            self::Verifier => 'heroicon-o-qr-code',
            self::Referee => 'heroicon-o-whistle',
        };
    }

    public function getColorHtml(): ?string
    {
        return match ($this) {
            self::SuperAdmin => 'bg-red-100 text-red-800',
            self::LeagueAdmin => 'bg-blue-100 text-blue-800',
            self::ClubDirector => 'bg-green-100 text-green-800',
            self::Player => 'bg-cyan-100 text-cyan-800',
            self::Coach => 'bg-yellow-100 text-yellow-800',
            self::SportsDoctor => 'bg-purple-100 text-purple-800',
            self::Verifier => 'bg-gray-100 text-gray-800',
            self::Referee => 'bg-amber-100 text-amber-800',
        };
    }

    public function getLabelHtml(): ?string
    {
        return '<span class="py-1 px-3 rounded-full text-xs font-medium '.$this->getColorHtml().'">'.$this->getLabel().'</span>';
    }

    public function getPermissionLevel(): int
    {
        return match ($this) {
            self::SuperAdmin => 10,
            self::LeagueAdmin => 8,
            self::ClubDirector => 6,
            self::Coach => 4,
            self::SportsDoctor => 5,
            self::Verifier => 3,
            self::Referee => 3,
            self::Player => 1,
        };
    }
}
