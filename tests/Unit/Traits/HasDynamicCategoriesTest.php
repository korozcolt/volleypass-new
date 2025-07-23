<?php

namespace Tests\Unit\Traits;

use Tests\TestCase;
use App\Traits\HasDynamicCategories;
use App\Enums\PlayerCategory;
use App\Models\League;
use App\Models\LeagueCategory;
use App\Models\Country;
use App\Models\Department;
use App\Models\City;
use Illuminate\Foundation\Testing\RefreshDatabase;

class HasDynamicCategoriesTest extends TestCase
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

    public function test_trait_methods_work_correctly()
    {
        // Crear una clase de prueba que use el trait
        $testClass = new class {
            use HasDynamicCategories;
        };

        // Test getCategoryForAge
        $category = $testClass->getCategoryForAge(9, 'female');
        $this->assertEquals(PlayerCategory::Mini, $category);

        // Test isAgeEligibleForCategory
        $isEligible = $testClass->isAgeEligibleForCategory(9, PlayerCategory::Mini);
        $this->assertTrue($isEligible);

        // Test getAgeRangeForCategory
        $range = $testClass->getAgeRangeForCategory(PlayerCategory::Mini);
        $this->assertEquals([8, 10], $range);

        // Test getCategoryOptions
        $options = $testClass->getCategoryOptions();
        $this->assertIsArray($options);
        $this->assertArrayHasKey('mini', $options);

        // Test isCategoryValidForLeague
        $isValid = $testClass->isCategoryValidForLeague('mini');
        $this->assertTrue($isValid);

        // Test isDynamicCategorySystemActive
        $isActive = $testClass->isDynamicCategorySystemActive($this->league);
        $this->assertFalse($isActive);

        // Test getCategorySystemInfo
        $info = $testClass->getCategorySystemInfo($this->league);
        $this->assertIsArray($info);
        $this->assertArrayHasKey('system_mode', $info);
    }

    public function test_trait_methods_work_with_dynamic_categories()
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

        $testClass = new class {
            use HasDynamicCategories;
        };

        // Test con sistema dinámico
        $category = $testClass->getCategoryForAge(8, 'female', $this->league);
        $this->assertEquals(PlayerCategory::Mini, $category);

        $range = $testClass->getAgeRangeForCategory(PlayerCategory::Mini, $this->league);
        $this->assertEquals([6, 9], $range);

        $isActive = $testClass->isDynamicCategorySystemActive($this->league);
        $this->assertTrue($isActive);

        $info = $testClass->getCategorySystemInfo($this->league);
        $this->assertEquals('dynamic', $info['system_mode']);
    }
}
