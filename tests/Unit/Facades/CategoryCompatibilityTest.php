<?php

namespace Tests\Unit\Facades;

use Tests\TestCase;
use App\Facades\CategoryCompatibility;
use App\Enums\PlayerCategory;
use App\Models\League;
use App\Models\LeagueCategory;
use App\Models\Country;
use App\Models\Department;
use App\Models\City;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CategoryCompatibilityTest extends TestCase
{
    use RefreshDatabase;

    protected League $league;

    protected function setUp(): void
    {
        parent::setUp();

        // Crear datos geográficos directamente (sin factories)
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

        $this->league = League::factory()->create([
            'country_id' => $country->id,
            'department_id' => $department->id,
            'city_id' => $city->id,
        ]);
    }

    public function test_facade_methods_work_correctly()
    {
        // Test getCategoryForAge
        $category = CategoryCompatibility::getCategoryForAge(9, 'female');
        $this->assertEquals(PlayerCategory::Mini, $category);

        // Test isAgeEligibleForCategory
        $isEligible = CategoryCompatibility::isAgeEligibleForCategory(9, PlayerCategory::Mini);
        $this->assertTrue($isEligible);

        // Test getAgeRangeForCategory
        $range = CategoryCompatibility::getAgeRangeForCategory(PlayerCategory::Mini);
        $this->assertEquals([8, 10], $range);

        // Test getAgeRangeTextForCategory
        $text = CategoryCompatibility::getAgeRangeTextForCategory(PlayerCategory::Mini);
        $this->assertEquals('8-10 años', $text);

        // Test getAvailableCategories
        $categories = CategoryCompatibility::getAvailableCategories();
        $this->assertCount(7, $categories);

        // Test getCategoryOptions
        $options = CategoryCompatibility::getCategoryOptions();
        $this->assertIsArray($options);
        $this->assertArrayHasKey('mini', $options);

        // Test isCategoryValidForLeague
        $isValid = CategoryCompatibility::isCategoryValidForLeague('mini');
        $this->assertTrue($isValid);

        // Test getCategoryEnum
        $enum = CategoryCompatibility::getCategoryEnum('mini');
        $this->assertEquals(PlayerCategory::Mini, $enum);

        // Test isDynamicSystemActive
        $isActive = CategoryCompatibility::isDynamicSystemActive($this->league);
        $this->assertFalse($isActive);

        // Test getCompatibilityInfo
        $info = CategoryCompatibility::getCompatibilityInfo($this->league);
        $this->assertIsArray($info);
        $this->assertArrayHasKey('system_mode', $info);
        $this->assertEquals('traditional', $info['system_mode']);
    }

    public function test_facade_methods_work_with_dynamic_categories()
    {
        // Crear categoría personalizada
        LeagueCategory::create([
            'league_id' => $this->league->id,
            'name' => 'Mini Personalizada',
            'code' => 'MINI',
            'min_age' => 6,
            'max_age' => 9,
            'gender' => 'mixed',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        // Test con sistema dinámico
        $category = CategoryCompatibility::getCategoryForAge(8, 'female', $this->league);
        $this->assertEquals(PlayerCategory::Mini, $category);

        $range = CategoryCompatibility::getAgeRangeForCategory(PlayerCategory::Mini, $this->league);
        $this->assertEquals([6, 9], $range);

        $text = CategoryCompatibility::getAgeRangeTextForCategory(PlayerCategory::Mini, $this->league);
        $this->assertEquals('6-9 años', $text);

        $categories = CategoryCompatibility::getAvailableCategories($this->league);
        $this->assertCount(1, $categories);
        $this->assertTrue($categories[0]['is_dynamic']);

        $isActive = CategoryCompatibility::isDynamicSystemActive($this->league);
        $this->assertTrue($isActive);

        $info = CategoryCompatibility::getCompatibilityInfo($this->league);
        $this->assertEquals('dynamic', $info['system_mode']);
    }

    public function test_facade_migration_methods()
    {
        $results = CategoryCompatibility::migratePlayersCategories($this->league);

        $this->assertArrayHasKey('migrated', $results);
        $this->assertArrayHasKey('errors', $results);
        $this->assertArrayHasKey('unchanged', $results);
        $this->assertArrayHasKey('details', $results);
    }

    public function test_facade_stats_methods()
    {
        $stats = CategoryCompatibility::getCategoryStats($this->league);

        $this->assertIsArray($stats);
    }
}
