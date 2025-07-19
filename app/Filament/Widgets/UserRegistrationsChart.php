<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class UserRegistrationsChart extends ChartWidget
{
    protected static ?string $heading = 'Registros de Usuarios';
    protected static string $color = 'info';
    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $labels = [];
        $counts = [];

        // Generar datos para los últimos 12 meses
        for ($i = 11; $i >= 0; $i--) {
            $startOfMonth = now()->subMonths($i)->startOfMonth();
            $endOfMonth = now()->subMonths($i)->endOfMonth();
            $monthName = $startOfMonth->format('M Y');

            $count = User::whereBetween('created_at', [$startOfMonth, $endOfMonth])->count();

            $labels[] = $monthName;
            $counts[] = $count;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Usuarios registrados',
                    'data' => $counts,
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'borderColor' => 'rgb(59, 130, 246)',
                    'fill' => true,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
