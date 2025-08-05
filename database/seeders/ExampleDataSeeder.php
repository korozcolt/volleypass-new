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
use App\Models\TeamPlayer;
use App\Models\League;
use App\Models\Tournament;
use App\Models\VolleyMatch;
use App\Models\MatchSet;
use App\Models\Country;
use App\Models\Department;
use App\Models\City;
use App\Models\Payment;
use App\Models\PlayerCard;
use App\Models\Award;
use App\Models\Injury;
use App\Models\UserProfile;
use App\Enums\PlayerCategory;
use Spatie\Permission\Models\Role;
use Carbon\Carbon;
use App\Enums\Gender;
use App\Enums\UserStatus;
use App\Enums\PaymentStatus;
use App\Enums\PaymentType;
use App\Enums\CardStatus;
use App\Enums\CardType;
use App\Enums\MedicalStatus;
use App\Enums\MatchPhase;
use App\Enums\EventType;
use App\Enums\AwardType;
use App\Enums\InjuryType;

class ExampleDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🏐 Creando datos de ejemplo completos para VolleyPass...');

        // Obtener ubicaciones base
        $colombia = Country::where('code', 'CO')->first();
        $sucre = Department::where('country_id', $colombia->id)->first();
        $sincelejo = City::where('department_id', $sucre->id)->first();

        if (!$colombia || !$sucre || !$sincelejo) {
            $this->command->error('❌ No se encontraron las ubicaciones base. Ejecute ColombiaLocationsSeeder primero.');
            return;
        }

        // 1. Crear usuarios adicionales
        $users = $this->createAdditionalUsers($colombia, $sucre, $sincelejo);
        
        // 2. Crear ligas
        $leagues = $this->createLeagues($colombia, $sucre, $sincelejo);
        
        // 3. Crear clubes
        $clubs = $this->createClubs($colombia, $sucre, $sincelejo, $leagues);
        
        // 4. Crear equipos
        $teams = $this->createTeams($clubs);
        
        // 5. Crear jugadores
        $players = $this->createPlayers($teams, $users);
        
        // 6. Crear entrenadores
        $coaches = $this->createCoaches($clubs, $users);
        
        // 7. Crear árbitros
        $referees = $this->createReferees($users);
        
        // 8. Crear torneos
        $tournaments = $this->createTournaments($leagues);
        
        // 9. Crear partidos
        $matches = $this->createMatches($tournaments, $teams, $referees);
        
        // 10. Crear pagos
        $this->createPayments($clubs, $players);
        
        // 11. Crear carnets
        $this->createPlayerCards($players);
        
        // 12. Crear premios
        $this->createAwards($players, $tournaments);
        
        // 13. Crear lesiones
        // $this->createInjuries($players); // Comentado temporalmente - tabla injuries no tiene columnas definidas

        $this->command->info('🎉 ¡Datos de ejemplo completos creados exitosamente!');
        $this->showStatistics();
    }

    private function createAdditionalUsers($colombia, $sucre, $sincelejo)
    {
        $this->command->info('👥 Creando usuarios adicionales...');

        $additionalUsers = [
            [
                'name' => 'María Fernández',
                'email' => 'maria.fernandez@volleypass.com',
                'first_name' => 'María',
                'last_name' => 'Fernández',
                'document_number' => '1088200001',
                'role' => 'Referee',
                'birth_date' => '1985-03-20',
                'gender' => 'female',
                'phone' => '+57 300 555 0001'
            ],
            [
                'name' => 'Ana Rodríguez',
                'email' => 'ana.rodriguez@volleypass.com',
                'first_name' => 'Ana',
                'last_name' => 'Rodríguez',
                'document_number' => '1088200002',
                'role' => 'Player',
                'birth_date' => '1998-07-15',
                'gender' => 'female',
                'phone' => '+57 300 555 0002'
            ],
            [
                'name' => 'Carlos Mendoza',
                'email' => 'carlos.mendoza@volleypass.com',
                'first_name' => 'Carlos',
                'last_name' => 'Mendoza',
                'document_number' => '1088200003',
                'role' => 'Coach',
                'birth_date' => '1980-11-10',
                'gender' => 'male',
                'phone' => '+57 300 555 0003'
            ],
            [
                'name' => 'Laura Gómez',
                'email' => 'laura.gomez@volleypass.com',
                'first_name' => 'Laura',
                'last_name' => 'Gómez',
                'document_number' => '1088200004',
                'role' => 'Player',
                'birth_date' => '1999-05-22',
                'gender' => 'female',
                'phone' => '+57 300 555 0004'
            ],
            [
                'name' => 'Roberto Silva',
                'email' => 'roberto.silva@volleypass.com',
                'first_name' => 'Roberto',
                'last_name' => 'Silva',
                'document_number' => '1088200005',
                'role' => 'ClubDirector',
                'birth_date' => '1975-09-14',
                'gender' => 'male',
                'phone' => '+57 300 555 0005'
            ]
        ];

        $users = [];
        foreach ($additionalUsers as $userData) {
            $existingUser = User::where('email', $userData['email'])
                              ->orWhere('document_number', $userData['document_number'])
                              ->first();
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
                    'country_id' => $colombia->id,
                    'department_id' => $sucre->id,
                    'city_id' => $sincelejo->id,
                    'status' => UserStatus::Active,
                    'email_verified_at' => now(),
                    'password' => Hash::make('Admin123')
                ]);

                // Asignar rol
                $role = Role::where('name', $userData['role'])->first();
                if ($role) {
                    $user->assignRole($userData['role']);
                }

                // Crear perfil si no existe
                if (!UserProfile::where('user_id', $user->id)->exists()) {
                    UserProfile::create([
                        'user_id' => $user->id,
                        'nickname' => $userData['first_name'],
                        'bio' => 'Usuario de ejemplo del sistema VolleyPass',
                        'joined_date' => now()->subYears(rand(1, 3)),
                        'blood_type' => ['O+', 'A+', 'B+', 'AB+', 'O-'][rand(0, 4)],
                        'emergency_contact_name' => 'Contacto de Emergencia',
                        'emergency_contact_phone' => '+57 300 999 9999',
                        'emergency_contact_relationship' => 'Familiar',
                        't_shirt_size' => ['S', 'M', 'L', 'XL'][rand(0, 3)],
                        'show_phone' => true,
                        'show_email' => true,
                        'show_address' => false,
                        'created_by' => $user->id,
                        'updated_by' => $user->id,
                    ]);
                }

                $users[] = $user;
                $this->command->info("✅ Usuario creado: {$userData['name']} ({$userData['role']})");
            } else {
                $users[] = $existingUser;
                $this->command->info("ℹ️ Usuario ya existe: {$userData['name']} ({$userData['role']})");
            }
        }

        return $users;
    }

    private function createLeagues($colombia, $sucre, $sincelejo)
    {
        $this->command->info('🏆 Creando ligas...');

        $leaguesData = [
            [
                'name' => 'Liga de Voleibol de Sucre',
                'short_name' => 'LVS',
                'description' => 'Liga oficial de voleibol del departamento de Sucre',
                'email' => 'info@ligavoleibolsucre.com',
                'phone' => '+57 5 282 0000'
            ],
            [
                'name' => 'Liga Metropolitana de Sincelejo',
                'short_name' => 'LMS',
                'description' => 'Liga metropolitana de voleibol de Sincelejo',
                'email' => 'info@ligametropolitana.com',
                'phone' => '+57 5 282 1111'
            ]
        ];

        $leagues = [];
        foreach ($leaguesData as $leagueData) {
            $existingLeague = League::where('name', $leagueData['name'])->first();
            if (!$existingLeague) {
                $league = League::create(array_merge($leagueData, [
                    'foundation_date' => now()->subYears(rand(5, 15)),
                    'status' => UserStatus::Active,
                    'is_active' => true,
                    'country_id' => $colombia->id,
                    'department_id' => $sucre->id,
                    'city_id' => $sincelejo->id,
                    'address' => 'Sincelejo, Sucre, Colombia',
                    'created_by' => 1
                ]));
                $leagues[] = $league;
                $this->command->info("✅ Liga creada: {$leagueData['name']}");
            } else {
                $leagues[] = $existingLeague;
            }
        }

        return $leagues;
    }

    private function createClubs($colombia, $sucre, $sincelejo, $leagues)
    {
        $this->command->info('🏢 Creando clubes...');

        $clubsData = [
            [
                'name' => 'Club Deportivo Sincelejo',
                'short_name' => 'CDS',
                'description' => 'Club deportivo de voleibol de Sincelejo',
                'email' => 'info@clubsincelejo.com',
                'phone' => '+57 5 282 2000'
            ],
            [
                'name' => 'Águilas Doradas Voleibol',
                'short_name' => 'ADV',
                'description' => 'Club de voleibol Águilas Doradas',
                'email' => 'info@aguilasdoradas.com',
                'phone' => '+57 5 282 2001'
            ],
            [
                'name' => 'Tigres de Corozal',
                'short_name' => 'TDC',
                'description' => 'Club de voleibol Tigres de Corozal',
                'email' => 'info@tigrescorozal.com',
                'phone' => '+57 5 282 2002'
            ],
            [
                'name' => 'Leones de Tolú',
                'short_name' => 'LDT',
                'description' => 'Club de voleibol Leones de Tolú',
                'email' => 'info@leonestolu.com',
                'phone' => '+57 5 282 2003'
            ]
        ];

        $clubs = [];
        foreach ($clubsData as $index => $clubData) {
            $existingClub = Club::where('name', $clubData['name'])->first();
            if (!$existingClub) {
                $club = Club::create(array_merge($clubData, [
                    'foundation_date' => now()->subYears(rand(3, 10)),
                    'status' => UserStatus::Active,
                    'is_active' => true,
                    'country_id' => $colombia->id,
                    'department_id' => $sucre->id,
                    'city_id' => $sincelejo->id,
                    'league_id' => $leagues[$index % count($leagues)]->id,
                    'address' => 'Sincelejo, Sucre, Colombia',
                    'es_federado' => rand(0, 1) == 1,
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

        $categories = ['juvenil', 'mayores', 'masters'];
        $teams = [];

        foreach ($clubs as $club) {
            foreach ($categories as $category) {
                $teamName = $club->name . ' ' . ucfirst($category);
                $existingTeam = Team::where('name', $teamName)->first();
                
                if (!$existingTeam) {
                    $team = Team::create([
                        'name' => $teamName,
                        'club_id' => $club->id,
                        'category' => $category,
                        'gender' => Gender::Female,
                        'status' => UserStatus::Active,
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

    private function createPlayers($teams, $users)
    {
        $this->command->info('🏐 Creando jugadores...');

        $categories = [PlayerCategory::Juvenil->value, PlayerCategory::Mayores->value, PlayerCategory::Masters->value];

        $playerUsers = collect($users)->filter(function($user) {
            return $user->hasRole('Player');
        });

        $players = [];
        $playerNames = [
            'Sofía Martínez', 'Isabella García', 'Valentina López', 'Camila Rodríguez',
            'Mariana Hernández', 'Daniela González', 'Gabriela Pérez', 'Alejandra Sánchez',
            'Natalia Ramírez', 'Andrea Torres', 'Carolina Flores', 'Paola Morales',
            'Juliana Castro', 'Fernanda Ortiz', 'Melissa Vargas', 'Diana Ruiz'
        ];

        foreach ($teams as $teamIndex => $team) {
            // Crear 8-12 jugadores por equipo
            $playersPerTeam = rand(8, 12);
            
            for ($i = 0; $i < $playersPerTeam; $i++) {
                $playerName = $playerNames[($teamIndex * $playersPerTeam + $i) % count($playerNames)];
                $documentNumber = '1088300' . str_pad(($teamIndex * $playersPerTeam + $i + 1), 3, '0', STR_PAD_LEFT);
                
                // Verificar si ya existe
                $existingPlayer = Player::where('document_number', $documentNumber)->first();
                if ($existingPlayer) {
                    continue;
                }

                // Crear usuario para el jugador si no existe
                $email = 'jugador' . ($teamIndex * $playersPerTeam + $i + 1) . '@volleypass.com';
                $user = User::where('email', $email)->first();
                
                if (!$user) {
                    $nameParts = explode(' ', $playerName);
                    $user = User::create([
                        'name' => $playerName,
                        'email' => $email,
                        'first_name' => $nameParts[0],
                        'last_name' => $nameParts[1] ?? 'Apellido',
                        'document_type' => 'cedula',
                        'document_number' => $documentNumber,
                        'birth_date' => now()->subYears(rand(16, 35)),
                        'gender' => Gender::Female,
                        'phone' => '+57 300 ' . rand(100, 999) . ' ' . rand(1000, 9999),
                        'address' => 'Dirección de ejemplo',
                        'country_id' => $team->club->country_id,
                        'department_id' => $team->club->department_id,
                        'city_id' => $team->club->city_id,
                        'status' => UserStatus::Active,
                        'email_verified_at' => now(),
                        'password' => Hash::make('Player123'),
                        'created_by' => 1,
                    ]);
                    
                    $user->assignRole('Player');
                }

                // Verificar si ya existe un jugador para este usuario
                $existingPlayer = Player::where('user_id', $user->id)->first();
                if ($existingPlayer) {
                    $player = $existingPlayer;
                } else {
                    // Crear jugador
                    $player = Player::create([
                        'user_id' => $user->id,
                        'jersey_number' => ($i + 1),
                        'position' => ['libero', 'setter', 'outside_hitter', 'middle_blocker', 'opposite'][rand(0, 4)],
                        'height' => rand(160, 190),
                        'weight' => rand(55, 80),
                        'blood_type' => ['O', 'A', 'B', 'AB'][rand(0, 3)],
                        'blood_rh' => ['positive', 'negative'][rand(0, 1)],
                        'category' => $categories[array_rand($categories)],
                        'status' => UserStatus::Active,
                        'created_by' => 1
                    ]);
                }

                // Asignar jugador al equipo
                TeamPlayer::create([
                    'team_id' => $team->id,
                    'player_id' => $player->id,
                    'jersey_number' => ($i + 1),
                    'position' => $player->position,
                    'is_captain' => $i == 0, // El primer jugador es capitán
                    'joined_at' => now()->subMonths(rand(1, 12)),
                    'created_by' => 1
                ]);

                $players[] = $player;
            }
        }

        $this->command->info("✅ Creados " . count($players) . " jugadores");
        return $players;
    }

    private function createCoaches($clubs, $users)
    {
        $this->command->info('👨‍🏫 Creando entrenadores...');

        $coachUsers = collect($users)->filter(function($user) {
            return $user->hasRole('Coach');
        });

        $coaches = [];
        $coachNames = [
            'Carlos Mendoza', 'Roberto Silva', 'Fernando García', 'Miguel Torres',
            'Andrés López', 'Diego Martínez', 'Alejandro Pérez', 'Javier González'
        ];

        foreach ($clubs as $index => $club) {
            $coachName = $coachNames[$index % count($coachNames)];
            $documentNumber = '1088400' . str_pad(($index + 1), 3, '0', STR_PAD_LEFT);
            
            // Verificar si ya existe
            $existingCoach = Coach::where('document_number', $documentNumber)->first();
            if ($existingCoach) {
                $coaches[] = $existingCoach;
                continue;
            }

            // Crear usuario para el entrenador si no existe
            $email = 'entrenador' . ($index + 1) . '@volleypass.com';
            $user = User::where('email', $email)->first();
            
            if (!$user) {
                $nameParts = explode(' ', $coachName);
                $user = User::create([
                    'name' => $coachName,
                    'email' => $email,
                    'first_name' => $nameParts[0],
                    'last_name' => $nameParts[1] ?? 'Apellido',
                    'document_type' => 'cedula',
                    'document_number' => $documentNumber,
                    'birth_date' => now()->subYears(rand(30, 55)),
                    'gender' => Gender::Male,
                    'phone' => '+57 300 ' . rand(100, 999) . ' ' . rand(1000, 9999),
                    'address' => 'Dirección de ejemplo',
                    'country_id' => $club->country_id,
                    'department_id' => $club->department_id,
                    'city_id' => $club->city_id,
                    'status' => UserStatus::Active,
                    'email_verified_at' => now(),
                    'password' => Hash::make('Coach123'),
                    'created_by' => 1,
                ]);
                
                $user->assignRole('Coach');
            }

            // Verificar si ya existe un entrenador para este usuario
            $existingCoach = Coach::where('user_id', $user->id)->first();
            if ($existingCoach) {
                $coach = $existingCoach;
            } else {
                // Crear entrenador
                $coach = Coach::create([
                    'user_id' => $user->id,
                    'club_id' => $club->id,
                    'license_number' => 'LIC-' . str_pad(($index + 1), 4, '0', STR_PAD_LEFT),
                    'license_level' => ['nivel_1', 'nivel_2', 'nivel_3'][rand(0, 2)],
                    'specialization' => ['juvenil', 'mayores', 'general'][rand(0, 2)],
                    'status' => UserStatus::Active,
                    'created_by' => 1
                ]);
            }

            $coaches[] = $coach;
            $this->command->info("✅ Entrenador creado: {$coachName}");
        }

        return $coaches;
    }

    private function createReferees($users)
    {
        $this->command->info('👨‍⚖️ Creando árbitros...');

        $refereeUsers = collect($users)->filter(function($user) {
            return $user->hasRole('Referee');
        });

        $referees = [];
        $refereeNames = [
            'María Fernández', 'Ana Rodríguez', 'Carmen López', 'Patricia García',
            'Luis Martínez', 'José González', 'Pedro Sánchez', 'Manuel Torres'
        ];

        for ($i = 0; $i < 6; $i++) {
            $refereeName = $refereeNames[$i % count($refereeNames)];
            $documentNumber = '1088500' . str_pad(($i + 1), 3, '0', STR_PAD_LEFT);
            
            // Verificar si ya existe
            $existingReferee = Referee::where('document_number', $documentNumber)->first();
            if ($existingReferee) {
                $referees[] = $existingReferee;
                continue;
            }

            // Crear usuario para el árbitro si no existe
            $email = 'arbitro' . ($i + 1) . '@volleypass.com';
            $user = User::where('email', $email)->first();
            
            if (!$user) {
                $nameParts = explode(' ', $refereeName);
                $user = User::create([
                    'name' => $refereeName,
                    'email' => $email,
                    'first_name' => $nameParts[0],
                    'last_name' => $nameParts[1] ?? 'Apellido',
                    'document_type' => 'cedula',
                    'document_number' => $documentNumber,
                    'birth_date' => now()->subYears(rand(25, 50)),
                    'gender' => $i < 4 ? Gender::Female : Gender::Male,
                    'phone' => '+57 300 ' . rand(100, 999) . ' ' . rand(1000, 9999),
                    'address' => 'Dirección de ejemplo',
                    'status' => UserStatus::Active,
                    'email_verified_at' => now(),
                    'password' => Hash::make('Referee123'),
                    'created_by' => 1,
                ]);
                
                $user->assignRole('Referee');
            }

            // Crear árbitro solo si no existe
            $existingReferee = Referee::where('user_id', $user->id)->first();
            if (!$existingReferee) {
                $referee = Referee::create([
                    'user_id' => $user->id,
                    'license_number' => 'REF-' . str_pad(($i + 1), 4, '0', STR_PAD_LEFT),
                    'category' => ['regional', 'nacional', 'internacional'][rand(0, 2)],
                    'status' => UserStatus::Active,
                    'created_by' => 1
                ]);
            } else {
                $referee = $existingReferee;
            }

            $referees[] = $referee;
            $this->command->info("✅ Árbitro creado: {$refereeName}");
        }

        return $referees;
    }

    private function createTournaments($leagues)
    {
        $this->command->info('🏅 Creando torneos...');

        $tournamentsData = [
            [
                'name' => 'Torneo Apertura 2024',
                'description' => 'Torneo de apertura de la temporada 2024',
                'registration_start' => now()->subDays(30),
                'registration_end' => now()->subDays(25),
                'start_date' => now()->subDays(20),
                'end_date' => now()->addDays(40),
                'status' => 'in_progress'
            ],
            [
                'name' => 'Copa Sucre 2024',
                'description' => 'Copa departamental de voleibol femenino',
                'registration_start' => now()->addDays(10),
                'registration_end' => now()->addDays(15),
                'start_date' => now()->addDays(20),
                'end_date' => now()->addDays(60),
                'status' => 'registration_open'
            ],
            [
                'name' => 'Torneo Clausura 2023',
                'description' => 'Torneo de clausura de la temporada 2023',
                'registration_start' => now()->subDays(150),
                'registration_end' => now()->subDays(145),
                'start_date' => now()->subDays(140),
                'end_date' => now()->subDays(80),
                'status' => 'finished'
            ],
            [
                'name' => 'Torneo Juvenil 2024',
                'description' => 'Torneo especial para categoría juvenil',
                'registration_start' => now()->addDays(30),
                'registration_end' => now()->addDays(35),
                'start_date' => now()->addDays(40),
                'end_date' => now()->addDays(70),
                'status' => 'registration_open'
            ]
        ];

        $tournaments = [];
        foreach ($tournamentsData as $index => $tournamentData) {
            $existingTournament = Tournament::where('name', $tournamentData['name'])->first();
            if (!$existingTournament) {
                $tournament = Tournament::create(array_merge($tournamentData, [
                    'league_id' => $leagues[$index % count($leagues)]->id,
                    'organizer_id' => 1,
                    'max_teams' => rand(8, 16),
                    'registration_fee' => rand(50000, 200000)
                ]));
                $tournaments[] = $tournament;
                $this->command->info("✅ Torneo creado: {$tournamentData['name']}");
            } else {
                $tournaments[] = $existingTournament;
            }
        }

        return $tournaments;
    }

    private function createMatches($tournaments, $teams, $referees)
    {
        $this->command->info('⚽ Creando partidos...');

        $matches = [];
        
        foreach ($tournaments as $tournament) {
            // Crear 6-10 partidos por torneo
            $matchesPerTournament = rand(6, 10);
            
            for ($i = 0; $i < $matchesPerTournament; $i++) {
                // Seleccionar equipos aleatorios
                $homeTeam = $teams[rand(0, count($teams) - 1)];
                $awayTeam = $teams[rand(0, count($teams) - 1)];
                
                // Asegurar que no sea el mismo equipo
                while ($awayTeam->id === $homeTeam->id) {
                    $awayTeam = $teams[rand(0, count($teams) - 1)];
                }
                
                // Seleccionar árbitros
                $mainReferee = $referees[rand(0, count($referees) - 1)];
                $assistantReferee = $referees[rand(0, count($referees) - 1)];
                
                while ($assistantReferee->id === $mainReferee->id) {
                    $assistantReferee = $referees[rand(0, count($referees) - 1)];
                }

                // Determinar fecha del partido basada en el estado del torneo
                $matchDate = $this->getMatchDate($tournament);
                
                $match = VolleyMatch::create([
                    'tournament_id' => $tournament->id,
                    'home_team_id' => $homeTeam->id,
                    'away_team_id' => $awayTeam->id,
                    'referees' => [$mainReferee->id, $assistantReferee->id],
                    'scheduled_at' => $matchDate,
                    'venue' => 'Coliseo Municipal de Sincelejo',
                    'phase' => MatchPhase::GROUP_STAGE->value,
                    'status' => $this->getMatchStatus($tournament)
                ]);

                // Si el partido ya terminó, crear sets
                if ($match->status === 'finished') {
                    $this->createMatchSets($match);
                }

                $matches[] = $match;
            }
        }

        $this->command->info("✅ Creados " . count($matches) . " partidos");
        return $matches;
    }

    private function getMatchDate($tournament)
    {
        switch ($tournament->status) {
            case 'finished':
                return now()->subDays(rand(80, 140));
            case 'in_progress':
                return now()->subDays(rand(1, 20));
            case 'registration_open':
                return now()->addDays(rand(20, 60));
            default:
                return now()->addDays(rand(40, 70));
        }
    }

    private function getMatchStatus($tournament)
    {
        switch ($tournament->status) {
            case 'finished':
                return 'finished';
            case 'in_progress':
                return ['scheduled', 'in_progress', 'finished'][rand(0, 2)];
            default:
                return 'scheduled';
        }
    }

    private function createMatchSets($match)
    {
        $setsCount = rand(3, 5); // Partidos de 3 a 5 sets
        $homeWins = 0;
        $awayWins = 0;
        
        for ($i = 1; $i <= $setsCount; $i++) {
            $homeScore = rand(20, 30);
            $awayScore = rand(20, 30);
            
            // Asegurar que haya un ganador claro
            if ($homeScore > $awayScore) {
                $homeScore = max($homeScore, $awayScore + 2);
                $homeWins++;
            } else {
                $awayScore = max($awayScore, $homeScore + 2);
                $awayWins++;
            }
            
            MatchSet::create([
                'match_id' => $match->id,
                'set_number' => $i,
                'home_score' => $homeScore,
                'away_score' => $awayScore,
                'duration_minutes' => rand(20, 45),
                'created_by' => 1
            ]);
            
            // Si un equipo ya ganó 3 sets, terminar
            if ($homeWins === 3 || $awayWins === 3) {
                break;
            }
        }
        
        // Actualizar resultado del partido
        $match->update([
            'home_sets' => $homeWins,
            'away_sets' => $awayWins,
            'winner_team_id' => $homeWins > $awayWins ? $match->home_team_id : $match->away_team_id
        ]);
    }

    private function createPayments($clubs, $players)
    {
        $this->command->info('💰 Creando pagos...');

        $payments = [];
        
        // Pagos de federación de clubes
        foreach ($clubs as $club) {
            if ($club->es_federado) {
                $referenceNumber = 'FED-' . $club->id . '-' . date('Y');
                
                $existingPayment = Payment::where('reference_number', $referenceNumber)->first();
                if (!$existingPayment) {
                    $payment = Payment::create([
                        'club_id' => $club->id,
                        'amount' => 50000, // Cuota de federación
                        'type' => PaymentType::Federation->value,
                        'status' => PaymentStatus::Verified->value,
                        'payment_date' => now()->subDays(rand(30, 365)),
                        'description' => 'Cuota anual de federación',
                        'reference_number' => $referenceNumber
                    ]);
                    $payments[] = $payment;
                }
            }
        }
        
        // Pagos de carnets de jugadores (algunos)
        $playersWithPayments = collect($players)->random(min(count($players), 20));
        
        foreach ($playersWithPayments as $player) {
            $referenceNumber = 'CARD-' . $player->id . '-' . date('Y');
            
            $existingPayment = Payment::where('reference_number', $referenceNumber)->first();
            if (!$existingPayment) {
                $payment = Payment::create([
                    'user_id' => $player->user_id,
                    'amount' => 25000, // Costo del carnet
                    'type' => PaymentType::Registration->value,
                    'status' => [PaymentStatus::Pending->value, PaymentStatus::Verified->value, PaymentStatus::Rejected->value][rand(0, 2)],
                    'payment_date' => now()->subDays(rand(1, 180)),
                    'description' => 'Pago de carnet de jugador',
                    'reference_number' => $referenceNumber
                ]);
                $payments[] = $payment;
            }
        }

        $this->command->info("✅ Creados " . count($payments) . " pagos");
        return $payments;
    }

    private function createPlayerCards($players)
    {
        $this->command->info('🆔 Creando carnets de jugadores...');

        $cards = [];
        $playersWithCards = collect($players)->random(min(count($players), 15));
        
        foreach ($playersWithCards as $index => $player) {
            $cardNumber = 'VP-' . date('Y') . '-' . str_pad(($index + 1), 4, '0', STR_PAD_LEFT);
            
            // Check if card number already exists
            if (PlayerCard::where('card_number', $cardNumber)->exists()) {
                continue; // Skip if card already exists
            }
            
            $card = PlayerCard::create([
                'player_id' => $player->id,
                'league_id' => $player->teamPlayers?->first()?->team?->league_id ?? 1,
                'card_number' => $cardNumber,
                'status' => [CardStatus::Active->value, CardStatus::Pending_Approval->value, CardStatus::Expired->value][rand(0, 2)],
                'issued_at' => now()->subDays(rand(30, 365)),
                'expires_at' => now()->addDays(rand(30, 365)),
                'qr_code' => 'QR-' . uniqid(),
                'verification_token' => 'VT-' . uniqid(),
                'medical_status' => [MedicalStatus::Fit->value, MedicalStatus::Restricted->value][rand(0, 1)],
                'issued_by' => 1, // Admin user
            ]);
            $cards[] = $card;
        }

        $this->command->info("✅ Creados " . count($cards) . " carnets");
        return $cards;
    }

    private function createAwards($players, $tournaments)
    {
        $this->command->info('🏆 Creando premios...');

        $awards = [];
        $awardTypes = [AwardType::MVP, AwardType::Top_Scorer, AwardType::Best_Blocker];
        
        foreach ($tournaments as $tournament) {
            if ($tournament->status === 'finished') {
                // Crear 2-3 premios por torneo terminado
                $awardsCount = rand(2, 3);
                $selectedPlayers = collect($players)->random(min(count($players), $awardsCount));
                
                foreach ($selectedPlayers as $index => $player) {
                    $award = Award::create([
                        'player_id' => $player->id,
                        'tournament_id' => $tournament->id,
                        'type' => $awardTypes[$index % count($awardTypes)]->value,
                        'title' => $this->getAwardTitle($awardTypes[$index % count($awardTypes)]),
                        'description' => 'Premio otorgado en ' . $tournament->name,
                        'awarded_date' => $tournament->end_date,
                        'created_by' => 1
                    ]);
                    $awards[] = $award;
                }
            }
        }

        $this->command->info("✅ Creados " . count($awards) . " premios");
        return $awards;
    }

    private function getAwardTitle($type)
    {
        switch ($type) {
            case AwardType::MVP:
                return 'Jugadora Más Valiosa';
            case AwardType::Top_Scorer:
                return 'Mejor Anotadora';
            case AwardType::Best_Blocker:
                return 'Mejor Defensora';
            default:
                return 'Premio Especial';
        }
    }

    private function createInjuries($players)
    {
        $this->command->info('🏥 Creando registros de lesiones...');

        $injuries = [];
        $playersWithInjuries = collect($players)->random(min(count($players), 8));
        
        foreach ($playersWithInjuries as $player) {
            $injury = Injury::create([
                'player_id' => $player->id,
                'type' => [InjuryType::Knee->value, InjuryType::Ankle->value, InjuryType::Shoulder->value][rand(0, 2)],
                'description' => 'Lesión durante entrenamiento/partido',
                'body_part' => ['rodilla', 'tobillo', 'hombro', 'muñeca', 'espalda'][rand(0, 4)],
                'severity' => ['leve', 'moderada', 'grave'][rand(0, 2)],
                'injury_date' => now()->subDays(rand(1, 180)),
                'expected_recovery_date' => now()->addDays(rand(7, 90)),
                'status' => ['en_tratamiento', 'recuperada', 'cronica'][rand(0, 2)],
                'created_by' => 1
            ]);
            $injuries[] = $injury;
        }

        $this->command->info("✅ Creados " . count($injuries) . " registros de lesiones");
        return $injuries;
    }

    private function showStatistics()
    {
        $this->command->info('');
        $this->command->info('📊 Estadísticas de datos creados:');
        $this->command->info('👥 Usuarios: ' . User::count());
        $this->command->info('🏆 Ligas: ' . League::count());
        $this->command->info('🏢 Clubes: ' . Club::count());
        $this->command->info('👥 Equipos: ' . Team::count());
        $this->command->info('🏐 Jugadores: ' . Player::count());
        $this->command->info('👨‍🏫 Entrenadores: ' . Coach::count());
        $this->command->info('👨‍⚖️ Árbitros: ' . Referee::count());
        $this->command->info('🏅 Torneos: ' . Tournament::count());
        $this->command->info('⚽ Partidos: ' . VolleyMatch::count());
        $this->command->info('💰 Pagos: ' . Payment::count());
        $this->command->info('🆔 Carnets: ' . PlayerCard::count());
        $this->command->info('🏆 Premios: ' . Award::count());
        $this->command->info('🏥 Lesiones: ' . Injury::count());
        $this->command->info('');
    }
}