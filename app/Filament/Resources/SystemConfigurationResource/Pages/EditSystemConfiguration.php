<?php

namespace App\Filament\Resources\SystemConfigurationResource\Pages;

use App\Filament\Resources\SystemConfigurationResource;
use App\Services\SystemConfigurationService;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditSystemConfiguration extends EditRecord
{
    protected static string $resource = SystemConfigurationResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Asegurar que el valor se muestre correctamente según el tipo
        if (isset($data['type']) && isset($data['value'])) {
            $data['value'] = match ($data['type']) {
                'boolean' => (bool) $data['value'],
                'number' => is_numeric($data['value']) ? (float) $data['value'] : 0,
                'json' => is_string($data['value']) ? $data['value'] : json_encode($data['value'], JSON_PRETTY_PRINT),
                'date' => $data['value'] ? \Carbon\Carbon::parse($data['value'])->format('Y-m-d') : null,
                default => $data['value'],
            };
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Asegurar que el valor se guarde correctamente según el tipo
        if (isset($data['type']) && isset($data['value'])) {
            $data['value'] = match ($data['type']) {
                'boolean' => $data['value'] ? '1' : '0',
                'json' => is_array($data['value']) ? json_encode($data['value']) : $data['value'],
                'date' => $data['value'] ? \Carbon\Carbon::parse($data['value'])->toDateString() : $data['value'],
                default => (string) $data['value'],
            };
        }

        return $data;
    }

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
                    Notification::make()
                        ->title('Configuraciones recargadas exitosamente.')
                        ->success()
                        ->send();
                }),
        ];
    }

    protected function afterSave(): void
    {
        // Recargar configuraciones después de guardar
        app(SystemConfigurationService::class)->reload();

        Notification::make()
            ->title('Configuración actualizada y aplicada exitosamente.')
            ->success()
            ->send();
    }
}
