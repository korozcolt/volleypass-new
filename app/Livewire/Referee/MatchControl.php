<?php

namespace App\Livewire\Referee;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class MatchControl extends Component
{
    public $match;
    public $homeScore = 0;
    public $awayScore = 0;
    public $currentSet = 1;
    public $sets = [];
    public $isLive = false;
    public $timeouts = [
        'home' => 2,
        'away' => 2
    ];
    public $substitutions = [
        'home' => 6,
        'away' => 6
    ];
    public $cards = [];
    public $rotations = [
        'home' => 1,
        'away' => 1
    ];
    public $matchEvents = [];
    
    protected $listeners = [
        'startMatch' => 'startMatch',
        'endMatch' => 'endMatch',
        'addPoint' => 'addPoint',
        'removePoint' => 'removePoint'
    ];

    public function mount($matchId = null)
    {
        if ($matchId) {
            $this->loadMatch($matchId);
        } else {
            $this->initializeNewMatch();
        }
    }

    public function loadMatch($matchId)
    {
        // En una implementación real, cargarías el partido desde la base de datos
        $this->match = [
            'id' => $matchId,
            'home_team' => 'Voleibol Bogotá',
            'away_team' => 'Medellín Eagles',
            'tournament' => 'Liga Profesional Sucre 2024',
            'venue' => 'Coliseo Municipal',
            'date' => now()->format('Y-m-d'),
            'time' => now()->format('H:i')
        ];
        
        $this->initializeSets();
    }

    public function initializeNewMatch()
    {
        $this->match = [
            'id' => 'demo',
            'home_team' => 'Equipo Local',
            'away_team' => 'Equipo Visitante',
            'tournament' => 'Torneo Demo',
            'venue' => 'Cancha Demo',
            'date' => now()->format('Y-m-d'),
            'time' => now()->format('H:i')
        ];
        
        $this->initializeSets();
    }

    public function initializeSets()
    {
        $this->sets = [
            1 => ['home' => 0, 'away' => 0, 'finished' => false],
            2 => ['home' => 0, 'away' => 0, 'finished' => false],
            3 => ['home' => 0, 'away' => 0, 'finished' => false],
            4 => ['home' => 0, 'away' => 0, 'finished' => false],
            5 => ['home' => 0, 'away' => 0, 'finished' => false]
        ];
    }

    public function startMatch()
    {
        $this->isLive = true;
        $this->addEvent('match_started', 'Inicio del partido');
        $this->dispatch('match-status-changed', ['status' => 'live']);
    }

    public function endMatch()
    {
        $this->isLive = false;
        $this->addEvent('match_ended', 'Fin del partido');
        $this->dispatch('match-status-changed', ['status' => 'finished']);
    }

    public function addPoint($team)
    {
        if (!$this->isLive) return;
        
        if ($team === 'home') {
            $this->homeScore++;
            $this->sets[$this->currentSet]['home']++;
        } else {
            $this->awayScore++;
            $this->sets[$this->currentSet]['away']++;
        }
        
        $this->addEvent('point_scored', "Punto para " . ($team === 'home' ? $this->match['home_team'] : $this->match['away_team']));
        
        // Verificar si el set terminó
        $this->checkSetEnd();
        
        // Verificar rotación automática cada 6 puntos
        $this->checkRotation();
        
        $this->dispatch('score-updated');
    }

    public function removePoint($team)
    {
        if (!$this->isLive) return;
        
        if ($team === 'home' && $this->homeScore > 0) {
            $this->homeScore--;
            $this->sets[$this->currentSet]['home']--;
        } elseif ($team === 'away' && $this->awayScore > 0) {
            $this->awayScore--;
            $this->sets[$this->currentSet]['away']--;
        }
        
        $this->addEvent('point_removed', "Punto removido de " . ($team === 'home' ? $this->match['home_team'] : $this->match['away_team']));
        $this->dispatch('score-updated');
    }

