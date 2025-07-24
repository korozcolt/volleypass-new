<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\League;
use App\Models\LeagueConfiguration;
use App\Models\Country;
use App\Models\Department;
use App\Models\City;
use App\Services\LeagueConfigurationService;
use App\Enums\ConfigurationType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;

class LeagueConfigurationServiceTest extends TestCase
{
    use RefreshDatabase;

    protected League $league;

    protected function setUp(): void
    {
        parent::setUp();

        // Create required geographic data
        $country = Country::create([
            'name' => 'Test Country',
            'code' => 'TC',
            'phone_code' => '+1',
            'currency' => 'USD',
            'flag' => 'test-flag.png'
        ]);

        $department = Department::create([
            'country_id' => $country->id,
            'name' => 'Test Department',
            'code' => 'TD'
        ]);

        $city = City::create([
            'department_id' => $department->id,
            'name' => 'Test City',
            'code' => 'TC'
        ]);

        // Create test league
        $this->league = League::create([
            'name' => 'Liga de Prueba Test',
            'short_name' => 'LPT',
            'status' => 'active',
            'country_id' => $country->id,
            'department_id' => $department->id,
            'city_id' => $city->id,
        ]);

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
        $value = LeagueConfiguration::get($this->league->id, 'transfer_approval_required');

        $this->assertTrue($value);
    }

    public function test_can_get_configuration_with_default(): void
    {
        $value = LeagueConfiguration::get($this->league->id, 'non_existent_key', 'default_value');

        $this->assertEquals('default_value', $value);
    }

    public function test_can_set_configuration_value(): void
    {
        $result = LeagueConfiguration::set($this->league->id, 'max_transfers_per_season', 5);

        $this->assertTrue($result);

        $value = LeagueConfiguration::get($this->league->id, 'max_transfers_per_season');
        $this->assertEquals(5, $value);
    }

    public function test_can_get_configurations_by_group(): void
    {
        $configs = LeagueConfiguration::getByGroup($this->league->id, 'transfers');

        $this->assertIsArray($configs);
        $this->assertArrayHasKey('transfer_approval_required', $configs);
        $this->assertArrayHasKey('max_transfers_per_season', $configs);
    }

    public function test_transfer_approval_required(): void
    {
        $required = LeagueConfiguration::get($this->league->id, 'transfer_approval_required', false);

        $this->assertTrue($required);
    }

    public function test_max_transfers_per_season(): void
    {
        $max = LeagueConfiguration::get($this->league->id, 'max_transfers_per_season', 0);

        $this->assertEquals(3, $max);
    }

    public function test_can_get_public_configurations(): void
    {
        $configs = LeagueConfiguration::getPublicConfigs($this->league->id);

        $this->assertIsArray($configs);
        $this->assertArrayHasKey('max_transfers_per_season', $configs);
    }

    public function test_transfer_window_dates_configuration(): void
    {
        // Test setting transfer window dates
        LeagueConfiguration::create([
            'league_id' => $this->league->id,
            'key' => 'transfer_window_start',
            'value' => now()->subDays(5)->toDateString(),
            'type' => ConfigurationType::DATE,
            'group' => 'transfers',
            'description' => 'Test config',
            'is_public' => true,
            'default_value' => null,
        ]);

        LeagueConfiguration::create([
            'league_id' => $this->league->id,
            'key' => 'transfer_window_end',
            'value' => now()->addDays(5)->toDateString(),
            'type' => ConfigurationType::DATE,
            'group' => 'transfers',
            'description' => 'Test config',
            'is_public' => true,
            'default_value' => null,
        ]);

        $startDate = LeagueConfiguration::get($this->league->id, 'transfer_window_start');
        $endDate = LeagueConfiguration::get($this->league->id, 'transfer_window_end');

        $this->assertNotNull($startDate);
        $this->assertNotNull($endDate);
    }

    public function test_boolean_configuration_handling(): void
    {
        // Test boolean true
        $value = LeagueConfiguration::get($this->league->id, 'transfer_approval_required');
        $this->assertTrue($value);
    }

    public function test_number_configuration_handling(): void
    {
        // Test number
        $value = LeagueConfiguration::get($this->league->id, 'max_transfers_per_season');
        $this->assertEquals(3, $value);
    }

    public function test_configuration_update(): void
    {
        // Get initial value
        $value1 = LeagueConfiguration::get($this->league->id, 'transfer_approval_required');

        // Update value
        $result = LeagueConfiguration::set($this->league->id, 'transfer_approval_required', false);

        // Get updated value
        $value2 = LeagueConfiguration::get($this->league->id, 'transfer_approval_required');

        $this->assertTrue($value1);
        $this->assertTrue($result);
        $this->assertFalse($value2);
    }

    public function test_configuration_validation(): void
    {
        $config = LeagueConfiguration::where('league_id', $this->league->id)
            ->where('key', 'transfer_approval_required')
            ->first();

        $this->assertNotNull($config);
        $this->assertTrue($config->validateValue(true));
        $this->assertTrue($config->validateValue(false));
    }
}
