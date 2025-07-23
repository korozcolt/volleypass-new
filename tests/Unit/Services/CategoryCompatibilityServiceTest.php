<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\CategoryCompatibilityService;
use App\Enums\PlayerCategory;
use App\Models\League;
use App\Models\LeagueCategory;
use App\Models\Country;
use App\Models\Department;
use App\Models\City;
use App\Models\Club;
use App\Models\Player;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CategoryCompatibilityServiceTest extends TestCase
{
    use RefreshDatabase;

    protected CategoryCompatibilityService $service;
    protected League $league;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new CategoryCompatibilityService();

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

    public function test_get_category_for_age_returns_traditional_when_no_league()
    {
        $category = $this->service->getCategoryForAge(9, 'female');

        $this->assertEquals(PlayerCategory::Mini, $category);
    }

    public function test_get_category_for_age_returns_traditional_when_league_has_no_custom_categories()
    {
        $category = $this->service->getCategoryForAge(9, 'female', $this->league);

        $this->assertEquals(PlayerCategory::Mini, $category);
    }

    public function test_get_category_for_age_returns_dynamic_when_league_has_custom_categories()
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

        $category = $this->service->getCategoryForAge(8, 'female', $this->league);

        $this->assertEquals(PlayerCategory::Mini, $category);
    }

    public function test_is_age_eligible_for_category_with_traditional_system()
    {
        $isEligible = $this->service->isAgeEligibleForCategory(9, PlayerCategory::Mini);

        $this->assertTrue($isEligible);
    }

    public function test_is_age_eligible_for_category_with_dynamic_system()
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

        $isEligible = $this->service->isAgeEligibleForCategory(7, PlayerCategory::Mini, $this->league);

        $this->assertTrue($isEligible);
    }

    public function test_get_age_range_for_category_returns_traditional_range()
    {
        $range = $this->service->getAgeRangeForCategory(PlayerCategory::Mini);

        $this->assertEquals([8, 10], $range);
    }

    public function test_get_age_range_for_category_returns_dynamic_range()
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

        $range = $this->service->getAgeRangeForCategory(PlayerCategory::Mini, $this->league);

        $this->assertEquals([6, 9], $range);
    }

    public function test_get_age_range_text_for_category()
    {
        $text = $this->service->getAgeRangeTextForCategory(PlayerCategory::Mini);

        $this->assertEquals('8-10 años', $text);
    }

    public function test_get_available_categories_returns_traditional_categories()
    {
        $categories = $this->service->getAvailableCategories();

        $this->assertCount(7, $categories);
        $this->assertFalse($categories[0]['is_dynamic']);
    }

    public function test_get_available_categories_returns_dynamic_categories()
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

        $categories = $this->service->getAvailableCategories($this->league);

        $this->assertCount(1, $categories);
        $this->assertTrue($categories[0]['is_dynamic']);
    }

    public function test_get_category_options_returns_formatted_options()
    {
        $options = $this->service->getCategoryOptions();

        $this->assertIsArray($options);
        $this->assertArrayHasKey('mini', $options);
        $this->assertEquals('Mini (8-10 años)', $options['mini']);
    }

    public function test_is_category_valid_for_league_with_traditional_system()
    {
        $isValid = $this->service->isCategoryValidForLeague('mini');

        $this->assertTrue($isValid);
    }

    public function test_is_category_valid_for_league_with_dynamic_system()
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

        $isValid = $this->service->isCategoryValidForLeague('mini', $this->league);

        $this->assertTrue($isValid);
    }

    public function test_get_category_enum_returns_correct_enum()
    {
        $enum = $this->service->getCategoryEnum('mini');

        $this->assertEquals(PlayerCategory::Mini, $enum);
    }

    public function test_get_category_enum_returns_null_for_invalid_value()
    {
        $enum = $this->service->getCategoryEnum('invalid');

        $this->assertNull($enum);
    }

    public function test_is_dynamic_system_active_returns_false_when_no_custom_categories()
    {
        $isActive = $this->service->isDynamicSystemActive($this->league);

        $this->assertFalse($isActive);
    }

    public function test_is_dynamic_system_active_returns_true_when_has_custom_categories()
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

        $isActive = $this->service->isDynamicSystemActive($this->league);

        $this->assertTrue($isActive);
    }

    public function test_get_compatibility_info_returns_correct_information()
    {
        $info = $this->service->getCompatibilityInfo($this->league);

        $this->assertArrayHasKey('league_id', $info);
        $this->assertArrayHasKey('has_custom_categories', $info);
        $this->assertArrayHasKey('system_mode', $info);
        $this->assertEquals($this->league->id, $info['league_id']);
        $this->assertFalse($info['has_custom_categories']);
        $this->assertEquals('traditional', $info['system_mode']);
    }

    public function test_get_compatibility_info_with_dynamic_system()
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

        $info = $this->service->getCompatibilityInfo($this->league);

        $this->assertTrue($info['has_custom_categories']);
        $this->assertEquals('dynamic', $info['system_mode']);
    }

    public function test_get_category_for_player_with_traditional_system()
    {
        // Crear club y jugador
        $club = Club::factory()->create(['league_id' => $this->league->id]);
        $user = User::factory()->create(['birth_date' => now()->subYears(9)]);
        $player = Player::factory()->create([
            'user_id' => $user->id,
            'current_club_id' => $club->id,
        ]);

        $category = $this->service->getCategoryForPlayer($player);

        $this->assertEquals(PlayerCategory::Mini, $category);
    }

    public function test_migrate_players_categories_returns_correct_structure()
    {
        $results = $this->service->migratePlayersCategories($this->league);

        $this->assertArrayHasKey('migrated', $results);
        $this->assertArrayHasKey('errors', $results);
        $this->assertArrayHasKey('unchanged', $results);
        $this->assertArrayHasKey('details', $results);
    }
}
