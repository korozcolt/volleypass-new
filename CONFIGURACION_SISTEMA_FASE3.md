# ⚙️ CONFIGURACIÓN DEL SISTEMA - FASE 3

## 🎯 **CONFIGURACIONES CRÍTICAS PARA FASE 3**

### **🔴 ELEMENTOS CRÍTICOS INCLUIDOS**

#### **1. Lógica de Negocio (Federación y Torneos)**
- Reglas de federación manual (sin pagos automáticos)
- Validaciones de elegibilidad para torneos
- Estados de jugadoras para partidos en vivo
- Control de traspasos con aprobaciones

#### **2. Preparación para Interfaces Críticas (Partidos en Vivo)**
- Estados de equipos y jugadoras
- Validaciones de participación en tiempo real
- Base de datos preparada para marcadores
- API endpoints para consultas rápidas

#### **3. Fundamentos para Vista Pública (Marcadores)**
- Configuraciones de datos públicos
- Estados de torneos visibles
- Información de equipos para marcadores
- Control de privacidad de datos

#### **4. Base para Testing Exhaustivo**
- Servicios desacoplados y testeable
- Estados y transiciones bien definidos
- Datos de prueba completos
- Validaciones robustas

## 🎯 **CONFIGURACIONES REQUERIDAS PARA FASE 3**

### **1. Configuraciones de Liga (LeagueConfiguration)**

#### **Traspasos**
```php
'transfer_settings' => [
    'approval_required' => true,           // ¿Requiere aprobación de liga?
    'approval_timeout_days' => 7,          // Días para aprobar/rechazar
    'max_transfers_per_season' => 2,       // Límite por temporada
    'transfer_window_start' => '2024-01-01', // Ventana de traspasos
    'transfer_window_end' => '2024-03-31',
    'inter_league_allowed' => false,       // ¿Permite traspasos entre ligas?
]
```

#### **Documentación**
```php
'document_requirements' => [
    'strictness_level' => 'high',         // low, medium, high
    'medical_certificate_required' => true,
    'medical_validity_months' => 6,
    'photo_required' => true,
    'parent_authorization_under_18' => true,
    'insurance_required' => false,
]
```

#### **Categorías y Restricciones**
```php
'category_rules' => [
    'age_verification_strict' => true,
    'category_mixing_allowed' => false,   // ¿Puede jugar en categoría superior?
    'guest_players_allowed' => true,      // ¿Permite jugadoras invitadas?
    'max_guest_players_per_match' => 2,
]
```

#### **Disciplina**
```php
'disciplinary_rules' => [
    'yellow_card_accumulation_limit' => 3,
    'suspension_games_per_red_card' => 1,
    'appeal_process_enabled' => true,
    'appeal_deadline_days' => 3,
]
```

#### **Federación (Manual)**
```php
'federation_rules' => [
    'federation_required_for_tournaments' => true,
    'federation_grace_period_days' => 30,
    'manual_approval_process' => true,     // Sin pagos automáticos
    'federation_validity_months' => 12,
]
```

### **2. Tipos de Clubes**

#### **Club Federado**
```php
'federated_club' => [
    'type' => 'federated',
    'tournament_access' => 'official_only',
    'transfer_restrictions' => 'strict',
    'document_requirements' => 'high',
    'league_oversight' => true,
    'manual_federation_status' => true,    // Estado manual
]
```

#### **Club Descentralizado**
```php
'decentralized_club' => [
    'type' => 'decentralized',
    'tournament_access' => 'alternate_only',
    'transfer_restrictions' => 'flexible',
    'document_requirements' => 'basic',
    'league_oversight' => false,
    'self_managed' => true,
]
```

### **3. Estados de Federación (Manual)**

#### **Estados Disponibles**
```php
enum FederationStatus {
    NOT_FEDERATED = 'not_federated';           // No federado
    DOCUMENTATION_PENDING = 'doc_pending';     // Documentos pendientes
    REVIEW_PENDING = 'review_pending';         // En revisión manual
    FEDERATED_ACTIVE = 'federated_active';     // Federado activo
    FEDERATED_SUSPENDED = 'federated_suspended'; // Suspendido
    FEDERATION_EXPIRED = 'federation_expired';  // Vencido
}
```

