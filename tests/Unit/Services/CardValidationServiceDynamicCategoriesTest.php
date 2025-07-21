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
use App\Enums\PlayerCategory;
use App\Enums\UserStatus;
use App\Enums\MedicalStatus;
use App\Enums\Gender;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CardValidationServiceDynamicCategoriesTest extends TestCase
{
    use RefreshDatabase;

    protected CardValidationService $cardValidationService;
    protected CategoryAssignmentService $categoryAssignmentService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->categoryAssignmentService = $this->createMock(CategoryAssignmentService::class);
        $this->cardValidationService = new CardValidationService($this->categoryAssignmentService);
    }

    public function test_validates_player_category_with_dynamic_categories()
    {
        // Arrange
        $league = League::factory()->create();
        $club = Club::factory()->create(['league_id' => $league->id]);

        $user = User::factory()->create([
            'birth_date' => now()->subYears(15),
            'gender' => Gender::Female,
            'status' => UserStatus::Active,
            'first_name' => 'Test',
            'last_name' => 'Player',
            'document_number' => '12345678'
        ]);

        $player = Player::factory()->create([
            'user_id' => $user->id,
            'current_club_id' => $club->id,
            'category' => 'Cadete',
            'position' => 'Delantera',
            'medical_status' => MedicalStatus::Fit
        ]);

        // Create league category
        $leagueCategory = LeagueCategory::factory()->create([
            'league_id' => $league->id,
            'name' => 'Cadete',
            'code' => 'cadete',
            'min_age' => 15,
            'max_age' => 16,
            'gender' => Gender::Female,
            'is_active' => true
        ]);

        // Mock category assignment service
        $this->categoryAssignmentService
            ->expects($this->once())
            ->method('assignAutomaticCategory')
            ->with($player)
            ->willReturn('Cadete');

        $this->categoryAssignmentService
            ->expects($this->once())
            ->method('validateCategoryChange')
            ->with($player, 'Cadete')
            ->willReturn(['errors' => [], 'warnings' => []]);

        // Act
        $result = $this->cardValidationService->validateForGeneration($player, $league);

        // Assert
        $this->assertTrue($result->isValid());
        $this->assertEmpty($result->getErrors());
    }

    public function test_fails_validation_when_category_not_configured_in_league()
    {
        // Arrange
        $league = League::factory()->create();
        $club = Club::factory()->create(['league_id' => $league->id]);

        $user = User::factory()->create([
            'birth_date' => now()->subYears(15),
            'gender' => Gender::Female,
            'status' => UserStatus::Active,
            'first_name' => 'Test',
            'last_name' => 'Player',
            'document_number' => '12345678'
        ]);

        $player = Player::factory()->create([
            'user_id' => $user->id,
            'current_club_id' => $club->id,
            'category' => 'InvalidCategory',
            'position' => 'Delantera',
            'medical_status' => MedicalStatus::Fit
        ]);

        // Create a different category in the league
        LeagueCategory::factory()->create([
            'league_id' => $league->id,
            'name' => 'Cadete',
            'code' => 'cadete',
            'min_age' => 15,
            'max_age' => 16,
            'gender' => Gender::Female,
            'is_active' => true
        ]);

        // Mock category assignment service
        $this->categoryAssignmentService
            ->expects($this->once())
            ->method('assignAutomaticCategory')
            ->with($player)
            ->willReturn('Cadete');

        $this->categoryAssignmentService
            ->expects($this->once())
            ->method('validateCategoryChange')
            ->with($player, 'Cadete')
            ->willReturn(['errors' => [], 'warnings' => []]);

        // Act
        $result = $this->cardValidationService->validateForGeneration($player, $league);

        // Assert
        $this->assertFalse($result->isValid());
        $this->assertContains("La categoría 'InvalidCategory' no está configurada en esta liga", $result->getErrors());
    }

    public function test_validates_special_rules_for_category()
    {
        // Arrange
        $league = League::factory()->create();
        $club = Club::factory()->create(['league_id' => $league->id]);

        $user = User::factory()->create([
            'birth_date' => now()->subYears(15),
            'gender' => Gender::Female,
            'status' => UserStatus::Active,
            'first_name' => 'Test',
            'last_name' => 'Player',
            'document_number' => '12345678'
        ]);

        $player = Player::factory()->create([
            'user_id' => $user->id,
            'current_club_id' => $club->id,
            'category' => 'Cadete',
            'position' => 'Delantera',
            'medical_status' => MedicalStatus::Pending // Not fit for special rule
        ]);

        // Create league category with special rules
        $leagueCategory = LeagueCategory::factory()->create([
            'league_id' => $league->id,
            'name' => 'Cadete',
            'code' => 'cadete',
            'min_age' => 15,
            'max_age' => 16,
            'gender' => Gender::Female,
            'is_active' => true,
            'special_rules' => [
                ['type' => 'requires_medical_clearance', 'value' => true]
            ]
        ]);

        // Mock category assignment service
        $this->categoryAssignmentService
            ->expects($this->once())
            ->method('assignAutomaticCategory')
            ->with($player)
            ->willReturn('Cadete');

        $this->categoryAssignmentService
            ->expects($this->once())
            ->method('validateCategoryChange')
            ->with($player, 'Cadete')
            ->willReturn(['errors' => [], 'warnings' => []]);

        // Act
        $result = $this->cardValidationService->validateForGeneration($player, $league);

        // Assert
        $this->assertFalse($result->isValid());
        $this->assertContains('Esta categoría requiere certificado médico especial vigente', $result->getErrors());
    }

    public function test_suggests_correct_category_when_mismatch()
    {
        // Arrange
        $league = League::factory()->create();
        $club = Club::factory()->create(['league_id' => $league->id]);

        $user = User::factory()->create([
            'birth_date' => now()->subYears(15),
            'gender' => Gender::Female,
            'status' => UserStatus::Active,
            'first_name' => 'Test',
            'last_name' => 'Player',
            'document_number' => '12345678'
        ]);

        $player = Player::factory()->create([
            'user_id' => $user->id,
            'current_club_id' => $club->id,
            'category' => 'Juvenil', // Wrong category for age 15
            'position' => 'Delantera',
            'medical_status' => MedicalStatus::Fit
        ]);

        // Create league category for current (wrong) category
        $leagueCategory = LeagueCategory::factory()->create([
            'league_id' => $league->id,
            'name' => 'Juvenil',
            'code' => 'juvenil',
            'min_age' => 17,
            'max_age' => 18,
            'gender' => Gender::Female,
            'is_active' => true
        ]);

        // Mock category assignment service to suggest different category
        $this->categoryAssignmentService
            ->expects($this->once())
            ->method('assignAutomaticCategory')
            ->with($player)
            ->willReturn('Cadete'); // Correct category for age 15

        $this->categoryAssignmentService
            ->expects($this->once())
            ->method('validateCategoryChange')
            ->with($player, 'Cadete')
            ->willReturn(['errors' => [], 'warnings' => ['Cambio recomendado por edad']]);

        // Act
        $result = $this->cardValidationService->validateForGeneration($player, $league);

        // Assert
        $this->assertTrue($result->hasWarnings());
        $this->assertContains('Categoría sugerida: Cadete (actual: Juvenil)', $result->getWarnings());
    }

    public function test_falls_back_to_traditional_validation_when_no_dynamic_categories()
    {
        // Arrange
        $league = League::factory()->create();
        $club = Club::factory()->create(['league_id' => $league->id]);

        $user = User::factory()->create([
            'birth_date' => now()->subYears(15),
            'gender' => Gender::Female,
            'status' => UserStatus::Active,
            'first_name' => 'Test',
            'last_name' => 'Player',
            'document_number' => '12345678'
        ]);

        $player = Player::factory()->create([
            'user_id' => $user->id,
            'current_club_id' => $club->id,
            'category' => PlayerCategory::Cadete,
            'position' => 'Delantera',
            'medical_status' => MedicalStatus::Fit
        ]);

        // No league categories created - should fall back to traditional validation

        // Mock category assignment service
        $this->categoryAssignmentService
            ->expects($this->once())
            ->method('assignAutomaticCategory')
            ->with($player)
            ->willReturn('Cadete');

        $this->categoryAssignmentService
            ->expects($this->once())
            ->method('validateCategoryChange')
            ->with($player, 'Cadete')
            ->willReturn(['errors' => [], 'warnings' => []]);

        // Act
        $result = $this->cardValidationService->validateForGeneration($player, $league);

        // Assert
        $this->assertTrue($result->isValid());
        $this->assertEmpty($result->getErrors());
    }

    public function test_validates_for_card_generation_with_dynamic_categories()
    {
        // Arrange
        $league = League::factory()->create();
        $club = Club::factory()->create(['league_id' => $league->id]);

        $user = User::factory()->create([
            'birth_date' => now()->subYears(15),
            'gender' => Gender::Female,
            'status' => UserStatus::Active,
            'first_name' => 'Test',
            'last_name' => 'Player',
            'document_number' => '12345678'
        ]);

        $player = Player::factory()->create([
            'user_id' => $user->id,
            'current_club_id' => $club->id,
            'category' => 'Cadete',
            'position' => 'Delantera',
            'medical_status' => MedicalStatus::Fit
        ]);

        // Create league category
        $leagueCategory = LeagueCategory::factory()->create([
            'league_id' => $league->id,
            'name' => 'Cadete',
            'code' => 'cadete',
            'min_age' => 15,
            'max_age' => 16,
            'gender' => Gender::Female,
            'is_active' => true
        ]);

        // Mock category assignment service
        $this->categoryAssignmentService
            ->expects($this->once())
            ->method('assignAutomaticCategory')
            ->with($player)
            ->willReturn('Cadete');

        // Act
        $result = $this->cardValidationService->validateForCardGeneration($player, $league);

        // Assert
        $this->assertTrue($result->isValid());
        $this->assertEmpty($result->getErrors());
        $this->assertEquals('dynamic', $result->getMetadata()['validation_type']);
        $this->assertEquals('card_generation', $result->getMetadata()['validated_for']);
    }

    public function test_fails_card_generation_when_age_not_eligible()
    {
        // Arrange
        $league = League::factory()->create();
        $club = Club::factory()->create(['league_id' => $league->id]);

        $user = User::factory()->create([
            'birth_date' => now()->subYears(20), // Too old for Cadete
            'gender' => Gender::Female,
            'status' => UserStatus::Active,
            'first_name' => 'Test',
            'last_name' => 'Player',
            'document_number' => '12345678'
        ]);

        $player = Player::factory()->create([
            'user_id' => $user->id,
            'current_club_id' => $club->id,
            'category' => 'Cadete',
            'position' => 'Delantera',
            'medical_status' => MedicalStatus::Fit
        ]);

        // Create league category with age range that doesn't match
        $leagueCategory = LeagueCategory::factory()->create([
            'league_id' => $league->id,
            'name' => 'Cadete',
            'code' => 'cadete',
            'min_age' => 15,
            'max_age' => 16,
            'gender' => Gender::Female,
            'is_active' => true
        ]);

        // Mock category assignment service
        $this->categoryAssignmentService
            ->expects($this->once())
            ->method('assignAutomaticCategory')
            ->with($player)
            ->willReturn('Mayores');

        // Act
        $result = $this->cardValidationService->validateForCardGeneration($player, $league);

        // Assert
        $this->assertFalse($result->isValid());
        $this->assertStringContainsString('No se puede generar carnet: la edad de la jugadora (20 años) no es elegible', $result->getErrorMessage());
    }

    public function test_validates_category_eligibility_method()
    {
        // Arrange
        $league = League::factory()->create();
        $club = Club::factory()->create(['league_id' => $league->id]);

        $user = User::factory()->create([
            'birth_date' => now()->subYears(15),
            'gender' => Gender::Female,
            'status' => UserStatus::Active,
            'first_name' => 'Test',
            'last_name' => 'Player',
            'document_number' => '12345678'
        ]);

        $player = Player::factory()->create([
            'user_id' => $user->id,
            'current_club_id' => $club->id,
            'category' => 'Cadete',
            'position' => 'Delantera',
            'medical_status' => MedicalStatus::Fit
        ]);

        // Create league category
        $leagueCategory = LeagueCategory::factory()->create([
            'league_id' => $league->id,
            'name' => 'Cadete',
            'code' => 'cadete',
            'min_age' => 15,
            'max_age' => 16,
            'gender' => Gender::Female,
            'is_active' => true
        ]);

        // Mock category assignment service
        $this->categoryAssignmentService
            ->expects($this->once())
            ->method('assignAutomaticCategory')
            ->with($player)
            ->willReturn('Cadete');

        // Act
        $result = $this->cardValidationService->validateCategoryEligibility($player, $league);

        // Assert
        $this->assertTrue($result->isValid());
        $this->assertEmpty($result->getErrors());
        $this->assertEquals('dynamic', $result->getMetadata()['validation_type']);
    }

    public function test_gets_validation_details()
    {
        // Arrange
        $league = League::factory()->create();
        $club = Club::factory()->create(['league_id' => $league->id]);

        $user = User::factory()->create([
            'birth_date' => now()->subYears(15),
            'gender' => Gender::Female,
            'status' => UserStatus::Active,
            'first_name' => 'Test',
            'last_name' => 'Player',
            'document_number' => '12345678'
        ]);

        $player = Player::factory()->create([
            'user_id' => $user->id,
            'current_club_id' => $club->id,
            'category' => 'Cadete',
            'position' => 'Delantera',
            'medical_status' => MedicalStatus::Fit
        ]);

        // Create league category
        $leagueCategory = LeagueCategory::factory()->create([
            'league_id' => $league->id,
            'name' => 'Cadete',
            'code' => 'cadete',
            'min_age' => 15,
            'max_age' => 16,
            'gender' => Gender::Female,
            'is_active' => true
        ]);

        // Mock category assignment service
        $this->categoryAssignmentService
            ->expects($this->exactly(2))
            ->method('assignAutomaticCategory')
            ->with($player)
            ->willReturn('Cadete');

        // Act
        $details = $this->cardValidationService->getValidationDetails($player, $league);

        // Assert
        $this->assertArrayHasKey('player_info', $details);
        $this->assertArrayHasKey('league_info', $details);
        $this->assertArrayHasKey('validation_results', $details);
        $this->assertArrayHasKey('category_suggestions', $details);
        $this->assertArrayHasKey('available_categories', $details);

        $this->assertEquals($player->id, $details['player_info']['id']);
        $this->assertEquals($league->id, $details['league_info']['id']);
        $this->assertTrue($details['league_info']['has_custom_categories']);
        $this->assertEquals('Cadete', $details['category_suggestions']['automatic']);
    }
}
