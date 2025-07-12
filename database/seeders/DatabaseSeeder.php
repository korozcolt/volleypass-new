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
            ColombiaLocationsSeeder::class,
            RolesAndPermissionsSeeder::class,
            KristianUsersSeeder::class,
        ]);

        $this->command->info('🎉 ¡Todos los seeders ejecutados exitosamente!');
    }
}
