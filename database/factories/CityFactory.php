<?php

namespace Database\Factories;

use App\Models\City;
use App\Models\Department;
use Illuminate\Database\Eloquent\Factories\Factory;

class CityFactory extends Factory
{
    protected $model = City::class;

    public function definition(): array
    {
        return [
            'department_id' => Department::factory(),
            'name' => $this->faker->city(),
            'code' => $this->faker->unique()->lexify('???'),
            'postal_code' => $this->faker->postcode(),
            'is_active' => true,
        ];
    }

    public function bogota(): static
    {
        return $this->state(fn(array $attributes) => [
            'name' => 'Bogotá',
            'code' => 'BOG',
            'postal_code' => '110111',
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_active' => false,
        ]);
    }
}
