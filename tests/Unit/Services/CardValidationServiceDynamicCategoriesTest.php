<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\CardValidationService;
use App\Services\CategoryAssignmentService;
use App\Models\Player;
use App\Models\League;
use App\Models\LeagueCategory;
use App\Models\User;
use App\Models\Club;

use App\Enums\UserStatus;
use App\Enums\MedicalStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CardValidationServiceDynamicCategoriesTest extends TestCase
{
    use RefreshDatabase;

    protected CardValidationService $cardValidationService;
    protected CategoryAssignmentService $categoryAssignmentService;

    protected function setUp(): void
    {
        parent::setUp();

        // Create basic location data for foreign key constraints
        $this->createBasicLocationData();

        // Create real instances for better integration testing
        $this->categoryAssignmentService = app(CategoryAssignmentService::class);
        $this->cardValidationService = new CardValidationService($this->categoryAssignmentService);
    }

    private function createBasicLocationData(): void
    {
        // Run the Colombia locations seeder to populate Country, Department, and City tables
        $this->artisan('db:seed', ['--class' => 'ColombiaLocationsSeeder']);
    }

    public function test_validates_player_category_with_dynamic_categories()
    {
        // Arrange
        $league = League::factory()->forTesting()->create();

        // Create a director for the club
        $director = User::factory()->create([
            'status' => UserStatus::Active,
            'first_name' => 'Director',
            'last_name' => 'Test'
        ]);

        $club = Club::factory()->create([
            'league_id' => $league->id,
            'is_active' => true,
            'status' => UserStatus::Active,
            'director_id' => $director->id
        ]);

        // Create a league category for Cadete
        $leagueCategory = LeagueCategory::factory()->create([
            'league_id' => $league->id,
            'code' => 'cadete',
            'name' => 'Cadete',
            'min_age' => 15,
            'max_age' => 16,
            'gender' => 'female',
            'is_active' => true
        ]);

        $user = User::factory()->create([
            'birth_date' => now()->subYears(15), // Age 15 to match cadete category (15-16 years)
            'gender' => 'female',
            'status' => UserStatus::Active,
            'first_name' => 'Maria',
            'last_name' => 'Garcia',
            'document_number' => '12345678'
        ]);

        $player = Player::factory()->create([
            'user_id' => $user->id,
            'current_club_id' => $club->id,
            'category' => 'cadete',
            'position' => 'outside_hitter',
            'medical_status' => MedicalStatus::Fit
        ]);

        // Act - Test the dynamic category validation specifically
        $result = $this->cardValidationService->validateCategoryEligibility($player, $league);

        // Assert
        $this->assertTrue($result->isValid(), 'Category validation should pass for correctly configured dynamic category');
        $this->assertEmpty($result->getErrors());
        $this->assertEquals('dynamic', $result->getMetadata()['validation_type']);
    }

    public function test_fails_validation_when_category_not_configured_in_league()
    {
        // Arrange
        $league = League::factory()->forTesting()->create();
        $club = Club::factory()->create(['league_id' => $league->id]);

        // Create a different category that doesn't match player's category
        LeagueCategory::factory()->create([
            'league_id' => $league->id,
            'code' => 'juvenil',
            'name' => 'Juvenil',
            'min_age' => 17,
            'max_age' => 18,
            'gender' => 'female',
            'is_active' => true
        ]);

        $user = User::factory()->create([
            'birth_date' => now()->subYears(15),
            'gender' => 'female',
            'status' => UserStatus::Active,
            'first_name' => 'Maria',
            'last_name' => 'Garcia',
            'document_number' => '12345678'
        ]);

        $player = Player::factory()->create([
            'user_id' => $user->id,
            'current_club_id' => $club->id,
            'category' => 'cadete', // Valid enum value but not configured in this league
            'position' => 'outside_hitter',
            'medical_status' => MedicalStatus::Fit
        ]);

        // Act - Test category validation specifically
        $result = $this->cardValidationService->validateCategoryEligibility($player, $league);

        // Assert
        $this->assertFalse($result->isValid());
        $this->assertStringContainsString("La categoría 'cadete' no está configurada en esta liga", implode(' ', $result->getErrors()));
    }

    public function test_validates_special_rules_for_category()
    {
        // Arrange
        $league = League::factory()->forTesting()->create();
        $club = Club::factory()->create(['league_id' => $league->id]);

        // Create category with special medical clearance rule
        $leagueCategory = LeagueCategory::factory()->create([
            'league_id' => $league->id,
            'code' => 'cadete',
            'name' => 'Cadete',
            'min_age' => 15,
            'max_age' => 16,
            'gender' => 'female',
            'is_active' => true,
            'special_rules' => [
                ['type' => 'requires_medical_clearance', 'value' => true]
            ]
        ]);

        $user = User::factory()->create([
            'birth_date' => now()->subYears(15),
            'gender' => 'female',
            'status' => UserStatus::Active,
            'first_name' => 'Maria',
            'last_name' => 'Garcia',
            'document_number' => '12345678'
        ]);

        $player = Player::factory()->create([
            'user_id' => $user->id,
            'current_club_id' => $club->id,
            'category' => 'cadete',
            'position' => 'outside_hitter',
            'medical_status' => MedicalStatus::Under_Treatment // Not fit for special rule
        ]);

        // Act
        $result = $this->cardValidationService->validateForGeneration($player, $league);

        // Assert
        $this->assertFalse($result->isValid());
        $this->assertStringContainsString('Esta categoría requiere certificado médico especial vigente', implode(' ', $result->getErrors()));
    }

    public function test_validates_age_eligibility_for_dynamic_category()
    {
        // Arrange
        $league = League::factory()->forTesting()->create();
        $club = Club::factory()->create(['league_id' => $league->id]);

        // Create category for ages 17-18
        $leagueCategory = LeagueCategory::factory()->create([
            'league_id' => $league->id,
            'code' => 'juvenil',
            'name' => 'Juvenil',
            'min_age' => 17,
            'max_age' => 18,
            'gender' => 'female',
            'is_active' => true
        ]);

        $user = User::factory()->create([
            'birth_date' => now()->subYears(15), // Age 15, but category is for 17-18
            'gender' => 'female',
            'status' => UserStatus::Active,
            'first_name' => 'Maria',
            'last_name' => 'Garcia',
            'document_number' => '12345678'
        ]);

        $player = Player::factory()->create([
            'user_id' => $user->id,
            'current_club_id' => $club->id,
            'category' => 'juvenil', // Wrong category for age 15
            'position' => 'outside_hitter',
            'medical_status' => MedicalStatus::Fit
        ]);

        // Act
        $result = $this->cardValidationService->validateForGeneration($player, $league);

        // Assert
        $this->assertFalse($result->isValid());
        $this->assertStringContainsString('no corresponde al rango de la categoría', implode(' ', $result->getErrors()));
    }

    public function test_falls_back_to_traditional_validation_when_no_dynamic_categories()
    {
        // Arrange
        $league = League::factory()->forTesting()->create();
        $club = Club::factory()->create(['league_id' => $league->id]);

        // No dynamic categories created for this league

        $user = User::factory()->create([
            'birth_date' => now()->subYears(15),
            'gender' => 'female',
            'status' => UserStatus::Active,
            'first_name' => 'Maria',
            'last_name' => 'Garcia',
            'document_number' => '12345678'
        ]);

        $player = Player::factory()->create([
            'user_id' => $user->id,
            'current_club_id' => $club->id,
            'category' => 'cadete', // Valid traditional category
            'position' => 'outside_hitter',
            'medical_status' => MedicalStatus::Fit
        ]);

        // Act - Test category validation specifically (should fall back to traditional)
        $result = $this->cardValidationService->validateCategoryEligibility($player, $league);

        // Assert
        $this->assertTrue($result->isValid());
        $this->assertEmpty($result->getErrors());
        $this->assertEquals('traditional', $result->getMetadata()['validation_type']);
    }

    public function test_validates_for_card_generation_with_specific_errors()
    {
        // Arrange
        $league = League::factory()->forTesting()->create();
        $club = Club::factory()->create(['league_id' => $league->id]);

        // Create category for ages 17-18
        $leagueCategory = LeagueCategory::factory()->create([
            'league_id' => $league->id,
            'code' => 'juvenil',
            'name' => 'Juvenil',
            'min_age' => 17,
            'max_age' => 18,
            'gender' => 'female',
            'is_active' => true
        ]);

        $user = User::factory()->create([
            'birth_date' => now()->subYears(15), // Age 15, but category is for 17-18
            'gender' => 'female',
            'status' => UserStatus::Active,
            'first_name' => 'Maria',
            'last_name' => 'Garcia',
            'document_number' => '12345678'
        ]);

        $player = Player::factory()->create([
            'user_id' => $user->id,
            'current_club_id' => $club->id,
            'category' => 'juvenil',
            'position' => 'outside_hitter',
            'medical_status' => MedicalStatus::Fit
        ]);

        // Act
        $result = $this->cardValidationService->validateForCardGeneration($player, $league);

        // Assert
        $this->assertFalse($result->isValid());
        $this->assertStringContainsString('No se puede generar carnet', implode(' ', $result->getErrors()));
    }

    public function test_validates_category_eligibility_method()
    {
        // Arrange
        $league = League::factory()->forTesting()->create();
        $club = Club::factory()->create(['league_id' => $league->id]);

        $leagueCategory = LeagueCategory::factory()->create([
            'league_id' => $league->id,
            'code' => 'cadete',
            'name' => 'Cadete',
            'min_age' => 15,
            'max_age' => 16,
            'gender' => 'female',
            'is_active' => true
        ]);

        $user = User::factory()->create([
            'birth_date' => now()->subYears(15),
            'gender' => 'female',
            'status' => UserStatus::Active,
            'first_name' => 'Maria',
            'last_name' => 'Garcia',
            'document_number' => '12345678'
        ]);

        $player = Player::factory()->create([
            'user_id' => $user->id,
            'current_club_id' => $club->id,
            'category' => 'cadete',
            'position' => 'outside_hitter',
            'medical_status' => MedicalStatus::Fit
        ]);

        // Act
        $result = $this->cardValidationService->validateCategoryEligibility($player, $league);

        // Assert
        $this->assertTrue($result->isValid());
        $this->assertEmpty($result->getErrors());
        $this->assertEquals('dynamic', $result->getMetadata()['validation_type']);
    }

    public function test_gets_validation_details_for_debugging()
    {
        // Arrange
        $league = League::factory()->forTesting()->create();
        $club = Club::factory()->create(['league_id' => $league->id]);

        $leagueCategory = LeagueCategory::factory()->create([
            'league_id' => $league->id,
            'code' => 'cadete',
            'name' => 'Cadete',
            'min_age' => 15,
            'max_age' => 16,
            'gender' => 'female',
            'is_active' => true
        ]);

        $user = User::factory()->create([
            'birth_date' => now()->subYears(15),
            'gender' => 'female',
            'status' => UserStatus::Active,
            'first_name' => 'Maria',
            'last_name' => 'Garcia',
            'document_number' => '12345678'
        ]);

        $player = Player::factory()->create([
            'user_id' => $user->id,
            'current_club_id' => $club->id,
            'category' => 'cadete',
            'position' => 'outside_hitter',
            'medical_status' => MedicalStatus::Fit
        ]);

        // Act
        $details = $this->cardValidationService->getValidationDetails($player, $league);

        // Assert
        $this->assertArrayHasKey('player_info', $details);
        $this->assertArrayHasKey('league_info', $details);
        $this->assertArrayHasKey('validation_results', $details);
        $this->assertArrayHasKey('available_categories', $details);
        $this->assertTrue($details['league_info']['has_custom_categories']);
        $this->assertEquals('Maria Garcia', $details['player_info']['name']);
        $this->assertEquals(15, $details['player_info']['age']);
    }
}
