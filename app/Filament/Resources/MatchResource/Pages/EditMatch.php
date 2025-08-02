<?php

namespace App\Filament\Resources\MatchResource\Pages;

use App\Filament\Resources\MatchResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditMatch extends EditRecord
{
    protected static string $resource = MatchResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Add any data mutations before saving
        $data['updated_by'] = Auth::id();
        
        return $data;
    }
}