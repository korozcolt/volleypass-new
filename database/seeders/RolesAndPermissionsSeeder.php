<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🔐 Creando roles y permisos con Spatie Permission...');

        // Resetear cached roles y permisos
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Crear permisos
        $permissions = [
            // Usuarios
            'users.view', 'users.create', 'users.edit', 'users.delete',

            // Jugadoras
            'players.view', 'players.create', 'players.edit', 'players.delete',
            'players.approve_documents', 'players.manage_cards',

            // Clubes
            'clubs.view', 'clubs.create', 'clubs.edit', 'clubs.delete',
            'clubs.manage_members',

            // Ligas
            'leagues.view', 'leagues.create', 'leagues.edit', 'leagues.delete',
            'leagues.manage_clubs',

            // Torneos
            'tournaments.view', 'tournaments.create', 'tournaments.edit', 'tournaments.delete',
            'tournaments.manage_registrations',

            // Médico
            'medical.view', 'medical.create', 'medical.edit', 'medical.delete',
            'medical.approve_certificates',

            // Sistema
            'system.access_admin', 'system.manage_settings', 'system.view_logs',
            'system.manage_backups',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Crear roles
        $superAdmin = Role::firstOrCreate(['name' => 'SuperAdmin']);
        $leagueAdmin = Role::firstOrCreate(['name' => 'LeagueAdmin']);
        $clubDirector = Role::firstOrCreate(['name' => 'ClubDirector']);
        $player = Role::firstOrCreate(['name' => 'Player']);
        $coach = Role::firstOrCreate(['name' => 'Coach']);
        $sportsDoctor = Role::firstOrCreate(['name' => 'SportsDoctor']);
        $verifier = Role::firstOrCreate(['name' => 'Verifier']);
        $referee = Role::firstOrCreate(['name' => 'Referee']);

        // Asignar permisos a roles
        $superAdmin->givePermissionTo(Permission::all());

        $leagueAdmin->givePermissionTo([
            'users.view', 'users.edit',
            'players.view', 'players.create', 'players.edit',
            'players.approve_documents', 'players.manage_cards',
            'clubs.view', 'clubs.edit', 'clubs.manage_members',
            'tournaments.view', 'tournaments.create', 'tournaments.edit',
            'medical.view', 'medical.approve_certificates',
            'system.access_admin',
        ]);

        $clubDirector->givePermissionTo([
            'users.view',
            'players.view', 'players.create', 'players.edit',
            'clubs.view', 'clubs.edit',
            'tournaments.view',
            'medical.view',
        ]);

        $sportsDoctor->givePermissionTo([
            'players.view',
            'medical.view', 'medical.create', 'medical.edit',
            'medical.approve_certificates',
        ]);

        $this->command->info('✅ Roles y permisos creados exitosamente');
    }
}

