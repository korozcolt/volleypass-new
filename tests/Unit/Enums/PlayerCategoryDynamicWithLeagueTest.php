<?php

namespace Tests\Unit\Enums;

use Tests\TestCase;
use App\Enums\PlayerCategory;
use App\Models\League;
use App\Models\LeagueCategory;
use App\Models\Country;
use App\Models\Department;
use App\Models\City;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PlayerCategoryDynamicWithLeagueTest extends TestCase
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

        // Crear liga usando factory pero con datos geográficos reales
        $this->league = League::factory()->create([
            'country_id' => $country->id,
            'department_id' => $department->id,
            'city_id' => $city->id,
        ]);
    }

    public function test_returns_traditional_age_range_when_league_has_no_custom_categories()
    {
        $category = PlayerCategory::Mini;

        $ageRange = $category->getAgeRange($this->league);

        $this->assertEquals([8, 10], $ageRange);
    }

    public function test_returns_dynamic_age_range_when_league_has_custom_categories()
    {
        // Crear categoría personalizada para la liga
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

        $category = PlayerCategory::Mini;

        $ageRange = $category->getAgeRange($this->league);

        $this->assertEquals([6, 9], $ageRange);
    }

    public function test_get_for_age_with_league_without_custom_categories()
    {
        $category = PlayerCategory::getForAge(9, 'female', $this->league);

        $this->assertEquals(PlayerCategory::Mini, $category);
    }

    public function test_get_for_age_with_league_with_custom_categories()
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

        $category = PlayerCategory::getForAge(8, 'female', $this->league);

        $this->assertEquals(PlayerCategory::Mini, $category);
    }

    public function test_get_for_age_returns_null_when_no_matching_dynamic_category()
    {
        // Crear categoría personalizada que no mapea al enum
        LeagueCategory::create([
            'league_id' => $this->league->id,
            'name' => 'Categoría Especial',
            'code' => 'ESPECIAL',
            'min_age' => 8,
            'max_age' => 10,
            'gender' => 'mixed',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $category = PlayerCategory::getForAge(9, 'female', $this->league);

        $this->assertNull($category);
    }

    public function test_is_age_eligible_with_dynamic_ranges()
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

        $category = PlayerCategory::Mini;

        $this->assertTrue($category->isAgeEligible(7, $this->league));
        $this->assertTrue($category->isAgeEligible(6, $this->league));
        $this->assertTrue($category->isAgeEligible(9, $this->league));
        $this->assertFalse($category->isAgeEligible(5, $this->league));
        $this->assertFalse($category->isAgeEligible(10, $this->league));
    }

    public function test_get_age_range_text_with_dynamic_ranges()
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

        $category = PlayerCategory::Mini;

        $text = $category->getAgeRangeText($this->league);

        $this->assertEquals('6-9 años', $text);
    }

    public function test_get_dynamic_label_with_traditional_configuration()
    {
        $category = PlayerCategory::Mini;

        $label = $category->getDynamicLabel($this->league);

        $this->assertEquals('Mini (8-10 años)', $label);
    }

    public function test_get_dynamic_label_with_custom_configuration()
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

        $category = PlayerCategory::Mini;

        $label = $category->getDynamicLabel($this->league);

        $this->assertEquals('Mini Personalizada (6-9 años)', $label);
    }

    public function test_has_custom_configuration_returns_false_when_no_custom_categories()
    {
        $category = PlayerCategory::Mini;

        $hasCustom = $category->hasCustomConfiguration($this->league);

        $this->assertFalse($hasCustom);
    }

    public function test_has_custom_configuration_returns_true_when_custom_category_exists()
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

        $category = PlayerCategory::Mini;

        $hasCustom = $category->hasCustomConfiguration($this->league);

        $this->assertTrue($hasCustom);
    }

    public function test_get_available_categories_returns_traditional_when_no_custom_categories()
    {
        $categories = PlayerCategory::getAvailableCategories($this->league);

        $this->assertCount(7, $categories);
        $this->assertFalse($categories[0]['is_dynamic']);
    }

    public function test_get_available_categories_returns_dynamic_when_custom_categories_exist()
    {
        // Crear categorías personalizadas
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

        LeagueCategory::create([
            'league_id' => $this->league->id,
            'name' => 'Juvenil Personalizada',
            'code' => 'JUVENIL',
            'min_age' => 16,
            'max_age' => 19,
            'gender' => 'mixed',
            'is_active' => true,
            'sort_order' => 2,
        ]);

        $categories = PlayerCategory::getAvailableCategories($this->league);

        $this->assertCount(2, $categories);
        $this->assertEquals('MINI', $categories[0]['value']);
        $this->assertEquals('Mini Personalizada (6-9 años)', $categories[0]['label']);
        $this->assertTrue($categories[0]['is_dynamic']);
        $this->assertEquals([6, 9], $categories[0]['age_range']);
    }

    public function test_dynamic_categories_only_affect_specific_league()
    {
        // Crear segunda liga
        $league2 = League::factory()->create([
            'country_id' => $this->league->country_id,
            'department_id' => $this->league->department_id,
            'city_id' => $this->league->city_id,
        ]);

        // Crear categoría personalizada solo para la primera liga
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

        $category = PlayerCategory::Mini;

        // Primera liga debe usar configuración personalizada
        $this->assertEquals([6, 9], $category->getAgeRange($this->league));

        // Segunda liga debe usar configuración tradicional
        $this->assertEquals([8, 10], $category->getAgeRange($league2));
    }

    public function test_inactive_categories_are_not_used()
    {
        // Crear categoría personalizada pero inactiva
        LeagueCategory::create([
            'league_id' => $this->league->id,
            'name' => 'Mini Personalizada',
            'code' => 'MINI',
            'min_age' => 6,
            'max_age' => 9,
            'gender' => 'mixed',
            'is_active' => false, // Inactiva
            'sort_order' => 1,
        ]);

        $category = PlayerCategory::Mini;

        // Debe usar configuración tradicional porque la personalizada está inactiva
        $this->assertEquals([8, 10], $category->getAgeRange($this->league));
        $this->assertFalse($category->hasCustomConfiguration($this->league));
    }
}
