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
        
        // Definir las rutas permitidas para cada rol
        $roleRoutes = [
            'Player' => [
                'player.dashboard',
                'player.profile', 
                'player.card',
                'player.stats',
                'player.matches',
                'settings.profile',
                'settings.password',
                'settings.appearance'
            ],
            'Coach' => [
                'coach.dashboard',
                'settings.profile',
                'settings.password', 
                'settings.appearance'
            ],
            'Referee' => [
                'referee.dashboard',
                'settings.profile',
                'settings.password',
                'settings.appearance'
            ],
            'ClubDirector' => [
                'club.dashboard',
                'settings.profile',
                'settings.password',
                'settings.appearance'
            ],
            'LeagueAdmin' => [
                'league.dashboard',
                'settings.profile',
                'settings.password',
                'settings.appearance'
            ],
            'SuperAdmin' => [
                'admin.dashboard',
                'settings.profile',
                'settings.password',
                'settings.appearance'
            ]
        ];

        // Obtener el rol principal del usuario
        $userRole = $this->getUserPrimaryRole($user);
        
        // Si el usuario no tiene rol definido, redirigir a tournaments public
        if (!$userRole) {
            return redirect()->route('tournaments.public');
        }

        // Verificar si la ruta actual está permitida para el rol del usuario
        $allowedRoutes = $roleRoutes[$userRole] ?? [];
        
        // Si la ruta actual no está permitida para el rol, redirigir al dashboard correspondiente
        if (!in_array($currentRoute, $allowedRoutes) && !$this->isPublicRoute($currentRoute)) {
            return redirect()->route($this->getDefaultDashboardRoute($userRole));
        }

        return $next($request);
    }

    /**
     * Obtener el rol principal del usuario
     */
    private function getUserPrimaryRole($user): ?string
    {
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

    /**
     * Obtener la ruta del dashboard por defecto según el rol
     */
    private function getDefaultDashboardRoute(string $role): string
    {
        return match($role) {
            'Player' => 'player.dashboard',
            'Coach' => 'coach.dashboard', 
            'Referee' => 'referee.dashboard',
            'ClubDirector' => 'club.dashboard',
            'LeagueAdmin' => 'league.dashboard',
            'SuperAdmin' => 'admin.dashboard',
            default => 'tournaments.public'
        };
    }

    /**
     * Verificar si es una ruta pública que no requiere redirección
     */
    private function isPublicRoute(string $routeName): bool
    {
        $publicRoutes = [
            'home',
            'tournaments.public',
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