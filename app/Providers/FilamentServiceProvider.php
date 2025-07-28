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
                NavigationGroup::make('Gestión Deportiva')
                    ->label('Gestión Deportiva')
                    ->icon('heroicon-o-trophy'),
                NavigationGroup::make('Gestión Médica y Documentos')
                    ->label('Gestión Médica y Documentos')
                    ->icon('heroicon-o-document-check'),
                NavigationGroup::make('Finanzas y Pagos')
                    ->label('Finanzas y Pagos')
                    ->icon('heroicon-o-credit-card'),
                NavigationGroup::make('Comunicación')
                    ->label('Comunicación')
                    ->icon('heroicon-o-chat-bubble-left-right'),
                NavigationGroup::make('Administración del Sistema')
                    ->label('Administración del Sistema')
                    ->icon('heroicon-o-cog-6-tooth'),
            ]);
        });
    }
}
