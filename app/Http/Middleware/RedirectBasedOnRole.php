<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectBasedOnRole
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();
        $currentRoute = $request->route()->getName();

        // Roles que van al panel administrativo
        $adminRoles = [
            'admin',
            'super_admin',
            'league_director',
            'club_director',
            'coach',
            'referee'
        ];

        // Verificar si el usuario tiene rol administrativo
        $hasAdminRole = false;
        foreach ($adminRoles as $role) {
            if ($user->hasRole($role)) {
                $hasAdminRole = true;
                break;
            }
        }

        // Si tiene rol administrativo y no está en rutas admin o públicas
        if ($hasAdminRole && !$this->isAdminRoute($currentRoute) && !$this->isPublicRoute($currentRoute)) {
            return redirect()->to('/admin');
        }

        // Solo jugadores van al dashboard de usuario final
        if ($user->hasRole('player') && !$this->isPlayerRoute($currentRoute) && !$this->isPublicRoute($currentRoute)) {
            return redirect()->route('player.dashboard');
        }

        // Si no tiene rol específico, redirigir a home
        if (!$hasAdminRole && !$user->hasRole('player')) {
            return redirect()->route('home');
        }

        return $next($request);
    }

    /**
     * Verificar si es una ruta del panel administrativo
     */
    private function isAdminRoute(string $routeName): bool
    {
        return str_starts_with($routeName, 'filament.') ||
               str_starts_with($routeName, 'admin.') ||
               $routeName === 'settings.profile' ||
               $routeName === 'settings.password' ||
               $routeName === 'settings.appearance';
    }

    /**
     * Verificar si es una ruta de jugador
     */
    private function isPlayerRoute(string $routeName): bool
    {
        $playerRoutes = [
            'player.dashboard',
            'player.profile',
            'player.card',
            'player.stats',
            'player.tournaments',
            'player.tournaments.show',
            'player.settings',
            'player.notifications',
            'settings.profile',
            'settings.password',
            'settings.appearance'
        ];

        return in_array($routeName, $playerRoutes);
    }

    /**
     * Verificar si es una ruta pública que no requiere redirección
     */
    private function isPublicRoute(string $routeName): bool
    {
        $publicRoutes = [
            'home',
            'public.tournament.show',
            'public.team.show',
            'public.standings',
            'public.schedule',
            'public.results',
            'tournaments.public',
            'tournaments.dashboard',
            'login',
            'register',
            'password.request',
            'password.reset',
            'verification.notice',
            'verification.verify',
            'password.confirm',
            'logout'
        ];

        return in_array($routeName, $publicRoutes);
    }
}
