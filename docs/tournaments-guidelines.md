# 🏆 FASE C - SISTEMA INTEGRAL DE GESTIÓN DE TORNEOS

## 🎯 **OBJETIVOS ESTRATÉGICOS**

### **🔑 VALOR COMERCIAL CLAVE**
- **Ligas Federadas**: Control total sobre torneos oficiales con integración federativa
- **Ligas Informales**: Gestión autónoma de torneos locales/recreativos
- **Escalabilidad**: Un club puede participar en ambos tipos de ligas
- **Diferenciación**: Único software que maneja ambos ecosistemas

---

## 🏛️ **ARQUITECTURA ORGANIZACIONAL**

### **📊 Jerarquía de Entidades**
```
Liga (Federada/Informal)
├── Configuración de Torneos
├── Reglas Específicas 
├── Clubes Participantes
│   ├── Equipos por Categoría
│   │   ├── Nómina A (principal)
│   │   ├── Nómina B (suplentes)
│   │   └── Nómina C (reservas)
│   └── Múltiples Torneos Simultáneos
└── Tipos de Torneo Permitidos
```

### **🔗 Relaciones Críticas**
- **Liga ←→ Club**: Múltiple (un club puede estar en varias ligas)
- **Liga ←→ Torneo**: Uno a muchos (liga controla sus torneos)
- **Club ←→ Equipo**: Múltiple por categoría
- **Torneo ←→ Equipo**: Muchos a muchos (inscripciones)

---

## 🏟️ **TIPOS DE TORNEO Y ETAPAS**

### **🎮 Tipos de Competencia**

#### **1. Liga Regular (Round Robin)**
```yaml
Descripción: Todos contra todos
Etapas:
  - Registro de equipos
  - Generación de fixture
  - Desarrollo de jornadas
  - Tabla final de posiciones
Puntuación: 3-1-0 o personalizada
Ganador: Mayor puntaje acumulado
```

#### **2. Copa/Eliminación Directa**
```yaml
Descripción: Mata-mata desde primera ronda
Etapas:
  - Registro de equipos
  - Sorteo de bracket
  - Rondas eliminatorias
  - Final y definición
Puntuación: Pasa o queda eliminado
Ganador: Último en pie
```

#### **3. Torneo Mixto (Grupos + Llaves)**
```yaml
Descripción: Fase de grupos + eliminatorias
Etapas:
  - Registro de equipos
  - Distribución en grupos
  - Fase de grupos (round robin)
  - Clasificación automática
  - Fase eliminatoria
  - Final
Puntuación: Combinada
Ganador: Campeón de llaves
```

#### **4. Torneo Relámpago**
```yaml
Descripción: Formato rápido, sets cortos
Etapas:
  - Registro express
  - Partidos simultáneos
  - Eliminación rápida
  - Premiación inmediata
Puntuación: Sets a 15 puntos
Ganador: Mayor eficiencia temporal
```

---

## ⚙️ **CONFIGURACIÓN DE TORNEOS**

### **🎛️ Parámetros Configurables por Liga**

#### **Configuración Básica**
```php
// Estructura de configuración
TournamentConfig {
    league_id: integer
    tournament_type: enum['league', 'cup', 'mixed', 'flash']
    category: enum['mini', 'infantil', 'cadete', 'juvenil', 'mayores']
    gender: enum['femenino', 'masculino', 'mixto']
    max_teams: integer
    min_teams: integer
    registration_deadline: datetime
    start_date: datetime
    end_date: datetime
}
```

#### **Reglas de Juego**
```php
// Configuración de puntuación
GameRules {
    sets_to_win: integer [2,3,5] // 2 de 3, 3 de 5
    points_per_set: integer [15,21,25]
    tiebreak_points: integer [15,25]
    table_points: array [3-1-0, 3-2-1-0] // Victoria-Derrota o Victoria-Derrota por sets
    timeout_per_set: integer
    substitutions_limit: integer
}
```

