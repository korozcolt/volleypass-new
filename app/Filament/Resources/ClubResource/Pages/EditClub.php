<?php

namespace App\Filament\Resources\ClubResource\Pages;

use App\Filament\Resources\ClubResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class EditClub extends EditRecord
{
    protected static string $resource = ClubResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make()
                ->label('Ver Club'),
            Actions\DeleteAction::make()
                ->label('Eliminar')
                ->requiresConfirmation()
                ->modalHeading('Eliminar Club')
                ->modalDescription('¿Estás seguro de que deseas eliminar este club? Esta acción no se puede deshacer.')
                ->modalSubmitActionLabel('Sí, eliminar'),
        ];
    }

    public function getTitle(): string
    {
        return 'Editar Club: ' . $this->record->nombre;
    }

    public function getSubheading(): ?string
    {
        return 'Modifica la información del club';
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }

    protected function afterSave(): void
    {
        $club = $this->record;
        
        // Invalidar cache relacionado
        Cache::forget('club_stats_' . $club->id);
        Cache::forget('club_players_count_' . $club->id);
        Cache::forget('clubs_by_department');
        Cache::forget('federation_stats');
        
        // Log de la actualización
        Log::info('Club actualizado', [
            'club_id' => $club->id,
            'nombre' => $club->nombre,
            'updated_by' => Auth::id(),
            'changes' => $this->record->getChanges(),
        ]);
        
        // Notificación de éxito
        Notification::make()
            ->title('Club actualizado')
            ->body("La información del club '{$club->nombre}' ha sido actualizada correctamente.")
            ->success()
            ->send();
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Club actualizado exitosamente';
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Validar código de federación único si es federado
        if ($data['es_federado'] && !empty($data['codigo_federacion'])) {
            $existingClub = \App\Models\Club::where('codigo_federacion', $data['codigo_federacion'])
                ->where('id', '!=', $this->record->id)
                ->first();
                
            if ($existingClub) {
                Notification::make()
                    ->title('Error de validación')
                    ->body('El código de federación ya está en uso por otro club.')
                    ->danger()
                    ->send();
                    
                throw new \Exception('Código de federación duplicado');
            }
        }
        
        // Actualizar timestamp de modificación
        if (Auth::check()) {
            $data['updated_by'] = Auth::id();
        }
        
        return $data;
    }

    protected function getFormActions(): array
    {
        return [
            $this->getSaveFormAction()
                ->label('Guardar Cambios'),
            $this->getCancelFormAction()
                ->label('Cancelar'),
        ];
    }

    public function getBreadcrumbs(): array
    {
        return [
            '/admin/clubs' => 'Clubes',
            '/admin/clubs/' . $this->record->id => $this->record->nombre,
            '' => 'Editar',
        ];
    }
}
