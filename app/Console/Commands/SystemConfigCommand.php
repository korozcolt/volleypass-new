<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\SystemConfigurationService;
use App\Models\SystemConfiguration;

class SystemConfigCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'system:config
                            {action : Acción a realizar (get, set, list, reload, reset)}
                            {key? : Clave de configuración}
                            {value? : Valor a establecer}
                            {--group= : Filtrar por grupo}
                            {--public : Solo configuraciones públicas}
                            {--force : Forzar la acción sin confirmación}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Gestionar configuraciones del sistema';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $action = $this->argument('action');
        $configService = app(SystemConfigurationService::class);

        return match ($action) {
            'get' => $this->getConfiguration($configService),
            'set' => $this->setConfiguration($configService),
            'list' => $this->listConfigurations($configService),
            'reload' => $this->reloadConfigurations($configService),
            'reset' => $this->resetConfigurations(),
            'test' => $this->testConfiguration($configService),
            default => $this->error("Acción no válida. Usa: get, set, list, reload, reset, test")
        };
    }

    /**
     * Obtener una configuración
     */
    private function getConfiguration(SystemConfigurationService $configService): int
    {
        $key = $this->argument('key');

        if (!$key) {
            $this->error('Debes especificar una clave de configuración.');
            return 1;
        }

        $value = $configService->get($key);

        if ($value === null) {
            $this->warn("Configuración '{$key}' no encontrada.");
            return 1;
        }

        $this->info("Configuración: {$key}");
        $this->line("Valor: " . (is_array($value) ? json_encode($value, JSON_PRETTY_PRINT) : $value));

        return 0;
    }

    /**
     * Establecer una configuración
     */
    private function setConfiguration(SystemConfigurationService $configService): int
    {
        $key = $this->argument('key');
        $value = $this->argument('value');

        if (!$key || $value === null) {
            $this->error('Debes especificar una clave y un valor.');
            return 1;
        }

        // Verificar si la configuración existe y es editable
        $config = SystemConfiguration::where('key', $key)->first();

        if (!$config) {
            $this->error("Configuración '{$key}' no existe.");
            return 1;
        }

        if (!$config->is_editable) {
            $this->error("Configuración '{$key}' no es editable.");
            return 1;
        }

        // Convertir el valor según el tipo
        $convertedValue = $this->convertValue($value, $config->type);

        if (!$this->option('force')) {
            $this->info("Configuración: {$key}");
            $this->line("Valor actual: " . $config->value);
            $this->line("Nuevo valor: " . $convertedValue);

            if (!$this->confirm('¿Confirmas el cambio?')) {
                $this->info('Operación cancelada.');
                return 0;
            }
        }

        $success = $configService->set($key, $convertedValue);

        if ($success) {
            $this->info("Configuración '{$key}' actualizada exitosamente.");
            return 0;
        } else {
            $this->error("Error al actualizar la configuración '{$key}'.");
            return 1;
        }
    }

    /**
     * Listar configuraciones
     */
    private function listConfigurations(SystemConfigurationService $configService): int
    {
        $group = $this->option('group');
        $publicOnly = $this->option('public');

        if ($group) {
            $configs = SystemConfiguration::where('group', $group);
        } else {
            $configs = SystemConfiguration::query();
        }

        if ($publicOnly) {
            $configs->where('is_public', true);
        }

        $configs = $configs->orderBy('group')->orderBy('key')->get();

        if ($configs->isEmpty()) {
            $this->warn('No se encontraron configuraciones.');
            return 0;
        }

        $this->info('Configuraciones del Sistema:');
        $this->newLine();

        $currentGroup = null;
        foreach ($configs as $config) {
            if ($currentGroup !== $config->group) {
                $currentGroup = $config->group;
                $this->line("<fg=yellow>[{$currentGroup}]</>");
            }

            $status = [];
            if (!$config->is_editable) $status[] = 'Solo lectura';
            if ($config->is_public) $status[] = 'Público';

            $statusText = $status ? ' (' . implode(', ', $status) . ')' : '';

            $this->line("  <fg=cyan>{$config->key}</> = {$config->value}{$statusText}");

            if ($config->description) {
                $this->line("    <fg=gray>{$config->description}</>");
            }
        }

        return 0;
    }

    /**
     * Recargar configuraciones
     */
    private function reloadConfigurations(SystemConfigurationService $configService): int
    {
        $this->info('Recargando configuraciones del sistema...');

        $configService->reload();

        $this->info('Configuraciones recargadas exitosamente.');
        return 0;
    }

    /**
     * Resetear configuraciones a valores por defecto
     */
    private function resetConfigurations(): int
    {
        if (!$this->option('force')) {
            $this->warn('Esta acción restablecerá TODAS las configuraciones a sus valores por defecto.');

            if (!$this->confirm('¿Estás seguro de continuar?')) {
                $this->info('Operación cancelada.');
                return 0;
            }
        }

        $this->info('Ejecutando seeder de configuraciones...');

        $this->call('db:seed', ['--class' => 'SystemConfigurationSeeder', '--force' => true]);

        $this->info('Configuraciones restablecidas exitosamente.');
        return 0;
    }

    /**
     * Probar configuraciones
     */
    private function testConfiguration(SystemConfigurationService $configService): int
    {
        $this->info('Probando configuraciones del sistema...');
        $this->newLine();

        // Probar configuraciones críticas
        $tests = [
            'app.name' => 'Nombre de la aplicación',
            'app.version' => 'Versión de la aplicación',
            'federation.annual_fee' => 'Cuota anual de federación',
            'notifications.email_enabled' => 'Notificaciones por email',
            'security.max_login_attempts' => 'Máximo intentos de login',
            'files.max_upload_size' => 'Tamaño máximo de archivo',
        ];

        $passed = 0;
        $failed = 0;

        foreach ($tests as $key => $description) {
            $value = $configService->get($key);

            if ($value !== null) {
                $this->line("<fg=green>✓</> {$description}: {$value}");
                $passed++;
            } else {
                $this->line("<fg=red>✗</> {$description}: No configurado");
                $failed++;
            }
        }

        $this->newLine();
        $this->info("Pruebas completadas: {$passed} exitosas, {$failed} fallidas");

        return $failed > 0 ? 1 : 0;
    }

    /**
     * Convertir valor según el tipo
     */
    private function convertValue(string $value, string $type)
    {
        return match ($type) {
            'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'number' => is_numeric($value) ? (float) $value : $value,
            'json' => json_decode($value, true) ?? $value,
            'date' => $value, // Se maneja como string
            default => $value,
        };
    }
}
