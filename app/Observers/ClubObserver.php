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
        if ($club->es_federado && empty($club->codigo_federacion)) {
            $club->codigo_federacion = $this->generateFederationCode($club);
        }
        
        // Establecer usuario que crea el registro
        if (Auth::check()) {
            $club->created_by = Auth::id();
        }
        
        // Log del evento
        Log::info('Creando nuevo club', [
            'nombre' => $club->nombre,
            'es_federado' => $club->es_federado,
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
            'nombre' => $club->nombre,
            'codigo_federacion' => $club->codigo_federacion,
        ]);
        
        // Crear entrada en el historial de actividades
        activity()
            ->performedOn($club)
            ->causedBy(Auth::user())
            ->withProperties([
                'nombre' => $club->nombre,
                'es_federado' => $club->es_federado,
                'tipo_federacion' => $club->tipo_federacion,
            ])
            ->log('Club creado');
    }

    /**
     * Handle the Club "updating" event.
     */
    public function updating(Club $club): void
    {
        // Validar cambios en federación
        if ($club->isDirty('es_federado')) {
            if ($club->es_federado && empty($club->codigo_federacion)) {
                $club->codigo_federacion = $this->generateFederationCode($club);
            } elseif (!$club->es_federado) {
                // Si se desfederó, limpiar datos de federación
                $club->codigo_federacion = null;
                $club->vencimiento_federacion = null;
                $club->tipo_federacion = null;
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
                'nombre' => $club->nombre,
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
            'nombre' => $club->nombre,
            'changes' => $changes,
        ]);
        
        // Registrar actividad si hay cambios significativos
        $significantChanges = array_intersect(array_keys($changes), [
            'nombre', 'es_federado', 'tipo_federacion', 'codigo_federacion'
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
                "No se puede eliminar el club '{$club->nombre}' porque tiene {$activePlayersCount} jugadoras activas."
            );
        }
        
        // Validar que no tenga pagos pendientes
        $pendingPaymentsCount = $club->pagos()->where('estado', 'pendiente')->count();
        if ($pendingPaymentsCount > 0) {
            throw new \Exception(
                "No se puede eliminar el club '{$club->nombre}' porque tiene {$pendingPaymentsCount} pagos pendientes."
            );
        }
        
        // Log del evento
        Log::warning('Eliminando club', [
            'club_id' => $club->id,
            'nombre' => $club->nombre,
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
            'nombre' => $club->nombre,
        ]);
        
        // Registrar actividad
        activity()
            ->performedOn($club)
            ->causedBy(Auth::user())
            ->withProperties([
                'nombre' => $club->nombre,
                'codigo_federacion' => $club->codigo_federacion,
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
            'nombre' => $club->nombre,
            'restored_by' => Auth::id(),
        ]);
        
        // Registrar actividad
        activity()
            ->performedOn($club)
            ->causedBy(Auth::user())
            ->withProperties(['nombre' => $club->nombre])
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
        $lastCode = Club::where('codigo_federacion', 'like', "{$departmentCode}{$year}%")
            ->orderBy('codigo_federacion', 'desc')
            ->value('codigo_federacion');
        
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