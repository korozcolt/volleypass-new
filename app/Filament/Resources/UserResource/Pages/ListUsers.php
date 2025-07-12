<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Nuevo Usuario')
                ->icon('heroicon-o-plus'),
        ];
    }

    public function getBreadcrumbs(): array
    {
        return [
            '/admin' => 'Dashboard',
            '/admin/users' => 'Usuarios',
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Resources\UserResource\Widgets\UserQuickStatsWidget::class,
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            \App\Filament\Resources\UserResource\Widgets\UserStatusChartWidget::class,
            \App\Filament\Resources\UserResource\Widgets\UserGrowthChartWidget::class,
        ];
    }

    public function getTitle(): string
    {
        return 'Gestión de Usuarios';
    }

    public function getHeading(): string
    {
        return 'Usuarios del Sistema';
    }

    public function getSubheading(): ?string
    {
        return 'Administra todos los usuarios registrados en VolleyPass';
    }
}
