<?php

namespace App\Filament\Resources\MedicalCertificateResource\Pages;

use App\Filament\Resources\MedicalCertificateResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMedicalCertificates extends ListRecords
{
    protected static string $resource = MedicalCertificateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
