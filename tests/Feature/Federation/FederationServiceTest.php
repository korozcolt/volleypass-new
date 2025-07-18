<?php

namespace Tests\Feature\Federation;

use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use App\Services\FederationService;
use App\Models\Player;
use App\Models\Club;
use App\Models\League;
use App\Models\Payment;
use App\Models\User;
use App\Models\Country;
use App\Models\Department;
use App\Models\City;
use App\Enums\FederationStatus;
use App\Enums\PaymentStatus;
use App\Enums\PaymentType;
use App\Enums\UserStatus;
use App\Enums\MedicalStatus;
use App\Enums\PlayerPosition;
use App\Enums\PlayerCategory;
use App\Enums\Gender;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FederationServiceTest extends TestCase
{
    use RefreshDatabase;

    private FederationService $federationService;
    private League $league;
    private Club $club;
    private Player $player;
    private Payment $payment;

    protected function setUp(): void
    {
        parent::setUp();

        $this->federationService = app(FederationService::class);
        $this->createTestData();
    }

    private function createTestData(): void
    {
        // Crear estructura geográfica
        $country = Country::create([
            'name' => 'Colombia',
            'code' => 'CO',
            'phone_code' => '+57',
            'currency_code' => 'COP',
            'is_active' => true,
        ]);

        $department = Department::create([
            'country_id' => $country->id,
            'name' => 'Bogotá D.C.',
            'code' => 'DC',
            'is_active' => true,
        ]);

        $city = City::create([
            'department_id' => $department->id,
            'name' => 'Bogotá',
            'code' => 'BOG',
            'postal_code' => '110111',
            'is_active' => true,
        ]);

        // Crear liga
        $this->league = League::create([
            'name' => 'Liga Test',
            'short_name' => 'LT',
            'description' => 'Liga para testing',
            'city_id' => $city->id,
            'department_id' => $department->id,
            'country_id' => $country->id,
            'status' => UserStatus::Active,
            'is_active' => true,
            'email' => 'liga@test.com',
            'phone' => '3001234567',
            'configurations' => [
                'federation_fee' => 50000,
            ],
        ]);

        // Crear director
        $director = User::create([
            'name' => 'Director Test',
            'first_name' => 'Director',
            'last_name' => 'Test',
            'email' => 'director@test.com',
            'document_number' => '12345678',
            'phone' => '3001234567',
            'status' => UserStatus::Active,
            'league_id' => $this->league->id,
            'password' => bcrypt('password'),
        ]);

        // Crear club
        $this->club = Club::create([
            'league_id' => $this->league->id,
            'name' => 'Club Test',
            'short_name' => 'CT',
            'description' => 'Club para testing',
            'city_id' => $city->id,
            'email' => 'club@test.com',
            'phone' => '3001234567',
            'director_id' => $director->id,
            'status' => UserStatus::Active,
            'is_active' => true,
        ]);

        $director->update(['club_id' => $this->club->id]);

        // Crear usuario jugadora
        $user = User::create([
            'name' => 'Jugadora Test',
            'first_name' => 'Jugadora',
            'last_name' => 'Test',
            'email' => 'jugadora@test.com',
            'document_number' => '87654321',
            'phone' => '3009876543',
            'birth_date' => now()->subYears(20),
            'gender' => Gender::Female,
            'status' => UserStatus::Active,
            'league_id' => $this->league->id,
            'club_id' => $this->club->id,
            'password' => bcrypt('password'),
        ]);

        // Crear jugadora
        $this->player = Player::create([
            'user_id' => $user->id,
            'current_club_id' => $this->club->id,
            'jersey_number' => 10,
            'position' => PlayerPosition::Setter,
            'category' => PlayerCategory::Mayores,
            'height' => 1.70,
            'weight' => 65.0,
            'dominant_hand' => 'right',
            'status' => UserStatus::Active,
            'medical_status' => MedicalStatus::Fit,
            'is_eligible' => true,
            'federation_status' => FederationStatus::NotFederated,
        ]);

        // Crear pago
        $this->payment = Payment::create([
            'club_id' => $this->club->id,
            'league_id' => $this->league->id,
            'user_id' => $director->id,
            'type' => PaymentType::Federation,
            'amount' => 50000,
            'currency' => 'COP',
            'reference_number' => 'TEST-001',
            'payment_method' => 'transfer',
            'status' => PaymentStatus::Verified,
            'paid_at' => now(),
            'verified_at' => now(),
            'verified_by' => $director->id,
        ]);
    }

    #[Test]
    public function it_can_federate_a_player_with_verified_payment()
    {
        $result = $this->federationService->federatePlayer($this->player, $this->payment);

        $this->assertTrue($result);
        $this->player->refresh();

        $this->assertEquals(FederationStatus::Federated, $this->player->federation_status);
        $this->assertNotNull($this->player->federation_date);
        $this->assertNotNull($this->player->federation_expires_at);
        $this->assertEquals($this->payment->id, $this->player->federation_payment_id);
    }

    #[Test]
    public function it_cannot_federate_player_with_unverified_payment()
    {
        $this->payment->update(['status' => PaymentStatus::Pending]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('El pago debe estar verificado para federar la jugadora');

        $this->federationService->federatePlayer($this->player, $this->payment);
    }

    #[Test]
    public function it_cannot_federate_player_with_non_federation_payment()
    {
        $this->payment->update(['type' => PaymentType::Registration]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('El pago debe ser de tipo federación');

        $this->federationService->federatePlayer($this->player, $this->payment);
    }

    #[Test]
    public function it_can_suspend_player_federation()
    {
        // Primero federar la jugadora
        $this->federationService->federatePlayer($this->player, $this->payment);

        $suspender = User::create([
            'name' => 'Suspender Test',
            'email' => 'suspender@test.com',
            'document_number' => '88888888',
            'password' => bcrypt('password'),
            'status' => UserStatus::Active,
        ]);
        $reason = 'Violación de reglamento';

        $result = $this->federationService->suspendPlayerFederation($this->player, $reason, $suspender);

        $this->assertTrue($result);
        $this->player->refresh();

        $this->assertEquals(FederationStatus::Suspended, $this->player->federation_status);
        $this->assertStringContainsString($reason, $this->player->federation_notes);
    }

    #[Test]
    public function it_can_renew_player_federation()
    {
        // Federar inicialmente
        $this->federationService->federatePlayer($this->player, $this->payment);

        // Crear nuevo pago para renovación
        $renewalPayment = Payment::create([
            'club_id' => $this->club->id,
            'league_id' => $this->league->id,
            'user_id' => $this->club->director_id,
            'type' => PaymentType::Federation,
            'amount' => 50000,
            'currency' => 'COP',
            'reference_number' => 'RENEWAL-001',
            'payment_method' => 'transfer',
            'status' => PaymentStatus::Verified,
            'paid_at' => now(),
            'verified_at' => now(),
            'verified_by' => $this->club->director_id,
        ]);

        $originalExpiration = $this->player->federation_expires_at;

        $result = $this->federationService->renewPlayerFederation($this->player, $renewalPayment);

        $this->assertTrue($result);
        $this->player->refresh();

        $this->assertEquals(FederationStatus::Federated, $this->player->federation_status);
        $this->assertTrue($this->player->federation_expires_at->isAfter($originalExpiration));
        $this->assertEquals($renewalPayment->id, $this->player->federation_payment_id);
    }

    #[Test]
    public function it_can_get_general_federation_stats()
    {
        // Crear jugadoras con diferentes estados
        $federatedPlayer = Player::factory()->create([
            'current_club_id' => $this->club->id,
            'federation_status' => FederationStatus::Federated,
        ]);

        $notFederatedPlayer = Player::factory()->create([
            'current_club_id' => $this->club->id,
            'federation_status' => FederationStatus::NotFederated,
        ]);

        $stats = $this->federationService->getGeneralFederationStats();

        $this->assertIsArray($stats);
        $this->assertArrayHasKey('total_players', $stats);
        $this->assertArrayHasKey('federated', $stats);
        $this->assertArrayHasKey('not_federated', $stats);
        $this->assertArrayHasKey('federation_percentage', $stats);

        $this->assertEquals(3, $stats['total_players']); // player original + 2 creadas
        $this->assertEquals(1, $stats['federated']);
        $this->assertEquals(2, $stats['not_federated']);
    }

    #[Test]
    public function it_can_update_expired_federations()
    {
        // Crear jugadora con federación vencida
        $expiredPlayer = Player::factory()->create([
            'current_club_id' => $this->club->id,
            'federation_status' => FederationStatus::Federated,
            'federation_expires_at' => now()->subDays(10),
        ]);

        $result = $this->federationService->updateExpiredFederations();

        $this->assertIsArray($result);
        $this->assertEquals(1, $result['updated']);

        $expiredPlayer->refresh();
        $this->assertEquals(FederationStatus::Expired, $expiredPlayer->federation_status);
    }

    #[Test]
    public function it_can_get_players_expiring_federation()
    {
        // Crear jugadora con federación próxima a vencer
        $expiringPlayer = Player::factory()->create([
            'current_club_id' => $this->club->id,
            'federation_status' => FederationStatus::Federated,
            'federation_expires_at' => now()->addDays(15),
        ]);

        $expiringPlayers = $this->federationService->getPlayersExpiringFederation(30);

        $this->assertEquals(1, $expiringPlayers->count());
        $this->assertEquals($expiringPlayer->id, $expiringPlayers->first()->id);
    }

    #[Test]
    public function it_can_check_if_player_can_play_in_federated_tournaments()
    {
        // Jugadora no federada
        $canPlay = $this->federationService->canPlayInFederatedTournaments($this->player);
        $this->assertFalse($canPlay);

        // Federar jugadora
        $this->federationService->federatePlayer($this->player, $this->payment);
        $this->player->refresh();

        $canPlay = $this->federationService->canPlayInFederatedTournaments($this->player);
        $this->assertTrue($canPlay);

        // Suspender federación
        $suspender = User::create([
            'name' => 'Suspender Test 2',
            'email' => 'suspender2@test.com',
            'document_number' => '77777777',
            'password' => bcrypt('password'),
            'status' => UserStatus::Active,
        ]);
        $this->federationService->suspendPlayerFederation($this->player, 'Test', $suspender);
        $this->player->refresh();

        $canPlay = $this->federationService->canPlayInFederatedTournaments($this->player);
        $this->assertFalse($canPlay);
    }

    #[Test]
    public function it_can_get_club_federation_stats()
    {
        // Crear jugadoras con diferentes estados para el club
        Player::factory()->create([
            'current_club_id' => $this->club->id,
            'federation_status' => FederationStatus::Federated,
        ]);

        Player::factory()->create([
            'current_club_id' => $this->club->id,
            'federation_status' => FederationStatus::NotFederated,
        ]);

        $stats = $this->federationService->getClubFederationStats($this->club);

        $this->assertIsArray($stats);
        $this->assertArrayHasKey('total', $stats);
        $this->assertArrayHasKey('federated', $stats);
        $this->assertArrayHasKey('not_federated', $stats);

        $this->assertEquals(3, $stats['total']); // player original + 2 creadas
        $this->assertEquals(1, $stats['federated']);
        $this->assertEquals(2, $stats['not_federated']);
    }

    #[Test]
    public function it_can_validate_if_club_can_federate_players()
    {
        $validation = $this->federationService->canClubFederatePlayers($this->club);

        $this->assertTrue($validation['can_federate']);
        $this->assertEmpty($validation['issues']);

        // Desactivar club
        $this->club->update(['is_active' => false]);

        $validation = $this->federationService->canClubFederatePlayers($this->club);

        $this->assertFalse($validation['can_federate']);
        $this->assertContains('El club no está activo', $validation['issues']);
    }
}
