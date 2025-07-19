<?php

namespace App\Services;

use App\Models\SystemConfiguration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class SystemConfigurationService
{
    /**
     * Cache key prefix
     */
    private const CACHE_PREFIX = 'system_config_';

    /**
     * Cache duration in minutes
     */
    private const CACHE_DURATION = 60;

    /**
     * Obtener una configuración del sistema
     */
    public function get(string $key, $default = null)
    {
        $cacheKey = self::CACHE_PREFIX . $key;

        return Cache::remember($cacheKey, self::CACHE_DURATION, function () use ($key, $default) {
            return SystemConfiguration::get($key, $default);
        });
    }

    /**
     * Establecer una configuración del sistema
     */
    public function set(string $key, $value): bool
    {
        $result = SystemConfiguration::set($key, $value);

        if ($result) {
            // Limpiar cache
            $this->clearCache($key);

            // Aplicar la configuración inmediatamente
            $this->applyConfiguration($key, $value);
        }

        return $result;
    }

    /**
     * Obtener configuraciones por grupo
     */
    public function getByGroup(string $group): array
    {
        $cacheKey = self::CACHE_PREFIX . 'group_' . $group;

        return Cache::remember($cacheKey, self::CACHE_DURATION, function () use ($group) {
            return SystemConfiguration::getByGroup($group);
        });
    }

    /**
     * Obtener configuraciones públicas
     */
    public function getPublicConfigs(): array
    {
        $cacheKey = self::CACHE_PREFIX . 'public';

        return Cache::remember($cacheKey, self::CACHE_DURATION, function () {
            return SystemConfiguration::getPublicConfigs();
        });
    }

    /**
     * Limpiar cache de una configuración específica
     */
    public function clearCache(string $key = null): void
    {
        if ($key) {
            Cache::forget(self::CACHE_PREFIX . $key);
        } else {
            // Limpiar todo el cache de configuraciones
            $keys = [
                'public',
                'general',
                'federation',
                'notifications',
                'security',
                'files',
                'maintenance'
            ];

            foreach ($keys as $cacheKey) {
                Cache::forget(self::CACHE_PREFIX . $cacheKey);
                Cache::forget(self::CACHE_PREFIX . 'group_' . $cacheKey);
            }
        }
    }

    /**
     * Aplicar configuración inmediatamente
     */
    private function applyConfiguration(string $key, $value): void
    {
        switch ($key) {
            case 'app.name':
                Config::set('app.name', $value);
                break;

            case 'federation.annual_fee':
                Config::set('federation.annual_fee', $value);
                break;

            case 'notifications.email_enabled':
                Config::set('notifications.email_enabled', $value);
                break;

            case 'security.max_login_attempts':
                Config::set('auth.throttle.max_attempts', $value);
                break;

            case 'security.session_timeout':
                Config::set('session.lifetime', $value);
                break;
        }
    }

    /**
     * Recargar todas las configuraciones
     */
    public function reload(): void
    {
        $this->clearCache();

        try {
            $configurations = SystemConfiguration::all();

            foreach ($configurations as $config) {
                $this->applyConfiguration($config->key, $config->typed_value);
            }
        } catch (\Exception $e) {
            Log::error('Error reloading system configurations: ' . $e->getMessage());
        }
    }

    /**
     * Verificar si el modo mantenimiento está activo
     */
    public function isMaintenanceMode(): bool
    {
        return (bool) $this->get('maintenance.mode', false);
    }

    /**
     * Obtener mensaje de mantenimiento
     */
    public function getMaintenanceMessage(): string
    {
        return $this->get('maintenance.message', 'El sistema está en mantenimiento. Volveremos pronto.');
    }

    /**
     * Obtener configuraciones de federación
     */
    public function getFederationConfig(): array
    {
        return [
            'annual_fee' => $this->get('federation.annual_fee', 50000),
            'card_validity_months' => $this->get('federation.card_validity_months', 12),
            'auto_approve_payments' => $this->get('federation.auto_approve_payments', false),
        ];
    }

    /**
     * Obtener configuraciones de notificaciones
     */
    public function getNotificationConfig(): array
    {
        return [
            'email_enabled' => $this->get('notifications.email_enabled', true),
            'whatsapp_enabled' => $this->get('notifications.whatsapp_enabled', false),
            'admin_email' => $this->get('notifications.admin_email', 'admin@federacion.com'),
        ];
    }

    /**
     * Obtener configuraciones de seguridad
     */
    public function getSecurityConfig(): array
    {
        return [
            'max_login_attempts' => $this->get('security.max_login_attempts', 5),
            'session_timeout' => $this->get('security.session_timeout', 120),
        ];
    }

    /**
     * Obtener configuraciones de archivos
     */
    public function getFileConfig(): array
    {
        return [
            'max_upload_size' => $this->get('files.max_upload_size', 10),
            'allowed_extensions' => $this->get('files.allowed_extensions', ['jpg', 'jpeg', 'png', 'pdf']),
        ];
    }
}