#### **Transiciones Manuales**
```php
'federation_transitions' => [
    'not_federated' => ['documentation_pending'],
    'documentation_pending' => ['review_pending', 'not_federated'],
    'review_pending' => ['federated_active', 'not_federated'],
    'federated_active' => ['federated_suspended', 'federation_expired'],
    'federated_suspended' => ['federated_active', 'federation_expired'],
    'federation_expired' => ['review_pending', 'not_federated'],
]
```

### **4. Sistema de Traspasos**

#### **Estados de Traspaso**
```php
enum TransferStatus {
    REQUESTED = 'requested';        // Solicitado por entrenador
    UNDER_REVIEW = 'under_review';  // En revisión por liga
    APPROVED = 'approved';          // Aprobado por liga
    REJECTED = 'rejected';          // Rechazado
    COMPLETED = 'completed';        // Completado
    CANCELLED = 'cancelled';        // Cancelado
}
```

#### **Validaciones de Traspaso**
```php
'transfer_validations' => [
    'player_must_be_active' => true,
    'no_pending_sanctions' => true,
    'within_transfer_window' => true,
    'club_capacity_check' => true,
    'category_compatibility' => true,
    'league_approval_if_required' => true,
]
```

### **5. Configuraciones del Sistema**

#### **Configuraciones Generales**
```php
// En SystemConfiguration
'league_management' => [
    'default_transfer_approval' => true,
    'default_document_strictness' => 'medium',
    'default_federation_validity' => 12,
    'manual_federation_mode' => true,      // Sin pagos automáticos
]
```

#### **Configuraciones para Interfaces Críticas**
```php
'tournament_interface' => [
    'live_match_updates' => true,          // Para partidos en vivo
    'real_time_scoring' => true,           // Marcadores tiempo real
    'player_eligibility_check' => true,    // Validación en partidos
    'referee_interface_enabled' => true,   // Interface para árbitros
]
```

#### **Configuraciones para Vista Pública**
```php
'public_data' => [
    'show_live_scores' => true,            // Marcadores públicos
    'show_team_rosters' => false,         // Nóminas privadas
    'show_player_stats' => true,          // Estadísticas públicas
    'show_tournament_brackets' => true,    // Llaves de torneos
    'public_api_enabled' => true,         // API pública
]
```

#### **Configuraciones de Testing**
```php
'testing_environment' => [
    'mock_data_enabled' => true,          // Datos de prueba
    'test_tournaments' => true,           // Torneos de prueba
    'simulation_mode' => true,            // Simulación de partidos
    'debug_api_calls' => true,           // Debug de API
]
```

#### **Configuraciones de Notificaciones**
```php
'notifications' => [
    'transfer_request_notification' => true,
    'federation_expiry_warning_days' => [30, 15, 7, 1],
    'document_approval_notification' => true,
    'disciplinary_action_notification' => true,
    'live_match_notifications' => true,    // Para partidos en vivo
    'public_score_updates' => true,       // Actualizaciones públicas
]
```

## 🛠️ **IMPLEMENTACIÓN EN CÓDIGO**

### **1. Seeder de Configuraciones**
```php
// database/seeders/LeagueConfigurationSeeder.php
$configurations = [
    [
        'league_id' => 1,
        'key' => 'transfer_approval_required',
        'value' => true,
        'type' => 'boolean',
        'description' => 'Requiere aprobación de liga para traspasos'
    ],
    [
        'league_id' => 1,
        'key' => 'document_strictness_level',
        'value' => 'high',
        'type' => 'string',
        'description' => 'Nivel de exigencia documental'
    ],
    // ... más configuraciones
];
```

### **2. Helper Functions**
```php
// app/helpers.php
function league_config($league_id, $key, $default = null) {
    return app(LeagueConfigurationService::class)->get($league_id, $key, $default);
}

function club_is_federated($club_id) {
    return Club::find($club_id)?->federation_type === 'federated';
}

function can_request_transfer($player_id, $to_club_id) {
    return app(TransferService::class)->canRequestTransfer($player_id, $to_club_id);
}
```

