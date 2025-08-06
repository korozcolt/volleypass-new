<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\SetupState;
use App\Enums\SetupStatus;

class EnsureSetupCompleted
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Permitir acceso a rutas de setup y login
        if ($this->shouldSkipSetupCheck($request)) {
            return $next($request);
        }

        // Verificar si el setup está completo
        if (!SetupState::isSetupComplete()) {
            // Solo superadmin puede acceder al wizard
            if (!$request->user() || !$request->user()->hasRole('superadmin')) {
                return redirect()->route('filament.admin.auth.login')
                    ->with('error', 'El sistema requiere configuración inicial. Contacte al administrador.');
            }

            return redirect()->route('setup.wizard');
        }

        return $next($request);
    }

    /**
     * Determinar si se debe omitir la verificación de setup
     */
    private function shouldSkipSetupCheck(Request $request): bool
    {
        $allowedRoutes = [
            'login',
            'logout',
            'setup.*',
            'password.*',
            'verification.*',
            'card.*',
            'player.card*',
            'contact',
        ];

        $allowedPaths = [
            '/',
            'contacto',
            'api/*',
            'livewire/*',
            '_debugbar/*',
        ];

        // Verificar rutas nombradas
        $currentRoute = $request->route();
        if ($currentRoute && $currentRoute->getName()) {
            foreach ($allowedRoutes as $pattern) {
                if (fnmatch($pattern, $currentRoute->getName())) {
                    return true;
                }
            }
        }

        // Verificar paths
        foreach ($allowedPaths as $pattern) {
            if (fnmatch($pattern, $request->path())) {
                return true;
            }
        }

        return false;
    }
}