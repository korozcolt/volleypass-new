<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Contracts\HasIcon;

enum SanctionType: string implements HasLabel, HasColor, HasIcon
{
    case Warning = 'warning';
    case YellowCard = 'yellow_card';
    case RedCard = 'red_card';
    case MatchBan = 'match_ban';
    case TournamentBan = 'tournament_ban';
    case Suspension = 'suspension';
    case Fine = 'fine';
    case Expulsion = 'expulsion';
    case Disqualification = 'disqualification';
    case TechnicalFoul = 'technical_foul';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Warning => 'Advertencia',
            self::YellowCard => 'Tarjeta Amarilla',
            self::RedCard => 'Tarjeta Roja',
            self::MatchBan => 'Suspensión de Partido',
            self::TournamentBan => 'Suspensión de Torneo',
            self::Suspension => 'Suspensión',
            self::Fine => 'Multa',
            self::Expulsion => 'Expulsión',
            self::Disqualification => 'Descalificación',
            self::TechnicalFoul => 'Falta Técnica',
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::Warning => 'warning',
            self::YellowCard => 'warning',
            self::RedCard => 'danger',
            self::MatchBan => 'danger',
            self::TournamentBan => 'danger',
            self::Suspension => 'danger',
            self::Fine => 'info',
            self::Expulsion => 'danger',
            self::Disqualification => 'danger',
            self::TechnicalFoul => 'warning',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::Warning => 'heroicon-o-exclamation-triangle',
            self::YellowCard => 'heroicon-o-rectangle-stack',
            self::RedCard => 'heroicon-o-rectangle-stack',
            self::MatchBan => 'heroicon-o-no-symbol',
            self::TournamentBan => 'heroicon-o-x-circle',
            self::Suspension => 'heroicon-o-pause-circle',
            self::Fine => 'heroicon-o-banknotes',
            self::Expulsion => 'heroicon-o-user-minus',
            self::Disqualification => 'heroicon-o-x-mark',
            self::TechnicalFoul => 'heroicon-o-flag',
        };
    }

    public function getDescription(): string
    {
        return match ($this) {
            self::Warning => 'Advertencia verbal o escrita sin consecuencias inmediatas',
            self::YellowCard => 'Tarjeta amarilla por conducta antideportiva',
            self::RedCard => 'Tarjeta roja por falta grave',
            self::MatchBan => 'Prohibición de participar en el partido actual',
            self::TournamentBan => 'Prohibición de participar en el torneo completo',
            self::Suspension => 'Suspensión temporal de la competición',
            self::Fine => 'Sanción económica',
            self::Expulsion => 'Expulsión definitiva del partido o competición',
            self::Disqualification => 'Descalificación del jugador o equipo',
            self::TechnicalFoul => 'Falta técnica por violación de reglas',
        };
    }

    public function isCardType(): bool
    {
        return in_array($this, [self::YellowCard, self::RedCard]);
    }

    public function requiresImmedateAction(): bool
    {
        return in_array($this, [self::RedCard, self::MatchBan, self::Expulsion]);
    }

    public function affectsEligibility(): bool
    {
        return in_array($this, [
            self::MatchBan,
            self::TournamentBan,
            self::Suspension,
            self::Expulsion,
            self::Disqualification
        ]);
    }

    public static function getCardTypes(): array
    {
        return [self::YellowCard, self::RedCard];
    }

    public static function getSuspensionTypes(): array
    {
        return [
            self::MatchBan,
            self::TournamentBan,
            self::Suspension,
            self::Expulsion,
            self::Disqualification
        ];
    }
}