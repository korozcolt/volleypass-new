<?php

namespace Database\Factories;

use App\Models\League;
use App\Models\Country;
use App\Models\Department;
use App\Models\City;
use App\Enums\UserStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

class LeagueFactory extends Factory
{
    protected $model = League::class;

    public function definition(): array
    {
        $name = 'Liga de ' . $this->faker->city();

        return [
            'name' => $name,
            'short_name' => strtoupper(substr($name, 0, 3)),
            'description' => $this->faker->paragraph(),
            'country_id' => Country::factory(),
            'department_id' => Department::factory(),
            'city_id' => City::factory(),
            'status' => UserStatus::Active,
            'foundation_date' => $this->faker->dateTimeBetween('-10 years', '-1 year'),
            'website' => $this->faker->optional()->url(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'address' => $this->faker->address(),
            'configurations' => [
                'federation_fee' => $this->faker->numberBetween(30000, 100000),
                'registration_fee' => $this->faker->numberBetween(15000, 50000),
                'tournament_fee' => $this->faker->numberBetween(10000, 30000),
                'max_clubs' => $this->faker->numberBetween(8, 20),
                'season_start' => now()->startOfYear()->format('Y-m-d'),
                'season_end' => now()->endOfYear()->format('Y-m-d'),
            ],
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
            'status' => UserStatus::Inactive,
        ]);
    }

    public function withCustomFees(int $federationFee, int $registrationFee = null, int $tournamentFee = null): static
    {
        return $this->state(function (array $attributes) use ($federationFee, $registrationFee, $tournamentFee) {
            $configurations = $attributes['configurations'] ?? [];
            $configurations['federation_fee'] = $federationFee;

            if ($registrationFee !== null) {
                $configurations['registration_fee'] = $registrationFee;
            }

            if ($tournamentFee !== null) {
                $configurations['tournament_fee'] = $tournamentFee;
            }

            return ['configurations' => $configurations];
        });
    }
}
