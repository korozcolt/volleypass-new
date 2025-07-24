<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Enums\PlayerCategory;
use App\Events\PlayerCategoryReassigned;
use App\Models\Player;
use App\Models\User;
use App\Services\CategoryAssignmentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class PlayerCategoryControllerTest extends TestCase
{
    use RefreshDatabase;
    
    protected User $user;
    protected Player $player;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->player = Player::factory()->create([
            'category' => PlayerCategory::Infantil
        ]);
    }

    /** @test */
    public function it_can_show_player_category_information()
    {
        $this->actingAs($this->user)
            ->getJson(route('api.players.category.show', $this->player->id))
            ->assertOk()
            ->assertJsonStructure([
                'success',
                'player' => [
                    'id',
                    'name',
                    'age',
                    'gender',
                    'current_category' => [
                        'name',
                        'value',
                        'color',
                        'icon',
                        'age_range',
                    ],
                    'is_correct_category',
                ]
            ])
            ->assertJson([
                'success' => true,
                'player' => [
                    'id' => $this->player->id,
                    'current_category' => [
                        'value' => PlayerCategory::Infantil,
                    ],
                ]
            ]);
    }

    /** @test */
    public function it_can_update_player_category()
    {
        Event::fake([PlayerCategoryReassigned::class]);

        $mockService = $this->mock(CategoryAssignmentService::class);
        $mockService->shouldReceive('updatePlayerCategory')
            ->once()
            ->andReturn([
                'success' => true,
                'message' => 'Categoría actualizada exitosamente'
            ]);

        $response = $this->actingAs($this->user)
            ->putJson(route('api.players.category.update', $this->player->id), [
                'category' => PlayerCategory::Cadete->value,
                'reason' => 'Prueba de cambio de categoría'
            ]);

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'message' => 'Categoría actualizada exitosamente',
                'player' => [
                    'id' => $this->player->id,
                    'old_category' => PlayerCategory::Infantil,
                    'new_category' => PlayerCategory::Cadete->value,
                    'reason' => 'Prueba de cambio de categoría'
                ]
            ]);
    }

    /** @test */
    public function it_validates_category_update_request()
    {
        $response = $this->actingAs($this->user)
            ->putJson(route('api.players.category.update', $this->player->id), [
                'category' => 'categoria_invalida',
                'reason' => 'Prueba de cambio de categoría'
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['category']);

        $response = $this->actingAs($this->user)
            ->putJson(route('api.players.category.update', $this->player->id), [
                'category' => PlayerCategory::Cadete->value,
                'reason' => ''
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['reason']);
    }

    /** @test */
    public function it_returns_error_when_category_is_the_same()
    {
        $response = $this->actingAs($this->user)
            ->putJson(route('api.players.category.update', $this->player->id), [
                'category' => PlayerCategory::Infantil->value,
                'reason' => 'Prueba de cambio de categoría'
            ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'El jugador ya está asignado a esta categoría'
            ]);
    }

    /** @test */
    public function it_returns_error_when_category_update_fails()
    {
        $mockService = $this->mock(CategoryAssignmentService::class);
        $mockService->shouldReceive('updatePlayerCategory')
            ->once()
            ->andReturn([
                'success' => false,
                'message' => 'El jugador no es elegible para esta categoría',
                'errors' => ['Error de validación']
            ]);

        $response = $this->actingAs($this->user)
            ->putJson(route('api.players.category.update', $this->player->id), [
                'category' => PlayerCategory::Mayores->value,
                'reason' => 'Prueba de cambio de categoría'
            ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'El jugador no es elegible para esta categoría',
                'errors' => ['Error de validación']
            ]);
    }
}