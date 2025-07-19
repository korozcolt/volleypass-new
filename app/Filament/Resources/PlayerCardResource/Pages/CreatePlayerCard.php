<?php

namespace App\Filament\Resources\PlayerCardResource\Pages;

use App\Filament\Resources\PlayerCardResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePlayerCard extends CreateRecord
{
    protected static string $resource = PlayerCardResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Generar código QR único
        $data['qr_code'] = 'QR-' . strtoupper(uniqid());

        return $data;
    }
}
