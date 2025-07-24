<?php

namespace App\Listeners;

use App\Events\PlayerCategoryReassigned;
use App\Models\User;
use App\Notifications\PlayerCategoryReassignedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class NotifyPlayerCategoryReassigned implements ShouldQueue
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
    public function handle(PlayerCategoryReassigned $event): void
    {
        try {
            // Notificar al jugador
            $this->notifyPlayer($event);
            
            // Notificar al director del club
            $this->notifyClubDirector($event);
            
            // Notificar al administrador de la liga si es necesario
            if ($this->shouldNotifyLeagueAdmin($event)) {
                $this->notifyLeagueAdmin($event);
            }
            
            // Registrar en el log
            Log::info('Notificaciones de reasignación de categoría enviadas', [
                'player_id' => $event->player->id,
                'player_name' => $event->player->user->full_name,
                'old_category' => $event->oldCategory,
                'new_category' => $event->newCategory,
                'reason' => $event->reason
            ]);
        } catch (\Exception $e) {
            Log::error('Error enviando notificaciones de reasignación de categoría', [
                'player_id' => $event->player->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
    
    /**
     * Notifica al jugador sobre el cambio de categoría
     */
    private function notifyPlayer(PlayerCategoryReassigned $event): void
    {
        $playerUser = $event->player->user;
        
        if (!$playerUser) {
            Log::warning('No se encontró usuario para el jugador', [
                'player_id' => $event->player->id
            ]);
            return;
        }
        
        Notification::send($playerUser, new PlayerCategoryReassignedNotification(
            $event->player,
            $event->oldCategory,
            $event->newCategory,
            $event->reason,
            'player'
        ));
    }
    
    /**
     * Notifica al director del club sobre el cambio de categoría del jugador
     */
    private function notifyClubDirector(PlayerCategoryReassigned $event): void
    {
        if (!$event->player->currentClub) {
            Log::info('El jugador no pertenece a ningún club actualmente', [
                'player_id' => $event->player->id
            ]);
            return;
        }
        
        $directorUsers = User::whereHas('roles', function ($query) {
            $query->where('name', 'club_director');
        })->whereHas('clubs', function ($query) use ($event) {
            $query->where('id', $event->player->currentClub->id);
        })->get();
        
        if ($directorUsers->isEmpty()) {
            Log::warning('No se encontraron directores para el club', [
                'club_id' => $event->player->currentClub->id,
                'club_name' => $event->player->currentClub->name
            ]);
            return;
        }
        
        Notification::send($directorUsers, new PlayerCategoryReassignedNotification(
            $event->player,
            $event->oldCategory,
            $event->newCategory,
            $event->reason,
            'director'
        ));
    }
    
    /**
     * Determina si se debe notificar al administrador de la liga
     */
    private function shouldNotifyLeagueAdmin(PlayerCategoryReassigned $event): bool
    {
        // Notificar al admin de la liga si hay un conflicto o problema con la categoría
        // Por ejemplo, si la razón contiene palabras clave como "conflicto", "error", "problema", etc.
        $problemKeywords = ['conflicto', 'error', 'problema', 'incompatible', 'inválido', 'inconsistencia'];
        
        foreach ($problemKeywords as $keyword) {
            if (stripos($event->reason, $keyword) !== false) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Notifica al administrador de la liga sobre el cambio de categoría problemático
     */
    private function notifyLeagueAdmin(PlayerCategoryReassigned $event): void
    {
        if (!$event->player->currentClub || !$event->player->currentClub->league) {
            Log::info('No se puede notificar al admin de la liga porque el jugador no está asociado a una liga', [
                'player_id' => $event->player->id
            ]);
            return;
        }
        
        $league = $event->player->currentClub->league;
        
        $adminUsers = User::whereHas('roles', function ($query) {
            $query->where('name', 'league_admin');
        })->whereHas('leagues', function ($query) use ($league) {
            $query->where('id', $league->id);
        })->get();
        
        if ($adminUsers->isEmpty()) {
            Log::warning('No se encontraron administradores para la liga', [
                'league_id' => $league->id,
                'league_name' => $league->name
            ]);
            return;
        }
        
        Notification::send($adminUsers, new PlayerCategoryReassignedNotification(
            $event->player,
            $event->oldCategory,
            $event->newCategory,
            $event->reason,
            'admin'
        ));
    }
}