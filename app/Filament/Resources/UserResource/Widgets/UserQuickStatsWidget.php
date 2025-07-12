<?php

namespace App\Filament\Resources\UserResource\Widgets;

use App\Models\User;
use App\Enums\UserStatus;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class UserQuickStatsWidget extends BaseWidget
{
    protected static ?string $pollingInterval = '30s';

    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        return [
            Stat::make('Total de Usuarios', User::count())
                ->description('Registrados en el sistema')
                ->descriptionIcon('heroicon-o-users')
                ->color('primary')
                ->chart($this->getWeeklyChart()),

            Stat::make('Activos', User::where('status', UserStatus::Active)->count())
                ->description('Estados activos')
                ->descriptionIcon('heroicon-o-check-circle')
                ->color('success'),

            Stat::make('Nuevos esta semana', $this->getNewUsersThisWeek())
                ->description('Últimos 7 días')
                ->descriptionIcon('heroicon-o-user-plus')
                ->color('info'),

            Stat::make('Por verificar', User::whereNull('email_verified_at')->count())
                ->description('Emails pendientes')
                ->descriptionIcon('heroicon-o-exclamation-triangle')
                ->color('warning'),
        ];
    }

    private function getNewUsersThisWeek(): int
    {
        return User::where('created_at', '>=', now()->subDays(7))->count();
    }

    private function getWeeklyChart(): array
    {
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $count = User::whereDate('created_at', $date)->count();
            $data[] = $count;
        }
        return $data;
    }

    public static function canView(): bool
    {
        return Auth::user()->hasAnyRole([
            'SuperAdmin',
            'LeagueAdmin'
        ]);
    }
}
