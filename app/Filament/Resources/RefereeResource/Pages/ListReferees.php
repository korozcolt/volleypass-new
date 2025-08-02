<?php

namespace App\Filament\Resources\RefereeResource\Pages;

use App\Filament\Resources\RefereeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListReferees extends ListRecords
{
    protected static string $resource = RefereeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            // Add widgets here if needed
        ];
    }
}