    public function checkSetEnd()
    {
        $homePoints = $this->sets[$this->currentSet]['home'];
        $awayPoints = $this->sets[$this->currentSet]['away'];
        
        // Reglas básicas de voleibol: 25 puntos con diferencia de 2
        if (($homePoints >= 25 && $homePoints - $awayPoints >= 2) || 
            ($awayPoints >= 25 && $awayPoints - $homePoints >= 2)) {
            
            $this->sets[$this->currentSet]['finished'] = true;
            $winner = $homePoints > $awayPoints ? $this->match['home_team'] : $this->match['away_team'];
            $this->addEvent('set_finished', "Set {$this->currentSet} ganado por {$winner}");
            
            // Verificar si el partido terminó
            if ($this->checkMatchEnd()) {
                $this->endMatch();
            } else {
                $this->nextSet();
            }
        }
    }

    public function checkMatchEnd()
    {
        $homeSets = 0;
        $awaySets = 0;
        
        foreach ($this->sets as $set) {
            if ($set['finished']) {
                if ($set['home'] > $set['away']) {
                    $homeSets++;
                } else {
                    $awaySets++;
                }
            }
        }
        
        // Mejor de 5 sets
        return $homeSets >= 3 || $awaySets >= 3;
    }

    public function nextSet()
    {
        if ($this->currentSet < 5) {
            $this->currentSet++;
            $this->homeScore = 0;
            $this->awayScore = 0;
            
            // Resetear timeouts para el nuevo set
            $this->timeouts = ['home' => 2, 'away' => 2];
            
            $this->addEvent('set_started', "Inicio del Set {$this->currentSet}");
        }
    }

    public function checkRotation()
    {
        // Rotación automática cada 6 puntos en voleibol
        if ($this->homeScore % 6 === 0 && $this->homeScore > 0) {
            $this->rotations['home'] = ($this->rotations['home'] % 6) + 1;
            $this->addEvent('rotation', "Rotación automática - {$this->match['home_team']}");
        }
        
        if ($this->awayScore % 6 === 0 && $this->awayScore > 0) {
            $this->rotations['away'] = ($this->rotations['away'] % 6) + 1;
            $this->addEvent('rotation', "Rotación automática - {$this->match['away_team']}");
        }
    }

    public function useTimeout($team)
    {
        if ($this->timeouts[$team] > 0) {
            $this->timeouts[$team]--;
            $teamName = $team === 'home' ? $this->match['home_team'] : $this->match['away_team'];
            $this->addEvent('timeout', "Tiempo fuera solicitado por {$teamName}");
        }
    }

    public function addSubstitution($team)
    {
        if ($this->substitutions[$team] > 0) {
            $this->substitutions[$team]--;
            $teamName = $team === 'home' ? $this->match['home_team'] : $this->match['away_team'];
            $this->addEvent('substitution', "Sustitución realizada por {$teamName}");
        }
    }

    public function addCard($team, $type, $player = null)
    {
        $this->cards[] = [
            'team' => $team,
            'type' => $type, // 'yellow' or 'red'
            'player' => $player ?? 'Jugador',
            'set' => $this->currentSet,
            'time' => now()->format('H:i:s')
        ];
        
        $teamName = $team === 'home' ? $this->match['home_team'] : $this->match['away_team'];
        $cardType = $type === 'yellow' ? 'Tarjeta Amarilla' : 'Tarjeta Roja';
        $this->addEvent('card', "{$cardType} para {$teamName} - {$player}");
    }

    public function addEvent($type, $description)
    {
        $this->matchEvents[] = [
            'type' => $type,
            'description' => $description,
            'time' => now()->format('H:i:s'),
            'set' => $this->currentSet
        ];
    }

    public function getMatchStats()
    {
        $homeSets = 0;
        $awaySets = 0;
        
        foreach ($this->sets as $set) {
            if ($set['finished']) {
                if ($set['home'] > $set['away']) {
                    $homeSets++;
                } else {
                    $awaySets++;
                }
            }
        }
        
        return [
            'home_sets' => $homeSets,
            'away_sets' => $awaySets,
            'total_points_home' => array_sum(array_column($this->sets, 'home')),
            'total_points_away' => array_sum(array_column($this->sets, 'away'))
        ];
    }

    public function render()
    {
        return view('livewire.referee.match-control');
    }
}