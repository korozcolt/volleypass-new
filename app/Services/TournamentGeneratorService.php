<?php

namespace App\Services;

use App\Models\Tournament;
use App\Models\Team;
use App\Models\VolleyMatch;
use App\Models\TournamentRound;
use App\Models\Venue;
use App\Models\Referee;
use App\Models\MatchEvent;
use App\Enums\TournamentFormat;
use App\Enums\MatchStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Exception;

class TournamentGeneratorService
{
    /**
     * Generar fixtures completos para un torneo
     */
    public function generateTournamentFixtures(Tournament $tournament): array
    {
        try {
            return DB::transaction(function () use ($tournament) {
                $teams = $tournament->teams()->get();
                
                if ($teams->count() < 2) {
                    throw new Exception('Se necesitan al menos 2 equipos para generar fixtures');
                }

                $fixtures = match ($tournament->format) {
                    TournamentFormat::RoundRobin => $this->generateRoundRobinFixtures($tournament),
                    TournamentFormat::Elimination => $this->generateEliminationBracket($tournament),
                    TournamentFormat::Mixed => $this->generateMixedFormat($tournament),
                    default => throw new Exception('Formato de torneo no soportado')
                };

                // Asignar fechas y horarios
                $this->assignScheduleDates($tournament, $fixtures);
                
                // Asignar venues si están disponibles
                $this->assignVenues($tournament, $fixtures);
                
                // Asignar árbitros si están disponibles
                $this->assignReferees($tournament, $fixtures);

                Log::info("Fixtures generados para torneo {$tournament->name}", [
                    'tournament_id' => $tournament->id,
                    'total_matches' => count($fixtures),
                    'format' => $tournament->format
                ]);

                return $fixtures;
            });
        } catch (Exception $e) {
            Log::error("Error generando fixtures para torneo {$tournament->id}", [
                'error' => $e->getMessage(),
                'tournament_id' => $tournament->id
            ]);
            throw $e;
        }
    }

    /**
     * Generar fixtures round-robin (todos contra todos)
     */
    public function generateRoundRobinFixtures(Tournament $tournament): array
    {
        $teams = $tournament->teams()->get()->toArray();
        $fixtures = [];
        $teamCount = count($teams);
        
        // Si hay número impar de equipos, agregar un "bye"
        if ($teamCount % 2 !== 0) {
            $teams[] = null; // Bye team
            $teamCount++;
        }

        $rounds = $teamCount - 1;
        $matchesPerRound = $teamCount / 2;

        for ($round = 1; $round <= $rounds; $round++) {
            $roundMatches = [];
            
            for ($match = 0; $match < $matchesPerRound; $match++) {
                $home = ($round + $match - 1) % ($teamCount - 1);
                $away = ($teamCount - 1 - $match + $round - 1) % ($teamCount - 1);
                
                // El último equipo siempre juega
                if ($match === 0) {
                    $away = $teamCount - 1;
                }
                
                // Verificar que no sea un bye
                if ($teams[$home] !== null && $teams[$away] !== null) {
                    $fixture = $this->createMatch(
                        $tournament,
                        $teams[$home],
                        $teams[$away],
                        $round
                    );
                    
                    $roundMatches[] = $fixture;
                    $fixtures[] = $fixture;
                }
            }
            
            // Crear ronda si tiene partidos
            if (!empty($roundMatches)) {
                TournamentRound::create([
                    'tournament_id' => $tournament->id,
                    'round_number' => $round,
                    'name' => "Jornada {$round}",
                    'matches_count' => count($roundMatches),
                    'status' => 'scheduled'
                ]);
            }
        }

        return $fixtures;
    }

    /**
     * Generar bracket de eliminación
     */
    public function generateEliminationBracket(Tournament $tournament): array
    {
        $teams = $tournament->teams()->get();
        $teamCount = $teams->count();
        
        // Verificar que sea potencia de 2
        if (($teamCount & ($teamCount - 1)) !== 0) {
            throw new Exception('Para eliminación directa se necesita un número de equipos que sea potencia de 2');
        }

        $fixtures = [];
        $currentTeams = $teams->shuffle()->values();
        $round = 1;

        while ($currentTeams->count() > 1) {
            $roundMatches = [];
            $winners = collect();
            
            // Crear partidos para esta ronda
            for ($i = 0; $i < $currentTeams->count(); $i += 2) {
                $homeTeam = $currentTeams[$i];
                $awayTeam = $currentTeams[$i + 1];
                
                $fixture = $this->createMatch(
                    $tournament,
                    $homeTeam,
                    $awayTeam,
                    $round,
                    $this->getEliminationRoundName($round, $currentTeams->count())
                );
                
                $roundMatches[] = $fixture;
                $fixtures[] = $fixture;
            }
            
            // Crear ronda
            TournamentRound::create([
                'tournament_id' => $tournament->id,
                'round_number' => $round,
                'name' => $this->getEliminationRoundName($round, $currentTeams->count()),
                'matches_count' => count($roundMatches),
                'status' => 'scheduled',
                'is_elimination' => true
            ]);
            
            // Para la siguiente ronda, necesitaríamos los ganadores
            // Por ahora solo creamos la estructura
            $currentTeams = $currentTeams->take($currentTeams->count() / 2);
            $round++;
        }

        return $fixtures;
    }

