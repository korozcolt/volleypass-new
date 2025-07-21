<?php

namespace Database\Factories;

use App\Models\LeagueCategory;
use App\Models\League;
use App\Enums\Gender;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LeagueCategory>
 */
class LeagueCategoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = LeagueCategory::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $categories = [
            ['key' => 'Mini', 'name' => 'Mini', 'min_age' => 8, 'max_age' => 10],
            ['key' => 'Pre_Mini', 'name' => 'Pre Mini', 'min_age' => 11, 'max_age' => 12],
            ['key' => 'Infantil', 'name' => 'Infantil', 'min_age' => 13, 'max_age' => 14],
            ['key' => 'Cadete', 'name' => 'Cadete', 'min_age' => 15, 'max_age' => 16],
            ['key' => 'Juvenil', 'name' => 'Juvenil', 'min_age' => 17, 'max_age' => 18],
            ['key' => 'Mayores', 'name' => 'Mayores', 'min_age' => 19, 'max_age' => 34],
            ['key' => 'Masters', 'name' => 'Masters', 'min_age' => 35, 'max_age' => 60],
        ];

        $category = $this->faker->randomElement($categories);

        return [
            'league_id' => League::factory(),
            'code' => $category['key'],
            'name' => $category['name'],
            'min_age' => $category['min_age'],
            'max_age' => $category['max_age'],
            'gender' => $this->faker->randomElement(Gender::cases()),
            'is_active' => true,
            'sort_order' => $this->faker->numberBetween(1, 10),
            'description' => $this->faker->optional()->sentence(),
            'special_rules' => null,
        ];
    }

    /**
     * Indicate that the category is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Indicate that the category has special rules.
     */
    public function withSpecialRules(array $rules = null): static
    {
        $defaultRules = [
            ['type' => 'requires_medical_clearance', 'value' => true],
            ['type' => 'minimum_experience_months', 'value' => 6],
        ];

        return $this->state(fn (array $attributes) => [
            'special_rules' => $rules ?? $defaultRules,
        ]);
    }

    /**
     * Create a specific category by name.
     */
    public function category(string $categoryKey, int $minAge, int $maxAge): static
    {
        return $this->state(fn (array $attributes) => [
            'code' => $categoryKey,
            'name' => $categoryKey,
            'min_age' => $minAge,
            'max_age' => $maxAge,
        ]);
    }

    /**
     * Create Mini category.
     */
    public function mini(): static
    {
        return $this->category('Mini', 8, 10);
    }

    /**
     * Create Pre Mini category.
     */
    public function preMini(): static
    {
        return $this->category('Pre_Mini', 11, 12);
    }

    /**
     * Create Infantil category.
     */
    public function infantil(): static
    {
        return $this->category('Infantil', 13, 14);
    }

    /**
     * Create Cadete category.
     */
    public function cadete(): static
    {
        return $this->category('Cadete', 15, 16);
    }

    /**
     * Create Juvenil category.
     */
    public function juvenil(): static
    {
        return $this->category('Juvenil', 17, 18);
    }

    /**
     * Create Mayores category.
     */
    public function mayores(): static
    {
        return $this->category('Mayores', 19, 34);
    }

    /**
     * Create Masters category.
     */
    public function masters(): static
    {
        return $this->category('Masters', 35, 60);
    }

    /**
     * Create category for specific gender.
     */
    public function forGender(Gender $gender): static
    {
        return $this->state(fn (array $attributes) => [
            'gender' => $gender,
        ]);
    }

    /**
     * Create mixed gender category.
     */
    public function mixed(): static
    {
        return $this->forGender(Gender::Mixed);
    }

    /**
     * Create female category.
     */
    public function female(): static
    {
        return $this->forGender(Gender::Female);
    }

    /**
     * Create male category.
     */
    public function male(): static
    {
        return $this->forGender(Gender::Male);
    }
}
