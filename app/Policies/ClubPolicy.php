<?php

namespace App\Policies;

use App\Models\Club;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ClubPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // SuperAdmin y LeagueAdmin pueden ver todos los clubes
        return $user->hasAnyRole(['SuperAdmin', 'LeagueAdmin']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Club $club): bool
    {
        // SuperAdmin y LeagueAdmin pueden ver cualquier club
        if ($user->hasAnyRole(['SuperAdmin', 'LeagueAdmin'])) {
            return true;
        }
        
        // ClubDirector pueden ver solo su club
        if ($user->hasRole('ClubDirector')) {
            return $this->isClubDirector($user, $club);
        }
        
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Solo SuperAdmin y LeagueAdmin pueden crear clubes
        return $user->hasAnyRole(['SuperAdmin', 'LeagueAdmin']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Club $club): bool
    {
        // SuperAdmin pueden editar cualquier club
        if ($user->hasRole('SuperAdmin')) {
            return true;
        }
        
        // LeagueAdmin pueden editar clubes de su jurisdicción
        if ($user->hasRole('LeagueAdmin')) {
            return $this->canManageClub($user, $club);
        }
        
        // ClubDirector pueden editar solo información básica de su club
        if ($user->hasRole('ClubDirector')) {
            return $this->isClubDirector($user, $club);
        }
        
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Club $club): bool
    {
        // Solo SuperAdmin pueden eliminar clubes
        if ($user->hasRole('SuperAdmin')) {
            return true;
        }
        
        // LeagueAdmin pueden eliminar clubes sin jugadoras activas
        if ($user->hasRole('LeagueAdmin')) {
            return $this->canManageClub($user, $club) && 
                   $club->players()->where('is_active', true)->count() === 0;
        }
        
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Club $club): bool
    {
        return $user->hasRole('SuperAdmin');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Club $club): bool
    {
        return $user->hasRole('SuperAdmin');
    }

    /**
     * Determine whether the user can federate clubs.
     */
    public function federate(User $user, Club $club): bool
    {
        // Solo SuperAdmin y LeagueAdmin pueden federar clubes
        return $user->hasAnyRole(['SuperAdmin', 'LeagueAdmin']);
    }

    /**
     * Determine whether the user can manage club directors.
     */
    public function manageDirectors(User $user, Club $club): bool
    {
        // SuperAdmin pueden gestionar directivos de cualquier club
        if ($user->hasRole('SuperAdmin')) {
            return true;
        }
        
        // LeagueAdmin pueden gestionar directivos en su jurisdicción
        if ($user->hasRole('LeagueAdmin')) {
            return $this->canManageClub($user, $club);
        }
        
        return false;
    }

    /**
     * Determine whether the user can export club data.
     */
    public function export(User $user): bool
    {
        return in_array($user->rol, ['admin', 'coordinador']);
    }

    /**
     * Determine whether the user can send notifications to clubs.
     */
    public function sendNotifications(User $user): bool
    {
        return in_array($user->rol, ['admin', 'coordinador']);
    }

    /**
     * Check if user is a director of the given club.
     */
    private function isClubDirector(User $user, Club $club): bool
    {
        return $club->directivos()
            ->wherePivot('user_id', $user->id)
            ->wherePivot('is_active', true)
            ->exists();
    }

    /**
     * Check if user can manage the given club based on jurisdiction.
     */
    private function canManageClub(User $user, Club $club): bool
    {
        // Si el coordinador tiene asignado un departamento específico
        if ($user->departamento_id) {
            return $club->departamento_id === $user->departamento_id;
        }
        
        // Si no tiene departamento específico, puede gestionar todos
        return true;
    }

    /**
     * Determine if user can update federation settings.
     */
    public function updateFederationSettings(User $user, Club $club): bool
    {
        // Solo administradores y coordinadores pueden modificar configuración de federación
        return in_array($user->rol, ['admin', 'coordinador']);
    }

    /**
     * Determine if user can view club statistics.
     */
    public function viewStatistics(User $user, Club $club): bool
    {
        return $this->view($user, $club);
    }
}