<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class SetupRoleSystem extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'roles:setup {--verify : Only verify the current setup}';

    /**
     * The console command description.
     */
    protected $description = 'Setup and verify the role system configuration';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🔐 Verificando sistema de roles...');

        if ($this->option('verify')) {
            return $this->verifySetup();
        }

        return $this->setupRoles();
    }

    private function verifySetup(): int
    {
        $errors = [];

        // Verificar roles requeridos
        $requiredRoles = [
            'SuperAdmin', 'LeagueAdmin', 'ClubDirector', 
            'Coach', 'SportsDoctor', 'Referee', 'Player', 'Verifier'
        ];

        foreach ($requiredRoles as $roleName) {
            if (!Role::where('name', $roleName)->exists()) {
                $errors[] = "Rol faltante: {$roleName}";
            }
        }

        // Verificar permisos críticos
        $criticalPermissions = [
            'system.access_admin', 'users.view', 'players.view',
            'clubs.view', 'tournaments.view', 'medical.view'
        ];

        foreach ($criticalPermissions as $permission) {
            if (!Permission::where('name', $permission)->exists()) {
                $errors[] = "Permiso faltante: {$permission}";
            }
        }

        // Verificar que SuperAdmin tenga todos los permisos
        $superAdmin = Role::where('name', 'SuperAdmin')->first();
        if ($superAdmin) {
            $totalPermissions = Permission::count();
            $superAdminPermissions = $superAdmin->permissions()->count();
            
            if ($totalPermissions !== $superAdminPermissions) {
                $errors[] = "SuperAdmin no tiene todos los permisos ({$superAdminPermissions}/{$totalPermissions})";
            }
        }

        // Verificar usuarios sin roles
        $usersWithoutRoles = User::doesntHave('roles')->count();
        if ($usersWithoutRoles > 0) {
            $this->warn("⚠️  {$usersWithoutRoles} usuarios sin roles asignados");
        }

        if (empty($errors)) {
            $this->info('✅ Sistema de roles configurado correctamente');
            $this->displayStats();
            return 0;
        }

        $this->error('❌ Errores encontrados:');
        foreach ($errors as $error) {
            $this->line("  - {$error}");
        }

        return 1;
    }

    private function setupRoles(): int
    {
        $this->info('🚀 Configurando sistema de roles...');

        try {
            DB::beginTransaction();

            // Ejecutar seeder
            $this->call('db:seed', ['--class' => 'RolesAndPermissionsSeeder']);

            DB::commit();

            $this->info('✅ Sistema de roles configurado exitosamente');
            $this->displayStats();

            return 0;
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("❌ Error al configurar roles: {$e->getMessage()}");
            return 1;
        }
    }

    private function displayStats(): void
    {
        $this->info('\n📊 Estadísticas del sistema:');
        
        $roles = Role::withCount('users')->get();
        $this->table(
            ['Rol', 'Usuarios'],
            $roles->map(fn($role) => [$role->name, $role->users_count])
        );

        $totalPermissions = Permission::count();
        $this->info("\n🔑 Total de permisos: {$totalPermissions}");
    }
}