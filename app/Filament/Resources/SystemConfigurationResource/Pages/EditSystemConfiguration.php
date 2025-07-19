<?php

namespace App\Filament\Resources\SystemConfigurationResource\Pages;

use App\Filament\Resources\SystemConfigurationResource;
use App\Services\SystemConfigurationService;
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
            Actions\Action::make('reload_config')
                ->label('Recargar Configuraciones')
                ->icon('heroicon-o-arrow-path')
                ->color('info')
                ->action(function () {
                    app(SystemConfigurationService::class)->reload();
                    $this->notify('success', 'Configuraciones recargadas exitosamente.');
                }),
        ];
    }

    protected function afterSave(): void
    {
        // Recargar configuraciones después de guardar
        app(SystemConfigurationService::class)->reload();

        $this->notify('success', 'Configuración actualizada y aplicada exitosamente.');
    }
}
