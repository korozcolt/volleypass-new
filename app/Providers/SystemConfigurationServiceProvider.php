<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use App\Models\SystemConfiguration;

class SystemConfigurationServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Registrar el servicio de configuraciones
        $this->app->singleton('system.config', function ($app) {
            return new \App\Services\SystemConfigurationService();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Solo cargar configuraciones si la tabla existe y no estamos en migración
        if ($this->shouldLoadConfigurations()) {
            $this->loadSystemConfigurations();
            $this->shareConfigurationsWithViews();
        }
    }

    /**
     * Verificar si debemos cargar las configuraciones
     */
    private function shouldLoadConfigurations(): bool
    {
        try {
            // Verificar si estamos en consola ejecutando migraciones
            if ($this->app->runningInConsole()) {
                $command = $_SERVER['argv'][1] ?? '';
                if (in_array($command, ['migrate', 'migrate:fresh', 'migrate:reset', 'migrate:rollback'])) {
                    return false;
                }
            }

            // Verificar si la tabla existe
            return Schema::hasTable('system_configurations');
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Cargar configuraciones del sistema
     */
    private function loadSystemConfigurations(): void
    {
        try {
            $configurations = SystemConfiguration::all();

            foreach ($configurations as $config) {
                // Aplicar configuraciones específicas del sistema
                $this->applySystemConfiguration($config);
            }
        } catch (\Exception $e) {
            // Log error but don't break the application
            Log::warning('Could not load system configurations: ' . $e->getMessage());
        }
    }

    /**
     * Aplicar configuración específica del sistema
     */
    private function applySystemConfiguration(SystemConfiguration $config): void
    {
        $value = $config->typed_value;

        switch ($config->key) {
            case 'app.name':
                Config::set('app.name', $value);
                break;

            case 'app.description':
                Config::set('app.description', $value);
                break;

            case 'app.version':
                Config::set('app.version', $value);
                break;

            case 'federation.annual_fee':
                Config::set('federation.annual_fee', $value);
                break;

            case 'federation.card_validity_months':
                Config::set('federation.card_validity_months', $value);
                break;

            case 'notifications.email_enabled':
                Config::set('notifications.email_enabled', $value);
                break;

            case 'notifications.whatsapp_enabled':
                Config::set('notifications.whatsapp_enabled', $value);
                break;

            case 'notifications.admin_email':
                Config::set('notifications.admin_email', $value);
                break;

            case 'security.max_login_attempts':
                Config::set('auth.throttle.max_attempts', $value);
                break;

            case 'security.session_timeout':
                Config::set('session.lifetime', $value);
                break;

            case 'files.max_upload_size':
                Config::set('filesystems.max_upload_size', $value * 1024); // Convert MB to KB
                break;

            case 'maintenance.mode':
                if ($value) {
                    // Activar modo mantenimiento si está habilitado
                    Config::set('app.maintenance', true);
                }
                break;
        }
    }

    /**
     * Compartir configuraciones con las vistas
     */
    private function shareConfigurationsWithViews(): void
    {
        try {
            $publicConfigs = SystemConfiguration::getPublicConfigs();

            View::share('systemConfig', $publicConfigs);

            // Compartir configuraciones específicas para el panel de Filament
            View::composer('filament::*', function ($view) use ($publicConfigs) {
                $view->with('systemConfig', $publicConfigs);
            });
        } catch (\Exception $e) {
            Log::warning('Could not share configurations with views: ' . $e->getMessage());
        }
    }
}
