<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class FederationDashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    
    protected static string $view = 'filament.pages.federation-dashboard';
    
    protected static ?string $title = 'Dashboard de Federación';
    
    protected static ?string $navigationLabel = 'Dashboard';
    
    protected static ?int $navigationSort = 1;
    
    protected static ?string $navigationGroup = 'Federación';
    
    public function getTitle(): string
    {
        return 'Dashboard de Federación';
    }
    
    public function getHeading(): string
    {
        return 'Panel de Control - Sistema de Federación';
    }
    
    public function getSubheading(): ?string
    {
        return 'Monitoreo en tiempo real del sistema de carnetización y federación';
    }
    
    public static function canAccess(): bool
    {
        return Auth::check() && Auth::user()->can('view_federation_dashboard');
    }
    
    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Widgets\FederationStatsWidget::class,
        ];
    }
    
    protected function getFooterWidgets(): array
    {
        return [
            \App\Filament\Widgets\LiveMetricsWidget::class,
            \App\Filament\Widgets\CategoryDistributionChart::class,
            \App\Filament\Widgets\SystemAlertsWidget::class,
        ];
    }
    

}