#### **Configuración de Grupos**
```php
// Distribución automática
GroupConfig {
    auto_distribution: boolean
    teams_per_group: integer [4,5,6]
    total_groups: integer // calculado automáticamente
    seeding_method: enum['random', 'ranking', 'geographic']
    balance_groups: boolean // equilibrar números entre grupos
}
```

#### **Premios y Reconocimientos**
```php
// Sistema de premiación
PrizesConfig {
    positions_awarded: integer [1,2,3,4,8] // Cuántos lugares se premian
    prize_types: array ['trophy', 'medal', 'certificate', 'money']
    mvp_awards: boolean
    best_setter: boolean
    best_spiker: boolean
    fair_play_award: boolean
}
```

---

## 🎯 **DISTRIBUCIÓN INTELIGENTE DE GRUPOS**

### **🔄 Algoritmo de Balanceo**

#### **Problema a Resolver**
- **Equipos inscritos**: Variable (ej: 13 equipos)
- **Grupos deseados**: Definido por liga (ej: 3 grupos)
- **Distribución**: 13 ÷ 3 = 4.33 → Grupos de 4-4-5

#### **Solución Implementada**
```php
class GroupDistributionService {
    
    public function distributeTeams($teams, $desired_groups) {
        $total_teams = count($teams);
        $base_size = intval($total_teams / $desired_groups);
        $remainder = $total_teams % $desired_groups;
        
        // Crear grupos base
        $groups = [];
        for($i = 0; $i < $desired_groups; $i++) {
            $group_size = $base_size + ($i < $remainder ? 1 : 0);
            $groups[$i] = [
                'size' => $group_size,
                'teams' => [],
                'expected_matches' => $this->calculateMatches($group_size)
            ];
        }
        
        return $this->assignTeamsToGroups($teams, $groups);
    }
    
    // Compensación de desventaja por menos partidos
    public function adjustPointSystem($groups) {
        $max_matches = max(array_column($groups, 'expected_matches'));
        
        foreach($groups as &$group) {
            if($group['expected_matches'] < $max_matches) {
                // Aplicar factor de compensación en puntuación
                $group['point_multiplier'] = $max_matches / $group['expected_matches'];
            }
        }
        
        return $groups;
    }
}
```

#### **Métodos de Distribución**
1. **Aleatorio**: Sorteo puro sin consideraciones
2. **Por Ranking**: Equipos fuertes separados en grupos diferentes
3. **Geográfico**: Minimizar distancias de viaje
4. **Híbrido**: Combinación balanceada de factores

---

## 🏆 **SISTEMA DE PUNTUACIÓN AVANZADO**

### **📊 Puntuación por Modalidad**

#### **Liga Regular**
```php
// Sistema tradicional 3-1-0
$points = [
    'win' => 3,
    'loss' => 0,
    'forfeit_win' => 3,
    'forfeit_loss' => -1 // Penalización
];

// Sistema avanzado 3-2-1-0 (considerando sets)
$points = [
    'win_3_0' => 3, // Victoria 3-0
    'win_3_1' => 3, // Victoria 3-1
    'win_3_2' => 3, // Victoria 3-2
    'loss_2_3' => 1, // Derrota 2-3 (buen partido)
    'loss_1_3' => 0, // Derrota 1-3
    'loss_0_3' => 0  // Derrota 0-3
];
```

#### **Criterios de Desempate**
```php
class TieBreakService {
    
    public function applyTieBreakCriteria($tied_teams) {
        // 1. Enfrentamientos directos
        $head_to_head = $this->calculateHeadToHead($tied_teams);
        if($this->hasWinner($head_to_head)) return $head_to_head;
        
        // 2. Diferencia de sets
        $set_difference = $this->calculateSetDifference($tied_teams);
        if($this->hasWinner($set_difference)) return $set_difference;
        
        // 3. Diferencia de puntos
        $point_difference = $this->calculatePointDifference($tied_teams);
        if($this->hasWinner($point_difference)) return $point_difference;
        
        // 4. Sorteo final
        return $this->randomTieBreak($tied_teams);
    }
}
```

---

## 🎮 **GESTOR DE PARTIDOS EN TIEMPO REAL**

### **📱 Interface de Partido**

