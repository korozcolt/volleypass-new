<?php

namespace Database\Factories;

use App\Models\Country;
use Illuminate\Database\Eloquent\Factories\Factory;

class CountryFactory extends Factory
{
    protected $model = Country::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->country(),
            'code' => $this->faker->unique()->countryCode(),
            'phone_code' => '+' . $this->faker->numberBetween(1, 999),
            'currency_code' => $this->faker->currencyCode(),
            'is_active' => true,
        ];
    }

    public function colombia(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Colombia',
            'code' => 'CO',
            'phone_code' => '+57',
            'currency_code' => 'COP',
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
