<?php

namespace App\Filament\Resources\PlayerCardResource\Pages;

use App\Filament\Resources\PlayerCardResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPlayerCard extends EditRecord
{
    protected static string $resource = PlayerCardResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
