<?php

namespace Tests\Unit\Enums;

use Tests\TestCase;
use App\Enums\PlayerCategory;
use App\Models\League;
use App\Models\LeagueCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PlayerCategoryDynamicSimpleTest extends TestCase
{
    use RefreshDatabase;

    public function test_returns_traditional_age_range_when_no_league_provided()
    {
        $category = PlayerCategory::Mini;

        $ageRange = $category->getAgeRange();

        $this->assertEquals([8, 10], $ageRange);
    }

    public function test_get_default_age_ranges_for_all_categories()
    {
        $testCases = [
            [PlayerCategory::Mini, [8, 10]],
            [PlayerCategory::Pre_Mini, [11, 12]],
            [PlayerCategory::Infantil, [13, 14]],
            [PlayerCategory::Cadete, [15, 16]],
            [PlayerCategory::Juvenil, [17, 18]],
            [PlayerCategory::Mayores, [19, 34]],
            [PlayerCategory::Masters, [35, 100]],
        ];

        foreach ($testCases as [$category, $expectedRange]) {
            $actualRange = $category->getDefaultAgeRange();
            $this->assertEquals($expectedRange, $actualRange, "Failed for category {$category->value}");
        }
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

    public function test_is_age_eligible_with_traditional_ranges()
    {
        $category = PlayerCategory::Mini;

        $this->assertTrue($category->isAgeEligible(8));
        $this->assertTrue($category->isAgeEligible(9));
        $this->assertTrue($category->isAgeEligible(10));
        $this->assertFalse($category->isAgeEligible(7));
        $this->assertFalse($category->isAgeEligible(11));
    }

    public function test_get_age_range_text_with_traditional_ranges()
    {
        $category = PlayerCategory::Mini;

        $text = $category->getAgeRangeText();

        $this->assertEquals('8-10 años', $text);
    }

    public function test_get_for_age_returns_correct_traditional_category()
    {
        $category = PlayerCategory::getForAge(9, 'female');

        $this->assertEquals(PlayerCategory::Mini, $category);
    }

    public function test_get_for_age_returns_null_for_invalid_age()
    {
        // Test edge cases
        $category = PlayerCategory::getForAge(5, 'female'); // Too young
        $this->assertEquals(PlayerCategory::Mayores, $category); // Should fallback to Mayores

        $category = PlayerCategory::getForAge(7, 'female'); // Still too young for Mini
        $this->assertEquals(PlayerCategory::Mayores, $category); // Should fallback to Mayores
    }

    public function test_get_available_categories_returns_traditional_when_no_league()
    {
        $categories = PlayerCategory::getAvailableCategories();

        $this->assertCount(7, $categories); // 7 categorías tradicionales
        $this->assertEquals('mini', $categories[0]['value']);
        $this->assertEquals('Mini (8-10 años)', $categories[0]['label']);
        $this->assertFalse($categories[0]['is_dynamic']);
        $this->assertEquals([8, 10], $categories[0]['age_range']);
        $this->assertEquals('mixed', $categories[0]['gender']);
    }

    public function test_has_custom_configuration_returns_false_when_no_league()
    {
        $category = PlayerCategory::Mini;

        $hasCustom = $category->hasCustomConfiguration();

        $this->assertFalse($hasCustom);
    }

    public function test_get_dynamic_label_returns_traditional_when_no_league()
    {
        $category = PlayerCategory::Mini;

        $label = $category->getDynamicLabel();

        $this->assertEquals('Mini (8-10 años)', $label);
    }

    public function test_enum_values_are_correct()
    {
        $expectedValues = [
            'mini', 'pre_mini', 'infantil', 'cadete', 'juvenil', 'mayores', 'masters'
        ];

        $actualValues = array_map(fn($case) => $case->value, PlayerCategory::cases());

        $this->assertEquals($expectedValues, $actualValues);
    }

    public function test_enum_labels_are_correct()
    {
        $testCases = [
            [PlayerCategory::Mini, 'Mini (8-10 años)'],
            [PlayerCategory::Pre_Mini, 'Pre-Mini (11-12 años)'],
            [PlayerCategory::Infantil, 'Infantil (13-14 años)'],
            [PlayerCategory::Cadete, 'Cadete (15-16 años)'],
            [PlayerCategory::Juvenil, 'Juvenil (17-18 años)'],
            [PlayerCategory::Mayores, 'Mayores (19+ años)'],
            [PlayerCategory::Masters, 'Masters (35+ años)'],
        ];

        foreach ($testCases as [$category, $expectedLabel]) {
            $actualLabel = $category->getLabel();
            $this->assertEquals($expectedLabel, $actualLabel, "Failed for category {$category->value}");
        }
    }
}
