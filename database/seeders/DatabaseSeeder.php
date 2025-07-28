<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('🚀 Iniciando seeders de VolleyPass...');

        $this->call([
            // 1. Primero las ubicaciones (países, departamentos, ciudades)
            ColombiaLocationsSeeder::class,
            
            // 2. Luego roles y permisos
            RolesAndPermissionsSeeder::class,
            
            // 3. Configuraciones del sistema
            SystemConfigurationSeeder::class,
            
            // 4. Usuarios básicos del sistema
            KristianUsersSeeder::class,
            
            // 5. Datos de prueba para federación
            FederationTestSeeder::class,
            
            // 6. Configuraciones específicas de ligas (después de crear ligas)
            LeagueConfigurationSeeder::class,
            
            // 7. Finalmente datos de ejemplo completos
            ExampleDataSeeder::class,
        ]);

        $this->command->info('🎉 ¡Todos los seeders ejecutados exitosamente!');
    }
}