    /**
     * Generar formato mixto (grupos + eliminación)
     */
    public function generateMixedFormat(Tournament $tournament): array
    {
        $teams = $tournament->teams()->get();
        $teamCount = $teams->count();
        
        if ($teamCount < 4) {
            throw new Exception('Se necesitan al menos 4 equipos para formato mixto');
        }

        $fixtures = [];
        
        // Fase de grupos (dividir en grupos de 4)
        $groupSize = 4;
        $groupCount = ceil($teamCount / $groupSize);
        $shuffledTeams = $teams->shuffle();
        
        for ($group = 1; $group <= $groupCount; $group++) {
            $groupTeams = $shuffledTeams->slice(($group - 1) * $groupSize, $groupSize);
            
            if ($groupTeams->count() >= 2) {
                $groupFixtures = $this->generateGroupFixtures($tournament, $groupTeams, $group);
                $fixtures = array_merge($fixtures, $groupFixtures);
            }
        }
        
        // Fase eliminatoria (se generaría después de completar grupos)
        // Por ahora solo creamos la estructura de grupos
        
        return $fixtures;
    }

    /**
     * Generar fixtures para un grupo específico
     */
    private function generateGroupFixtures(Tournament $tournament, $teams, int $groupNumber): array
    {
        $fixtures = [];
        $teamsArray = $teams->values()->toArray();
        $teamCount = count($teamsArray);
        
        for ($i = 0; $i < $teamCount; $i++) {
            for ($j = $i + 1; $j < $teamCount; $j++) {
                $fixture = $this->createMatch(
                    $tournament,
                    $teamsArray[$i],
                    $teamsArray[$j],
                    $groupNumber,
                    "Grupo {$groupNumber}"
                );
                
                $fixtures[] = $fixture;
            }
        }
        
        return $fixtures;
    }

    /**
     * Crear un partido
     */
    private function createMatch(Tournament $tournament, $homeTeam, $awayTeam, int $round, string $roundName = null): VolleyMatch
    {
        // Asegurar que tenemos IDs correctos
        $homeTeamId = is_array($homeTeam) ? $homeTeam['id'] : $homeTeam->id;
        $awayTeamId = is_array($awayTeam) ? $awayTeam['id'] : $awayTeam->id;
        
        return VolleyMatch::create([
            'tournament_id' => $tournament->id,
            'home_team_id' => $homeTeamId,
            'away_team_id' => $awayTeamId,
            'round' => $round,
            'round_name' => $roundName ?? "Jornada {$round}",
            'status' => MatchStatus::Scheduled,
            'created_by' => Auth::id() ?? 1
        ]);
    }

    /**
     * Asignar fechas y horarios a los partidos
     */
    public function assignScheduleDates(Tournament $tournament, array $fixtures): void
    {
        $startDate = Carbon::parse($tournament->start_date);
        $endDate = Carbon::parse($tournament->end_date);
        $totalDays = $startDate->diffInDays($endDate);
        
        if ($totalDays <= 0) {
            throw new Exception('Las fechas del torneo no son válidas');
        }

        $matchesPerDay = ceil(count($fixtures) / $totalDays);
        $currentDate = $startDate->copy();
        $matchesScheduledToday = 0;
        
        foreach ($fixtures as $index => $match) {
            // Si ya programamos suficientes partidos hoy, pasar al siguiente día
            if ($matchesScheduledToday >= $matchesPerDay && $currentDate->lt($endDate)) {
                $currentDate->addDay();
                $matchesScheduledToday = 0;
            }
            
            // Horarios típicos de voleibol (tardes/noches)
            $hour = 18 + ($matchesScheduledToday * 2); // 18:00, 20:00, 22:00
            if ($hour > 22) {
                $hour = 18;
            }
            
            $scheduledDateTime = $currentDate->copy()->setTime($hour, 0);
            
            $match->update([
                'scheduled_at' => $scheduledDateTime,
                'date' => $currentDate->toDateString(),
                'time' => $scheduledDateTime->toTimeString()
            ]);
            
            $matchesScheduledToday++;
        }
    }

