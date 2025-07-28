<?php

namespace App\Http\Middleware;

use App\Services\RoleRedirectionService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class PostLoginRoleRedirection
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Solo aplicar redirección después del login
        if ($request->routeIs('login') && $request->isMethod('POST') && Auth::check()) {
            $redirectUrl = RoleRedirectionService::getRedirectUrl();
            return redirect($redirectUrl);
        }

        // Para rutas de dashboard, verificar que el usuario esté en el dashboard correcto
        if ($request->routeIs('dashboard') && Auth::check()) {
            $user = Auth::user();
            $expectedUrl = RoleRedirectionService::getRedirectUrl($user);
            
            // Si el usuario no debería estar en este dashboard, redirigir
            if ($request->url() !== $expectedUrl) {
                return redirect($expectedUrl);
            }
        }

        return $next($request);
    }
}