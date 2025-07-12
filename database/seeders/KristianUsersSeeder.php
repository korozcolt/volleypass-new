<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\Country;
use App\Models\Department;
use App\Models\City;
use Spatie\Permission\Models\Role;

class KristianUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('👤 Creando usuarios de prueba para Kristian Orozco...');

        // Obtener ubicación por defecto (Colombia, Sucre, Sincelejo)
        $colombia = Country::where('code', 'CO')->first();
        $sucre = Department::where('code', 'SUC')->first();
        $sincelejo = City::where('name', 'Sincelejo')->first();

        // Datos base del usuario
        $baseUserData = [
            'first_name' => 'Kristian',
            'last_name' => 'Orozco',
            'document_type' => 'cedula',
            'document_number' => '1088123456', // Base, se incrementará
            'birth_date' => '1990-05-15',
            'gender' => 'male',
            'phone' => '+57 300 123 4567',
            'address' => 'Carrera 25 #16-50',
            'country_id' => $colombia?->id,
            'department_id' => $sucre?->id,
            'city_id' => $sincelejo?->id,
            'status' => 'active',
            'email_verified_at' => now(),
            'password' => Hash::make('Admin123'),
            'created_by' => 1,
        ];

        // Datos base del perfil
        $baseProfileData = [
            'nickname' => 'Kristian',
            'bio' => 'Usuario de prueba del sistema VolleyPass',
            'joined_date' => now()->subYears(2),
            'blood_type' => 'O+',
            'emergency_contact_name' => 'María Orozco',
            'emergency_contact_phone' => '+57 300 987 6543',
            'emergency_contact_relationship' => 'Hermana',
            't_shirt_size' => 'L',
            'show_phone' => true,
            'show_email' => true,
            'show_address' => false,
        ];

        // Roles a crear
        $rolesToCreate = [
            [
                'role' => 'SuperAdmin',
                'email' => 'ing.korozco+admin@gmail.com',
                'document' => '1088123456',
                'bio' => 'Administrador del sistema VolleyPass'
            ],
            [
                'role' => 'LeagueAdmin',
                'email' => 'ing.korozco+liga@gmail.com',
                'document' => '1088123457',
                'bio' => 'Administrador de la Liga de Voleibol de Sucre'
            ],
            [
                'role' => 'ClubDirector',
                'email' => 'ing.korozco+director@gmail.com',
                'document' => '1088123458',
                'bio' => 'Director de Club de Voleibol'
            ],
            [
                'role' => 'Player',
                'email' => 'ing.korozco+jugador@gmail.com',
                'document' => '1088123459',
                'bio' => 'Jugadora de voleibol categoría mayores'
            ],
            [
                'role' => 'Coach',
                'email' => 'ing.korozco+entrenador@gmail.com',
                'document' => '1088123460',
                'bio' => 'Entrenador certificado de voleibol'
            ],
            [
                'role' => 'SportsDoctor',
                'email' => 'ing.korozco+medico@gmail.com',
                'document' => '1088123461',
                'bio' => 'Médico deportivo especializado en voleibol'
            ],
            [
                'role' => 'Verifier',
                'email' => 'ing.korozco+verificador@gmail.com',
                'document' => '1088123462',
                'bio' => 'Verificador de carnets en eventos deportivos'
            ]
        ];

        foreach ($rolesToCreate as $userData) {
            $this->command->info("Creando usuario con rol: {$userData['role']}");

            // Verificar si el rol existe
            $role = Role::where('name', $userData['role'])->first();
            if (!$role) {
                $this->command->warn("⚠️ Rol {$userData['role']} no encontrado. Saltando...");
                continue;
            }

            // Verificar si el usuario ya existe
            $existingUser = User::where('email', $userData['email'])->first();
            if ($existingUser) {
                $this->command->warn("⚠️ Usuario {$userData['email']} ya existe. Saltando...");
                continue;
            }

            // Verificar si el documento ya existe
            $existingDoc = User::where('document_number', $userData['document'])->first();
            if ($existingDoc) {
                $this->command->warn("⚠️ Documento {$userData['document']} ya existe. Saltando...");
                continue;
            }

            // Crear el usuario
            $user = User::create(array_merge($baseUserData, [
                'name' => 'Kristian Orozco', // Campo name requerido por Laravel
                'email' => $userData['email'],
                'document_number' => $userData['document'],
            ]));

            // Asignar el rol (sin teams)
            $user->assignRole($userData['role']);

            // Verificar si ya tiene perfil antes de crear
            $existingProfile = UserProfile::where('user_id', $user->id)->first();
            if (!$existingProfile) {
                // Crear el perfil
                UserProfile::create(array_merge($baseProfileData, [
                    'user_id' => $user->id,
                    'bio' => $userData['bio'],
                    'created_by' => $user->id,
                    'updated_by' => $user->id,
                ]));
            }

            $this->command->info("✅ Usuario creado: {$userData['email']} con rol {$userData['role']}");
        }

        $this->command->info('🎉 Todos los usuarios de prueba creados exitosamente');
        $this->command->info('');
        $this->command->info('📝 Datos de acceso:');
        $this->command->info('Contraseña para todos: Admin123');
        $this->command->info('');
        $this->command->table(
            ['Rol', 'Email', 'Documento'],
            collect($rolesToCreate)->map(fn($user) => [
                $user['role'],
                $user['email'],
                $user['document']
            ])->toArray()
        );
    }
}
