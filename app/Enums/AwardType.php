<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Contracts\HasIcon;

enum AwardType: string implements HasLabel, HasColor, HasIcon {
    case MVP = 'mvp';
    case Top_Scorer = 'top_scorer';
    case Best_Blocker = 'best_blocker';
    case Best_Server = 'best_server';
    case Best_Setter = 'best_setter';
    case Fair_Play = 'fair_play';
    case Team_Selection = 'team_selection';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::MVP => 'Mejor Jugadora (MVP)',
            self::Top_Scorer => 'Máxima Anotadora',
            self::Best_Blocker => 'Mejor Bloqueadora',
            self::Best_Server => 'Mejor Sacadora',
            self::Best_Setter => 'Mejor Armadora',
            self::Fair_Play => 'Juego Limpio',
            self::Team_Selection => 'Selección de Equipo',
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::MVP => 'warning',
            self::Top_Scorer => 'danger',
            self::Best_Blocker => 'success',
            self::Best_Server => 'info',
            self::Best_Setter => 'purple',
            self::Fair_Play => 'gray',
            self::Team_Selection => 'primary',
        };
    }

    public function getIcon(): string | null
    {
        return match ($this) {
            self::MVP => 'heroicon-o-trophy',
            self::Top_Scorer => 'heroicon-o-star',
            self::Best_Blocker => 'heroicon-o-shield-check',
            self::Best_Server => 'heroicon-o-arrow-up-circle',
            self::Best_Setter => 'heroicon-o-hand-raised',
            self::Fair_Play => 'heroicon-o-heart',
            self::Team_Selection => 'heroicon-o-user-group',
        };
    }

    public function getColorHtml(): ?string
    {
        return match ($this) {
            self::MVP => 'bg-yellow-100 text-yellow-800',
            self::Top_Scorer => 'bg-red-100 text-red-800',
            self::Best_Blocker => 'bg-green-100 text-green-800',
            self::Best_Server => 'bg-blue-100 text-blue-800',
            self::Best_Setter => 'bg-purple-100 text-purple-800',
            self::Fair_Play => 'bg-gray-100 text-gray-800',
            self::Team_Selection => 'bg-indigo-100 text-indigo-800',
        };
    }

    public function getLabelHtml(): ?string
    {
        return '<span class="py-1 px-3 rounded-full text-xs font-medium '.$this->getColorHtml().'">'.$this->getLabel().'</span>';
    }
}
