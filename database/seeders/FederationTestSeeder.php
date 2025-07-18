<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\League;
use App\Models\Club;
use App\Models\Player;
use App\Models\Payment;
use App\Models\City;
use App\Enums\FederationStatus;
use App\Enums\PaymentStatus;
use App\Enums\PaymentType;
use App\Enums\PlayerPosition;
use App\Enums\PlayerCategory;
use App\Enums\MedicalStatus;
use App\Enums\UserStatus;
use App\Enums\Gender;
use Database\Factories\PlayerFactory;
use Database\Factories\ClubFactory;
use Database\Factories\PaymentFactory;

class FederationTestSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🏐 Seeding Federation Test Data...');

        // Crear liga de prueba
        $league = $this->createTestLeague();

        // Crear clubes de prueba
        $clubs = $this->createTestClubs($league);

        // Crear jugadoras de prueba
        $players = $this->createTestPlayers($clubs);

        // Crear pagos de prueba
        $this->createTestPayments($clubs, $league);

        // Simular diferentes estados de federación
        $this->simulateFederationStates($players);

        $this->command->info('✅ Federation test data seeded successfully!');
    }

    private function createTestLeague(): League
    {
        // Crear país si no existe
        $country = \App\Models\Country::firstOrCreate([
            'code' => 'CO'
        ], [
            'name' => 'Colombia',
            'phone_code' => '+57',
            'currency_code' => 'COP',
            'is_active' => true,
        ]);

        // Crear departamento si no existe
        $department = \App\Models\Department::firstOrCreate([
            'country_id' => $country->id,
            'code' => 'DC'
        ], [
            'name' => 'Bogotá D.C.',
            'is_active' => true,
        ]);

        // Crear ciudad si no existe
        $city = City::firstOrCreate([
            'department_id' => $department->id,
            'code' => 'BOG'
        ], [
            'name' => 'Bogotá',
            'postal_code' => '110111',
            'is_active' => true,
        ]);

        return League::create([
            'name' => 'Liga de Voleibol de Prueba',
            'short_name' => 'LVP',
            'description' => 'Liga creada para pruebas del sistema de federación',
            'city_id' => $city->id,
            'department_id' => $department->id,
            'country_id' => $country->id,
            'status' => UserStatus::Active,
            'is_active' => true,
            'email' => 'liga@test.com',
            'phone' => '3001234567',
            'configurations' => [
                'federation_fee' => 50000,
                'registration_fee' => 25000,
                'tournament_fee' => 15000,
            ],
        ]);
    }

    private function createTestClubs(League $league): array
    {
        $clubs = [];
        $clubNames = [
            'Club Águilas Doradas',
            'Club Tigres FC',
            'Club Leones Unidos',
            'Club Panteras Negras',
        ];

        foreach ($clubNames as $name) {
            // Crear director del club
            $director = User::create([
                'name' => 'Director ' . $name,
                'first_name' => 'Director',
                'last_name' => $name,
                'email' => strtolower(str_replace(' ', '', $name)) . '@test.com',
                'document_number' => '1000' . rand(100000, 999999),
                'phone' => '300' . rand(1000000, 9999999),
                'status' => UserStatus::Active,
                'league_id' => $league->id,
                'password' => bcrypt('password'),
            ]);

            $club = Club::create([
                'league_id' => $league->id,
                'name' => $name,
                'short_name' => substr($name, 5, 3),
                'description' => 'Club de prueba para el sistema de federación',
                'city_id' => $league->city_id,
                'email' => strtolower(str_replace(' ', '', $name)) . '@club.com',
                'phone' => '301' . rand(1000000, 9999999),
                'director_id' => $director->id,
                'status' => UserStatus::Active,
                'is_active' => true,
            ]);

            $director->update(['club_id' => $club->id]);
            $clubs[] = $club;
        }

        return $clubs;
    }

    private function createTestPlayers(array $clubs): array
    {
        $players = [];
        $positions = PlayerPosition::cases();
        $categories = PlayerCategory::cases();

        foreach ($clubs as $club) {
            // Crear 8-12 jugadoras por club
            $playerCount = rand(8, 12);

            for ($i = 1; $i <= $playerCount; $i++) {
                $user = User::create([
                    'name' => "Jugadora {$i} {$club->short_name}",
                    'first_name' => "Jugadora {$i}",
                    'last_name' => $club->short_name,
                    'email' => "jugadora{$i}.{$club->short_name}@test.com",
                    'document_number' => $club->id . str_pad($i, 3, '0', STR_PAD_LEFT) . rand(1000, 9999),
                    'phone' => '302' . rand(1000000, 9999999),
                    'birth_date' => now()->subYears(rand(16, 35))->subDays(rand(1, 365)),
                    'gender' => Gender::Female,
                    'status' => UserStatus::Active,
                    'league_id' => $club->league_id,
                    'club_id' => $club->id,
                    'password' => bcrypt('password'),
                ]);

                $player = Player::create([
                    'user_id' => $user->id,
                    'current_club_id' => $club->id,
                    'jersey_number' => $i,
                    'position' => $positions[array_rand($positions)],
                    'category' => $categories[array_rand($categories)],
                    'height' => rand(160, 190) / 100,
                    'weight' => rand(55, 80),
                    'dominant_hand' => ['right', 'left'][array_rand(['right', 'left'])],
                    'status' => UserStatus::Active,
                    'medical_status' => MedicalStatus::Fit,
                    'debut_date' => now()->subMonths(rand(1, 24)),
                    'is_eligible' => true,
                    'eligibility_checked_at' => now()->subDays(rand(1, 30)),
                    'federation_status' => FederationStatus::NotFederated,
                ]);

                $players[] = $player;
            }
        }

        return $players;
    }

    private function createTestPayments(array $clubs, League $league): void
    {
        foreach ($clubs as $club) {
            // Crear 2-4 pagos por club
            $paymentCount = rand(2, 4);

            for ($i = 1; $i <= $paymentCount; $i++) {
                Payment::create([
                    'club_id' => $club->id,
                    'league_id' => $league->id,
                    'user_id' => $club->director_id,
                    'type' => PaymentType::Federation,
                    'amount' => 50000,
                    'currency' => 'COP',
                    'reference_number' => 'REF-' . $club->id . '-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                    'payment_method' => ['transfer', 'deposit', 'online'][array_rand(['transfer', 'deposit', 'online'])],
                    'status' => [PaymentStatus::Pending, PaymentStatus::Verified][array_rand([PaymentStatus::Pending, PaymentStatus::Verified])],
                    'paid_at' => now()->subDays(rand(1, 15)),
                    'notes' => "Pago de federación #{$i} para {$club->name}",
                ]);
            }
        }
    }

    private function simulateFederationStates(array $players): void
    {
        $totalPlayers = count($players);

        // 40% federadas
        $federatedCount = (int)($totalPlayers * 0.4);
        for ($i = 0; $i < $federatedCount; $i++) {
            $player = $players[$i];
            $payment = Payment::where('club_id', $player->current_club_id)
                ->where('status', PaymentStatus::Verified)
                ->first();

            if ($payment) {
                $player->update([
                    'federation_status' => FederationStatus::Federated,
                    'federation_date' => now()->subDays(rand(30, 300)),
                    'federation_expires_at' => now()->addDays(rand(30, 365)),
                    'federation_payment_id' => $payment->id,
                    'federation_notes' => "Federada automáticamente en seeder de prueba",
                ]);
            }
        }

        // 20% con pago pendiente
        $pendingCount = (int)($totalPlayers * 0.2);
        for ($i = $federatedCount; $i < $federatedCount + $pendingCount; $i++) {
            if (isset($players[$i])) {
                $players[$i]->update([
                    'federation_status' => FederationStatus::PendingPayment,
                ]);
            }
        }

        // 10% con pago enviado
        $submittedCount = (int)($totalPlayers * 0.1);
        for ($i = $federatedCount + $pendingCount; $i < $federatedCount + $pendingCount + $submittedCount; $i++) {
            if (isset($players[$i])) {
                $players[$i]->update([
                    'federation_status' => FederationStatus::PaymentSubmitted,
                ]);
            }
        }

        // 5% vencidas
        $expiredCount = (int)($totalPlayers * 0.05);
        for ($i = $federatedCount + $pendingCount + $submittedCount; $i < $federatedCount + $pendingCount + $submittedCount + $expiredCount; $i++) {
            if (isset($players[$i])) {
                $players[$i]->update([
                    'federation_status' => FederationStatus::Expired,
                    'federation_date' => now()->subYear(),
                    'federation_expires_at' => now()->subDays(rand(1, 30)),
                ]);
            }
        }

        // El resto permanece como no federadas
    }
}