#### **Estados del Partido**
```php
enum MatchStatus {
    SCHEDULED,     // Programado
    PRE_MATCH,     // Pre-partido (verificación jugadoras)
    IN_PROGRESS,   // En curso
    SET_BREAK,     // Descanso entre sets
    FINISHED,      // Finalizado
    POSTPONED,     // Pospuesto
    CANCELLED      // Cancelado
}
```

#### **Tracking en Tiempo Real**
```php
class LiveMatchService {
    
    public function updateScore($match_id, $set, $team_id, $points) {
        // Validar punto válido
        $this->validateScoreUpdate($match_id, $set, $points);
        
        // Actualizar marcador
        $this->updateMatchScore($match_id, $set, $team_id, $points);
        
        // Verificar fin de set/partido
        if($this->isSetComplete($match_id, $set)) {
            $this->completeSet($match_id, $set);
        }
        
        if($this->isMatchComplete($match_id)) {
            $this->completeMatch($match_id);
        }
        
        // Broadcast en tiempo real
        broadcast(new MatchUpdated($match_id));
    }
    
    public function trackRotation($match_id, $team_id, $rotation_data) {
        // Registrar rotación
        $this->recordRotation($match_id, $team_id, $rotation_data);
        
        // Validar rotación correcta
        $this->validateRotation($rotation_data);
        
        // Actualizar posiciones en tiempo real
        broadcast(new RotationUpdated($match_id, $team_id));
    }
}
```

---

## 🔄 **SISTEMA DE ROTACIONES Y SUSTITUCIONES**

### **⚙️ Control de Rotaciones**

#### **Tracking Automático**
```php
class RotationSystem {
    
    public function initializeRotation($match_id, $team_id, $starting_lineup) {
        $rotation = [
            'match_id' => $match_id,
            'team_id' => $team_id,
            'set_number' => 1,
            'positions' => [
                1 => $starting_lineup[0], // Servidor
                2 => $starting_lineup[1], // Zona 2
                3 => $starting_lineup[2], // Zona 3
                4 => $starting_lineup[3], // Zona 4
                5 => $starting_lineup[4], // Zona 5
                6 => $starting_lineup[5]  // Zona 6
            ],
            'rotation_order' => 0
        ];
        
        return $this->saveRotation($rotation);
    }
    
    public function rotateTeam($match_id, $team_id) {
        $current = $this->getCurrentRotation($match_id, $team_id);
        
        // Rotar posiciones (todos avanzan una posición)
        $new_positions = [
            1 => $current['positions'][6], // El de zona 6 pasa a servir
            2 => $current['positions'][1],
            3 => $current['positions'][2],
            4 => $current['positions'][3],
            5 => $current['positions'][4],
            6 => $current['positions'][5]
        ];
        
        return $this->updateRotation($match_id, $team_id, $new_positions);
    }
}
```

#### **Gestión de Sustituciones**
```php
class SubstitutionSystem {
    
    public function makeSubstitution($match_id, $team_id, $player_out, $player_in, $position) {
        // Validar sustitución legal
        $this->validateSubstitution($match_id, $team_id, $player_out, $player_in);
        
        // Registrar cambio
        $substitution = [
            'match_id' => $match_id,
            'team_id' => $team_id,
            'set_number' => $this->getCurrentSet($match_id),
            'player_out_id' => $player_out,
            'player_in_id' => $player_in,
            'position' => $position,
            'minute' => now(),
            'score_at_substitution' => $this->getCurrentScore($match_id)
        ];
        
        // Actualizar rotación activa
        $this->updateActiveRotation($match_id, $team_id, $position, $player_in);
        
        return $this->recordSubstitution($substitution);
    }
    
    public function validateSubstitution($match_id, $team_id, $player_out, $player_in) {
        // Verificar límite de cambios por set
        $substitutions_count = $this->getSubstitutionsCount($match_id, $team_id);
        if($substitutions_count >= 6) {
            throw new MaxSubstitutionsReachedException();
        }
        
        // Verificar que el jugador entrante no haya sido sustituido antes
        if($this->wasPlayerSubstituted($match_id, $player_in)) {
            throw new PlayerAlreadySubstitutedException();
        }
        
        // Verificar que el jugador esté en la nómina del partido
        if(!$this->isPlayerInMatchRoster($match_id, $team_id, $player_in)) {
            throw new PlayerNotInRosterException();
        }
    }
}
```

