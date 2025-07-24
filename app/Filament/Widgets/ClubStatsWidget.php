<?php

namespace App\Filament\Widgets;

use App\Models\Club;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class ClubStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $totalClubes = Club::count();
        $clubesFederados = Club::where('es_federado', true)->count();
        $clubesNoFederados = $totalClubes - $clubesFederados;
        $crecimientoMensual = $this->getCrecimientoMensual();
        
        return [
            Stat::make('Total Clubes', $totalClubes)
                ->description('Clubes registrados')
                ->descriptionIcon('heroicon-m-building-office')
                ->color('primary')
                ->chart($this->getClubsChart()),
                
            Stat::make('Clubes Federados', $clubesFederados)
                ->description($clubesNoFederados . ' no federados')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success')
                ->chart($this->getFederatedChart()),
                
            Stat::make('Crecimiento Mensual', $crecimientoMensual . '%')
                ->description('Comparado con el mes anterior')
                ->descriptionIcon($crecimientoMensual >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($crecimientoMensual >= 0 ? 'success' : 'danger')
                ->chart($this->getGrowthChart()),
                
            Stat::make('Distribución por Departamento', $this->getDepartamentoConMasClubes())
                ->description('Departamento líder')
                ->descriptionIcon('heroicon-m-map-pin')
                ->color('warning')
                ->chart($this->getDepartmentChart()),
        ];
    }
    
    private function getCrecimientoMensual(): float
    {
        $currentMonth = Club::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
            
        $previousMonth = Club::whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->count();
            
        if ($previousMonth == 0) {
            return $currentMonth > 0 ? 100 : 0;
        }
        
        return round((($currentMonth - $previousMonth) / $previousMonth) * 100, 1);
    }
    
    private function getDepartamentoConMasClubes(): string
    {
        $departamento = Club::select('departments.name')
            ->join('departments', 'clubs.department_id', '=', 'departments.id')
            ->groupBy('departments.id', 'departments.name')
            ->orderByRaw('COUNT(*) DESC')
            ->first();
            
        return $departamento ? $departamento->nombre : 'N/A';
    }
    
    private function getClubsChart(): array
    {
        return Club::selectRaw('COUNT(*) as count')
            ->whereDate('created_at', '>=', now()->subDays(7))
            ->groupByRaw('DATE(created_at)')
            ->orderBy('created_at')
            ->pluck('count')
            ->toArray();
    }
    
    private function getFederatedChart(): array
    {
        return Club::selectRaw('COUNT(*) as count')
            ->where('es_federado', true)
            ->whereDate('created_at', '>=', now()->subDays(7))
            ->groupByRaw('DATE(created_at)')
            ->orderBy('created_at')
            ->pluck('count')
            ->toArray();
    }
    
    private function getGrowthChart(): array
    {
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $count = Club::whereDate('created_at', $date)->count();
            $data[] = $count;
        }
        return $data;
    }
    
    private function getDepartmentChart(): array
    {
        return Club::select(DB::raw('COUNT(*) as count'))
            ->join('departments', 'clubs.department_id', '=', 'departments.id')
            ->groupBy('departments.id')
            ->orderByRaw('COUNT(*) DESC')
            ->limit(7)
            ->pluck('count')
            ->toArray();
    }
}