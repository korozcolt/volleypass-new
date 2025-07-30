<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\Country;
use App\Models\Department;
use App\Models\City;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class CleanSystemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🧹 Limpiando sistema y creando usuario SuperAdmin...');

        // Deshabilitar verificaciones de claves foráneas para SQLite
        DB::statement('PRAGMA foreign_keys = OFF;');

        // Limpiar tablas de datos (manteniendo configuraciones base)
        $this->cleanDataTables();

        // Crear usuario SuperAdmin
        $this->createSuperAdminUser();

        // Rehabilitar verificaciones de claves foráneas para SQLite
        DB::statement('PRAGMA foreign_keys = ON;');

        $this->command->info('✅ Sistema limpiado exitosamente. Solo queda el usuario SuperAdmin.');
    }

    private function cleanDataTables(): void
    {
        $this->command->info('🗑️ Eliminando datos de usuarios y entidades...');

        // Tablas a limpiar (manteniendo configuraciones del sistema)
        $tablesToClean = [
            'user_profiles',
            'model_has_roles',
            'model_has_permissions', 
            'users',
            'teams',
            'players',
            'coaches',
            'referees',
            'clubs',
            'leagues',
            'matches',
            'match_sets',
            'tournaments',
            'transfers',
            'injuries',
            'awards',
            'activities',
            'qr_scan_logs',
            'invoices',
            'payments',
            'data_backups'
        ];

        foreach ($tablesToClean as $table) {
            if (DB::getSchemaBuilder()->hasTable($table)) {
                DB::table($table)->delete();
                $this->command->info("  ✓ Tabla {$table} limpiada");
            }
        }
    }

    private function createSuperAdminUser(): void
    {
        $this->command->info('👤 Creando usuario SuperAdmin...');

        // Obtener ubicación por defecto (Colombia, Sucre, Sincelejo)
        $colombia = Country::where('code', 'CO')->first();
        $sucre = Department::where('code', 'SUC')->first();
        $sincelejo = City::where('name', 'Sincelejo')->first();

        // Crear o actualizar usuario SuperAdmin
        $user = User::updateOrCreate(
            ['email' => 'ing.korozco@gmail.com'],
            [
                'name' => 'Kristian Orozco',
                'first_name' => 'Kristian',
                'last_name' => 'Orozco',
                'document_type' => 'cedula',
                'document_number' => '1088123456',
                'birth_date' => '1990-05-15',
                'gender' => 'male',
                'phone' => '+57 300 123 4567',
                'address' => 'Carrera 25 #16-50',
                'country_id' => $colombia?->id,
                'department_id' => $sucre?->id,
                'city_id' => $sincelejo?->id,
                'status' => 'active',
                'email_verified_at' => now(),
                'password' => Hash::make('Q@10op29+'),
                'created_by' => 1,
            ]
        );

        // Asignar rol SuperAdmin
        $superAdminRole = Role::where('name', 'SuperAdmin')->first();
        if ($superAdminRole) {
            $user->assignRole($superAdminRole);
        }

        // Crear perfil de usuario si no existe
        if (!UserProfile::where('user_id', $user->id)->exists()) {
            UserProfile::create([
                'user_id' => $user->id,
                'nickname' => 'Kristian',
                'bio' => 'Administrador del sistema VolleyPass Soft',
                'joined_date' => now(),
                'blood_type' => 'O+',
                'emergency_contact_name' => 'Contacto de Emergencia',
                'emergency_contact_phone' => '+57 300 999 9999',
                'emergency_contact_relationship' => 'Familiar',
                't_shirt_size' => 'L',
                'show_phone' => true,
                'show_email' => true,
                'show_address' => false,
                'created_by' => $user->id,
            ]);
        }

        $this->command->info("✅ Usuario SuperAdmin creado: {$user->email}");
    }
}