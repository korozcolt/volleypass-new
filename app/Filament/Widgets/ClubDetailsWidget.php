<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Database\Eloquent\Model;

class ClubDetailsWidget extends Widget
{
    protected static string $view = 'filament.widgets.club-details-widget';
    
    protected int | string | array $columnSpan = 'full';
    
    public ?Model $record = null;
    
    protected function getViewData(): array
    {
        if (!$this->record) {
            return [
                'club' => null,
                'jugadoras' => collect(),
                'directivos' => collect(),
                'torneos' => collect(),
                'stats' => [
                    'total_jugadoras' => 0,
                    'jugadoras_federadas' => 0,
                    'directivos_activos' => 0,
                    'torneos_participados' => 0,
                ],
            ];
        }
        
        $club = $this->record;
        
        // Obtener jugadoras activas
        $jugadoras = $club->jugadoras()
            ->where('activa', true)
            ->with(['user'])
            ->latest()
            ->limit(10)
            ->get();
        
        // Obtener directivos activos
        $directivos = $club->directivos()
            ->wherePivot('is_active', true)
            ->with(['user'])
            ->get();
        
        // Obtener torneos participados (últimos 5)
        $torneos = $club->torneos()
            ->with(['torneo'])
            ->latest()
            ->limit(5)
            ->get();
        
        // Calcular estadísticas
        $stats = [
            'total_jugadoras' => $club->jugadoras()->where('activa', true)->count(),
            'jugadoras_federadas' => $club->jugadoras()
                ->where('activa', true)
                ->where('es_federada', true)
                ->count(),
            'directivos_activos' => $directivos->count(),
            'torneos_participados' => $club->torneos()->count(),
        ];
        
        return [
            'club' => $club,
            'jugadoras' => $jugadoras,
            'directivos' => $directivos,
            'torneos' => $torneos,
            'stats' => $stats,
        ];
    }
    
    public static function canView(): bool
    {
        return true;
    }
}