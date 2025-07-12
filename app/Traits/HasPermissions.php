<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

trait HasPermissions
{
    /**
     * Scope para filtrar registros según permisos del usuario
     */
    public function scopeVisibleTo(Builder $query, $user = null): Builder
    {
        $user = $user ?? Auth::user();

        if (!$user) {
            return $query->whereRaw('1 = 0'); // No mostrar nada si no hay usuario
        }

        // Super admin ve todo
        if ($user->hasRole('SuperAdmin')) {
            return $query;
        }

        // Administrador de liga ve solo su liga
        if ($user->hasRole('LeagueAdmin')) {
            return $query->where('league_id', $user->league_id);
        }

        // Director de club ve solo su club
        if ($user->hasRole('ClubDirector')) {
            return $query->where('club_id', $user->club_id);
        }

        // Jugadora ve solo sus registros
        if ($user->hasRole('Player')) {
            return $query->where('user_id', $user->id);
        }

        return $query;
    }

    /**
     * Verifica si el usuario puede ver este registro
     */
    public function isVisibleTo($user = null): bool
    {
        $user = $user ?? Auth::user();

        if (!$user) {
            return false;
        }

        // Super admin ve todo
        if ($user->hasRole('SuperAdmin')) {
            return true;
        }

        // Verificar por liga
        if (isset($this->league_id) && $user->league_id) {
            return $this->league_id === $user->league_id;
        }

        // Verificar por club
        if (isset($this->club_id) && $user->club_id) {
            return $this->club_id === $user->club_id;
        }

        // Verificar propiedad
        if (isset($this->user_id)) {
            return $this->user_id === $user->id;
        }

        return false;
    }

    /**
     * Verifica si el usuario puede editar este registro
     */
    public function isEditableBy($user = null): bool
    {
        $user = $user ?? Auth::user();

        if (!$user) {
            return false;
        }

        // Super admin edita todo
        if ($user->hasRole('SuperAdmin')) {
            return true;
        }

        // Admin de liga edita en su liga
        if ($user->hasRole('LeagueAdmin') && isset($this->league_id)) {
            return $this->league_id === $user->league_id;
        }

        // Director de club edita en su club
        if ($user->hasRole('ClubDirector') && isset($this->club_id)) {
            return $this->club_id === $user->club_id;
        }

        return false;
    }
}
