<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\UserProfile;
use App\Models\Player;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;

class UserProfileTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed', ['--class' => 'RoleSeeder']);
    }

    /** @test */
    public function user_can_get_their_own_profile()
    {
        $user = User::factory()->create([
            'first_name' => 'Juan',
            'last_name' => 'Pérez',
            'email' => 'juan@example.com',
            'phone' => '+57 300 123 4567',
        ]);

        UserProfile::factory()->create([
            'user_id' => $user->id,
            'nickname' => 'Juancho',
            'bio' => 'Jugador de voleibol desde 2010',
        ]);

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/v1/users/profile');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'id',
                    'name',
                    'email',
                    'first_name',
                    'last_name',
                    'phone',
                    'user_type',
                    'profile' => [
                        'nickname',
                        'bio',
                        'avatar_url',
                    ],
                    'location'
                ]
            ])
            ->assertJson([
                'success' => true,
                'data' => [
                    'first_name' => 'Juan',
                    'last_name' => 'Pérez',
                    'email' => 'juan@example.com',
                    'phone' => '+57 300 123 4567',
                    'user_type' => 'user',
                    'profile' => [
                        'nickname' => 'Juancho',
                        'bio' => 'Jugador de voleibol desde 2010',
                    ]
                ]
            ]);
    }

    /** @test */
    public function player_profile_includes_player_info()
    {
        $user = User::factory()->create();
        $user->assignRole('Player');
        
        $player = Player::factory()->create([
            'user_id' => $user->id,
            'jersey_number' => 10,
            'height' => 1.75,
            'weight' => 70.5,
        ]);

        UserProfile::factory()->create([
            'user_id' => $user->id,
            'nickname' => 'Juancho',
        ]);

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/v1/users/profile');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'user_type',
                    'player_info' => [
                        'position',
                        'jersey_number',
                        'height',
                        'weight',
                        'current_club',
                        'category',
                    ]
                ]
            ])
            ->assertJson([
                'success' => true,
                'data' => [
                    'user_type' => 'player',
                    'player_info' => [
                        'jersey_number' => 10,
                        'height' => 1.75,
                        'weight' => 70.5,
                    ]
                ]
            ]);
    }

    /** @test */
    public function user_can_update_their_profile()
    {
        $user = User::factory()->create();
        
        UserProfile::factory()->create([
            'user_id' => $user->id,
        ]);

        Sanctum::actingAs($user);

        $updateData = [
            'first_name' => 'Carlos',
            'last_name' => 'González',
            'phone' => '+57 300 999 8888',
            'nickname' => 'Carlitos',
            'bio' => 'Entrenador de voleibol',
            'emergency_contact_name' => 'Ana González',
            'emergency_contact_phone' => '+57 300 777 6666',
            'blood_type' => 'A+',
        ];

        $response = $this->putJson('/api/v1/users/profile', $updateData);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Perfil actualizado exitosamente',
            ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'first_name' => 'Carlos',
            'last_name' => 'González',
            'phone' => '+57 300 999 8888',
        ]);

        $this->assertDatabaseHas('user_profiles', [
            'user_id' => $user->id,
            'nickname' => 'Carlitos',
            'bio' => 'Entrenador de voleibol',
            'emergency_contact_name' => 'Ana González',
            'emergency_contact_phone' => '+57 300 777 6666',
            'blood_type' => 'A+',
        ]);
    }

    /** @test */
    public function user_can_view_public_profile_of_another_user()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create([
            'first_name' => 'María',
            'last_name' => 'López',
        ]);

        UserProfile::factory()->create([
            'user_id' => $otherUser->id,
            'nickname' => 'Mari',
            'bio' => 'Árbitra de voleibol',
        ]);

        Sanctum::actingAs($user);

        $response = $this->getJson("/api/v1/users/{$otherUser->id}/profile");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'id',
                    'name',
                    'first_name',
                    'last_name',
                    'user_type',
                    'profile' => [
                        'nickname',
                        'bio',
                        'avatar_url',
                    ],
                    'location'
                ]
            ])
            ->assertJson([
                'success' => true,
                'data' => [
                    'first_name' => 'María',
                    'last_name' => 'López',
                    'profile' => [
                        'nickname' => 'Mari',
                        'bio' => 'Árbitra de voleibol',
                    ]
                ]
            ]);

        // Verificar que no se muestran datos privados
        $response->assertJsonMissing([
            'email',
            'phone',
            'emergency_contact_name',
        ]);
    }

    /** @test */
    public function unauthenticated_user_cannot_access_profile_endpoints()
    {
        $response = $this->getJson('/api/v1/users/profile');
        $response->assertStatus(401);

        $response = $this->putJson('/api/v1/users/profile', []);
        $response->assertStatus(401);

        $response = $this->getJson('/api/v1/users/1/profile');
        $response->assertStatus(401);
    }

    /** @test */
    public function profile_update_validates_input()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $invalidData = [
            'blood_type' => 'INVALID',
            't_shirt_size' => 'XXXL',
            'first_name' => str_repeat('a', 101), // Too long
        ];

        $response = $this->putJson('/api/v1/users/profile', $invalidData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'blood_type',
                't_shirt_size',
                'first_name',
            ]);
    }
}