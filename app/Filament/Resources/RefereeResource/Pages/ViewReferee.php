<?php

namespace App\Filament\Resources\RefereeResource\Pages;

use App\Filament\Resources\RefereeResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewReferee extends ViewRecord
{
    protected static string $resource = RefereeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            // Add referee-specific widgets here if needed
        ];
    }
}