<?php

namespace App\Filament\Resources\SystemConfigurationResource\Pages;

use App\Filament\Resources\SystemConfigurationResource;
use App\Services\SystemConfigurationService;
use Filament\Resources\Pages\CreateRecord;

class CreateSystemConfiguration extends CreateRecord
{
    protected static string $resource = SystemConfigurationResource::class;

    protected function afterCreate(): void
    {
        // Recargar configuraciones después de crear
        app(SystemConfigurationService::class)->reload();

        $this->notify('success', 'Configuración creada y aplicada exitosamente.');
    }
}
