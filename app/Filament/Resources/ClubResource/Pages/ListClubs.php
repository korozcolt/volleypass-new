<?php

namespace App\Filament\Resources\ClubResource\Pages;

use App\Filament\Resources\ClubResource;
use App\Filament\Widgets\ClubStatsWidget;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\MaxWidth;

class ListClubs extends ListRecords
{
    protected static string $resource = ClubResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Nuevo Club')
                ->icon('heroicon-o-plus')
                ->modalWidth(MaxWidth::SevenExtraLarge),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            ClubStatsWidget::class,
        ];
    }

    public function getTitle(): string
    {
        return 'Gestión de Clubes';
    }

    public function getSubheading(): ?string
    {
        return 'Administra todos los clubes registrados en el sistema';
    }

    protected function getTableRecordsPerPageSelectOptions(): array
    {
        return [10, 25, 50, 100];
    }
}
