<?php

namespace App\Filament\Resources\SystemConfigurationResource\Pages;

use App\Filament\Resources\SystemConfigurationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSystemConfiguration extends EditRecord
{
    protected static string $resource = SystemConfigurationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
