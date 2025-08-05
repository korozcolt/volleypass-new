<?php

namespace App\Filament\Resources\PlayerResource\Pages;

use App\Filament\Resources\PlayerResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPlayer extends EditRecord
{
    protected static string $resource = PlayerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Asegurar que la relación user esté cargada
        $this->record->load('user');
        
        // Cargar los datos de la relación User para mostrarlos en el formulario
        if ($this->record->user) {
            $user = $this->record->user;
            $data['user.first_name'] = $user->first_name;
            $data['user.last_name'] = $user->last_name;
            $data['user.document_type'] = $user->document_type;
            $data['user.document_number'] = $user->document_number;
            $data['user.birth_date'] = $user->birth_date;
            $data['user.gender'] = $user->gender;
            $data['user.email'] = $user->email;
            $data['user.phone'] = $user->phone;
            $data['user.phone_secondary'] = $user->phone_secondary;
            $data['user.address'] = $user->address;
            $data['user.country_id'] = $user->country_id;
            $data['user.department_id'] = $user->department_id;
            $data['user.city_id'] = $user->city_id;
        }
        
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Separar los datos del usuario de los datos del jugador
        $userData = [];
        $playerData = [];
        
        foreach ($data as $key => $value) {
            if (str_starts_with($key, 'user.')) {
                $userKey = str_replace('user.', '', $key);
                $userData[$userKey] = $value;
            } else {
                $playerData[$key] = $value;
            }
        }
        
        // Actualizar los datos del usuario
        if (!empty($userData) && $this->record->user) {
            $this->record->user->update($userData);
        }
        
        return $playerData;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Información de jugador actualizada';
    }
}
