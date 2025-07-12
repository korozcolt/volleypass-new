<?php
// tests/TestCase.php - ACTUALIZAR

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

abstract class TestCase extends BaseTestCase
{
    /**
     * Setup básico para todos los tests
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Crear roles y permisos básicos si no existen
        $this->createBasicRolesAndPermissions();
    }

    /**
     * Crear roles y permisos básicos para tests
     */
    protected function createBasicRolesAndPermissions(): void
    {
        // Solo crear si la tabla existe (evita errores en tests que no usan base de datos)
        if (!Schema::hasTable('roles')) {
            return;
        }

        // Roles básicos
        $roles = [
            'SuperAdmin',
            'LeagueAdmin',
            'ClubDirector',
            'Verifier',
            'Player',
            'MedicalStaff',
            'Coach'
        ];

        foreach ($roles as $roleName) {
            Role::firstOrCreate([
                'name' => $roleName,
                'guard_name' => 'web'
            ]);
        }

        // Permisos básicos para API
        $permissions = [
            'verify:qr',
            'verify:batch',
            'view:stats',
            'view:reports',
            'manage:events',
            'invalidate:cache',
            'view:own-stats',
            'manage:scanner-session'
        ];

        foreach ($permissions as $permissionName) {
            Permission::firstOrCreate([
                'name' => $permissionName,
                'guard_name' => 'web'
            ]);
        }
    }

    /**
     * Helper para crear usuario con rol específico
     */
    protected function createUserWithRole(string $role): \App\Models\User
    {
        /** @var \App\Models\User $user */
        $user = \App\Models\User::factory()->create();
        $user->assignRole($role);

        return $user;
    }

    /**
     * Helper para crear token de API
     */
    protected function createApiToken(\App\Models\User $user, array $abilities = ['*']): string
    {
        return $user->createToken('Test Token', $abilities)->plainTextToken;
    }
}
