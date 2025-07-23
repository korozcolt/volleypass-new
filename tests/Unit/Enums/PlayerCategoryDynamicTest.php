<?php

namespace Tests\Unit\Enums;

use Tests\TestCase;
use App\Enums\PlayerCategory;
use App\Models\League;
use App\Models\LeagueCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PlayerCategoryDynamicTest extends TestCase
{
    use RefreshDatabase;

    protected League $league;

    protected function setUp(): void
    {
        parent::setUp();

        // Crear datos necesarios para League
        $country = \App\Models\Country::factory()->create();
        $department = \App\Models\Department::factory()->create(['country_id' => $country->id]);
        $city = \App\Models\City::factory()->create(['department_id' => $department->id]);

        $this->league = League::factory()->create([
            'country_id' => $country->id,
            'department_id' => $department->id,
            'city_id' => $city->id,
        ]);
    }

    public function test_returns_traditional_age_range_when_no_league_provided()
    {
        $category = PlayerCategory::Mini;

        $ageRange = $category->getAgeRange();

        $this->assertEquals([8, 10], $ageRange);
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
        LeagueCategory::factory()->create([
            'league_id' => $this->league->id,
            'name' => 'Mini Personalizada',
            'code' => 'MINI',
            'min_age' => 6,
            'max_age' => 9,
            'gender' => 'mixed',
            'is_active' => true,
        ]);

        $category = PlayerCategory::Mini;

        $ageRange = $category->getAgeRange($this->league);

        $this->assertEquals([6, 9], $ageRange);
    }

    public function test_get_for_age_returns_correct_traditional_category()
    {
        $category = PlayerCategory::getForAge(9, 'female');

        $this->assertEquals(PlayerCategory::Mini, $category);
    }

    public function test_get_for_age_with_league_without_custom_categories()
    {
        $category = PlayerCategory::getForAge(9, 'female', $this->league);

        $this->assertEquals(PlayerCategory::Mini, $category);
    }

    public function test_get_for_age_with_league_with_custom_categories()
    {
        // Crear categoría personalizada
        LeagueCategory::factory()->create([
            'league_id' => $this->league->id,
            'name' => 'Mini Personalizada',
            'code' => 'MINI',
            'min_age' => 6,
            'max_age' => 9,
            'gender' => 'mixed',
            'is_active' => true,
        ]);

        $category = PlayerCategory::getForAge(8, 'female', $this->league);

        $this->assertEquals(PlayerCategory::Mini, $category);
    }

    public function test_get_for_age_returns_null_when_no_matching_dynamic_category()
    {
        // Crear categoría personalizada que no mapea al enum
        LeagueCategory::factory()->create([
            'league_id' => $this->league->id,
            'name' => 'Categoría Especial',
            'code' => 'ESPECIAL',
            'min_age' => 8,
            'max_age' => 10,
            'gender' => 'mixed',
            'is_active' => true,
        ]);

        $category = PlayerCategory::getForAge(9, 'female', $this->league);

        $this->assertNull($category);
    }

    public function test_is_age_eligible_with_traditional_ranges()
    {
        $category = PlayerCategory::Mini;

        $this->assertTrue($category->isAgeEligible(9));
        $this->assertFalse($category->isAgeEligible(12));
    }

    public function test_is_age_eligible_with_dynamic_ranges()
    {
        // Crear categoría personalizada
        LeagueCategory::factory()->create([
            'league_id' => $this->league->id,
            'name' => 'Mini Personalizada',
            'code' => 'MINI',
            'min_age' => 6,
            'max_age' => 9,
            'gender' => 'mixed',
            'is_active' => true,
        ]);

        $category = PlayerCategory::Mini;

        $this->assertTrue($category->isAgeEligible(7, $this->league));
        $this->assertFalse($category->isAgeEligible(10, $this->league));
    }

    public function test_get_age_range_text_with_traditional_ranges()
    {
        $category = PlayerCategory::Mini;

        $text = $category->getAgeRangeText();

        $this->assertEquals('8-10 años', $text);
    }

    public function test_get_age_range_text_with_dynamic_ranges()
    {
        // Crear categoría personalizada
        LeagueCategory::factory()->create([
            'league_id' => $this->league->id,
            'name' => 'Mini Personalizada',
            'code' => 'MINI',
            'min_age' => 6,
            'max_age' => 9,
            'gender' => 'mixed',
            'is_active' => true,
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
        LeagueCategory::factory()->create([
            'league_id' => $this->league->id,
            'name' => 'Mini Personalizada',
            'code' => 'MINI',
            'min_age' => 6,
            'max_age' => 9,
            'gender' => 'mixed',
            'is_active' => true,
        ]);

        $category = PlayerCategory::Mini;

        $label = $category->getDynamicLabel($this->league);

        $this->assertEquals('Mini Personalizada (6-9 años)', $label);
    }

    public function test_has_custom_configuration_returns_false_when_no_league()
    {
        $category = PlayerCategory::Mini;

        $hasCustom = $category->hasCustomConfiguration();

        $this->assertFalse($hasCustom);
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
        LeagueCategory::factory()->create([
            'league_id' => $this->league->id,
            'name' => 'Mini Personalizada',
            'code' => 'MINI',
            'min_age' => 6,
            'max_age' => 9,
            'gender' => 'mixed',
            'is_active' => true,
        ]);

        $category = PlayerCategory::Mini;

        $hasCustom = $category->hasCustomConfiguration($this->league);

        $this->assertTrue($hasCustom);
    }

    public function test_get_available_categories_returns_traditional_when_no_league()
    {
        $categories = PlayerCategory::getAvailableCategories();

        $this->assertCount(7, $categories); // 7 categorías tradicionales
        $this->assertEquals('mini', $categories[0]['value']);
        $this->assertEquals('Mini (8-10 años)', $categories[0]['label']);
        $this->assertFalse($categories[0]['is_dynamic']);
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
        LeagueCategory::factory()->create([
            'league_id' => $this->league->id,
            'name' => 'Mini Personalizada',
            'code' => 'MINI',
            'min_age' => 6,
            'max_age' => 9,
            'gender' => 'mixed',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        LeagueCategory::factory()->create([
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
    }

    public function test_get_traditional_category_for_age_covers_all_ranges()
    {
        $testCases = [
            [8, PlayerCategory::Mini],
            [9, PlayerCategory::Mini],
            [10, PlayerCategory::Mini],
            [11, PlayerCategory::Pre_Mini],
            [12, PlayerCategory::Pre_Mini],
            [13, PlayerCategory::Infantil],
            [14, PlayerCategory::Infantil],
            [15, PlayerCategory::Cadete],
            [16, PlayerCategory::Cadete],
            [17, PlayerCategory::Juvenil],
            [18, PlayerCategory::Juvenil],
            [19, PlayerCategory::Mayores],
            [25, PlayerCategory::Mayores],
            [34, PlayerCategory::Mayores],
            [35, PlayerCategory::Masters],
            [50, PlayerCategory::Masters],
        ];

        foreach ($testCases as [$age, $expectedCategory]) {
            $actualCategory = PlayerCategory::getTraditionalCategoryForAge($age);
            $this->assertEquals($expectedCategory, $actualCategory, "Failed for age {$age}");
        }
    }
}