### **3. Middleware para Validaciones**
```php
// app/Http/Middleware/ValidateFederationAccess.php
class ValidateFederationAccess {
    public function handle($request, Closure $next) {
        $tournament = $request->route('tournament');
        $club = auth()->user()->club;
        
        if ($tournament->requires_federation && !$club->is_federated) {
            abort(403, 'Este torneo requiere clubes federados');
        }
        
        return $next($request);
    }
}
```

## 📋 **CHECKLIST DE IMPLEMENTACIÓN**

### **Modelos y Migraciones**
- [ ] Crear `LeagueConfiguration` model
- [ ] Actualizar `Club` model con federation_type
- [ ] Actualizar `PlayerTransfer` model
- [ ] Crear migración para configuraciones de liga
- [ ] Crear migración para tipos de federación

### **Servicios**
- [ ] Implementar `LeagueConfigurationService`
- [ ] Implementar `TransferService`
- [ ] Implementar `FederationTypeService`
- [ ] Actualizar `FederationService` (sin pagos)

### **Resources de Filament**
- [ ] Actualizar `LeagueResource` con tab de configuraciones
- [ ] Actualizar `ClubResource` con tipo de federación
- [ ] Crear `TransferResource`
- [ ] Actualizar `PlayerResource` con tab de traspasos

### **Testing**
- [ ] Tests para configuraciones de liga
- [ ] Tests para sistema de traspasos
- [ ] Tests para tipos de federación
- [ ] Tests de integración

### **Seeders**
- [ ] `LeagueConfigurationSeeder`
- [ ] Actualizar `ClubSeeder` con tipos
- [ ] `TransferTestSeeder`

## ✅ **RESULTADO ESPERADO**

Al completar esta configuración tendremos:

1. **Sistema flexible** que se adapta a diferentes tipos de ligas
2. **Gestión manual** de federación sin dependencias de pagos
3. **Control granular** de traspasos según reglas de liga
4. **Diferenciación clara** entre clubes federados y descentralizados
5. **Base sólida** para implementar torneos en Fase 4

---

**¡Sistema configurado para desarrollo ágil sin bloqueos por pagos!** 🚀

## 🚀 **PREPARACIÓN PARA ELEMENTOS CRÍTICOS**

### **1. Lógica de Negocio (Federación y Torneos)**

#### **API Endpoints Preparados**
```php
// Para consultas rápidas en interfaces críticas
Route::get('/api/player/{id}/eligibility', 'PlayerEligibilityController@check');
Route::get('/api/club/{id}/federation-status', 'ClubFederationController@status');
Route::get('/api/tournament/{id}/participants', 'TournamentController@participants');
Route::get('/api/league/{id}/rules', 'LeagueConfigurationController@rules');
```

#### **Servicios para Validación Rápida**
```php
// Para uso en partidos en vivo
class QuickValidationService {
    public function isPlayerEligible($player_id, $tournament_id);
    public function getClubFederationStatus($club_id);
    public function validateTransferStatus($player_id);
    public function getLeagueRules($league_id);
}
```

### **2. Preparación para Interfaces Críticas (Partidos en Vivo)**

#### **Estados de Jugadoras para Partidos**
```php
enum PlayerMatchStatus {
    ELIGIBLE = 'eligible';                 // Apta para jugar
    MEDICAL_RESTRICTION = 'medical_restriction'; // Restricción médica
    TRANSFER_PENDING = 'transfer_pending'; // Traspaso pendiente
    SUSPENDED = 'suspended';               // Suspendida
    NOT_FEDERATED = 'not_federated';      // No federada (si requerido)
    DOCUMENTATION_MISSING = 'doc_missing'; // Documentos faltantes
}
```

#### **Cache para Consultas Rápidas**
```php
// Para interfaces de partidos en vivo
'match_cache' => [
    'player_eligibility_cache_minutes' => 5,
    'team_roster_cache_minutes' => 10,
    'tournament_rules_cache_minutes' => 60,
    'federation_status_cache_minutes' => 30,
]
```

### **3. Fundamentos para Vista Pública (Marcadores)**

