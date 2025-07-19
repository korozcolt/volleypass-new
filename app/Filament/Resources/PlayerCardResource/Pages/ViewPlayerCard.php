<?php

namespace App\Filament\Resources\PlayerCardResource\Pages;

use App\Filament\Resources\PlayerCardResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPlayerCard extends ViewRecord
{
    protected static string $resource = PlayerCardResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
