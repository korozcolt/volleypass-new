<?php

namespace App\Filament\Resources\SystemConfigurationResource\Pages;

use App\Filament\Resources\SystemConfigurationResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewSystemConfiguration extends ViewRecord
{
    protected static string $resource = SystemConfigurationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
