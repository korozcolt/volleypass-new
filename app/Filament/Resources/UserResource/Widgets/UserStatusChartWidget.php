<?php

namespace App\Filament\Resources\UserResource\Widgets;

use App\Models\User;
use App\Enums\UserStatus;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class UserStatusChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Distribución por Estado';

    protected static ?string $description = 'Estados actuales de usuarios';

    protected static ?int $sort = 2;

    protected static ?string $pollingInterval = '60s';

    protected int | string | array $columnSpan = [
        'md' => 2,
        'xl' => 3,
    ];

    protected function getData(): array
    {
        // Query usando el valor string del enum para agrupación
        $statusData = User::selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->orderBy('count', 'desc')
            ->get();

        $labels = [];
        $data = [];
        $colors = [];

        // Definir colores usando los valores string del enum
        $statusColors = [
            UserStatus::Active->value => '#10b981',      // verde
            UserStatus::Pending->value => '#f59e0b',     // amarillo
            UserStatus::Suspended->value => '#ef4444',   // rojo
            UserStatus::Inactive->value => '#6b7280',    // gris
            UserStatus::Blocked->value => '#7c2d12',     // rojo oscuro
        ];

        foreach ($statusData as $status) {
            // $status->status ya es una instancia de UserStatus debido al casting
            $statusEnum = $status->status;

            $labels[] = $statusEnum->getLabel();
            $data[] = $status->count;
            $colors[] = $statusColors[$statusEnum->value] ?? '#94a3b8';
        }

        return [
            'datasets' => [
                [
                    'label' => 'Usuarios por estado',
                    'data' => $data,
                    'backgroundColor' => $colors,
                    'borderColor' => $colors,
                    'borderWidth' => 2,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                ],
                'tooltip' => [
                    'callbacks' => [
                        'label' => 'function(context) {
                            const label = context.label || "";
                            const value = context.parsed;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((value / total) * 100).toFixed(1);
                            return label + ": " + value + " (" + percentage + "%)";
                        }'
                    ]
                ]
            ],
            'responsive' => true,
            'maintainAspectRatio' => false,
        ];
    }

    public static function canView(): bool
    {
        return Auth::user()?->hasAnyRole([
            'SuperAdmin',
            'LeagueAdmin'
        ]) ?? false;
    }
}
