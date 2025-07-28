<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

class RoleRedirectionService
{
    /**
     * Obtener la URL de redirección basada en el rol del usuario
     */
    public static function getRedirectUrl(?User $user = null): string
    {
        /** @var User|null $user */
        $user = $user ?? Auth::user();

        if (!$user) {
            return route('home');
        }

        // Roles que van al panel administrativo
        $adminRoles = [
            'SuperAdmin',
            'LeagueAdmin',
            'ClubDirector',
            'Coach',
            'Referee',
            'SportsDoctor',
            'Verifier'
        ];

        // Verificar si tiene rol administrativo
        foreach ($adminRoles as $role) {
            if ($user->hasRole($role)) {
                return '/admin';
            }
        }

        // Solo jugadores van al dashboard de usuario final
        if ($user->hasRole('Player')) {
            return route('dashboard');
        }

        // Si no tiene rol específico, redirigir a home
        return route('home');
    }

    /**
     * Verificar si el usuario puede acceder a una ruta específica
     */
    public static function canAccessRoute(string $routeName, ?User $user = null): bool
    {
        /** @var User|null $user */
        $user = $user ?? Auth::user();

        if (!$user) {
            return self::isPublicRoute($routeName);
        }

        // Rutas permitidas por rol
        $roleRoutes = [
            'Player' => [
                'player.dashboard',
                'player.profile',
                'player.card',
                'player.stats',
                'player.matches'
            ],
            'Coach' => [
                'coach.dashboard'
            ],
            'Referee' => [
                'referee.dashboard'
            ],
            'ClubDirector' => [
                'club.dashboard'
            ],
            'LeagueAdmin' => [
                'league.dashboard'
            ],
            'SuperAdmin' => [
                'admin.dashboard'
            ]
        ];

        // Rutas comunes para todos los usuarios autenticados
        $commonRoutes = [
            'settings.profile',
            'settings.password',
            'settings.appearance',
            'logout'
        ];

        // Verificar rutas públicas
        if (self::isPublicRoute($routeName)) {
            return true;
        }

        // Verificar rutas comunes
        if (in_array($routeName, $commonRoutes)) {
            return true;
        }

        // Verificar rutas específicas del rol
        foreach ($roleRoutes as $role => $routes) {
            if ($user->hasRole($role) && in_array($routeName, $routes)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Verificar si es una ruta pública
     */
    public static function isPublicRoute(string $routeName): bool
    {
        $publicRoutes = [
            'home',
            'public.tournaments',
            'tournaments.dashboard',
            'login',
            'register',
            'password.request',
            'password.reset',
            'verification.notice',
            'verification.verify',
            'password.confirm'
        ];

        return in_array($routeName, $publicRoutes);
    }

    /**
     * Obtener el rol principal del usuario
     */
    public static function getUserPrimaryRole(?User $user = null): ?string
    {
        /** @var User|null $user */
        $user = $user ?? Auth::user();

        if (!$user) {
            return null;
        }

        // Orden de prioridad de roles
        $rolePriority = [
            'SuperAdmin',
            'LeagueAdmin',
            'ClubDirector',
            'Coach',
            'Referee',
            'Player'
        ];

        foreach ($rolePriority as $role) {
            if ($user->hasRole($role)) {
                return $role;
            }
        }

        return null;
    }
}
