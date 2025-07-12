<?php
// app/Http/Middleware/ApiSecurityHeaders.php - VERSIÓN FLEXIBLE

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiSecurityHeaders
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Headers de seguridad básicos (siempre aplicados)
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=(self)');

        // CSP específico para API
        $response->headers->set(
            'Content-Security-Policy',
            "default-src 'none'; frame-ancestors 'none';"
        );

        // ✅ CORS FLEXIBLE - Solo para rutas API
        if ($request->is('api/*')) {
            $this->applyCorsHeaders($request, $response);
        }

        // Ocultar información del servidor
        $response->headers->remove('X-Powered-By');
        $response->headers->remove('Server');

        // Rate limiting headers informativos
        if ($request->is('api/*/verify-*')) {
            $response->headers->set('X-API-Type', 'qr-verification');
        }

        return $response;
    }

    /**
     * Aplicar headers CORS de forma flexible
     */
    private function applyCorsHeaders(Request $request, Response $response): void
    {
        $origin = $request->header('Origin');

        // ✅ OPCIÓN 1: Modo desarrollo - permitir todo
        if (app()->environment(['local', 'testing'])) {
            $response->headers->set('Access-Control-Allow-Origin', '*');
            $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
            $response->headers->set('Access-Control-Allow-Headers',
                'Content-Type, Authorization, X-Requested-With, X-App-Version, X-Device-ID, X-XSRF-TOKEN'
            );
            $response->headers->set('Access-Control-Allow-Credentials', 'false'); // false para wildcard
            $response->headers->set('Access-Control-Max-Age', '3600');
            return;
        }

        // ✅ OPCIÓN 2: Producción - configuración flexible desde .env
        $corsMode = config('cors.mode', 'restrictive'); // 'open', 'restrictive', 'custom'

        switch ($corsMode) {
            case 'open':
                // Permitir cualquier origen
                $response->headers->set('Access-Control-Allow-Origin', '*');
                $response->headers->set('Access-Control-Allow-Credentials', 'false');
                break;

            case 'custom':
                // Usar lista personalizada desde configuración
                $allowedOrigins = config('cors.allowed_origins', []);

                if (empty($allowedOrigins) || in_array('*', $allowedOrigins)) {
                    $response->headers->set('Access-Control-Allow-Origin', '*');
                    $response->headers->set('Access-Control-Allow-Credentials', 'false');
                } elseif ($origin && in_array($origin, $allowedOrigins)) {
                    $response->headers->set('Access-Control-Allow-Origin', $origin);
                    $response->headers->set('Access-Control-Allow-Credentials', 'true');
                }
                break;

            case 'restrictive':
            default:
                // Solo permitir same-origin y localhost en desarrollo
                $allowedPatterns = [
                    '/^https?:\/\/localhost(:\d+)?$/',
                    '/^https?:\/\/127\.0\.0\.1(:\d+)?$/',
                    '/^https?:\/\/.*\.localhost(:\d+)?$/',
                ];

                // Agregar dominio de la aplicación si está configurado
                $appUrl = config('app.url');
                if ($appUrl && $appUrl !== 'http://localhost') {
                    $allowedPatterns[] = '/^' . preg_quote($appUrl, '/') . '$/';
                }

                if ($origin && $this->isOriginAllowed($origin, $allowedPatterns)) {
                    $response->headers->set('Access-Control-Allow-Origin', $origin);
                    $response->headers->set('Access-Control-Allow-Credentials', 'true');
                }
                break;
        }

        // Headers comunes para todos los modos
        $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        $response->headers->set('Access-Control-Allow-Headers',
            'Content-Type, Authorization, X-Requested-With, X-App-Version, X-Device-ID, X-XSRF-TOKEN, Accept'
        );
        $response->headers->set('Access-Control-Max-Age', '3600');
    }

    /**
     * Verificar si el origen está permitido según los patrones
     */
    private function isOriginAllowed(string $origin, array $patterns): bool
    {
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $origin)) {
                return true;
            }
        }
        return false;
    }
}
