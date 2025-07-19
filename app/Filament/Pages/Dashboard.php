<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static string $view = 'filament.pages.dashboard';

    public function getWidgets(): array
    {
        return [
            \App\Filament\Widgets\SystemStatsWidget::class,
            \App\Filament\Widgets\UserRegistrationsChart::class,
            \App\Filament\Widgets\RecentActivitiesWidget::class,
        ];
    }

    public function getColumns(): int | string | array
    {
        return 2;
    }
}
