<?php

namespace App\Filament\Resources\PlayerResource\Pages;

use App\Filament\Resources\PlayerResource;
use App\Enums\FederationStatus;
use Filament\Resources\Pages\CreateRecord;

class CreatePlayer extends CreateRecord
{
    protected static string $resource = PlayerResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Establecer valores por defecto para federación
        $data['federation_status'] = $data['federation_status'] ?? FederationStatus::NotFederated;

        return $data;
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Jugadora registrada exitosamente';
    }
}
