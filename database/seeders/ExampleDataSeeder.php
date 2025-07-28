<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Player;
use App\Models\Referee;
use App\Models\Coach;
use App\Models\Club;
use App\Models\Team;
use App\Models\League;
use App\Models\Tournament;
use App\Models\VolleyMatch;
use App\Models\MatchSet;
use App\Models\Country;
use App\Models\Department;
use App\Models\City;
use Spatie\Permission\Models\Role;
use Carbon\Carbon;

class ExampleDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🏐 Creando datos de ejemplo para VolleyPass...');

        // Obtener ubicación por defecto
        $colombia = Country::where('code', 'CO')->first();
        $sucre = Department::where('code', '70')->first();
        $sincelejo = City::where('name', 'Sincelejo')->first();

        // Crear usuarios adicionales si no existen
        $this->createAdditionalUsers($colombia, $sucre, $sincelejo);
        
        // Crear liga primero
        $league = $this->createLeague($colombia, $sucre, $sincelejo);
        
        // Crear clubes
        $clubs = $this->createClubs($colombia, $sucre, $sincelejo, $league);
        
        // Crear equipos
        $teams = $this->createTeams($clubs);
        
        // Crear torneos
        $tournaments = $this->createTournaments($league);
        
        // Crear jugadores
        $this->createPlayers($teams);
        
        // Crear árbitros
        $this->createReferees();
        
        // Crear partidos
        $this->createMatches($tournaments, $teams);

        $this->command->info('🎉 Datos de ejemplo creados exitosamente!');
    }

    private function createAdditionalUsers($colombia, $sucre, $sincelejo)
    {
        $this->command->info('👥 Creando usuarios adicionales...');

        $additionalUsers = [
            [
                'name' => 'María Fernández',
                'email' => 'ing.korozco+arbitro@gmail.com',
                'first_name' => 'María',
                'last_name' => 'Fernández',
                'document_number' => '1088123470',
                'role' => 'Referee',
                'birth_date' => '1985-03-20',
                'gender' => 'female',
                'phone' => '+57 300 555 0001'
            ],
            [
                'name' => 'Ana Rodríguez',
                'email' => 'ing.korozco+jugador@gmail.com',
                'first_name' => 'Ana',
                'last_name' => 'Rodríguez',
                'document_number' => '1088123471',
                'role' => 'Player',
                'birth_date' => '1998-07-15',
                'gender' => 'female',
                'phone' => '+57 300 555 0002'
            ]
        ];

        foreach ($additionalUsers as $userData) {
            $existingUser = User::where('email', $userData['email'])->first();
            if (!$existingUser) {
                $user = User::create([
                    'name' => $userData['name'],
                    'email' => $userData['email'],
                    'first_name' => $userData['first_name'],
                    'last_name' => $userData['last_name'],
                    'document_type' => 'cedula',
                    'document_number' => $userData['document_number'],
                    'birth_date' => $userData['birth_date'],
                    'gender' => $userData['gender'],
                    'phone' => $userData['phone'],
                    'address' => 'Carrera 25 #16-50',
                    'country_id' => $colombia?->id,
                    'department_id' => $sucre?->id,
                    'city_id' => $sincelejo?->id,
                    'status' => 'active',
                    'email_verified_at' => now(),
                    'password' => Hash::make('Admin123'),
                    'created_by' => 1,
                ]);

                $role = Role::where('name', $userData['role'])->first();
                if ($role) {
                    $user->assignRole($userData['role']);
                }

                $this->command->info("✅ Usuario creado: {$userData['email']} con rol {$userData['role']}");
            }
        }
    }

    private function createClubs($colombia, $sucre, $sincelejo, $league)
    {
        $this->command->info('🏢 Creando clubes...');

        $clubsData = [
            [
                'name' => 'Club Águilas Doradas',
                'short_name' => 'Águilas',
                'foundation_date' => '2010-03-15',
                'address' => 'Calle 20 #15-30',
                'phone' => '+57 5 282 1234',
                'email' => 'info@aguilasdoradas.com'
            ],
            [
                'name' => 'Club Tigres de Sucre',
                'short_name' => 'Tigres',
                'foundation_date' => '2008-07-20',
                'address' => 'Carrera 22 #18-45',
                'phone' => '+57 5 282 5678',
                'email' => 'contacto@tigresucre.com'
            ],
            [
                'name' => 'Club Panteras Sincelejo',
                'short_name' => 'Panteras',
                'foundation_date' => '2015-11-10',
                'address' => 'Avenida Las Peñitas #25-10',
                'phone' => '+57 5 282 9012',
                'email' => 'admin@panterassincelejo.com'
            ],
            [
                'name' => 'Club Cóndores FC',
                'short_name' => 'Cóndores',
                'foundation_date' => '2012-05-25',
                'address' => 'Barrio Majagual Calle 30 #12-25',
                'phone' => '+57 5 282 3456',
                'email' => 'info@condoresfc.com'
            ]
        ];

        $clubs = [];
        foreach ($clubsData as $clubData) {
            $existingClub = Club::where('name', $clubData['name'])->first();
            if (!$existingClub) {
                $club = Club::create(array_merge($clubData, [
                    'league_id' => $league->id,
                    'country_id' => $colombia?->id,
                    'department_id' => $sucre?->id,
                    'city_id' => $sincelejo?->id,
                    'status' => 'active',
                    'created_by' => 1
                ]));
                $clubs[] = $club;
                $this->command->info("✅ Club creado: {$clubData['name']}");
            } else {
                $clubs[] = $existingClub;
            }
        }

        return $clubs;
    }

    private function createTeams($clubs)
    {
        $this->command->info('👥 Creando equipos...');

        $teams = [];
        $categories = ['mayores', 'juvenil', 'infantil'];
        
        foreach ($clubs as $club) {
            foreach ($categories as $category) {
                $teamName = $club->short_name . ' ' . $category;
                $existingTeam = Team::where('name', $teamName)->where('club_id', $club->id)->first();
                
                if (!$existingTeam) {
                    $team = Team::create([
                        'name' => $teamName,
                        'club_id' => $club->id,
                        'category' => $category,
                        'gender' => 'female',
                        'status' => 'active',
                        'founded_date' => now()->subYears(rand(1, 5)),
                        'created_by' => 1
                    ]);
                    $teams[] = $team;
                    $this->command->info("✅ Equipo creado: {$teamName}");
                } else {
                    $teams[] = $existingTeam;
                }
            }
        }

        return $teams;
    }

    private function createLeague($colombia, $sucre, $sincelejo)
    {
        $this->command->info('🏆 Creando liga...');

        $existingLeague = League::where('name', 'Liga de Voleibol de Sucre')->first();
        if (!$existingLeague) {
            $league = League::create([
                'name' => 'Liga de Voleibol de Sucre',
                'short_name' => 'LVS',
                'description' => 'Liga oficial de voleibol del departamento de Sucre',
                'foundation_date' => '2020-01-15',
                'status' => 'active',
                'is_active' => true,
                'country_id' => $colombia?->id,
                'department_id' => $sucre?->id,
                'city_id' => $sincelejo?->id,
                'email' => 'info@ligavoleibolsucre.com',
                'phone' => '+57 5 282 0000',
                'address' => 'Sincelejo, Sucre, Colombia'
            ]);
            $this->command->info('✅ Liga creada: Liga de Voleibol de Sucre');
            return $league;
        }
        
        return $existingLeague;
    }

    private function createTournaments($league)
    {
        $this->command->info('🏅 Creando torneos...');

        $tournamentsData = [
            [
                'name' => 'Torneo Apertura 2024',
                'description' => 'Torneo de apertura de la temporada 2024',
                'registration_start' => now(),
                'registration_end' => now()->addDays(5),
                'start_date' => now()->addDays(10),
                'end_date' => now()->addDays(60),
                'status' => 'registration_open'
            ],
            [
                'name' => 'Copa Sucre 2024',
                'description' => 'Copa departamental de voleibol femenino',
                'registration_start' => now()->addDays(65),
                'registration_end' => now()->addDays(68),
                'start_date' => now()->addDays(70),
                'end_date' => now()->addDays(100),
                'status' => 'registration_open'
            ],
            [
                'name' => 'Torneo Clausura 2023',
                'description' => 'Torneo de clausura de la temporada 2023',
                'registration_start' => now()->subDays(120),
                'registration_end' => now()->subDays(95),
                'start_date' => now()->subDays(90),
                'end_date' => now()->subDays(30),
                'status' => 'finished'
            ]
        ];

        $tournaments = [];
        foreach ($tournamentsData as $tournamentData) {
            $existingTournament = Tournament::where('name', $tournamentData['name'])->first();
            if (!$existingTournament) {
                $tournament = Tournament::create(array_merge($tournamentData, [
                    'league_id' => $league->id,
                    'organizer_id' => 1
                ]));
                $tournaments[] = $tournament;
                $this->command->info("✅ Torneo creado: {$tournamentData['name']}");
            } else {
                $tournaments[] = $existingTournament;
            }
        }

        return $tournaments;
    }

    private function createPlayers($teams)
    {
        $this->command->info('🏐 Creando jugadoras...');

        // Obtener usuario jugador
        $playerUser = User::where('email', 'ing.korozco+jugador@gmail.com')->first();
        if ($playerUser && !$playerUser->player) {
            $team = $teams[0] ?? null; // Asignar al primer equipo
            
            Player::create([
                'user_id' => $playerUser->id,
                'current_club_id' => $team?->club_id,
                'position' => 'outside_hitter',
                'jersey_number' => 15,
                'height' => 175,
                'weight' => 65,
                'category' => 'mayores',
                'status' => 'active'
            ]);
            
            $this->command->info('✅ Jugadora creada para: ' . $playerUser->email);
        }

        // Crear jugadoras adicionales
        $playersData = [
            ['name' => 'Sofía Martínez', 'position' => 'libero', 'jersey' => 1],
            ['name' => 'Camila López', 'position' => 'middle_blocker', 'jersey' => 8],
            ['name' => 'Valentina García', 'position' => 'opposite', 'jersey' => 10],
            ['name' => 'Isabella Torres', 'position' => 'setter', 'jersey' => 5],
            ['name' => 'Lucía Herrera', 'position' => 'outside_hitter', 'jersey' => 12]
        ];

        foreach ($playersData as $index => $playerData) {
            $email = 'jugadora' . ($index + 1) . '@volleypass.com';
            $existingUser = User::where('email', $email)->first();
            
            if (!$existingUser) {
                $user = User::create([
                    'name' => $playerData['name'],
                    'email' => $email,
                    'first_name' => explode(' ', $playerData['name'])[0],
                    'last_name' => explode(' ', $playerData['name'])[1] ?? '',
                    'document_type' => 'cedula',
                    'document_number' => '1088' . str_pad(123480 + $index, 6, '0', STR_PAD_LEFT),
                    'birth_date' => now()->subYears(rand(18, 25))->format('Y-m-d'),
                    'gender' => 'female',
                    'phone' => '+57 300 555 ' . str_pad(100 + $index, 4, '0', STR_PAD_LEFT),
                    'status' => 'active',
                    'email_verified_at' => now(),
                    'password' => Hash::make('Admin123'),
                    'created_by' => 1,
                ]);

                $role = Role::where('name', 'Player')->first();
                if ($role) {
                    $user->assignRole('Player');
                }

                $team = $teams[$index % count($teams)];
                Player::create([
                    'user_id' => $user->id,
                    'current_club_id' => $team->club_id,
                    'position' => $playerData['position'],
                    'jersey_number' => $playerData['jersey'],
                    'height' => rand(160, 185),
                    'weight' => rand(55, 75),
                    'category' => 'mayores',
                     'status' => 'active'
                ]);
            }
        }
    }

    private function createReferees()
    {
        $this->command->info('👨‍⚖️ Creando árbitros...');

        // Obtener usuario árbitro
        $refereeUser = User::where('email', 'ing.korozco+arbitro@gmail.com')->first();
        if ($refereeUser && !$refereeUser->referee) {
            $existingReferee = Referee::where('license_number', 'ARB-2024-001')->first();
            if (!$existingReferee) {
                Referee::create([
                    'user_id' => $refereeUser->id,
                    'license_number' => 'ARB-2024-001',
                    'category' => 'Nacional',
                    'experience_years' => 8,
                    'status' => 'active',
                    'created_by' => 1
                ]);
                
                $this->command->info('✅ Árbitro creado para: ' . $refereeUser->email);
            } else {
                $this->command->info('ℹ️ Árbitro ya existe para: ' . $refereeUser->email);
            }
        }

        // Crear árbitros adicionales
        $refereesData = [
            ['name' => 'Carlos Mendoza', 'license' => 'ARB-2024-002', 'category' => 'Regional'],
            ['name' => 'Luis Ramírez', 'license' => 'ARB-2024-003', 'category' => 'Nacional'],
            ['name' => 'Pedro Jiménez', 'license' => 'ARB-2024-004', 'category' => 'Departamental']
        ];

        foreach ($refereesData as $index => $refereeData) {
            $email = 'arbitro' . ($index + 1) . '@volleypass.com';
            $existingUser = User::where('email', $email)->first();
            $existingReferee = Referee::where('license_number', $refereeData['license'])->first();
            
            if (!$existingUser && !$existingReferee) {
                $user = User::create([
                    'name' => $refereeData['name'],
                    'email' => $email,
                    'first_name' => explode(' ', $refereeData['name'])[0],
                    'last_name' => explode(' ', $refereeData['name'])[1] ?? '',
                    'document_type' => 'cedula',
                    'document_number' => '1088' . str_pad(123490 + $index, 6, '0', STR_PAD_LEFT),
                    'birth_date' => now()->subYears(rand(25, 45))->format('Y-m-d'),
                    'gender' => 'male',
                    'phone' => '+57 300 555 ' . str_pad(200 + $index, 4, '0', STR_PAD_LEFT),
                    'status' => 'active',
                    'email_verified_at' => now(),
                    'password' => Hash::make('Admin123'),
                    'created_by' => 1,
                ]);

                $role = Role::where('name', 'Referee')->first();
                if ($role) {
                    $user->assignRole('Referee');
                }

                Referee::create([
                    'user_id' => $user->id,
                    'license_number' => $refereeData['license'],
                    'category' => $refereeData['category'],
                    'experience_years' => rand(3, 15),
                    'status' => 'active',
                    'created_by' => 1
                ]);
            }
        }
    }

    private function createMatches($tournaments, $teams)
    {
        $this->command->info('⚽ Creando partidos...');

        if (empty($tournaments) || empty($teams)) {
            $this->command->warn('⚠️ No hay torneos o equipos para crear partidos');
            return;
        }

        $referees = Referee::all();
        $tournament = $tournaments[0]; // Usar el primer torneo

        // Crear partidos pasados (completados)
        for ($i = 0; $i < 5; $i++) {
            $homeTeam = $teams[array_rand($teams)];
            $awayTeam = $teams[array_rand($teams)];
            
            // Evitar que un equipo juegue contra sí mismo
            while ($awayTeam->id === $homeTeam->id) {
                $awayTeam = $teams[array_rand($teams)];
            }

            $match = VolleyMatch::create([
                'tournament_id' => $tournament->id,
                'home_team_id' => $homeTeam->id,
                'away_team_id' => $awayTeam->id,
                'scheduled_at' => now()->subDays(rand(1, 30)),
                'status' => 'finished',
                'venue' => 'Coliseo Municipal de Sincelejo'
            ]);

            // Crear sets para el partido
            $this->createMatchSets($match);
        }

        // Crear partidos próximos
        for ($i = 0; $i < 8; $i++) {
            $homeTeam = $teams[array_rand($teams)];
            $awayTeam = $teams[array_rand($teams)];
            
            while ($awayTeam->id === $homeTeam->id) {
                $awayTeam = $teams[array_rand($teams)];
            }

            VolleyMatch::create([
                'tournament_id' => $tournament->id,
                'home_team_id' => $homeTeam->id,
                'away_team_id' => $awayTeam->id,
                'scheduled_at' => now()->addDays(rand(1, 30)),
                'status' => 'scheduled',
                'venue' => ['Coliseo Municipal de Sincelejo', 'Polideportivo Central', 'Gimnasio La Pradera'][rand(0, 2)]
            ]);
        }

        // Crear un partido en vivo
        $homeTeam = $teams[array_rand($teams)];
        $awayTeam = $teams[array_rand($teams)];
        
        while ($awayTeam->id === $homeTeam->id) {
            $awayTeam = $teams[array_rand($teams)];
        }

        $liveMatch = VolleyMatch::create([
            'tournament_id' => $tournament->id,
            'home_team_id' => $homeTeam->id,
            'away_team_id' => $awayTeam->id,
            'scheduled_at' => now()->subMinutes(45),
            'status' => 'in_progress',
            'venue' => 'Coliseo Municipal de Sincelejo'
        ]);

        // Crear sets para el partido en vivo
        $this->createLiveMatchSets($liveMatch);

        $this->command->info('✅ Partidos creados exitosamente');
    }

    private function createMatchSets($match)
    {
        $setsToPlay = max($match->home_score, $match->away_score) + 1;
        $homeWins = $match->home_score;
        $awayWins = $match->away_score;
        
        for ($setNumber = 1; $setNumber <= $setsToPlay; $setNumber++) {
            if ($setNumber <= $homeWins) {
                // Set ganado por equipo local
                $homeScore = rand(25, 30);
                $awayScore = rand(15, 24);
            } elseif ($setNumber <= $awayWins) {
                // Set ganado por equipo visitante
                $homeScore = rand(15, 24);
                $awayScore = rand(25, 30);
            } else {
                // Set final decisivo
                if ($match->home_score > $match->away_score) {
                    $homeScore = rand(25, 30);
                    $awayScore = rand(15, 24);
                } else {
                    $homeScore = rand(15, 24);
                    $awayScore = rand(25, 30);
                }
            }

            MatchSet::create([
                'match_id' => $match->id,
                'set_number' => $setNumber,
                'home_score' => $homeScore,
                'away_score' => $awayScore,
                'status' => 'completed',
                'created_by' => 1
            ]);
        }
    }

    private function createLiveMatchSets($match)
    {
        // Sets completados
        MatchSet::create([
            'match_id' => $match->id,
            'set_number' => 1,
            'home_score' => 25,
            'away_score' => 20,
            'status' => 'completed',
            'created_by' => 1
        ]);

        MatchSet::create([
            'match_id' => $match->id,
            'set_number' => 2,
            'home_score' => 22,
            'away_score' => 25,
            'status' => 'completed',
            'created_by' => 1
        ]);

        MatchSet::create([
            'match_id' => $match->id,
            'set_number' => 3,
            'home_score' => 25,
            'away_score' => 18,
            'status' => 'completed',
            'created_by' => 1
        ]);

        // Set en progreso
        MatchSet::create([
            'match_id' => $match->id,
            'set_number' => 4,
            'home_score' => 15,
            'away_score' => 12,
            'status' => 'in_progress',
            'created_by' => 1
        ]);
    }
}