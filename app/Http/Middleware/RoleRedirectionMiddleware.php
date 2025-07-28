<?php

namespace App\Http\Middleware;

use App\Services\RoleRedirectionService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleRedirectionMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();
            $currentRoute = $request->route()->getName();
            
            // Verificar si el usuario puede acceder a la ruta actual
            if (!RoleRedirectionService::canAccessRoute($currentRoute, $user)) {
                // Redirigir al dashboard apropiado para su rol
                $redirectUrl = RoleRedirectionService::getRedirectUrl($user);
                return redirect($redirectUrl);
            }
        }

        return $next($request);
    }
}