<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use App\Services\SystemConfigurationService;

class ApplySystemConfigMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Aplicar configuraciones del sistema si la tabla existe
        if ($this->shouldApplyConfigurations()) {
            $this->applySystemConfigurations();
        }

        return $next($request);
    }

    /**
     * Verificar si debemos aplicar las configuraciones
     */
    private function shouldApplyConfigurations(): bool
    {
        try {
            return Schema::hasTable('system_configurations');
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Aplicar configuraciones del sistema
     */
    private function applySystemConfigurations(): void
    {
        try {
            $configService = app(SystemConfigurationService::class);

            // Aplicar configuraciones críticas del sistema
            $this->applyAppConfigurations($configService);
            $this->applySecurityConfigurations($configService);
            $this->applyFileConfigurations($configService);
            $this->applyNotificationConfigurations($configService);

        } catch (\Exception $e) {
            // Log error but don't break the application
            Log::warning('Could not apply system configurations: ' . $e->getMessage());
        }
    }

    /**
     * Aplicar configuraciones de la aplicación
     */
    private function applyAppConfigurations(SystemConfigurationService $configService): void
    {
        // Nombre de la aplicación
        $appName = $configService->get('app.name');
        if ($appName) {
            Config::set('app.name', $appName);
        }

        // Descripción de la aplicación
        $appDescription = $configService->get('app.description');
        if ($appDescription) {
            Config::set('app.description', $appDescription);
        }

        // Versión de la aplicación
        $appVersion = $configService->get('app.version');
        if ($appVersion) {
            Config::set('app.version', $appVersion);
        }
    }

    /**
     * Aplicar configuraciones de seguridad
     */
    private function applySecurityConfigurations(SystemConfigurationService $configService): void
    {
        // Máximo de intentos de login
        $maxAttempts = $configService->get('security.max_login_attempts');
        if ($maxAttempts) {
            Config::set('auth.throttle.max_attempts', (int) $maxAttempts);
        }

        // Timeout de sesión
        $sessionTimeout = $configService->get('security.session_timeout');
        if ($sessionTimeout) {
            Config::set('session.lifetime', (int) $sessionTimeout);
        }
    }

    /**
     * Aplicar configuraciones de archivos
     */
    private function applyFileConfigurations(SystemConfigurationService $configService): void
    {
        // Tamaño máximo de archivo
        $maxUploadSize = $configService->get('files.max_upload_size');
        if ($maxUploadSize) {
            $sizeInKb = (int) $maxUploadSize * 1024;
            Config::set('filesystems.max_upload_size', $sizeInKb);

            // También aplicar a PHP si es posible
            if (function_exists('ini_set')) {
                ini_set('upload_max_filesize', $maxUploadSize . 'M');
                ini_set('post_max_size', ($maxUploadSize + 2) . 'M');
            }
        }

        // Extensiones permitidas
        $allowedExtensions = $configService->get('files.allowed_extensions');
        if ($allowedExtensions) {
            Config::set('filesystems.allowed_extensions', $allowedExtensions);
        }
    }

    /**
     * Aplicar configuraciones de notificaciones
     */
    private function applyNotificationConfigurations(SystemConfigurationService $configService): void
    {
        // Email habilitado
        $emailEnabled = $configService->get('notifications.email_enabled');
        if ($emailEnabled !== null) {
            Config::set('notifications.email_enabled', (bool) $emailEnabled);
        }

        // WhatsApp habilitado
        $whatsappEnabled = $configService->get('notifications.whatsapp_enabled');
        if ($whatsappEnabled !== null) {
            Config::set('notifications.whatsapp_enabled', (bool) $whatsappEnabled);
        }

        // Email del administrador
        $adminEmail = $configService->get('notifications.admin_email');
        if ($adminEmail) {
            Config::set('notifications.admin_email', $adminEmail);
            Config::set('mail.from.address', $adminEmail);
        }
    }
}
