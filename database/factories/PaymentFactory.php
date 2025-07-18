<?php

namespace Database\Factories;

use App\Models\Payment;
use App\Models\Club;
use App\Models\League;
use App\Models\User;
use App\Enums\PaymentStatus;
use App\Enums\PaymentType;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        return [
            'club_id' => Club::factory(),
            'league_id' => League::factory(),
            'user_id' => User::factory(),
            'type' => $this->faker->randomElement(PaymentType::cases()),
            'amount' => $this->faker->numberBetween(10000, 100000),
            'currency' => 'COP',
            'reference_number' => 'REF-' . $this->faker->unique()->numerify('######'),
            'payment_method' => $this->faker->randomElement(['transfer', 'deposit', 'online', 'cash']),
            'status' => PaymentStatus::Pending,
            'paid_at' => $this->faker->dateTimeBetween('-30 days', 'now'),
            'notes' => $this->faker->optional()->sentence(),
            'metadata' => [
                'bank' => $this->faker->optional()->company(),
                'account' => $this->faker->optional()->bankAccountNumber(),
            ],
        ];
    }

    public function verified(): static
    {
        return $this->state(function (array $attributes) {
            $verifier = User::factory()->create();

            return [
                'status' => PaymentStatus::Verified,
                'verified_at' => $this->faker->dateTimeBetween($attributes['paid_at'] ?? '-30 days', 'now'),
                'verified_by' => $verifier->id,
            ];
        });
    }

    public function rejected(): static
    {
        return $this->state(function (array $attributes) {
            $verifier = User::factory()->create();

            return [
                'status' => PaymentStatus::Rejected,
                'verified_at' => $this->faker->dateTimeBetween($attributes['paid_at'] ?? '-30 days', 'now'),
                'verified_by' => $verifier->id,
                'notes' => 'Rechazado: ' . $this->faker->sentence(),
            ];
        });
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PaymentStatus::Pending,
            'verified_at' => null,
            'verified_by' => null,
        ]);
    }

    public function federation(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => PaymentType::Federation,
            'amount' => $this->faker->numberBetween(40000, 60000),
        ]);
    }

    public function registration(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => PaymentType::Registration,
            'amount' => $this->faker->numberBetween(20000, 40000),
        ]);
    }

    public function tournament(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => PaymentType::Tournament,
            'amount' => $this->faker->numberBetween(10000, 25000),
        ]);
    }

    public function withAmount(int $amount): static
    {
        return $this->state(fn (array $attributes) => [
            'amount' => $amount,
        ]);
    }

    public function withReference(string $reference): static
    {
        return $this->state(fn (array $attributes) => [
            'reference_number' => $reference,
        ]);
    }
}
