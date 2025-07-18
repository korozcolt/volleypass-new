<?php

namespace Database\Factories;

use App\Models\Department;
use App\Models\Country;
use Illuminate\Database\Eloquent\Factories\Factory;

class DepartmentFactory extends Factory
{
    protected $model = Department::class;

    public function definition(): array
    {
        return [
            'country_id' => Country::factory(),
            'name' => $this->faker->state(),
            'code' => $this->faker->unique()->stateAbbr(),
            'is_active' => true,
        ];
    }

    public function bogota(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Bogotá D.C.',
            'code' => 'DC',
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
