<?php

namespace App\Listeners;

use App\Events\CategoryConfigurationChanged;
use App\Models\User;
use App\Notifications\CategoryConfigurationChangedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class NotifyCategoryConfigurationChanged implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(CategoryConfigurationChanged $event): void
    {
        try {
            // Preparar datos para la notificación
            $changedCategories = $this->prepareChangedCategoriesData($event->oldConfiguration, $event->newConfiguration);
            
            if (empty($changedCategories)) {
                Log::info('No se detectaron cambios significativos en la configuración de categorías', [
                    'league_id' => $event->league->id,
                    'league_name' => $event->league->name
                ]);
                return;
            }
            
            // Notificar al administrador de la liga
            $this->notifyLeagueAdmin($event, $changedCategories);
            
            // Notificar a los directores de club de la liga
            $this->notifyClubDirectors($event, $changedCategories);
            
            // Registrar en el log
            Log::info('Notificaciones de cambio de configuración de categorías enviadas', [
                'league_id' => $event->league->id,
                'league_name' => $event->league->name,
                'changes_count' => count($changedCategories)
            ]);
        } catch (\Exception $e) {
            Log::error('Error enviando notificaciones de cambio de configuración de categorías', [
                'league_id' => $event->league->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
    
    /**
     * Prepara los datos de las categorías que han cambiado
     */
    private function prepareChangedCategoriesData(array $oldConfig, array $newConfig): array
    {
        $changedCategories = [];
        
        // Identificar categorías nuevas
        foreach ($newConfig as $newCategory) {
            $found = false;
            foreach ($oldConfig as $oldCategory) {
                if ($oldCategory['id'] === $newCategory['id']) {
                    $found = true;
                    
                    // Verificar si hubo cambios en propiedades importantes
                    $changes = [];
                    
                    if ($oldCategory['min_age'] !== $newCategory['min_age'] || 
                        $oldCategory['max_age'] !== $newCategory['max_age']) {
                        $changes[] = "rango de edad actualizado de {$oldCategory['min_age']}-{$oldCategory['max_age']} a {$newCategory['min_age']}-{$newCategory['max_age']}";
                    }
                    
                    if ($oldCategory['is_active'] !== $newCategory['is_active']) {
                        $changes[] = $newCategory['is_active'] ? "activada" : "desactivada";
                    }
                    
                    if ($oldCategory['name'] !== $newCategory['name']) {
                        $changes[] = "nombre cambiado de '{$oldCategory['name']}' a '{$newCategory['name']}'";
                    }
                    
                    if (!empty($changes)) {
                        $changedCategories[] = [
                            'id' => $newCategory['id'],
                            'name' => $newCategory['name'],
                            'change_description' => implode(", ", $changes)
                        ];
                    }
                    
                    break;
                }
            }
            
            if (!$found) {
                $changedCategories[] = [
                    'id' => $newCategory['id'],
                    'name' => $newCategory['name'],
                    'change_description' => "nueva categoría añadida"
                ];
            }
        }
        
        // Identificar categorías eliminadas
        foreach ($oldConfig as $oldCategory) {
            $found = false;
            foreach ($newConfig as $newCategory) {
                if ($oldCategory['id'] === $newCategory['id']) {
                    $found = true;
                    break;
                }
            }
            
            if (!$found) {
                $changedCategories[] = [
                    'id' => $oldCategory['id'],
                    'name' => $oldCategory['name'],
                    'change_description' => "categoría eliminada"
                ];
            }
        }
        
        return $changedCategories;
    }
    
    /**
     * Notifica al administrador de la liga
     */
    private function notifyLeagueAdmin(CategoryConfigurationChanged $event, array $changedCategories): void
    {
        $adminUsers = User::whereHas('roles', function ($query) {
            $query->where('name', 'league_admin');
        })->whereHas('leagues', function ($query) use ($event) {
            $query->where('id', $event->league->id);
        })->get();
        
        if ($adminUsers->isEmpty()) {
            Log::warning('No se encontraron administradores para la liga', [
                'league_id' => $event->league->id
            ]);
            return;
        }
        
        Notification::send($adminUsers, new CategoryConfigurationChangedNotification(
            $event->league,
            $changedCategories,
            'admin'
        ));
    }
    
    /**
     * Notifica a los directores de club de la liga
     */
    private function notifyClubDirectors(CategoryConfigurationChanged $event, array $changedCategories): void
    {
        $directorUsers = User::whereHas('roles', function ($query) {
            $query->where('name', 'club_director');
        })->whereHas('clubs', function ($query) use ($event) {
            $query->where('league_id', $event->league->id);
        })->get();
        
        if ($directorUsers->isEmpty()) {
            Log::info('No se encontraron directores de club para la liga', [
                'league_id' => $event->league->id
            ]);
            return;
        }
        
        Notification::send($directorUsers, new CategoryConfigurationChangedNotification(
            $event->league,
            $changedCategories,
            'director'
        ));
    }
}