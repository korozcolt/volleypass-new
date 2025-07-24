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
        
        // Crear datos geográficos básicos si no existen
        $this->createBasicGeographicData();
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

    /**
     * Crear datos geográficos básicos para tests
     */
    protected function createBasicGeographicData(): void
    {
        // Solo crear si las tablas existen (evita errores en tests que no usan base de datos)
        if (!Schema::hasTable('countries') || !Schema::hasTable('departments') || !Schema::hasTable('cities')) {
            return;
        }

        // Crear país Colombia si no existe
        $country = \App\Models\Country::firstOrCreate(
            ['code' => 'CO'],
            [
                'name' => 'Colombia',
                'phone_code' => '+57',
                'currency_code' => 'COP',
                'is_active' => true,
            ]
        );

        // Crear departamento de prueba si no existe
        $department = \App\Models\Department::firstOrCreate(
            [
                'country_id' => $country->id,
                'code' => 'TEST'
            ],
            [
                'name' => 'Test Department',
                'is_active' => true,
            ]
        );

        // Crear ciudad de prueba si no existe
        \App\Models\City::firstOrCreate(
            [
                'department_id' => $department->id,
                'code' => 'TEST'
            ],
            [
                'name' => 'Test City',
                'postal_code' => '00000',
                'is_active' => true,
            ]
        );
    }
}
