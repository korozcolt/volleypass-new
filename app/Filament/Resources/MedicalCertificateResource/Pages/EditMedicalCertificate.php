<?php

namespace App\Filament\Resources\MedicalCertificateResource\Pages;

use App\Filament\Resources\MedicalCertificateResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMedicalCertificate extends EditRecord
{
    protected static string $resource = MedicalCertificateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
