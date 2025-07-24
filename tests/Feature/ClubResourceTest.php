<?php

namespace Tests\Feature;

use App\Models\Club;
use App\Models\User;
use App\Models\Department;
use App\Models\City;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Livewire\Livewire;
use App\Filament\Resources\ClubResource;
use App\Filament\Resources\ClubResource\Pages\ListClubs;
use App\Filament\Resources\ClubResource\Pages\CreateClub;
use App\Filament\Resources\ClubResource\Pages\EditClub;
use App\Filament\Resources\ClubResource\Pages\ViewClub;

class ClubResourceTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User $adminUser;
    protected User $coordinatorUser;
    protected User $directorUser;
    protected Department $department;
    protected City $city;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Crear departamento y ciudad
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
        
        // Crear usuarios de prueba
        $this->adminUser = User::factory()->create([
            'rol' => 'admin',
            'email' => 'admin@test.com'
        ]);
        
        $this->coordinatorUser = User::factory()->create([
            'rol' => 'coordinador',
            'departamento_id' => $this->department->id,
            'email' => 'coordinator@test.com'
        ]);
        
        $this->directorUser = User::factory()->create([
            'rol' => 'director',
            'email' => 'director@test.com'
        ]);
    }

    /** @test */
    public function admin_can_view_clubs_list(): void
    {
        $this->actingAs($this->adminUser);
        
        Club::factory()->count(3)->create([
            'departamento_id' => $this->department->id,
            'ciudad_id' => $this->city->id
        ]);
        
        Livewire::test(ListClubs::class)
            ->assertSuccessful()
            ->assertCanSeeTableRecords(Club::all());
    }

    /** @test */
    public function admin_can_create_club(): void
    {
        $this->actingAs($this->adminUser);
        
        $clubData = [
            'nombre' => 'Club Test',
            'nombre_corto' => 'CT',
            'email' => 'club@test.com',
            'telefono' => '1234567890',
            'direccion' => 'Test Address',
            'departamento_id' => $this->department->id,
            'ciudad_id' => $this->city->id,
            'fundacion' => '2020-01-01',
            'es_federado' => true,
            'tipo_federacion' => 'departamental',
        ];
        
        Livewire::test(CreateClub::class)
            ->fillForm($clubData)
            ->call('create')
            ->assertHasNoFormErrors();
        
        $this->assertDatabaseHas('clubs', [
            'nombre' => 'Club Test',
            'email' => 'club@test.com',
            'es_federado' => true,
        ]);
        
        $club = Club::where('nombre', 'Club Test')->first();
        $this->assertNotNull($club->codigo_federacion);
    }

    /** @test */
    public function admin_can_edit_club(): void
    {
        $this->actingAs($this->adminUser);
        
        $club = Club::factory()->create([
            'nombre' => 'Original Name',
            'departamento_id' => $this->department->id,
            'ciudad_id' => $this->city->id
        ]);
        
        Livewire::test(EditClub::class, ['record' => $club->getRouteKey()])
            ->fillForm([
                'nombre' => 'Updated Name',
                'email' => 'updated@test.com',
            ])
            ->call('save')
            ->assertHasNoFormErrors();
        
        $this->assertDatabaseHas('clubs', [
            'id' => $club->id,
            'nombre' => 'Updated Name',
            'email' => 'updated@test.com',
        ]);
    }

    /** @test */
    public function admin_can_view_club_details(): void
    {
        $this->actingAs($this->adminUser);
        
        $club = Club::factory()->create([
            'departamento_id' => $this->department->id,
            'ciudad_id' => $this->city->id
        ]);
        
        Livewire::test(ViewClub::class, ['record' => $club->getRouteKey()])
            ->assertSuccessful()
            ->assertSee($club->nombre)
            ->assertSee($club->email);
    }

    /** @test */
    public function admin_can_delete_club(): void
    {
        $this->actingAs($this->adminUser);
        
        $club = Club::factory()->create([
            'departamento_id' => $this->department->id,
            'ciudad_id' => $this->city->id
        ]);
        
        Livewire::test(EditClub::class, ['record' => $club->getRouteKey()])
            ->callAction('delete')
            ->assertSuccessful();
        
        $this->assertSoftDeleted('clubs', ['id' => $club->id]);
    }

    /** @test */
    public function coordinator_can_manage_clubs_in_their_department(): void
    {
        $this->actingAs($this->coordinatorUser);
        
        $clubInDepartment = Club::factory()->create([
            'departamento_id' => $this->department->id,
            'ciudad_id' => $this->city->id
        ]);
        
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
        $clubInOtherDepartment = Club::factory()->create([
            'departamento_id' => $otherDepartment->id,
            'ciudad_id' => $otherCity->id
        ]);
        
        Livewire::test(ListClubs::class)
            ->assertCanSeeTableRecords([$clubInDepartment])
            ->assertCanNotSeeTableRecords([$clubInOtherDepartment]);
    }

    /** @test */
    public function director_can_only_view_their_club(): void
    {
        $this->actingAs($this->directorUser);
        
        $directorClub = Club::factory()->create([
            'departamento_id' => $this->department->id,
            'ciudad_id' => $this->city->id
        ]);
        
        // Asociar director al club
        $directorClub->directivos()->attach($this->directorUser->id, [
            'role' => 'presidente',
            'is_active' => true,
            'start_date' => now(),
        ]);
        
        $otherClub = Club::factory()->create([
            'departamento_id' => $this->department->id,
            'ciudad_id' => $this->city->id
        ]);
        
        Livewire::test(ListClubs::class)
            ->assertCanSeeTableRecords([$directorClub])
            ->assertCanNotSeeTableRecords([$otherClub]);
    }

    /** @test */
    public function can_filter_clubs_by_federation_status(): void
    {
        $this->actingAs($this->adminUser);
        
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
        
        Livewire::test(ListClubs::class)
            ->filterTable('es_federado', true)
            ->assertCanSeeTableRecords([$federatedClub])
            ->assertCanNotSeeTableRecords([$nonFederatedClub]);
    }

    /** @test */
    public function can_search_clubs_by_name(): void
    {
        $this->actingAs($this->adminUser);
        
        $club1 = Club::factory()->create([
            'nombre' => 'Volleyball Club',
            'departamento_id' => $this->department->id,
            'ciudad_id' => $this->city->id
        ]);
        
        $club2 = Club::factory()->create([
            'nombre' => 'Basketball Club',
            'departamento_id' => $this->department->id,
            'ciudad_id' => $this->city->id
        ]);
        
        Livewire::test(ListClubs::class)
            ->searchTable('Volleyball')
            ->assertCanSeeTableRecords([$club1])
            ->assertCanNotSeeTableRecords([$club2]);
    }

    /** @test */
    public function can_perform_bulk_federation_action(): void
    {
        $this->actingAs($this->adminUser);
        
        $clubs = Club::factory()->count(3)->create([
            'es_federado' => false,
            'departamento_id' => $this->department->id,
            'ciudad_id' => $this->city->id
        ]);
        
        Livewire::test(ListClubs::class)
            ->selectTableRecords($clubs->pluck('id')->toArray())
            ->callTableBulkAction('federate', [
                'tipo_federacion' => 'departamental',
                'vencimiento_federacion' => now()->addYear()->format('Y-m-d'),
            ])
            ->assertSuccessful();
        
        foreach ($clubs as $club) {
            $this->assertDatabaseHas('clubs', [
                'id' => $club->id,
                'es_federado' => true,
                'tipo_federacion' => 'departamental',
            ]);
        }
    }

    /** @test */
    public function validates_required_fields_when_creating_club(): void
    {
        $this->actingAs($this->adminUser);
        
        Livewire::test(CreateClub::class)
            ->fillForm([
                'nombre' => '', // Required field empty
                'email' => 'invalid-email', // Invalid email
            ])
            ->call('create')
            ->assertHasFormErrors([
                'nombre' => 'required',
                'email' => 'email',
            ]);
    }

    /** @test */
    public function validates_unique_federation_code(): void
    {
        $this->actingAs($this->adminUser);
        
        $existingClub = Club::factory()->create([
            'codigo_federacion' => 'TEST2024001',
            'departamento_id' => $this->department->id,
            'ciudad_id' => $this->city->id
        ]);
        
        $clubData = [
            'nombre' => 'New Club',
            'email' => 'new@test.com',
            'departamento_id' => $this->department->id,
            'ciudad_id' => $this->city->id,
            'es_federado' => true,
            'codigo_federacion' => 'TEST2024001', // Duplicate code
        ];
        
        Livewire::test(CreateClub::class)
            ->fillForm($clubData)
            ->call('create')
            ->assertHasFormErrors(['codigo_federacion']);
    }

    /** @test */
    public function can_export_clubs_data(): void
    {
        $this->actingAs($this->adminUser);
        
        Club::factory()->count(5)->create([
            'departamento_id' => $this->department->id,
            'ciudad_id' => $this->city->id
        ]);
        
        Livewire::test(ListClubs::class)
            ->callTableBulkAction('export', [])
            ->assertSuccessful();
        
        // Verificar que se generó el archivo de exportación
        // Esto dependería de la implementación específica del export
    }
}