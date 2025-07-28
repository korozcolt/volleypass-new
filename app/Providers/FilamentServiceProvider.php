<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Filament\Facades\Filament;
use Filament\Navigation\NavigationItem;
use Filament\Navigation\NavigationGroup;
use Illuminate\Support\Facades\Auth;

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
            $this->registerDynamicNavigationGroups();
        });
    }

    private function registerDynamicNavigationGroups(): void
    {
        if (!Auth::check()) {
            return;
        }

        $user = Auth::user();
        $role = $user->getRoleNames()->first();

        $groups = $this->getNavigationGroupsByRole($role);
        
        Filament::registerNavigationGroups($groups);
    }

    private function getNavigationGroupsByRole(string $role): array
    {
        $baseGroups = [
            NavigationGroup::make('Gestión Deportiva')
                ->label('Gestión Deportiva')
                ->icon('heroicon-o-trophy'),
            NavigationGroup::make('Gestión Médica y Documentos')
                ->label('Gestión Médica y Documentos')
                ->icon('heroicon-o-document-check'),
            NavigationGroup::make('Comunicación')
                ->label('Comunicación')
                ->icon('heroicon-o-chat-bubble-left-right'),
        ];

        return match($role) {
            'SuperAdmin' => array_merge($baseGroups, [
                NavigationGroup::make('Finanzas y Pagos')
                    ->label('Finanzas y Pagos')
                    ->icon('heroicon-o-credit-card'),
                NavigationGroup::make('Administración del Sistema')
                    ->label('Administración del Sistema')
                    ->icon('heroicon-o-cog-6-tooth'),
                NavigationGroup::make('Reportes y Análisis')
                    ->label('Reportes y Análisis')
                    ->icon('heroicon-o-chart-bar'),
            ]),
            'LeagueAdmin' => array_merge($baseGroups, [
                NavigationGroup::make('Finanzas y Pagos')
                    ->label('Finanzas y Pagos')
                    ->icon('heroicon-o-credit-card'),
                NavigationGroup::make('Reportes')
                    ->label('Reportes')
                    ->icon('heroicon-o-chart-bar'),
            ]),
            'ClubDirector' => array_merge($baseGroups, [
                NavigationGroup::make('Finanzas y Pagos')
                    ->label('Finanzas y Pagos')
                    ->icon('heroicon-o-credit-card'),
            ]),
            'SportsDoctor' => [
                NavigationGroup::make('Gestión Médica y Documentos')
                    ->label('Gestión Médica y Documentos')
                    ->icon('heroicon-o-document-check'),
                NavigationGroup::make('Comunicación')
                    ->label('Comunicación')
                    ->icon('heroicon-o-chat-bubble-left-right'),
            ],
            'Coach', 'Verifier' => $baseGroups,
            default => $baseGroups,
        };
    }
}
