<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class UserRoleDistributionWidget extends ChartWidget
{
    protected static ?string $heading = 'Distribución de Usuarios por Rol';

    protected static ?string $description = 'Cantidad de usuarios por cada rol del sistema';

    protected static ?int $sort = 2;

    protected static ?string $pollingInterval = '60s';

    protected function getData(): array
    {
        $roleData = DB::table('model_has_roles')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->join('users', 'model_has_roles.model_id', '=', 'users.id')
            ->where('model_has_roles.model_type', User::class)
            ->whereNull('users.deleted_at') // Solo usuarios no eliminados
            ->select('roles.name', DB::raw('count(*) as count'))
            ->groupBy('roles.name')
            ->orderBy('count', 'desc')
            ->get();

        $labels = [];
        $data = [];
        $colors = [];

        // Colores específicos para cada rol
        $roleColors = [
            'SuperAdmin' => '#ef4444',      // rojo
            'LeagueAdmin' => '#3b82f6',     // azul
            'ClubDirector' => '#10b981',    // verde
            'Player' => '#06b6d4',          // cyan
            'Coach' => '#f59e0b',           // amarillo
            'SportsDoctor' => '#8b5cf6',    // púrpura
            'Verifier' => '#6b7280',        // gris
            'Referee' => '#f97316',         // naranja
        ];

        foreach ($roleData as $role) {
            $labels[] = $this->translateRoleName($role->name);
            $data[] = $role->count;
            $colors[] = $roleColors[$role->name] ?? '#94a3b8';
        }

        // Si no hay usuarios con roles, mostrar mensaje
        if (empty($data)) {
            $labels = ['Sin roles asignados'];
            $data = [User::doesntHave('roles')->count()];
            $colors = ['#e5e7eb'];
        }

        return [
            'datasets' => [
                [
                    'label' => 'Usuarios por rol',
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
        return 'doughnut';
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
                    'enabled' => true,
                    'callbacks' => [
                        'label' => 'function(context) {
                            const label = context.label || "";
                            const value = context.parsed || 0;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = Math.round((value / total) * 100);
                            return label + ": " + value + " (" + percentage + "%)";
                        }'
                    ],
                ],
            ],
            'responsive' => true,
            'maintainAspectRatio' => false,
            'cutout' => '60%',
        ];
    }

    /**
     * Traducir nombres de roles al español
     */
    private function translateRoleName(string $roleName): string
    {
        $translations = [
            'SuperAdmin' => 'Super Administrador',
            'LeagueAdmin' => 'Admin de Liga',
            'ClubDirector' => 'Director de Club',
            'Player' => 'Jugadora',
            'Coach' => 'Entrenador',
            'SportsDoctor' => 'Médico Deportivo',
            'Verifier' => 'Verificador',
            'Referee' => 'Árbitro',
        ];

        return $translations[$roleName] ?? $roleName;
    }

    /**
     * Determinar si puede ver este widget según el rol
     */
    public static function canView(): bool
    {
        return Auth::user()->hasAnyRole([
            'SuperAdmin',
            'LeagueAdmin'
        ]);
    }
}