#### **Datos Públicos Configurables**
```php
'public_tournament_data' => [
    'basic_info' => [
        'name', 'start_date', 'end_date', 'location'
    ],
    'team_info' => [
        'name', 'logo', 'category', 'club_name'
    ],
    'match_info' => [
        'date', 'time', 'venue', 'status', 'score'
    ],
    'player_info' => [
        'name', 'number', 'position' // Sin datos personales
    ]
]
```

#### **API Pública Preparada**
```php
// Endpoints para vista pública (sin autenticación)
Route::get('/api/public/tournaments/active', 'PublicTournamentController@active');
Route::get('/api/public/matches/live', 'PublicMatchController@live');
Route::get('/api/public/tournament/{id}/standings', 'PublicTournamentController@standings');
Route::get('/api/public/match/{id}/score', 'PublicMatchController@score');
```

### **4. Base para Testing Exhaustivo**

#### **Test Data Factories**
```php
// Factories para testing completo
PlayerFactory::class           // Con todos los estados
ClubFactory::class            // Federados y descentralizados
TournamentFactory::class      // Diferentes tipos
TransferFactory::class        // Todos los estados de traspaso
LeagueConfigurationFactory::class // Diferentes configuraciones
```

#### **Test Scenarios**
```php
'test_scenarios' => [
    'federation_workflows' => [
        'manual_federation_approval',
        'federation_expiry_handling',
        'suspension_and_reactivation',
    ],
    'transfer_workflows' => [
        'simple_transfer_approval',
        'inter_league_transfer_rejection',
        'transfer_with_restrictions',
    ],
    'tournament_eligibility' => [
        'federated_only_tournament',
        'mixed_tournament_participation',
        'player_eligibility_validation',
    ],
    'live_match_scenarios' => [
        'player_eligibility_check',
        'real_time_status_updates',
        'emergency_player_substitution',
    ]
]
```

## ✅ **CHECKLIST DE PREPARACIÓN CRÍTICA**

### **Lógica de Negocio**
- [ ] Estados de federación manual implementados
- [ ] Reglas configurables por liga funcionando
- [ ] Validaciones de elegibilidad para torneos
- [ ] Sistema de traspasos con aprobaciones
- [ ] API endpoints para consultas rápidas

### **Preparación para Interfaces Críticas**
- [ ] Estados de jugadoras para partidos en vivo
- [ ] Cache para consultas rápidas implementado
- [ ] Servicios de validación rápida creados
- [ ] Base de datos optimizada para consultas en tiempo real
- [ ] Estructura preparada para marcadores

### **Fundamentos para Vista Pública**
- [ ] Configuraciones de datos públicos definidas
- [ ] API pública sin autenticación preparada
- [ ] Control de privacidad implementado
- [ ] Endpoints para marcadores públicos listos
- [ ] Estructura para dashboard público

### **Base para Testing Exhaustivo**
- [ ] Factories completas para todos los modelos
- [ ] Scenarios de testing definidos
- [ ] Servicios desacoplados y testeables
- [ ] Datos de prueba realistas
- [ ] Flujos documentados y validados

---

## 🎯 **RESULTADO DE FASE 3 CRÍTICA**

Al completar esta Fase 3 con elementos críticos tendremos:

### **✅ Lógica de Negocio Sólida**
- Sistema de federación manual completamente funcional
- Reglas configurables que impactan todo el sistema
- Validaciones robustas para todas las operaciones
- Base sólida para construcción de torneos

### **✅ Preparación para Interfaces Críticas**
- Estados y validaciones listos para partidos en vivo
- API optimizada para consultas rápidas
- Cache implementado para performance
- Estructura preparada para marcadores en tiempo real

### **✅ Fundamentos para Vista Pública**
- Configuraciones de privacidad implementadas
- API pública lista para dashboard sin autenticación
- Control granular de datos visibles
- Base para marcadores públicos

### **✅ Testing Exhaustivo Preparado**
- Servicios completamente testeables
- Scenarios de prueba definidos
- Datos de prueba realistas
- Validaciones robustas en todos los niveles

**🚀 LISTO PARA FASE 4 CON TODOS LOS ELEMENTOS CRÍTICOS CUBIERTOS**
