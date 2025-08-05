<?php

namespace App\Filament\Resources\TeamResource\Pages;

use App\Filament\Resources\TeamResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;

class ListTeams extends ListRecords
{
    protected static string $resource = TeamResource::class;

    protected function getHeaderActions(): array
    {
        $actions = [
            Actions\CreateAction::make(),
        ];

        // Solo mostrar el botón de selecciones departamentales para SuperAdmin y LeagueAdmin
        if (Auth::user()?->hasAnyRole(['SuperAdmin', 'LeagueAdmin'])) {
            $actions[] = Actions\Action::make('manage_departmental_selections')
                ->label('Gestionar Selecciones Departamentales')
                ->icon('heroicon-o-flag')
                ->color('primary')
                ->url(static::getResource()::getUrl('manage-departmental-selections'));
        }

        return $actions;
    }
}
