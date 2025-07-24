<?php

namespace App\Services;

use App\Events\CategoryConfigurationChanged;
use App\Events\PlayerCategoryReassigned;
use App\Models\League;
use App\Models\Player;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class CategoryNotificationService
{
    /**
     * Notifica sobre cambios en la configuración de categorías de una liga
     *
     * @param League $league La liga cuya configuración ha cambiado
     * @param array $oldConfiguration La configuración anterior
     * @param array $newConfiguration La nueva configuración
     * @param User $changedBy El usuario que realizó el cambio
     * @return void
     */
    public function notifyCategoryConfigurationChanged(
        League $league,
        array $oldConfiguration,
        array $newConfiguration,
        User $changedBy
    ): void {
        try {
            // Disparar el evento
            event(new CategoryConfigurationChanged(
                $league,
                $oldConfiguration,
                $newConfiguration,
                $changedBy
            ));
            
            Log::info('Evento de cambio de configuración de categorías disparado', [
                'league_id' => $league->id,
                'league_name' => $league->name,
                'changed_by' => $changedBy->email
            ]);
        } catch (\Exception $e) {
            Log::error('Error al disparar evento de cambio de configuración de categorías', [
                'league_id' => $league->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
    
    /**
     * Notifica sobre la reasignación de categoría de un jugador
     *
     * @param Player $player El jugador cuya categoría ha cambiado
     * @param string $oldCategory La categoría anterior
     * @param string $newCategory La nueva categoría
     * @param string $reason El motivo del cambio
     * @return void
     */
    public function notifyPlayerCategoryReassigned(
        Player $player,
        string $oldCategory,
        string $newCategory,
        string $reason
    ): void {
        try {
            // Disparar el evento
            event(new PlayerCategoryReassigned(
                $player,
                $oldCategory,
                $newCategory,
                $reason
            ));
            
            Log::info('Evento de reasignación de categoría de jugador disparado', [
                'player_id' => $player->id,
                'player_name' => $player->user->full_name ?? 'Desconocido',
                'old_category' => $oldCategory,
                'new_category' => $newCategory,
                'reason' => $reason
            ]);
        } catch (\Exception $e) {
            Log::error('Error al disparar evento de reasignación de categoría de jugador', [
                'player_id' => $player->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
    
    /**
     * Notifica a múltiples jugadores sobre cambios en sus categorías
     *
     * @param array $playerChanges Array de cambios de categoría con formato:
     *                            [['player' => Player, 'old_category' => string, 'new_category' => string, 'reason' => string], ...]
     * @return void
     */
    public function notifyBulkPlayerCategoryReassignments(array $playerChanges): void
    {
        $count = 0;
        $errors = 0;
        
        foreach ($playerChanges as $change) {
            try {
                if (!isset($change['player']) || !isset($change['old_category']) || 
                    !isset($change['new_category']) || !isset($change['reason'])) {
                    Log::warning('Datos de cambio de categoría incompletos', [
                        'change' => $change
                    ]);
                    continue;
                }
                
                $this->notifyPlayerCategoryReassigned(
                    $change['player'],
                    $change['old_category'],
                    $change['new_category'],
                    $change['reason']
                );
                
                $count++;
            } catch (\Exception $e) {
                $errors++;
                Log::error('Error en notificación masiva de cambio de categoría', [
                    'player_id' => $change['player']->id ?? 'Desconocido',
                    'error' => $e->getMessage()
                ]);
            }
        }
        
        Log::info('Proceso de notificación masiva de cambios de categoría completado', [
            'total' => count($playerChanges),
            'successful' => $count,
            'errors' => $errors
        ]);
    }
}