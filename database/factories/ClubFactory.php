<?php

namespace Database\Factories;

use App\Models\Club;
use App\Models\League;
use App\Models\User;
use App\Models\City;
use App\Models\Department;
use App\Enums\UserStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClubFactory extends Factory
{
    protected $model = Club::class;

    public function definition(): array
    {
        $name = $this->faker->company() . ' Club';
        $shortName = strtoupper(substr($name, 0, 3));

        return [
            'league_id' => League::factory(),
            'name' => $name,
            'nombre' => $name,
            'short_name' => $shortName,
            'nombre_corto' => $shortName,
            'description' => $this->faker->paragraph(),
            'city_id' => City::first()?->id ?? 1,
            'departamento_id' => Department::first()?->id ?? 1,
            'address' => $this->faker->address(),
            'direccion' => $this->faker->address(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'telefono' => $this->faker->phoneNumber(),
            'website' => $this->faker->optional()->url(),
            'foundation_date' => $this->faker->dateTimeBetween('-20 years', '-1 year'),
            'fundacion' => $this->faker->dateTimeBetween('-20 years', '-1 year'),
            'colors' => $this->faker->colorName() . ' y ' . $this->faker->colorName(),
            'history' => $this->faker->optional()->paragraph(),
            'status' => UserStatus::Active,
            'is_active' => true,
            'es_federado' => $this->faker->boolean(30), // 30% de probabilidad
            'tipo_federacion' => null,
            'codigo_federacion' => null,
            'vencimiento_federacion' => null,
            'observaciones_federacion' => null,
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

    public function federado(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'es_federado' => true,
                'tipo_federacion' => $this->faker->randomElement(['departamental', 'nacional']),
                'codigo_federacion' => $this->generateFederationCode(),
                'vencimiento_federacion' => $this->faker->dateTimeBetween('now', '+2 years'),
            ];
        });
    }

    public function noFederado(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'es_federado' => false,
                'tipo_federacion' => null,
                'codigo_federacion' => null,
                'vencimiento_federacion' => null,
            ];
        });
    }

    public function federacionExpirada(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'es_federado' => true,
                'tipo_federacion' => $this->faker->randomElement(['departamental', 'nacional']),
                'codigo_federacion' => $this->generateFederationCode(),
                'vencimiento_federacion' => $this->faker->dateTimeBetween('-1 year', '-1 day'),
            ];
        });
    }

    private function generateFederationCode(): string
    {
        do {
            $code = 'FED-' . strtoupper($this->faker->bothify('??###'));
        } while (Club::where('codigo_federacion', $code)->exists());
        
        return $code;
    }
}
