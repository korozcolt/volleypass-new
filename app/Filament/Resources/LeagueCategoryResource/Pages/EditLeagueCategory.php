<?php

namespace App\Filament\Resources\LeagueCategoryResource\Pages;

use App\Filament\Resources\LeagueCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLeagueCategory extends EditRecord
{
    protected static string $resource = LeagueCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
