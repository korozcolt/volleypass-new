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
        // Skip middleware for login routes
        if ($request->routeIs('filament.admin.auth.login')) {
            return $next($request);
        }

        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $adminRoles = [
            'admin',
            'super_admin',
            'league_director',
            'club_director',
            'coach',
            'referee'
        ];

        $user = auth()->user();
        $hasAdminRole = false;

        // Verificar si el usuario tiene alguno de los roles administrativos
        foreach ($adminRoles as $role) {
            if ($user->hasRole($role)) {
                $hasAdminRole = true;
                break;
            }
        }

        if (!$hasAdminRole) {
            abort(403, 'No tienes permisos para acceder al panel administrativo.');
        }

        return $next($request);
    }
}
