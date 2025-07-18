<?php

namespace Database\Factories;

use App\Models\Player;
use App\Models\User;
use App\Models\Club;
use App\Enums\PlayerPosition;
use App\Enums\PlayerCategory;
use App\Enums\MedicalStatus;
use App\Enums\UserStatus;
use App\Enums\FederationStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

class PlayerFactory extends Factory
{
    protected $model = Player::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'current_club_id' => Club::factory(),
            'jersey_number' => $this->faker->numberBetween(1, 99),
            'position' => $this->faker->randomElement(PlayerPosition::cases()),
            'category' => $this->faker->randomElement(PlayerCategory::cases()),
            'height' => $this->faker->randomFloat(2, 1.50, 1.90),
            'weight' => $this->faker->randomFloat(1, 50, 90),
            'dominant_hand' => $this->faker->randomElement(['right', 'left', 'both']),
            'status' => UserStatus::Active,
            'medical_status' => MedicalStatus::Fit,
            'debut_date' => $this->faker->dateTimeBetween('-5 years', 'now'),
            'is_eligible' => true,
            'eligibility_checked_at' => now(),
            'federation_status' => FederationStatus::NotFederated,
        ];
    }

    public function federated(): static
    {
        return $this->state(fn (array $attributes) => [
            'federation_status' => FederationStatus::Federated,
            'federation_date' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'federation_expires_at' => $this->faker->dateTimeBetween('now', '+1 year'),
        ]);
    }

    public function notFederated(): static
    {
        return $this->state(fn (array $attributes) => [
            'federation_status' => FederationStatus::NotFederated,
        ]);
    }

    public function pendingPayment(): static
    {
        return $this->state(fn (array $attributes) => [
            'federation_status' => FederationStatus::PendingPayment,
        ]);
    }

    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'federation_status' => FederationStatus::Expired,
            'federation_date' => $this->faker->dateTimeBetween('-2 years', '-1 year'),
            'federation_expires_at' => $this->faker->dateTimeBetween('-6 months', '-1 day'),
        ]);
    }

    public function suspended(): static
    {
        return $this->state(fn (array $attributes) => [
            'federation_status' => FederationStatus::Suspended,
            'federation_date' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'federation_expires_at' => $this->faker->dateTimeBetween('now', '+1 year'),
            'federation_notes' => 'Suspendida por: ' . $this->faker->sentence(),
        ]);
    }

    public function ineligible(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_eligible' => false,
            'medical_status' => MedicalStatus::Unfit,
        ]);
    }

    public function withJerseyNumber(int $number): static
    {
        return $this->state(fn (array $attributes) => [
            'jersey_number' => $number,
        ]);
    }

    public function withPosition(PlayerPosition $position): static
    {
        return $this->state(fn (array $attributes) => [
            'position' => $position,
        ]);
    }

    public function withCategory(PlayerCategory $category): static
    {
        return $this->state(fn (array $attributes) => [
            'category' => $category,
        ]);
    }
}
