<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Services\RoleRedirectionService;

class RoleSystemTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Crear roles y permisos básicos
        $this->artisan('db:seed', ['--class' => 'RolesAndPermissionsSeeder']);
    }

    /** @test */
    public function super_admin_can_access_all_resources()
    {
        $user = User::factory()->create();
        $user->assignRole('SuperAdmin');
        
        $this->assertTrue($user->canAccessAdminPanel());
        $this->assertTrue($user->isSystemAdmin());
        $this->assertEquals('SuperAdmin', $user->getPrimaryRole());
    }

    /** @test */
    public function league_admin_has_correct_permissions()
    {
        $user = User::factory()->create();
        $user->assignRole('LeagueAdmin');
        
        $this->assertTrue($user->canAccessAdminPanel());
        $this->assertTrue($user->isLeagueAdmin());
        $this->assertFalse($user->isSystemAdmin());
        $this->assertEquals('LeagueAdmin', $user->getPrimaryRole());
    }

    /** @test */
    public function club_director_has_limited_access()
    {
        $user = User::factory()->create();
        $user->assignRole('ClubDirector');
        
        $this->assertTrue($user->canAccessAdminPanel());
        $this->assertTrue($user->isClubDirector());
        $this->assertFalse($user->isSystemAdmin());
        $this->assertEquals('ClubDirector', $user->getPrimaryRole());
    }

    /** @test */
    public function player_cannot_access_admin_panel()
    {
        $user = User::factory()->create();
        $user->assignRole('Player');
        
        $this->assertFalse($user->canAccessAdminPanel());
        $this->assertTrue($user->isPlayer());
        $this->assertEquals('Player', $user->getPrimaryRole());
    }

    /** @test */
    public function referee_cannot_access_admin_panel()
    {
        $user = User::factory()->create();
        $user->assignRole('Referee');
        
        $this->assertFalse($user->canAccessAdminPanel());
        $this->assertTrue($user->isReferee());
        $this->assertEquals('Referee', $user->getPrimaryRole());
    }

    /** @test */
    public function role_hierarchy_works_correctly()
    {
        $user = User::factory()->create();
        $user->assignRole(['Player', 'Coach', 'ClubDirector']);
        
        // Debe devolver el rol de mayor jerarquía
        $this->assertEquals('ClubDirector', $user->getPrimaryRole());
    }

    /** @test */
    public function navigation_groups_are_filtered_by_role()
    {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('SuperAdmin');
        
        $player = User::factory()->create();
        $player->assignRole('Player');
        
        $clubDirector = User::factory()->create();
        $clubDirector->assignRole('ClubDirector');
        
        // SuperAdmin debe tener acceso a todos los grupos
        $this->assertCount(5, $superAdmin->getAllowedNavigationGroups());
        
        // Player no debe tener grupos de navegación
        $this->assertCount(0, $player->getAllowedNavigationGroups());
        
        // ClubDirector debe tener acceso limitado
        $this->assertCount(2, $clubDirector->getAllowedNavigationGroups());
    }

    /** @test */
    public function post_login_redirection_works_correctly()
    {
        $admin = User::factory()->create();
        $admin->assignRole('SuperAdmin');
        
        $player = User::factory()->create();
        $player->assignRole('Player');
        
        $this->assertEquals('/admin', $admin->getPostLoginRedirectUrl());
        $this->assertEquals(route('dashboard'), $player->getPostLoginRedirectUrl());
    }

    /** @test */
    public function role_redirection_service_works()
    {
        $admin = User::factory()->create();
        $admin->assignRole('SuperAdmin');
        
        $player = User::factory()->create();
        $player->assignRole('Player');
        
        $this->assertEquals('/admin', RoleRedirectionService::getRedirectUrl($admin));
        $this->assertEquals(route('dashboard'), RoleRedirectionService::getRedirectUrl($player));
    }

    /** @test */
    public function user_can_manage_permissions_work()
    {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('SuperAdmin');
        
        $leagueAdmin = User::factory()->create();
        $leagueAdmin->assignRole('LeagueAdmin');
        
        $player = User::factory()->create();
        $player->assignRole('Player');
        
        // SuperAdmin puede gestionar a todos
        $this->assertTrue($superAdmin->canManageUser($leagueAdmin));
        $this->assertTrue($superAdmin->canManageUser($player));
        
        // LeagueAdmin puede gestionar jugadores pero no otros admins
        $this->assertTrue($leagueAdmin->canManageUser($player));
        $this->assertFalse($leagueAdmin->canManageUser($superAdmin));
        
        // Player no puede gestionar a nadie
        $this->assertFalse($player->canManageUser($superAdmin));
        $this->assertFalse($player->canManageUser($leagueAdmin));
    }

    /** @test */
    public function sports_doctor_has_medical_permissions()
    {
        $doctor = User::factory()->create();
        $doctor->assignRole('SportsDoctor');
        
        $this->assertTrue($doctor->canAccessAdminPanel());
        $this->assertTrue($doctor->isSportsDoctor());
        $this->assertTrue($doctor->can('medical.view'));
        $this->assertTrue($doctor->can('medical.create'));
        $this->assertTrue($doctor->can('medical.approve_certificates'));
    }

    /** @test */
    public function verifier_has_verification_permissions()
    {
        $verifier = User::factory()->create();
        $verifier->assignRole('Verifier');
        
        $this->assertTrue($verifier->canAccessAdminPanel());
        $this->assertTrue($verifier->isVerifier());
        $this->assertTrue($verifier->can('players.approve_documents'));
    }
}