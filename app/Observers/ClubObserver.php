<?php

namespace App\Observers;

use App\Models\Club;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;

class ClubObserver
{
    /**
     * Handle the Club "creating" event.
     */
    public function creating(Club $club): void
    {
        // Generar código de federación si es federado y no tiene código
        if ($club->is_federated && empty($club->federation_code)) {
            $club->federation_code = $this->generateFederationCode($club);
        }
        
        // Establecer usuario que crea el registro
        if (Auth::check()) {
            $club->created_by = Auth::id();
        }
        
        // Log del evento
        Log::info('Creando nuevo club', [
            'nombre' => $club->name,
            'es_federado' => $club->is_federated,
            'created_by' => $club->created_by,
        ]);
    }

    /**
     * Handle the Club "created" event.
     */
    public function created(Club $club): void
    {
        // Invalidar caches relacionados
        $this->invalidateRelatedCaches();
        
        // Log del evento
        Log::info('Club creado exitosamente', [
            'club_id' => $club->id,
            'nombre' => $club->name,
            'codigo_federacion' => $club->federation_code,
        ]);
        
        // Crear entrada en el historial de actividades
        activity()
            ->performedOn($club)
            ->causedBy(Auth::user())
            ->withProperties([
                'nombre' => $club->name,
                'es_federado' => $club->is_federated,
                'tipo_federacion' => $club->federation_type,
            ])
            ->log('Club creado');
    }

    /**
     * Handle the Club "updating" event.
     */
    public function updating(Club $club): void
    {
        // Validar cambios en federación
        if ($club->isDirty('is_federated')) {
            if ($club->is_federated && empty($club->federation_code)) {
                $club->federation_code = $this->generateFederationCode($club);
            } elseif (!$club->is_federated) {
                // Si se desfederó, limpiar datos de federación
                $club->federation_code = null;
                $club->federation_expiry = null;
                $club->federation_type = null;
            }
        }
        
        // Establecer usuario que actualiza
        if (Auth::check()) {
            $club->updated_by = Auth::id();
        }
        
        // Log de cambios importantes
        $changes = $club->getDirty();
        if (!empty($changes)) {
            Log::info('Actualizando club', [
                'club_id' => $club->id,
                'nombre' => $club->name,
                'changes' => array_keys($changes),
                'updated_by' => $club->updated_by,
            ]);
        }
    }

    /**
     * Handle the Club "updated" event.
     */
    public function updated(Club $club): void
    {
        // Invalidar caches relacionados
        $this->invalidateRelatedCaches($club->id);
        
        // Log del evento
        $changes = $club->getChanges();
        Log::info('Club actualizado', [
            'club_id' => $club->id,
            'nombre' => $club->name,
            'changes' => $changes,
        ]);
        
        // Registrar actividad si hay cambios significativos
        $significantChanges = array_intersect(array_keys($changes), [
            'name', 'is_federated', 'federation_type', 'federation_code'
        ]);
        
        if (!empty($significantChanges)) {
            activity()
                ->performedOn($club)
                ->causedBy(Auth::user())
                ->withProperties([
                    'changes' => $changes,
                    'significant_changes' => $significantChanges,
                ])
                ->log('Club actualizado');
        }
    }

    /**
     * Handle the Club "deleting" event.
     */
    public function deleting(Club $club): void
    {
        // Validar que no tenga jugadoras activas
        $activePlayersCount = $club->jugadoras()->where('activa', true)->count();
        if ($activePlayersCount > 0) {
            throw new \Exception(
                "No se puede eliminar el club '{$club->name}' porque tiene {$activePlayersCount} jugadoras activas."
            );
        }
        
        // Validar que no tenga pagos pendientes
        $pendingPaymentsCount = $club->pagos()->where('estado', 'pendiente')->count();
        if ($pendingPaymentsCount > 0) {
            throw new \Exception(
                "No se puede eliminar el club '{$club->name}' porque tiene {$pendingPaymentsCount} pagos pendientes."
            );
        }
        
        // Log del evento
        Log::warning('Eliminando club', [
            'club_id' => $club->id,
            'nombre' => $club->name,
            'deleted_by' => Auth::id(),
        ]);
    }

    /**
     * Handle the Club "deleted" event.
     */
    public function deleted(Club $club): void
    {
        // Invalidar caches relacionados
        $this->invalidateRelatedCaches();
        
        // Log del evento
        Log::warning('Club eliminado', [
            'club_id' => $club->id,
            'nombre' => $club->name,
        ]);
        
        // Registrar actividad
        activity()
            ->performedOn($club)
            ->causedBy(Auth::user())
            ->withProperties([
                'nombre' => $club->name,
                'codigo_federacion' => $club->federation_code,
            ])
            ->log('Club eliminado');
    }

    /**
     * Handle the Club "restored" event.
     */
    public function restored(Club $club): void
    {
        // Invalidar caches relacionados
        $this->invalidateRelatedCaches();
        
        // Log del evento
        Log::info('Club restaurado', [
            'club_id' => $club->id,
            'nombre' => $club->name,
            'restored_by' => Auth::id(),
        ]);
        
        // Registrar actividad
        activity()
            ->performedOn($club)
            ->causedBy(Auth::user())
            ->withProperties(['nombre' => $club->name])
            ->log('Club restaurado');
    }

    /**
     * Generate a unique federation code for the club.
     */
    private function generateFederationCode(Club $club): string
    {
        $departmentCode = strtoupper(substr($club->departamento->nombre ?? 'XX', 0, 2));
        $year = date('Y');
        
        // Buscar el siguiente número secuencial
        $lastCode = Club::where('federation_code', 'like', "{$departmentCode}{$year}%")
            ->orderBy('federation_code', 'desc')
            ->value('federation_code');
        
        if ($lastCode) {
            $lastNumber = (int) substr($lastCode, -3);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }
        
        return $departmentCode . $year . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Invalidate related caches.
     */
    private function invalidateRelatedCaches(?int $clubId = null): void
    {
        // Caches generales
        Cache::forget('clubs_count');
        Cache::forget('federated_clubs_count');
        Cache::forget('clubs_by_department');
        Cache::forget('federation_stats');
        Cache::forget('club_stats_widget');
        
        // Caches específicos del club
        if ($clubId) {
            Cache::forget("club_stats_{$clubId}");
            Cache::forget("club_players_count_{$clubId}");
            Cache::forget("club_directivos_{$clubId}");
        }
        
        // Cache de navegación
        Cache::forget('filament_navigation');
    }
}