    /**
     * Asignar venues a los partidos
     */
    public function assignVenues(Tournament $tournament, array $fixtures): void
    {
        $venues = Venue::where('is_active', true)
            ->where('capacity', '>=', 100)
            ->get();
            
        if ($venues->isEmpty()) {
            Log::warning("No hay venues disponibles para el torneo {$tournament->id}");
            return;
        }

        foreach ($fixtures as $index => $match) {
            $venue = $venues[$index % $venues->count()];
            
            $match->update([
                'venue_id' => $venue->id,
                'venue_name' => $venue->name
            ]);
        }
    }

    /**
     * Asignar árbitros a los partidos
     */
    public function assignReferees(Tournament $tournament, array $fixtures): void
    {
        $referees = Referee::where('is_active', true)
            ->where('is_available', true)
            ->get();
            
        if ($referees->count() < 2) {
            Log::warning("No hay suficientes árbitros disponibles para el torneo {$tournament->id}");
            return;
        }

        foreach ($fixtures as $index => $match) {
            $mainReferee = $referees[$index % $referees->count()];
            $assistantReferee = $referees[($index + 1) % $referees->count()];
            
            // Asegurar que no sea el mismo árbitro
            if ($mainReferee->id === $assistantReferee->id && $referees->count() > 1) {
                $assistantReferee = $referees[($index + 2) % $referees->count()];
            }
            
            $match->update([
                'main_referee_id' => $mainReferee->id,
                'assistant_referee_id' => $assistantReferee->id
            ]);
        }
    }

    /**
     * Validar configuración del torneo antes de generar fixtures
     *
     * @param \App\Models\Tournament $tournament
     * @return array
     */
    public function validateTournamentSetup(Tournament $tournament): array
    {
        $errors = [];
        $warnings = [];
        
        // Validar equipos
        $teamCount = $tournament->teams()->count();
        if ($teamCount < 2) {
            $errors[] = 'Se necesitan al menos 2 equipos';
        }
        
        // Validar fechas
        if (!$tournament->start_date || !$tournament->end_date) {
            $errors[] = 'Las fechas de inicio y fin son obligatorias';
        }
        
        if ($tournament->start_date && $tournament->end_date) {
            if (Carbon::parse($tournament->start_date)->gte(Carbon::parse($tournament->end_date))) {
                $errors[] = 'La fecha de inicio debe ser anterior a la fecha de fin';
            }
        }
        
        // Validar formato específico
        if ($tournament->format === TournamentFormat::Elimination) {
            if (($teamCount & ($teamCount - 1)) !== 0) {
                $warnings[] = 'Para eliminación directa se recomienda un número de equipos que sea potencia de 2';
            }
        }
        
        // Validar venues
        $venueCount = Venue::where('is_active', true)->count();
        if ($venueCount === 0) {
            $warnings[] = 'No hay venues activos configurados';
        }
        
        // Validar árbitros
        $refereeCount = Referee::where('is_active', true)->count();
        if ($refereeCount < 2) {
            $warnings[] = 'Se recomienda tener al menos 2 árbitros activos';
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'warnings' => $warnings
        ];
    }

    /**
     * Obtener nombre de ronda para eliminación
     */
    private function getEliminationRoundName(int $round, int $teamsInRound): string
    {
        return match ($teamsInRound) {
            2 => 'Final',
            4 => 'Semifinal',
            8 => 'Cuartos de Final',
            16 => 'Octavos de Final',
            default => "Ronda {$round}"
        };
    }

    /**
     * Calcular estadísticas del torneo
     */
    public function calculateTournamentStats(Tournament $tournament): array
    {
        $matches = $tournament->matches();
        
        return [
            'total_matches' => $matches->count(),
            'completed_matches' => $matches->where('status', MatchStatus::Finished)->count(),
            'pending_matches' => $matches->where('status', MatchStatus::Scheduled)->count(),
            'in_progress_matches' => $matches->where('status', MatchStatus::In_Progress)->count(),
            'total_teams' => $tournament->teams()->count(),
            'rounds_completed' => TournamentRound::where('tournament_id', $tournament->id)
                ->where('status', 'completed')->count(),
            'total_rounds' => TournamentRound::where('tournament_id', $tournament->id)->count()
        ];
    }
}