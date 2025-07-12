<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Encriptar la contraseña
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        // Combinar first_name y last_name en name
        $data['name'] = trim(($data['first_name'] ?? '') . ' ' . ($data['last_name'] ?? ''));

        // Agregar created_by
        $data['created_by'] = Auth::id();

        return $data;
    }

    protected function afterCreate(): void
    {
        // Crear el perfil de usuario automáticamente si es necesario
        if (!$this->record->profile) {
            $this->record->profile()->create([
                'created_by' => Auth::id(),
            ]);
        }
    }

    public function getTitle(): string
    {
        return 'Crear Usuario';
    }

    public function getHeading(): string
    {
        return 'Nuevo Usuario';
    }

    public function getSubheading(): ?string
    {
        return 'Registra un nuevo usuario en el sistema VolleyPass';
    }
}
