<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Models\Player;
use App\Models\Club;
use App\Models\League;
use App\Models\Payment;
use App\Enums\UserStatus;
use App\Enums\PaymentStatus;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SystemStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Usuarios', User::count())
                ->description('Usuarios registrados')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),

            Stat::make('Usuarios Activos', User::where('status', UserStatus::Active)->count())
                ->description('Usuarios activos')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Total Jugadoras', Player::count())
                ->description('Jugadoras registradas')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('info'),

            Stat::make('Total Clubes', Club::count())
                ->description('Clubes registrados')
                ->descriptionIcon('heroicon-m-building-office')
                ->color('warning'),

            Stat::make('Total Ligas', League::count())
                ->description('Ligas activas')
                ->descriptionIcon('heroicon-m-trophy')
                ->color('primary'),

            Stat::make('Pagos Pendientes', Payment::where('status', PaymentStatus::Pending)->count())
                ->description('Pagos por confirmar')
                ->descriptionIcon('heroicon-m-credit-card')
                ->color('danger'),
        ];
    }
}
