<?php

namespace Tests\Feature\Federation;

use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
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
use Livewire\Livewire;
use App\Filament\Resources\PlayerResource;

class PlayerResourceTest extends TestCase
{
    use RefreshDatabase;

    private League $league;
    private Club $club;
    private User $admin;
    private Player $player;

    protected function setUp(): void
    {
        parent::setUp();
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
        ]);

        // Crear admin
        $this->admin = User::create([
            'name' => 'Admin Test',
            'first_name' => 'Admin',
            'last_name' => 'Test',
            'email' => 'admin@test.com',
            'document_number' => '11111111',
            'phone' => '3001111111',
            'status' => UserStatus::Active,
            'league_id' => $this->league->id,
            'password' => bcrypt('password'),
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
    }

    #[Test]
    public function it_can_display_players_list()
    {
        $this->actingAs($this->admin, 'web');

        $response = $this->get(PlayerResource::getUrl('index'));

        $response->assertSuccessful();
        $response->assertSee($this->player->user->full_name);
        $response->assertSee($this->club->name);
    }

    #[Test]
    public function it_can_create_new_player()
    {
        $this->actingAs($this->admin);

        // Crear usuario para la nueva jugadora
        $newUser = User::create([
            'name' => 'Nueva Jugadora',
            'first_name' => 'Nueva',
            'last_name' => 'Jugadora',
            'email' => 'nueva@test.com',
            'document_number' => '99999999',
            'phone' => '3009999999',
            'birth_date' => now()->subYears(22),
            'gender' => Gender::Female,
            'status' => UserStatus::Active,
            'league_id' => $this->league->id,
            'club_id' => $this->club->id,
            'password' => bcrypt('password'),
        ]);

        $playerData = [
            'user_id' => $newUser->id,
            'current_club_id' => $this->club->id,
            'jersey_number' => 15,
            'position' => PlayerPosition::Outside_Hitter,
            'category' => PlayerCategory::Mayores,
            'height' => 1.75,
            'weight' => 70.0,
            'dominant_hand' => 'left',
            'status' => UserStatus::Active,
            'medical_status' => MedicalStatus::Fit,
            'is_eligible' => true,
            'federation_status' => FederationStatus::NotFederated,
        ];

        Livewire::test(PlayerResource\Pages\CreatePlayer::class)
            ->fillForm($playerData)
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('players', [
            'user_id' => $newUser->id,
            'jersey_number' => 15,
            'position' => PlayerPosition::Outside_Hitter->value,
        ]);
    }

    #[Test]
    public function it_can_edit_player_information()
    {
        $this->actingAs($this->admin);

        $newData = [
            'jersey_number' => 20,
            'position' => PlayerPosition::Libero,
            'height' => 1.68,
            'weight' => 62.0,
        ];

        Livewire::test(PlayerResource\Pages\EditPlayer::class, [
            'record' => $this->player->getRouteKey(),
        ])
            ->fillForm($newData)
            ->call('save')
            ->assertHasNoFormErrors();

        $this->player->refresh();
        $this->assertEquals(20, $this->player->jersey_number);
        $this->assertEquals(PlayerPosition::Libero, $this->player->position);
        $this->assertEquals(1.68, $this->player->height);
    }

    #[Test]
    public function it_can_view_player_details()
    {
        $this->actingAs($this->admin);

        $response = $this->get(PlayerResource::getUrl('view', [
            'record' => $this->player,
        ]));

        $response->assertSuccessful();
        $response->assertSee($this->player->user->full_name);
        $response->assertSee($this->player->federation_status->getLabel());
        $response->assertSee($this->player->medical_status->getLabel());
    }

    #[Test]
    public function it_can_filter_players_by_federation_status()
    {
        $this->actingAs($this->admin);

        // Crear jugadora federada
        $federatedUser = User::create([
            'name' => 'Jugadora Federada',
            'first_name' => 'Jugadora',
            'last_name' => 'Federada',
            'email' => 'federada@test.com',
            'document_number' => '88888888',
            'phone' => '3008888888',
            'birth_date' => now()->subYears(21),
            'gender' => Gender::Female,
            'status' => UserStatus::Active,
            'league_id' => $this->league->id,
            'club_id' => $this->club->id,
            'password' => bcrypt('password'),
        ]);

        $federatedPlayer = Player::create([
            'user_id' => $federatedUser->id,
            'current_club_id' => $this->club->id,
            'jersey_number' => 11,
            'position' => PlayerPosition::Setter,
            'category' => PlayerCategory::Mayores,
            'height' => 1.72,
            'weight' => 67.0,
            'dominant_hand' => 'right',
            'status' => UserStatus::Active,
            'medical_status' => MedicalStatus::Fit,
            'is_eligible' => true,
            'federation_status' => FederationStatus::Federated,
        ]);

        Livewire::test(PlayerResource\Pages\ListPlayers::class)
            ->filterTable('federation_status', FederationStatus::Federated->value)
            ->assertCanSeeTableRecords([$federatedPlayer])
            ->assertCanNotSeeTableRecords([$this->player]);
    }

    #[Test]
    public function it_can_filter_players_by_club()
    {
        $this->actingAs($this->admin);

        // Crear otro club
        $otherClub = Club::create([
            'league_id' => $this->league->id,
            'name' => 'Otro Club',
            'short_name' => 'OC',
            'description' => 'Otro club para testing',
            'city_id' => $this->club->city_id,
            'email' => 'otro@test.com',
            'phone' => '3002222222',
            'status' => UserStatus::Active,
            'is_active' => true,
        ]);

        // Crear jugadora en otro club
        $otherUser = User::create([
            'name' => 'Jugadora Otro Club',
            'first_name' => 'Jugadora',
            'last_name' => 'Otro',
            'email' => 'otro@test.com',
            'document_number' => '77777777',
            'phone' => '3007777777',
            'birth_date' => now()->subYears(19),
            'gender' => Gender::Female,
            'status' => UserStatus::Active,
            'league_id' => $this->league->id,
            'club_id' => $otherClub->id,
            'password' => bcrypt('password'),
        ]);

        $otherPlayer = Player::create([
            'user_id' => $otherUser->id,
            'current_club_id' => $otherClub->id,
            'jersey_number' => 12,
            'position' => PlayerPosition::Outside_Hitter,
            'category' => PlayerCategory::Juvenil,
            'height' => 1.68,
            'weight' => 60.0,
            'dominant_hand' => 'right',
            'status' => UserStatus::Active,
            'medical_status' => MedicalStatus::Fit,
            'is_eligible' => true,
            'federation_status' => FederationStatus::NotFederated,
        ]);

        Livewire::test(PlayerResource\Pages\ListPlayers::class)
            ->filterTable('current_club_id', $this->club->id)
            ->assertCanSeeTableRecords([$this->player])
            ->assertCanNotSeeTableRecords([$otherPlayer]);
    }

    #[Test]
    public function it_can_search_players_by_name()
    {
        $this->actingAs($this->admin);

        Livewire::test(PlayerResource\Pages\ListPlayers::class)
            ->searchTable('Jugadora Test')
            ->assertCanSeeTableRecords([$this->player]);
    }

    #[Test]
    public function it_can_search_players_by_document()
    {
        $this->actingAs($this->admin);

        Livewire::test(PlayerResource\Pages\ListPlayers::class)
            ->searchTable('87654321')
            ->assertCanSeeTableRecords([$this->player]);
    }

    #[Test]
    public function it_shows_federation_status_badge_correctly()
    {
        $this->actingAs($this->admin);

        $response = $this->get(PlayerResource::getUrl('index'));

        $response->assertSuccessful();
        $response->assertSee(FederationStatus::NotFederated->getLabel());
    }

    #[Test]
    public function it_can_bulk_federate_players()
    {
        $this->actingAs($this->admin);

        // Crear pago verificado
        $payment = Payment::create([
            'club_id' => $this->club->id,
            'league_id' => $this->league->id,
            'user_id' => $this->club->director_id,
            'type' => PaymentType::Federation,
            'amount' => 50000,
            'currency' => 'COP',
            'reference_number' => 'BULK-001',
            'payment_method' => 'transfer',
            'status' => PaymentStatus::Verified,
            'paid_at' => now(),
            'verified_at' => now(),
            'verified_by' => $this->admin->id,
        ]);

        // Crear otra jugadora no federada
        $otherUser = User::create([
            'name' => 'Otra Jugadora',
            'first_name' => 'Otra',
            'last_name' => 'Jugadora',
            'email' => 'otra@test.com',
            'document_number' => '66666666',
            'phone' => '3006666666',
            'birth_date' => now()->subYears(23),
            'gender' => Gender::Female,
            'status' => UserStatus::Active,
            'league_id' => $this->league->id,
            'club_id' => $this->club->id,
            'password' => bcrypt('password'),
        ]);

        $otherPlayer = Player::create([
            'user_id' => $otherUser->id,
            'current_club_id' => $this->club->id,
            'jersey_number' => 13,
            'position' => PlayerPosition::Middle_Blocker,
            'category' => PlayerCategory::Mayores,
            'height' => 1.80,
            'weight' => 75.0,
            'dominant_hand' => 'right',
            'status' => UserStatus::Active,
            'medical_status' => MedicalStatus::Fit,
            'is_eligible' => true,
            'federation_status' => FederationStatus::NotFederated,
        ]);

        Livewire::test(PlayerResource\Pages\ListPlayers::class)
            ->selectTableRecords([$this->player, $otherPlayer])
            ->callTableBulkAction('bulk_federate', [
                'club_id' => $this->club->id,
                'payment_id' => $payment->id,
            ]);

        $this->player->refresh();
        $otherPlayer->refresh();

        $this->assertEquals(FederationStatus::Federated, $this->player->federation_status);
        $this->assertEquals(FederationStatus::Federated, $otherPlayer->federation_status);
    }

    #[Test]
    public function it_validates_jersey_number_uniqueness_within_club()
    {
        $this->actingAs($this->admin);

        // Crear usuario para nueva jugadora
        $newUser = User::create([
            'name' => 'Nueva Jugadora',
            'first_name' => 'Nueva',
            'last_name' => 'Jugadora',
            'email' => 'nueva2@test.com',
            'document_number' => '55555555',
            'phone' => '3005555555',
            'birth_date' => now()->subYears(24),
            'gender' => Gender::Female,
            'status' => UserStatus::Active,
            'league_id' => $this->league->id,
            'club_id' => $this->club->id,
            'password' => bcrypt('password'),
        ]);

        $playerData = [
            'user_id' => $newUser->id,
            'current_club_id' => $this->club->id,
            'jersey_number' => 10, // Mismo número que la jugadora existente
            'position' => PlayerPosition::Outside_Hitter,
            'category' => PlayerCategory::Mayores,
            'height' => 1.75,
            'weight' => 70.0,
            'dominant_hand' => 'left',
            'status' => UserStatus::Active,
            'medical_status' => MedicalStatus::Fit,
            'is_eligible' => true,
            'federation_status' => FederationStatus::NotFederated,
        ];

        // Esto debería fallar por número duplicado
        $this->expectException(\Exception::class);

        Player::create($playerData);
    }

    #[Test]
    public function it_shows_correct_tabs_in_list_view()
    {
        $this->actingAs($this->admin);

        // Crear jugadoras con diferentes estados
        $federatedUser = User::create([
            'name' => 'Federada',
            'email' => 'fed@test.com',
            'document_number' => '44444444',
            'status' => UserStatus::Active,
            'league_id' => $this->league->id,
            'club_id' => $this->club->id,
            'password' => bcrypt('password'),
        ]);

        Player::create([
            'user_id' => $federatedUser->id,
            'current_club_id' => $this->club->id,
            'jersey_number' => 14,
            'position' => PlayerPosition::Setter,
            'category' => PlayerCategory::Mayores,
            'status' => UserStatus::Active,
            'medical_status' => MedicalStatus::Fit,
            'is_eligible' => true,
            'federation_status' => FederationStatus::Federated,
        ]);

        $response = $this->get(PlayerResource::getUrl('index'));

        $response->assertSuccessful();
        $response->assertSee('Todas');
        $response->assertSee('Federadas');
        $response->assertSee('No Federadas');
        $response->assertSee('Pago Pendiente');
    }
}
