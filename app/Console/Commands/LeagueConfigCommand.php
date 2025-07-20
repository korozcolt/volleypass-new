<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\League;
use App\Services\LeagueConfigurationService;
use App\Models\LeagueConfiguration;

class LeagueConfigCommand extends Command
{
    protected $signature = 'league:config
                            {action : get, set, list, reset}
                            {league_id : ID de la liga}
                            {key? : Clave de configuración}
                            {value? : Valor a establecer}
                            {--group= : Filtrar por grupo}
                            {--force : Forzar reset sin confirmación}';

    protected $description = 'Gestionar configuraciones de liga';

    protected LeagueConfigurationService $configService;

    public function __construct(LeagueConfigurationService $configService)
    {
        parent::__construct();
        $this->configService = $configService;
    }

    public function handle(): int
    {
        $action = $this->argument('action');
        $leagueId = $this->argument('league_id');

        // Verificar que la liga existe
        $league = League::find($leagueId);
        if (!$league) {
            $this->error("Liga con ID {$leagueId} no encontrada");
            return 1;
        }

        $this->info("Liga: {$league->name}");
        $this->newLine();

        return match ($action) {
            'get' => $this->handleGet($leagueId),
            'set' => $this->handleSet($leagueId),
            'list' => $this->handleList($leagueId),
            'reset' => $this->handleReset($leagueId),
            default => $this->handleInvalidAction($action),
        };
    }

    private function handleGet(int $leagueId): int
    {
        $key = $this->argument('key');
        if (!$key) {
            $this->error('Clave requerida para obtener configuración');
            return 1;
        }

        $value = $this->configService->get($leagueId, $key);

        if ($value === null) {
            $this->warn("Configuración '{$key}' no encontrada");
            return 1;
        }

        $this->info("Configuración: {$key}");
        $this->line("Valor: " . (is_bool($value) ? ($value ? 'true' : 'false') : $value));

        return 0;
    }

    private function handleSet(int $leagueId): int
    {
        $key = $this->argument('key');
        $value = $this->argument('value');

        if (!$key || $value === null) {
            $this->error('Clave y valor requeridos para establecer configuración');
            return 1;
        }

        $result = $this->configService->set($leagueId, $key, $value);

        if ($result) {
            $this->info("Configuración '{$key}' actualizada exitosamente");
            $this->line("Nuevo valor: {$value}");
        } else {
            $this->error("Error al actualizar configuración '{$key}'");
            return 1;
        }

        return 0;
    }

    private function handleList(int $leagueId): int
    {
        $group = $this->option('group');

        if ($group) {
            $configurations = $this->configService->getByGroup($leagueId, $group);
            $this->info("Configuraciones del grupo '{$group}':");
        } else {
            $configurations = $this->configService->getAllConfigurations($leagueId);
            $this->info('Todas las configuraciones:');
        }

        if (empty($configurations)) {
            $this->warn('No se encontraron configuraciones');
            return 0;
        }

        $this->newLine();

        if ($group) {
            // Mostrar configuraciones de un grupo específico
            foreach ($configurations as $key => $value) {
                $displayValue = is_bool($value) ? ($value ? 'true' : 'false') : $value;
                $this->line("  {$key}: {$displayValue}");
            }
        } else {
            // Mostrar todas las configuraciones agrupadas
            foreach ($configurations as $groupName => $groupConfigs) {
                $this->info("Grupo: {$groupName}");
                foreach ($groupConfigs as $key => $value) {
                    $displayValue = is_bool($value) ? ($value ? 'true' : 'false') : $value;
                    $this->line("  {$key}: {$displayValue}");
                }
                $this->newLine();
            }
        }

        return 0;
    }

    private function handleReset(int $leagueId): int
    {
        if (!$this->option('force')) {
            if (!$this->confirm('¿Estás seguro de que quieres resetear todas las configuraciones a sus valores por defecto?')) {
                $this->info('Operación cancelada');
                return 0;
            }
        }

        $configurations = LeagueConfiguration::where('league_id', $leagueId)->get();
        $resetCount = 0;

        foreach ($configurations as $config) {
            if ($config->default_value !== null) {
                $config->update(['value' => $config->default_value]);
                $resetCount++;
            }
        }

        // Limpiar cache
        $this->configService->reload($leagueId);

        $this->info("Se resetearon {$resetCount} configuraciones a sus valores por defecto");
        return 0;
    }

    private function handleInvalidAction(string $action): int
    {
        $this->error("Acción '{$action}' no válida");
        $this->info('Acciones disponibles: get, set, list, reset');
        return 1;
    }
}
