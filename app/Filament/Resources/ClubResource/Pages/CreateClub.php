<?php

namespace App\Filament\Resources\ClubResource\Pages;

use App\Filament\Resources\ClubResource;
use App\Models\Club;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class CreateClub extends CreateRecord
{
    protected static string $resource = ClubResource::class;

    public function getTitle(): string
    {
        return 'Crear Nuevo Club';
    }

    public function getSubheading(): ?string
    {
        return 'Registra un nuevo club en el sistema';
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function afterCreate(): void
    {
        $club = $this->record;
        
        // Log de la creación
        Log::info('Nuevo club creado', [
            'club_id' => $club->id,
            'name' => $club->name,
            'created_by' => Auth::id(),
        ]);
        
        // Notificación de éxito
        Notification::make()
            ->title('Club creado exitosamente')
            ->body("El club '{$club->name}' ha sido registrado correctamente.")
            ->success()
            ->send();
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Club registrado exitosamente';
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Asegurar que el código de federación sea único si es federado
        if (isset($data['is_federated']) && $data['is_federated'] && empty($data['federation_code'])) {
            $data['federation_code'] = $this->generateFederationCode($data['name']);
        }
        
        // Establecer valores por defecto
        if (Auth::check()) {
             $data['created_by'] = Auth::id();
         }
        
        return $data;
    }

    private function generateFederationCode(string $name): string
    {
        $prefix = strtoupper(substr($name, 0, 3));
        $suffix = str_pad(Club::where('is_federated', true)->count() + 1, 4, '0', STR_PAD_LEFT);
        
        return $prefix . $suffix;
    }

    protected function handleRecordCreation(array $data): Model
    {
        $club = static::getModel()::create($data);
        
        // Crear directivo inicial si se proporcionó
        if (!empty($data['directors'])) {
            foreach ($data['directors'] as $director) {
                $club->directors()->attach($director['user_id'], [
                    'role' => $director['role'],
                    'is_active' => $director['is_active'] ?? true,
                    'start_date' => $director['start_date'] ?? now(),
                    'end_date' => $director['end_date'] ?? null,
                ]);
            }
        }
        
        return $club;
    }

    protected function getFormActions(): array
    {
        return [
            $this->getCreateFormAction()
                ->label('Crear Club'),
            $this->getCreateAnotherFormAction()
                ->label('Crear y Agregar Otro'),
            $this->getCancelFormAction()
                ->label('Cancelar'),
        ];
    }
}
