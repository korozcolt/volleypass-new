<?php

namespace App\Filament\Resources\MatchResource\Pages;

use App\Filament\Resources\MatchResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateMatch extends CreateRecord
{
    protected static string $resource = MatchResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Set default values or perform validations
        $data['created_by'] = Auth::id();
        
        return $data;
    }
}