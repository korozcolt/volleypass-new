<?php

namespace Tests\Feature\Federation;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;

class FederationTestSuite extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_run_complete_federation_system_test()
    {
        $this->artisan('migrate:fresh');

        // Test 1: Seeder funciona correctamente
        $this->artisan('db:seed', ['--class' => 'FederationTestSeeder'])
            ->assertExitCode(0);

        // Test 2: Comando de testing funciona
        $this->artisan('volleypass:test-federation')
            ->assertExitCode(0);

        // Test 3: Verificar que los datos se crearon correctamente
        $this->assertDatabaseHas('leagues', ['name' => 'Liga de Voleibol de Prueba']);
        $this->assertDatabaseHas('clubs', ['short_name' => 'ÁGU']);
        $this->assertDatabaseHas('players', ['jersey_number' => 1]);
        $this->assertDatabaseHas('payments', ['type' => 'federation']);

        // Test 4: Verificar estados de federación
        $this->assertDatabaseHas('players', ['federation_status' => 'federated']);
        $this->assertDatabaseHas('players', ['federation_status' => 'not_federated']);
        $this->assertDatabaseHas('players', ['federation_status' => 'pending_payment']);

        $this->assertTrue(true, 'Federation system test suite completed successfully');
    }

    /** @test */
    public function it_can_reset_federation_data()
    {
        // Crear algunos datos
        $this->artisan('db:seed', ['--class' => 'FederationTestSeeder']);

        // Reset
        $this->artisan('volleypass:test-federation', ['--reset'])
            ->assertExitCode(0);

        // Verificar que se reseteó
        $this->assertDatabaseMissing('players', ['federation_status' => 'federated']);
        $this->assertDatabaseHas('players', ['federation_status' => 'not_federated']);
    }

    /** @test */
    public function it_validates_system_integrity()
    {
        $this->artisan('db:seed', ['--class' => 'FederationTestSeeder']);

        // Verificar integridad de relaciones
        $players = \App\Models\Player::with(['user', 'currentClub', 'federationPayment'])->get();

        foreach ($players as $player) {
            $this->assertNotNull($player->user, "Player {$player->id} should have a user");
            $this->assertNotNull($player->currentClub, "Player {$player->id} should have a club");

            if ($player->federation_payment_id) {
                $this->assertNotNull($player->federationPayment, "Player {$player->id} should have federation payment");
            }
        }

        $clubs = \App\Models\Club::with(['league', 'director'])->get();

        foreach ($clubs as $club) {
            $this->assertNotNull($club->league, "Club {$club->id} should have a league");
            $this->assertNotNull($club->director, "Club {$club->id} should have a director");
        }

        $this->assertTrue(true, 'System integrity validated successfully');
    }
}
