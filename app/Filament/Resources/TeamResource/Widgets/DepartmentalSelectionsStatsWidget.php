<?php

namespace App\Filament\Resources\TeamResource\Widgets;

use App\Enums\TeamType;
use App\Models\Team;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DepartmentalSelectionsStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $totalSelections = Team::where('team_type', TeamType::SELECTION)->count();
        
        $playersInSelections = Team::where('team_type', TeamType::SELECTION)
            ->withCount('players')
            ->get()
            ->sum('players_count');
        
        $activeDepartments = Team::where('team_type', TeamType::SELECTION)
            ->distinct('department_id')
            ->count('department_id');
        
        $participatingLeagues = Team::where('team_type', TeamType::SELECTION)
            ->distinct('league_id')
            ->count('league_id');

        return [
            Stat::make('Total Selecciones', $totalSelections)
                ->description('Selecciones departamentales creadas')
                ->descriptionIcon('heroicon-m-flag')
                ->color('success')
                ->chart([7, 2, 10, 3, 15, 4, 17]),
            
            Stat::make('Jugadores en Selecciones', $playersInSelections)
                ->description('Total de jugadores seleccionados')
                ->descriptionIcon('heroicon-m-users')
                ->color('info')
                ->chart([3, 8, 5, 12, 9, 15, 11]),
            
            Stat::make('Departamentos Activos', $activeDepartments)
                ->description('Departamentos con selecciones')
                ->descriptionIcon('heroicon-m-map')
                ->color('warning')
                ->chart([2, 4, 3, 6, 5, 8, 7]),
            
            Stat::make('Ligas Participantes', $participatingLeagues)
                ->description('Ligas con selecciones departamentales')
                ->descriptionIcon('heroicon-m-trophy')
                ->color('danger')
                ->chart([1, 3, 2, 5, 4, 6, 5]),
        ];
    }

    protected function getColumns(): int
    {
        return 4;
    }
}