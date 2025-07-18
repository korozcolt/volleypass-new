<?php

namespace Database\Factories;

use App\Models\Club;
use App\Models\League;
use App\Models\City;
use App\Models\User;
use App\Enums\UserStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClubFactory extends Factory
{
    protected $model = Club::class;

    public function definition(): array
    {
        $name = $this->faker->company() . ' Club';

        return [
            'league_id' => League::factory(),
            'name' => $name,
            'short_name' => strtoupper(substr($name, 0, 3)),
            'description' => $this->faker->paragraph(),
            'city_id' => City::factory(),
            'address' => $this->faker->address(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'website' => $this->faker->optional()->url(),
            'foundation_date' => $this->faker->dateTimeBetween('-20 years', '-1 year'),
            'colors' => $this->faker->colorName() . ' y ' . $this->faker->colorName(),
            'history' => $this->faker->optional()->paragraph(),
            'status' => UserStatus::Active,
            'is_active' => true,
            'configurations' => [
                'max_players' => $this->faker->numberBetween(15, 25),
                'allow_transfers' => true,
            ],
        ];
    }

    public function withDirector(): static
    {
        return $this->state(function (array $attributes) {
            $director = User::factory()->create([
                'league_id' => $attributes['league_id'] ?? League::factory(),
            ]);

            return [
                'director_id' => $director->id,
            ];
        });
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
            'status' => UserStatus::Inactive,
        ]);
    }

    public function withoutDirector(): static
    {
        return $this->state(fn (array $attributes) => [
            'director_id' => null,
        ]);
    }
}
