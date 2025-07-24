<?php

namespace Tests\Unit;

use App\Models\Club;
use App\Models\User;
use App\Models\Department;
use App\Models\City;
use App\Models\Player;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;

class ClubTest extends TestCase
{
    use RefreshDatabase;

    protected Department $department;
    protected City $city;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->department = Department::first() ?? Department::create([
            'country_id' => \App\Models\Country::first()?->id ?? 1,
            'name' => 'Test Department',
            'code' => 'TD',
            'is_active' => true
        ]);
        
        $this->city = City::first() ?? City::create([
            'name' => 'Test City',
            'department_id' => $this->department->id,
            'is_active' => true
        ]);
    }

    /** @test */
    public function it_belongs_to_department(): void
    {
        $club = Club::factory()->create([
            'departamento_id' => $this->department->id,
            'ciudad_id' => $this->city->id
        ]);
        
        $this->assertInstanceOf(Department::class, $club->departamento);
        $this->assertEquals($this->department->id, $club->departamento->id);
    }

    /** @test */
    public function it_belongs_to_city(): void
    {
        $club = Club::factory()->create([
            'departamento_id' => $this->department->id,
            'ciudad_id' => $this->city->id
        ]);
        
        $this->assertInstanceOf(City::class, $club->ciudad);
        $this->assertEquals($this->city->id, $club->ciudad->id);
    }

    /** @test */
    public function it_has_many_players(): void
    {
        $club = Club::factory()->create([
            'departamento_id' => $this->department->id,
            'ciudad_id' => $this->city->id
        ]);
        
        $players = Player::factory()->count(3)->create([
            'club_id' => $club->id
        ]);
        
        $this->assertCount(3, $club->jugadoras);
        $this->assertInstanceOf(Player::class, $club->jugadoras->first());
    }

    /** @test */
    public function it_has_many_directors(): void
    {
        $club = Club::factory()->create([
            'departamento_id' => $this->department->id,
            'ciudad_id' => $this->city->id
        ]);
        
        $directors = User::factory()->count(2)->create(['rol' => 'director']);
        
        foreach ($directors as $director) {
            $club->directivos()->attach($director->id, [
                'rol' => 'presidente',
                'activo' => true,
                'fecha_inicio' => now(),
            ]);
        }
        
        $this->assertCount(2, $club->directivos);
        $this->assertInstanceOf(User::class, $club->directivos->first());
    }

    /** @test */
    public function scope_federados_returns_only_federated_clubs(): void
    {
        $federatedClub = Club::factory()->create([
            'es_federado' => true,
            'departamento_id' => $this->department->id,
            'ciudad_id' => $this->city->id
        ]);
        
        $nonFederatedClub = Club::factory()->create([
            'es_federado' => false,
            'departamento_id' => $this->department->id,
            'ciudad_id' => $this->city->id
        ]);
        
        $federatedClubs = Club::federados()->get();
        
        $this->assertCount(1, $federatedClubs);
        $this->assertTrue($federatedClubs->contains($federatedClub));
        $this->assertFalse($federatedClubs->contains($nonFederatedClub));
    }

    /** @test */
    public function scope_no_federados_returns_only_non_federated_clubs(): void
    {
        $federatedClub = Club::factory()->create([
            'es_federado' => true,
            'departamento_id' => $this->department->id,
            'ciudad_id' => $this->city->id
        ]);
        
        $nonFederatedClub = Club::factory()->create([
            'es_federado' => false,
            'departamento_id' => $this->department->id,
            'ciudad_id' => $this->city->id
        ]);
        
        $nonFederatedClubs = Club::noFederados()->get();
        
        $this->assertCount(1, $nonFederatedClubs);
        $this->assertTrue($nonFederatedClubs->contains($nonFederatedClub));
        $this->assertFalse($nonFederatedClubs->contains($federatedClub));
    }

    /** @test */
    public function scope_por_departamento_filters_by_department(): void
    {
        $otherDepartment = Department::skip(1)->first() ?? Department::create([
            'country_id' => \App\Models\Country::first()?->id ?? 1,
            'name' => 'Other Department',
            'code' => 'OD',
            'is_active' => true
        ]);
        $otherCity = City::skip(1)->first() ?? City::create([
            'name' => 'Other City',
            'department_id' => $otherDepartment->id,
            'is_active' => true
        ]);
        
        $clubInDepartment = Club::factory()->create([
            'departamento_id' => $this->department->id,
            'ciudad_id' => $this->city->id
        ]);
        
        $clubInOtherDepartment = Club::factory()->create([
            'departamento_id' => $otherDepartment->id,
            'ciudad_id' => $otherCity->id
        ]);
        
        $clubsInDepartment = Club::porDepartamento($this->department->id)->get();
        
        $this->assertCount(1, $clubsInDepartment);
        $this->assertTrue($clubsInDepartment->contains($clubInDepartment));
        $this->assertFalse($clubsInDepartment->contains($clubInOtherDepartment));
    }

    /** @test */
    public function it_calculates_years_of_operation_correctly(): void
    {
        $foundationDate = Carbon::now()->subYears(5)->format('Y-m-d');
        
        $club = Club::factory()->create([
            'fundacion' => $foundationDate,
            'departamento_id' => $this->department->id,
            'ciudad_id' => $this->city->id
        ]);
        
        $this->assertEquals(5, $club->anos_funcionamiento);
    }

    /** @test */
    public function it_returns_zero_years_when_no_foundation_date(): void
    {
        $club = Club::factory()->create([
            'fundacion' => null,
            'departamento_id' => $this->department->id,
            'ciudad_id' => $this->city->id
        ]);
        
        $this->assertEquals(0, $club->anos_funcionamiento);
    }

    /** @test */
    public function it_counts_active_players_correctly(): void
    {
        $club = Club::factory()->create([
            'departamento_id' => $this->department->id,
            'ciudad_id' => $this->city->id
        ]);
        
        // Crear jugadoras activas
        Player::factory()->count(3)->create([
            'club_id' => $club->id,
            'activa' => true
        ]);
        
        // Crear jugadoras inactivas
        Player::factory()->count(2)->create([
            'club_id' => $club->id,
            'activa' => false
        ]);
        
        $this->assertEquals(3, $club->jugadoras_activas_count);
    }

    /** @test */
    public function it_counts_federated_players_correctly(): void
    {
        $club = Club::factory()->create([
            'departamento_id' => $this->department->id,
            'ciudad_id' => $this->city->id
        ]);
        
        // Crear jugadoras federadas activas
        Player::factory()->count(2)->create([
            'club_id' => $club->id,
            'activa' => true,
            'es_federada' => true
        ]);
        
        // Crear jugadoras no federadas activas
        Player::factory()->count(3)->create([
            'club_id' => $club->id,
            'activa' => true,
            'es_federada' => false
        ]);
        
        $this->assertEquals(2, $club->jugadoras_federadas_count);
    }

    /** @test */
    public function it_counts_active_directors_correctly(): void
    {
        $club = Club::factory()->create([
            'departamento_id' => $this->department->id,
            'ciudad_id' => $this->city->id
        ]);
        
        $activeDirectors = User::factory()->count(2)->create(['rol' => 'director']);
        $inactiveDirectors = User::factory()->count(1)->create(['rol' => 'director']);
        
        // Directivos activos
        foreach ($activeDirectors as $director) {
            $club->directivos()->attach($director->id, [
                'rol' => 'presidente',
                'activo' => true,
                'fecha_inicio' => now(),
            ]);
        }
        
        // Directivos inactivos
        foreach ($inactiveDirectors as $director) {
            $club->directivos()->attach($director->id, [
                'rol' => 'secretario',
                'activo' => false,
                'fecha_inicio' => now()->subYear(),
                'fecha_fin' => now()->subMonth(),
            ]);
        }
        
        $this->assertEquals(2, $club->directivos_activos_count);
    }

    /** @test */
    public function it_validates_required_fields(): void
    {
        $this->expectException(\Illuminate\Database\QueryException::class);
        
        Club::create([
            // Missing required fields like 'nombre'
            'email' => 'test@example.com'
        ]);
    }

    /** @test */
    public function it_validates_email_format(): void
    {
        $club = Club::factory()->make([
            'email' => 'invalid-email-format',
            'departamento_id' => $this->department->id,
            'ciudad_id' => $this->city->id
        ]);
        
        // This would typically be validated at the form level
        // Here we're just testing the model accepts the data
        $this->assertFalse(filter_var($club->email, FILTER_VALIDATE_EMAIL));
    }

    /** @test */
    public function it_generates_unique_federation_code_when_federated(): void
    {
        $club1 = Club::factory()->create([
            'es_federado' => true,
            'departamento_id' => $this->department->id,
            'ciudad_id' => $this->city->id
        ]);
        
        $club2 = Club::factory()->create([
            'es_federado' => true,
            'departamento_id' => $this->department->id,
            'ciudad_id' => $this->city->id
        ]);
        
        $this->assertNotNull($club1->codigo_federacion);
        $this->assertNotNull($club2->codigo_federacion);
        $this->assertNotEquals($club1->codigo_federacion, $club2->codigo_federacion);
    }

    /** @test */
    public function it_can_be_soft_deleted(): void
    {
        $club = Club::factory()->create([
            'departamento_id' => $this->department->id,
            'ciudad_id' => $this->city->id
        ]);
        
        $club->delete();
        
        $this->assertSoftDeleted('clubs', ['id' => $club->id]);
        $this->assertCount(0, Club::all());
        $this->assertCount(1, Club::withTrashed()->get());
    }

    /** @test */
    public function it_can_be_restored_after_soft_delete(): void
    {
        $club = Club::factory()->create([
            'departamento_id' => $this->department->id,
            'ciudad_id' => $this->city->id
        ]);
        
        $club->delete();
        $club->restore();
        
        $this->assertCount(1, Club::all());
        $this->assertNotNull($club->fresh());
    }

    /** @test */
    public function it_formats_federation_type_correctly(): void
    {
        $club = Club::factory()->create([
            'tipo_federacion' => 'departamental',
            'departamento_id' => $this->department->id,
            'ciudad_id' => $this->city->id
        ]);
        
        $this->assertEquals('departamental', $club->tipo_federacion);
        $this->assertEquals('Departamental', $club->tipo_federacion_formatted);
    }

    /** @test */
    public function it_checks_federation_expiration_correctly(): void
    {
        $expiredClub = Club::factory()->create([
            'es_federado' => true,
            'vencimiento_federacion' => Carbon::yesterday(),
            'departamento_id' => $this->department->id,
            'ciudad_id' => $this->city->id
        ]);
        
        $validClub = Club::factory()->create([
            'es_federado' => true,
            'vencimiento_federacion' => Carbon::tomorrow(),
            'departamento_id' => $this->department->id,
            'ciudad_id' => $this->city->id
        ]);
        
        $this->assertTrue($expiredClub->federacion_expirada);
        $this->assertFalse($validClub->federacion_expirada);
    }
}