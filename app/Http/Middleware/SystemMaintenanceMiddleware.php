<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SystemMaintenanceMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Verificar si el modo mantenimiento está activo
        if (is_maintenance_mode()) {
            // Permitir acceso a rutas de admin para super administradores
            if ($request->is('admin*') && Auth::check() && Auth::user()->hasRole('SuperAdmin')) {
                return $next($request);
            }

            // Permitir acceso a API de verificación de estado
            if ($request->is('api/status') || $request->is('up')) {
                return $next($request);
            }

            // Mostrar página de mantenimiento
            return response()->view('maintenance', [
                'message' => maintenance_message(),
                'app_name' => app_name(),
                'app_version' => app_version(),
            ], 503);
        }

        return $next($request);
    }
}
