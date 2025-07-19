<?php

namespace App\Filament\Resources\MedicalCertificateResource\Pages;

use App\Filament\Resources\MedicalCertificateResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewMedicalCertificate extends ViewRecord
{
    protected static string $resource = MedicalCertificateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
