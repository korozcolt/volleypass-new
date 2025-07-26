<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Filament\Facades\Filament;
use Filament\Navigation\NavigationItem;
use Filament\Navigation\NavigationGroup;

class FilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Filament::serving(function () {
            // Configuraciones adicionales del panel admin
            Filament::registerNavigationGroups([
                NavigationGroup::make('Gestión de Usuarios')
                    ->label('Gestión de Usuarios')
                    ->icon('heroicon-o-users'),
                NavigationGroup::make('Torneos y Competencias')
                    ->label('Torneos y Competencias')
                    ->icon('heroicon-o-trophy'),
                NavigationGroup::make('Equipos y Clubes')
                    ->label('Equipos y Clubes')
                    ->icon('heroicon-o-building-office'),
                NavigationGroup::make('Configuración')
                    ->label('Configuración')
                    ->icon('heroicon-o-cog-6-tooth'),
            ]);
        });
    }
}