---

## 🟨 **SISTEMA DE AMONESTACIONES Y DISCIPLINA**

### **⚖️ Tipos de Sanciones**

#### **Clasificación de Faltas**
```php
enum CardType {
    YELLOW,        // Advertencia
    RED,          // Expulsión del set
    RED_MATCH,    // Expulsión del partido
    RED_TOURNAMENT // Expulsión del torneo
}

enum ViolationType {
    MISCONDUCT,           // Mala conducta
    DELAY_OF_GAME,       // Retraso de juego
    UNSPORTSMANLIKE,     // Conducta antideportiva
    VIOLENT_CONDUCT,     // Conducta violenta
    REFEREE_ABUSE,       // Irrespeto al árbitro
    ILLEGAL_SUBSTITUTION, // Sustitución ilegal
    ROTATION_FAULT       // Falta de rotación
}
```

#### **Sistema de Registro**
```php
class DisciplinarySystem {
    
    public function issueCard($match_id, $player_id, $card_type, $violation_type, $description) {
        $card = [
            'match_id' => $match_id,
            'player_id' => $player_id,
            'team_id' => $this->getPlayerTeam($match_id, $player_id),
            'card_type' => $card_type,
            'violation_type' => $violation_type,
            'description' => $description,
            'set_number' => $this->getCurrentSet($match_id),
            'minute' => now(),
            'referee_id' => $this->getMatchReferee($match_id)
        ];
        
        // Aplicar consecuencias automáticas
        $this->applyCardConsequences($card);
        
        // Notificar al sistema de registro
        $this->recordDisciplinaryAction($card);
        
        return $card;
    }
    
    public function applyCardConsequences($card) {
        switch($card['card_type']) {
            case CardType::YELLOW:
                // Solo registro, sin consecuencias inmediatas
                break;
                
            case CardType::RED:
                // Expulsar jugador del set actual
                $this->expelPlayerFromSet($card['match_id'], $card['player_id']);
                break;
                
            case CardType::RED_MATCH:
                // Expulsar jugador del partido completo
                $this->expelPlayerFromMatch($card['match_id'], $card['player_id']);
                break;
                
            case CardType::RED_TOURNAMENT:
                // Expulsar jugador del torneo
                $this->expelPlayerFromTournament($card['player_id']);
                break;
        }
    }
}
```

---

## 📊 **REPORTES Y ESTADÍSTICAS AVANZADAS**

### **🏆 Rankings y Clasificaciones**

#### **Estadísticas por Jugadora**
```php
class PlayerStatsService {
    
    public function generatePlayerStats($player_id, $tournament_id = null) {
        return [
            'matches_played' => $this->getMatchesPlayed($player_id, $tournament_id),
            'sets_played' => $this->getSetsPlayed($player_id, $tournament_id),
            'points_scored' => $this->getPointsScored($player_id, $tournament_id),
            'aces' => $this->getAces($player_id, $tournament_id),
            'blocks' => $this->getBlocks($player_id, $tournament_id),
            'attacks' => $this->getAttacks($player_id, $tournament_id),
            'attack_efficiency' => $this->calculateAttackEfficiency($player_id, $tournament_id),
            'service_efficiency' => $this->calculateServiceEfficiency($player_id, $tournament_id),
            'reception_efficiency' => $this->calculateReceptionEfficiency($player_id, $tournament_id)
        ];
    }
}
```

