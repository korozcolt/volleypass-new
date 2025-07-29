<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Referee;
use App\Models\VolleyMatch;
use App\Models\Team;
use App\Models\Tournament;
use App\Enums\UserStatus;
use App\Enums\Gender;
use App\Enums\MatchPhase;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class RefereeMatchAssignmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🏐 Asignando partidos al árbitro específico...');

        // Buscar o crear el usuario árbitro
        $refereeUser = $this->createOrFindRefereeUser();
        
        // Buscar o crear el perfil de árbitro
        $referee = $this->createOrFindRefereeProfile($refereeUser);
        
        // Asignar partidos al árbitro
        $this->assignMatchesToReferee($referee);
        
        $this->command->info('✅ Partidos asignados exitosamente al árbitro.');
    }

    private function createOrFindRefereeUser()
    {
        $email = 'ing.korozco+arbitro@gmail.com';
        
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            $this->command->info('👤 Creando usuario árbitro...');
            
            $user = User::create([
                'name' => 'Kevin Orozco',
                'email' => $email,
                'first_name' => 'Kevin',
                'last_name' => 'Orozco',
                'document_type' => 'cedula',
                'document_number' => '1088600001',
                'birth_date' => '1990-05-15',
                'gender' => Gender::Male,
                'phone' => '+57 300 123 4567',
                'address' => 'Dirección de ejemplo',
                'status' => UserStatus::Active,
                'email_verified_at' => now(),
                'password' => Hash::make('Referee123'),
                'created_by' => 1,
            ]);
            
            // Asignar rol de árbitro
            $refereeRole = Role::where('name', 'Referee')->first();
            if ($refereeRole) {
                $user->assignRole($refereeRole);
            }
            
            $this->command->info('✅ Usuario árbitro creado: ' . $user->name);
        } else {
            $this->command->info('👤 Usuario árbitro encontrado: ' . $user->name);
        }
        
        return $user;
    }

    private function createOrFindRefereeProfile($user)
    {
        $referee = Referee::where('user_id', $user->id)->first();
        
        if (!$referee) {
            $this->command->info('⚖️ Creando perfil de árbitro...');
            
            $referee = Referee::create([
                'user_id' => $user->id,
                'license_number' => 'REF-KEVIN-001',
                'category' => 'nacional',
                'status' => UserStatus::Active,
                'created_by' => 1
            ]);
            
            $this->command->info('✅ Perfil de árbitro creado');
        } else {
            $this->command->info('⚖️ Perfil de árbitro encontrado');
        }
        
        return $referee;
    }

    private function assignMatchesToReferee($referee)
    {
        $this->command->info('🏐 Asignando partidos...');
        
        // Obtener equipos y torneos existentes
        $teams = Team::take(4)->get();
        $tournament = Tournament::first();
        
        if ($teams->count() < 4 || !$tournament) {
            $this->command->error('❌ No hay suficientes equipos o torneos para crear partidos.');
            return;
        }
        
        // Crear dos partidos para el árbitro
        $matches = [];
        
        // Partido 1 - Programado para mañana
        $match1 = VolleyMatch::create([
            'tournament_id' => $tournament->id,
            'home_team_id' => $teams[0]->id,
            'away_team_id' => $teams[1]->id,
            'referees' => [$referee->user->name], // Usar el nombre del usuario
            'scheduled_at' => now()->addDay()->setTime(19, 0), // Mañana a las 7 PM
            'venue' => 'Coliseo Municipal de Sincelejo',
            'phase' => MatchPhase::GROUP_STAGE->value,
            'status' => 'scheduled'
        ]);
        
        $matches[] = $match1;
        $this->command->info('✅ Partido 1 creado: ' . $teams[0]->name . ' vs ' . $teams[1]->name);
        
        // Partido 2 - Programado para pasado mañana
        $match2 = VolleyMatch::create([
            'tournament_id' => $tournament->id,
            'home_team_id' => $teams[2]->id,
            'away_team_id' => $teams[3]->id,
            'referees' => [$referee->user->name], // Usar el nombre del usuario
            'scheduled_at' => now()->addDays(2)->setTime(20, 30), // Pasado mañana a las 8:30 PM
            'venue' => 'Polideportivo Central',
            'phase' => MatchPhase::GROUP_STAGE->value,
            'status' => 'scheduled'
        ]);
        
        $matches[] = $match2;
        $this->command->info('✅ Partido 2 creado: ' . $teams[2]->name . ' vs ' . $teams[3]->name);
        
        $this->command->info('🎯 Total de partidos asignados: ' . count($matches));
        
        return $matches;
    }
}