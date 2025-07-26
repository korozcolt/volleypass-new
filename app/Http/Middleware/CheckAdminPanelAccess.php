<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAdminPanelAccess
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Excluir rutas de login/logout de Filament
        if ($request->routeIs('filament.admin.auth.login') || 
            $request->routeIs('filament.admin.auth.logout') ||
            str_starts_with($request->route()->getName() ?? '', 'filament.admin.auth.')) {
            return $next($request);
        }

        // Verificar autenticación web
        if (!auth('web')->check()) {
            return redirect()->route('filament.admin.auth.login');
        }

        $user = auth('web')->user();
        
        // Roles permitidos en panel admin (usando los nombres correctos de los roles)
        $adminRoles = [
            'SuperAdmin', 
            'LeagueAdmin', 
            'ClubDirector', 
            'Coach', 
            'SportsDoctor',
            'Referee',
            'Verifier'
        ];
        
        $hasAdminRole = false;
        foreach ($adminRoles as $role) {
            if ($user->hasRole($role)) {
                $hasAdminRole = true;
                break;
            }
        }

        if (!$hasAdminRole) {
            // Redirigir jugadoras a su dashboard
            if ($user->hasRole('Player')) {
                return redirect()->route('player.dashboard');
            }
            
            abort(403, 'No tienes permisos para acceder al panel administrativo.');
        }

        return $next($request);
    }
}
