<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Contracts\HasIcon;

enum MatchPhase: string implements HasLabel, HasColor, HasIcon
{
    case GROUP_STAGE = 'group_stage';
    case ROUND_OF_16 = 'round_of_16';
    case QUARTER_FINALS = 'quarter_finals';
    case SEMI_FINALS = 'semi_finals';
    case THIRD_PLACE = 'third_place';
    case FINAL = 'final';
    case REGULAR_SEASON = 'regular_season';
    case PLAYOFFS = 'playoffs';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::GROUP_STAGE => 'Fase de Grupos',
            self::ROUND_OF_16 => 'Octavos de Final',
            self::QUARTER_FINALS => 'Cuartos de Final',
            self::SEMI_FINALS => 'Semifinales',
            self::THIRD_PLACE => 'Tercer Lugar',
            self::FINAL => 'Final',
            self::REGULAR_SEASON => 'Temporada Regular',
            self::PLAYOFFS => 'Playoffs',
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::GROUP_STAGE => 'gray',
            self::ROUND_OF_16 => 'blue',
            self::QUARTER_FINALS => 'indigo',
            self::SEMI_FINALS => 'purple',
            self::THIRD_PLACE => 'orange',
            self::FINAL => 'yellow',
            self::REGULAR_SEASON => 'green',
            self::PLAYOFFS => 'red',
        };
    }

    public function getIcon(): string | null
    {
        return match ($this) {
            self::GROUP_STAGE => 'heroicon-o-squares-2x2',
            self::ROUND_OF_16 => 'heroicon-o-rectangle-group',
            self::QUARTER_FINALS => 'heroicon-o-squares-plus',
            self::SEMI_FINALS => 'heroicon-o-trophy',
            self::THIRD_PLACE => 'heroicon-o-star',
            self::FINAL => 'heroicon-o-crown',
            self::REGULAR_SEASON => 'heroicon-o-calendar-days',
            self::PLAYOFFS => 'heroicon-o-fire',
        };
    }

    public function getOrder(): int
    {
        return match ($this) {
            self::GROUP_STAGE => 1,
            self::REGULAR_SEASON => 1,
            self::ROUND_OF_16 => 2,
            self::QUARTER_FINALS => 3,
            self::SEMI_FINALS => 4,
            self::THIRD_PLACE => 5,
            self::FINAL => 6,
            self::PLAYOFFS => 7,
        };
    }

    public static function getKnockoutPhases(): array
    {
        return [
            self::ROUND_OF_16,
            self::QUARTER_FINALS,
            self::SEMI_FINALS,
            self::THIRD_PLACE,
            self::FINAL,
        ];
    }
}