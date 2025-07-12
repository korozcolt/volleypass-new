<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Enums\UserStatus;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserStatsOverviewWidget extends BaseWidget
{
    protected static ?string $pollingInterval = '30s';

    protected function getStats(): array
    {
        return [
            // Total de usuarios
            Stat::make('Total Usuarios', User::count())
                ->description('Usuarios registrados en el sistema')
                ->descriptionIcon('heroicon-o-users')
                ->color('primary')
                ->chart($this->getUserRegistrationChart())
                ->extraAttributes([
                    'class' => 'cursor-pointer',
                ]),

            // Usuarios activos
            Stat::make('Usuarios Activos', User::where('status', UserStatus::Active)->count())
                ->description('Usuarios con estado activo')
                ->descriptionIcon('heroicon-o-check-circle')
                ->color('success')
                ->extraAttributes([
                    'class' => 'cursor-pointer',
                ]),

            // Nuevos usuarios este mes
            Stat::make('Nuevos este mes', $this->getNewUsersThisMonth())
                ->description('Registrados en ' . now()->format('F Y'))
                ->descriptionIcon('heroicon-o-user-plus')
                ->color('info')
                ->chart($this->getMonthlyRegistrationChart()),

            // Usuarios por verificar
            Stat::make('Sin verificar', User::whereNull('email_verified_at')->count())
                ->description('Emails pendientes de verificación')
                ->descriptionIcon('heroicon-o-exclamation-triangle')
                ->color($this->getPendingVerificationCount() > 10 ? 'warning' : 'gray'),

            // Distribución por roles
            Stat::make('Con roles asignados', $this->getUsersWithRolesCount())
                ->description('Usuarios con al menos un rol')
                ->descriptionIcon('heroicon-o-shield-check')
                ->color('purple'),

            // Usuarios suspendidos
            Stat::make('Suspendidos', User::where('status', UserStatus::Suspended)->count())
                ->description('Usuarios temporalmente suspendidos')
                ->descriptionIcon('heroicon-o-no-symbol')
                ->color('danger'),
        ];
    }

    /**
     * Obtener nuevos usuarios este mes
     */
    private function getNewUsersThisMonth(): int
    {
        return User::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
    }

    /**
     * Obtener usuarios pendientes de verificación
     */
    private function getPendingVerificationCount(): int
    {
        return User::whereNull('email_verified_at')->count();
    }

    /**
     * Obtener usuarios con roles asignados
     */
    private function getUsersWithRolesCount(): int
    {
        return User::has('roles')->count();
    }

    /**
     * Gráfico de registros de usuarios (últimos 7 días)
     */
    private function getUserRegistrationChart(): array
    {
        $data = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $count = User::whereDate('created_at', $date)->count();
            $data[] = $count;
        }

        return $data;
    }

    /**
     * Gráfico de registros mensuales (últimos 6 meses)
     */
    private function getMonthlyRegistrationChart(): array
    {
        $data = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $count = User::whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->count();
            $data[] = $count;
        }

        return $data;
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
