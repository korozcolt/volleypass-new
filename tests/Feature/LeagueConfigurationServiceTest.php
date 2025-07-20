<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\League;
use App\Models\LeagueConfiguration;
use App\Services\LeagueConfigurationService;
use App\Enums\ConfigurationType;
// use Illuminate\Foundation\Testing\RefreshDatabase; // Removido para evitar conflictos
use Illuminate\Support\Facades\Cache;

class LeagueConfigurationServiceTest extends TestCase
{
    // Removemos RefreshDatabase para evitar conflictos con la BD existente

    protected LeagueConfigurationService $service;
    protected League $league;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = app(LeagueConfigurationService::class);

        // Usar liga existente en lugar de crear una nueva
        $this->league = League::first();

        // Si no hay liga, crear una
        if (!$this->league) {
            $this->league = League::create([
                'name' => 'Liga de Prueba Test',
                'short_name' => 'LPT',
                'status' => 'active',
                'country_id' => 1,
            ]);
        }

        // Limpiar configuraciones existentes para este test
        LeagueConfiguration::where('league_id', $this->league->id)
            ->whereIn('key', ['transfer_approval_required', 'max_transfers_per_season'])
            ->delete();

        // Crear configuraciones de prueba
        LeagueConfiguration::create([
            'league_id' => $this->league->id,
            'key' => 'transfer_approval_required',
            'value' => '1',
            'type' => ConfigurationType::BOOLEAN,
            'group' => 'transfers',
            'description' => 'Test config',
            'is_public' => false,
            'default_value' => '1',
        ]);

        LeagueConfiguration::create([
            'league_id' => $this->league->id,
            'key' => 'max_transfers_per_season',
            'value' => '3',
            'type' => ConfigurationType::NUMBER,
            'group' => 'transfers',
            'description' => 'Test config',
            'is_public' => true,
            'default_value' => '2',
        ]);
    }

    public function test_can_get_configuration_value(): void
    {
        $value = $this->service->get($this->league->id, 'transfer_approval_required');

        $this->assertTrue($value);
    }

    public function test_can_get_configuration_with_default(): void
    {
        $value = $this->service->get($this->league->id, 'non_existent_key', 'default_value');

        $this->assertEquals('default_value', $value);
    }

    public function test_can_set_configuration_value(): void
    {
        $result = $this->service->set($this->league->id, 'max_transfers_per_season', 5);

        $this->assertTrue($result);

        $value = $this->service->get($this->league->id, 'max_transfers_per_season');
        $this->assertEquals(5, $value);
    }

    public function test_can_get_configurations_by_group(): void
    {
        $configs = $this->service->getByGroup($this->league->id, 'transfers');

        $this->assertIsArray($configs);
        $this->assertArrayHasKey('transfer_approval_required', $configs);
        $this->assertArrayHasKey('max_transfers_per_season', $configs);
    }

    public function test_transfer_approval_required(): void
    {
        $required = $this->service->isTransferApprovalRequired($this->league->id);

        $this->assertTrue($required);
    }

    public function test_max_transfers_per_season(): void
    {
        $max = $this->service->getMaxTransfersPerSeason($this->league->id);

        $this->assertEquals(3, $max);
    }

    public function test_transfer_window_open_without_dates(): void
    {
        // Sin fechas configuradas, la ventana debe estar siempre abierta
        $isOpen = $this->service->isTransferWindowOpen($this->league->id);

        $this->assertTrue($isOpen);
    }

    public function test_transfer_window_open_with_dates(): void
    {
        // Configurar ventana de traspasos
        LeagueConfiguration::create([
            'league_id' => $this->league->id,
            'key' => 'transfer_window_start',
            'value' => now()->subDays(10)->toDateString(),
            'type' => ConfigurationType::DATE,
            'group' => 'transfers',
            'description' => 'Test config',
        ]);

        LeagueConfiguration::create([
            'league_id' => $this->league->id,
            'key' => 'transfer_window_end',
            'value' => now()->addDays(10)->toDateString(),
            'type' => ConfigurationType::DATE,
            'group' => 'transfers',
            'description' => 'Test config',
        ]);

        $isOpen = $this->service->isTransferWindowOpen($this->league->id);

        $this->assertTrue($isOpen);
    }

    public function test_cache_is_used(): void
    {
        // Primera llamada - debe cachear
        $value1 = $this->service->get($this->league->id, 'transfer_approval_required');

        // Cambiar valor directamente en BD (sin usar el servicio)
        LeagueConfiguration::where('league_id', $this->league->id)
            ->where('key', 'transfer_approval_required')
            ->update(['value' => '0']);

        // Segunda llamada - debe devolver valor cacheado
        $value2 = $this->service->get($this->league->id, 'transfer_approval_required');

        $this->assertEquals($value1, $value2);
        $this->assertTrue($value2); // Debe seguir siendo true por el cache
    }

    public function test_cache_is_cleared_when_setting_value(): void
    {
        // Primera llamada - cachea el valor
        $value1 = $this->service->get($this->league->id, 'transfer_approval_required');
        $this->assertTrue($value1);

        // Cambiar valor usando el servicio - debe limpiar cache
        $this->service->set($this->league->id, 'transfer_approval_required', false);

        // Nueva llamada - debe devolver el nuevo valor
        $value2 = $this->service->get($this->league->id, 'transfer_approval_required');
        $this->assertFalse($value2);
    }

    public function test_get_all_configurations(): void
    {
        $allConfigs = $this->service->getAllConfigurations($this->league->id);

        $this->assertIsArray($allConfigs);
        $this->assertArrayHasKey('transfers', $allConfigs);
        $this->assertArrayHasKey('transfer_approval_required', $allConfigs['transfers']);
    }

    public function test_configuration_stats(): void
    {
        $stats = $this->service->getConfigurationStats($this->league->id);

        $this->assertIsArray($stats);
        $this->assertArrayHasKey('total_configurations', $stats);
        $this->assertArrayHasKey('by_group', $stats);
        $this->assertArrayHasKey('public_configurations', $stats);
        $this->assertEquals(2, $stats['total_configurations']);
        $this->assertEquals(1, $stats['public_configurations']);
    }

    public function test_reload_clears_cache(): void
    {
        // Cachear un valor
        $this->service->get($this->league->id, 'transfer_approval_required');

        // Verificar que está en cache
        $cacheKey = "league_config_{$this->league->id}_transfer_approval_required";
        $this->assertTrue(Cache::has($cacheKey));

        // Recargar configuraciones
        $this->service->reload($this->league->id);

        // Verificar que el cache se limpió
        $this->assertFalse(Cache::has($cacheKey));
    }
}
