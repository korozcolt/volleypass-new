<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
            Actions\RestoreAction::make(),
            Actions\ForceDeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // No mostrar la contraseña en el formulario
        unset($data['password']);

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Solo encriptar la contraseña si se ha proporcionado una nueva
        if (isset($data['password']) && !empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            // Mantener la contraseña actual
            unset($data['password']);
        }

        // Actualizar el campo name
        if (isset($data['first_name']) || isset($data['last_name'])) {
            $firstName = $data['first_name'] ?? $this->record->first_name;
            $lastName = $data['last_name'] ?? $this->record->last_name;
            $data['name'] = trim($firstName . ' ' . $lastName);
        }

        // Agregar updated_by
        $data['updated_by'] = Auth::id();

        return $data;
    }

    public function getTitle(): string
    {
        return 'Editar Usuario';
    }

    public function getHeading(): string
    {
        return 'Editar: ' . $this->record->name;
    }

    public function getSubheading(): ?string
    {
        return 'Modifica la información del usuario';
    }
}