#### **Rankings de Equipos**
```php
class TeamRankingService {
    
    public function generateTournamentRanking($tournament_id) {
        $teams = $this->getTournamentTeams($tournament_id);
        
        foreach($teams as &$team) {
            $team['stats'] = [
                'matches_played' => $this->getTeamMatches($team['id'], $tournament_id),
                'wins' => $this->getTeamWins($team['id'], $tournament_id),
                'losses' => $this->getTeamLosses($team['id'], $tournament_id),
                'sets_won' => $this->getTeamSetsWon($team['id'], $tournament_id),
                'sets_lost' => $this->getTeamSetsLost($team['id'], $tournament_id),
                'points_for' => $this->getTeamPointsFor($team['id'], $tournament_id),
                'points_against' => $this->getTeamPointsAgainst($team['id'], $tournament_id),
                'table_points' => $this->getTeamTablePoints($team['id'], $tournament_id)
            ];
        }
        
        return $this->sortTeamsByRanking($teams);
    }
}
```

---

## 🚀 **ROADMAP DE IMPLEMENTACIÓN - 15 DÍAS**

### **🔵 SPRINT 1: Fundaciones (Días 1-5)**
- **Día 1**: Modelos base (Tournament, Match, Team, TournamentTeam)
- **Día 2**: Enums y configuraciones (TournamentType, MatchStatus, CardType)
- **Día 3**: TournamentService + algoritmos de distribución
- **Día 4**: MatchService + sistema de puntuación
- **Día 5**: Testing unitario de servicios base

### **🟢 SPRINT 2: Funcionalidades Core (Días 6-10)**
- **Día 6**: Sistema de rotaciones + RotationService
- **Día 7**: Sistema disciplinario + DisciplinaryService
- **Día 8**: Generación automática de fixtures
- **Día 9**: API tiempo real + WebSockets para partidos
- **Día 10**: Sistema de estadísticas + PlayerStatsService

### **🟡 SPRINT 3: Interfaces Admin (Días 11-15)**
- **Día 11**: TournamentResource (Filament)
- **Día 12**: MatchResource + interface tiempo real
- **Día 13**: TeamResource + gestión nóminas
- **Día 14**: Dashboard torneos + reportes
- **Día 15**: Testing integral + refinamientos

---

## 💡 **CONSIDERACIONES TÉCNICAS CRÍTICAS**

### **🔒 Seguridad y Validaciones**
- **Validación de nóminas**: Verificar elegibilidad antes de cada partido
- **Anti-fraude**: Hash de resultados para prevenir manipulación
- **Auditoría completa**: Log de todas las acciones en partidos
- **Control de acceso**: Solo árbitros pueden modificar marcadores

### **⚡ Performance y Escalabilidad**
- **Cache inteligente**: Tabla de posiciones y estadísticas
- **WebSockets optimizados**: Solo broadcasts necesarios
- **Jobs en cola**: Procesamiento de estadísticas complejas
- **Índices estratégicos**: Consultas de rankings y estadísticas

### **🔄 Integraciones Futuras**
- **API externa**: Para apps móviles de espectadores
- **Streaming**: Integración con plataformas de transmisión
- **Federaciones**: Conexión con sistemas federativos nacionales
- **Analytics**: Integración con herramientas de análisis deportivo

---

## 📋 **CASOS DE USO PRINCIPALES**

### **🎯 Liga Federada - Torneo Oficial**
1. Liga crea torneo con reglas federativas
2. Clubes inscriben equipos con jugadoras federadas
3. Sistema valida eligibilidad automáticamente
4. Genera fixture respetando calendario federativo
5. Partidos con verificación QR obligatoria
6. Resultados reportados automáticamente a federación

### **🎯 Liga Informal - Torneo Local**
1. Liga configura torneo con reglas propias
2. Acepta clubes no federados
3. Flexibilidad en fechas y formato
4. Sistema de puntuación personalizado
5. Premios y reconocimientos locales
6. Dashboard público para comunidad

### **🎯 Club Multi-Liga**
1. Club participa en liga federada Y liga informal
2. Mismo pool de jugadoras
3. Calendarios no conflictivos
4. Estadísticas separadas por liga
5. Gestión unificada desde un dashboard

---

**🎯 PRÓXIMO PASO**: ¿Iniciamos la implementación inmediatamente con este enfoque? Este sistema de torneos será el verdadero diferenciador comercial de VolleyPass.
