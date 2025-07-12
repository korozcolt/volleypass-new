<?php
// tests/Feature/FilamentCompatibilityTest.php - CORREGIR

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FilamentCompatibilityTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function filament_admin_still_works_after_sanctum_installation()
    {
        // ✅ CORRECCIÓN: Type hint explícito y crear usuario individual
        /** @var User $admin */
        $admin = User::factory()->create([
            'email' => 'admin@test.com',
            'password' => bcrypt('password')
        ]);

        $admin->assignRole('SuperAdmin');

        // Login web (Filament) debe seguir funcionando
        $response = $this->actingAs($admin, 'web') // ✅ Especificar guard web
            ->get('/admin');

        $response->assertStatus(200);
    }

    /** @test */
    public function sanctum_api_works_independently()
    {
        /** @var User $verifier */
        $verifier = User::factory()->create([
            'email' => 'verifier@test.com',
            'password' => bcrypt('password')
        ]);

        $verifier->assignRole('Verifier');

        // API login debe funcionar independiente
        $response = $this->postJson('/api/v1/auth/login', [
            'email' => $verifier->email,
            'password' => 'password',
            'device_name' => 'Test Device'
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'token',
                'token_type',
                'expires_in',
                'user' => [
                    'id',
                    'name',
                    'email',
                    'roles',
                    'abilities'
                ]
            ]);
    }

    /** @test */
    public function both_authentication_systems_work_simultaneously()
    {
        /** @var User $user */
        $user = User::factory()->create([
            'email' => 'dual@test.com',
            'password' => bcrypt('password')
        ]);

        $user->assignRole(['SuperAdmin', 'Verifier']);

        // Usuario puede estar logueado en Filament (web guard)
        $this->actingAs($user, 'web');

        // Y también obtener token API
        $response = $this->postJson('/api/v1/auth/login', [
            'email' => $user->email,
            'password' => 'password',
            'device_name' => 'Test Device'
        ]);

        $response->assertStatus(200);

        // Ambas sesiones son independientes
        $this->assertAuthenticated('web'); // Filament session

        // Verificar que el token fue creado
        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_id' => $user->id,
            'tokenable_type' => User::class,
            'name' => 'Test Device'
        ]);
    }

    /** @test */
    public function api_token_logout_does_not_affect_web_session()
    {
        /** @var User $user */
        $user = User::factory()->create([
            'password' => bcrypt('password')
        ]);

        $user->assignRole(['SuperAdmin', 'Verifier']);

        // Login en ambos sistemas
        $this->actingAs($user, 'web');

        $tokenResponse = $this->postJson('/api/v1/auth/login', [
            'email' => $user->email,
            'password' => 'password',
            'device_name' => 'Test Device'
        ]);

        $token = $tokenResponse->json('token');

        // Logout API
        $logoutResponse = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/v1/auth/logout');

        $logoutResponse->assertStatus(200);

        // Web session debe seguir activa
        $this->assertAuthenticated('web');

        // Pero token API debe estar revocado
        $this->assertDatabaseMissing('personal_access_tokens', [
            'tokenable_id' => $user->id,
            'name' => 'Test Device'
        ]);
    }
}
