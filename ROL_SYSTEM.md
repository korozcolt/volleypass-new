# 🔐 **Sistema de Roles y Permisos Granular - VolleyPass**

## 📋 **ARQUITECTURA DE ROLES**

### **🎯 Principios del Sistema:**
1. **Menús Dinámicos:** Solo aparecen opciones que el rol puede usar
2. **Contenido Filtrado:** La información se limita al scope del rol
3. **Acciones Específicas:** Cada rol solo puede realizar acciones permitidas
4. **Separación de Contextos:** Admin vs Dashboard interno según el actor

---

## 👥 **DEFINICIÓN DETALLADA DE ROLES**

### **🔴 SUPER ADMINISTRADOR**
**Scope:** Control total del sistema  
**Acceso:** Panel Admin completo  
**Dashboard Interno:** No aplica (trabaja desde admin)

#### **Menús Visibles en Filament:**
```php
// Todos los menús disponibles
'Gestión Deportiva' => [
    'Ligas',           // ✅ CRUD completo
    'Torneos',         // ✅ CRUD completo  
    'Clubes',          // ✅ CRUD completo
    'Equipos',         // ✅ CRUD completo
    'Jugadoras',       // ✅ CRUD completo
    'Traspasos',       // ✅ CRUD completo + aprobaciones
],
'Gestión Médica y Documentos' => [
    'Certificados Médicos',  // ✅ CRUD completo
    'Carnets',              // ✅ CRUD completo
],
'Finanzas y Pagos' => [
    'Pagos',           // ✅ CRUD completo
],
'Comunicación' => [
    'Notificaciones',  // ✅ CRUD completo
],
'Administración del Sistema' => [
    'Usuarios',        // ✅ CRUD completo
    'Roles',           // ✅ CRUD completo  
    'Configuración',   // ✅ CRUD completo
],
```

#### **Capacidades Específicas:**
- ✅ Crear/editar cualquier usuario de cualquier rol
- ✅ Modificar configuraciones globales del sistema
- ✅ Acceso a logs y auditoría completa
- ✅ Gestión de roles y permisos
- ✅ Override de cualquier restricción

---

### **🟡 ADMINISTRADOR DE LIGA**
**Scope:** Gestión de una liga específica y sus elementos  
**Acceso:** Panel Admin filtrado  
**Dashboard Interno:** No aplica (trabaja desde admin)

#### **Menús Visibles en Filament:**
```php
'Gestión Deportiva' => [
    'Ligas',           // ⚠️ Solo SU liga (readonly en muchos campos)
    'Torneos',         // ✅ CRUD completo de SU liga
    'Clubes',          // ✅ CRUD de clubes de SU liga
    'Equipos',         // ✅ CRUD de equipos de SU liga
    'Jugadoras',       // ✅ CRUD de jugadoras de SU liga
    'Traspasos',       // ✅ CRUD + aprobar traspasos en SU liga
],
'Gestión Médica y Documentos' => [
    'Certificados Médicos',  // ✅ Ver/aprobar de SU liga
    'Carnets',              // ✅ CRUD de carnets de SU liga
],
'Finanzas y Pagos' => [
    'Pagos',           // ⚠️ Solo pagos relacionados a SU liga
],
'Comunicación' => [
    'Notificaciones',  // ✅ Crear/ver notificaciones de SU liga
],
'Gestión de Usuarios' => [  // ⚠️ Menú reducido
    'Usuarios',        // ⚠️ Solo crear usuarios de menor rango
],
```

#### **Restricciones Específicas:**
- ❌ **NO ve:** Menú "Administración del Sistema"
- ❌ **NO puede:** Crear otros Administradores de Liga
- ❌ **NO puede:** Crear Super Administradores
- ❌ **NO puede:** Modificar configuraciones globales
- ✅ **SÍ puede:** Crear Directores de Club, Entrenadores, Árbitros
- ✅ **SÍ puede:** Gestionar todo dentro de su liga

#### **Filtros Automáticos:**
```php
// Ejemplo en LeagueResource
public static function getEloquentQuery(): Builder
{
    $query = parent::getEloquentQuery();
    
    if (auth()->user()->hasRole('league_admin')) {
        return $query->where('id', auth()->user()->league_id);
    }
    
    return $query;
}
```

---

### **🔵 DIRECTOR DE CLUB**
**Scope:** Gestión de UN club específico y sus elementos  
**Acceso:** Panel Admin muy limitado  
**Dashboard Interno:** No aplica (trabaja desde admin)

#### **Menús Visibles en Filament:**
```php
'Gestión Deportiva' => [
    'Clubes',          // ⚠️ Solo SU club (readonly)
    'Equipos',         // ✅ CRUD de equipos de SU club
    'Jugadoras',       // ✅ CRUD de jugadoras de SU club
    'Traspasos',       // ⚠️ Solo iniciar traspasos, no aprobar
],
'Gestión Médica y Documentos' => [
    'Carnets',         // ⚠️ Solo carnets de jugadoras de SU club
],
'Finanzas y Pagos' => [
    'Pagos',           // ⚠️ Solo pagos de SU club
],
'Comunicación' => [
    'Notificaciones',  // ⚠️ Solo recibir/ver (no crear masivas)
],
```

#### **Restricciones Específicas:**
- ❌ **NO ve:** Certificados Médicos (no es su competencia)
- ❌ **NO ve:** Torneos (solo puede inscribir equipos)
- ❌ **NO ve:** Otras ligas o clubes
- ❌ **NO puede:** Crear otros clubes
- ❌ **NO puede:** Aprobar traspasos
- ❌ **NO puede:** Crear usuarios (solo solicitar)
- ✅ **SÍ puede:** Gestionar equipos y jugadoras de su club
- ✅ **SÍ puede:** Iniciar procesos de traspaso

#### **Filtros Automáticos:**
```php
// Ejemplo en PlayerResource
public static function getEloquentQuery(): Builder
{
    $query = parent::getEloquentQuery();
    
    if (auth()->user()->hasRole('club_director')) {
        return $query->whereHas('currentClub', function($q) {
            $q->where('id', auth()->user()->club_id);
        });
    }
    
    return $query;
}
```

---

### **🟢 ENTRENADOR**
**Scope:** Gestión de equipos asignados  
**Acceso:** Panel Admin muy limitado  
**Dashboard Interno:** Sí (gestión de entrenamientos y tácticas)

#### **Menús Visibles en Filament:**
```php
'Gestión Deportiva' => [
    'Equipos',         // ⚠️ Solo equipos que entrena (readonly)
    'Jugadoras',       // ⚠️ Solo jugadoras de sus equipos (readonly)
],
'Comunicación' => [
    'Notificaciones',  // ⚠️ Solo recibir (no crear)
],
```

#### **Dashboard Interno (Inertia):**
**Ruta:** `/coach/dashboard`
```php
'features' => [
    'Equipos Asignados',      // Gestión de sus equipos
    'Convocatorias',          // Crear convocatorias
    'Entrenamientos',         // Programar entrenamientos  
    'Estadísticas de Equipo', // Ver rendimiento
    'Comunicación Interna',   // Chat con jugadoras
],
```

#### **Restricciones:**
- ❌ **NO accede** a la mayoría del panel admin
- ❌ **NO puede** crear/editar jugadoras
- ❌ **NO puede** aprobar traspasos
- ✅ **Trabaja principalmente** desde dashboard interno
- ✅ **SÍ puede** gestionar convocatorias y entrenamientos

---

### **🟣 ÁRBITRO**
**Scope:** Control de partidos asignados  
**Acceso:** ❌ SIN acceso al panel admin  
**Dashboard Interno:** ✅ Exclusivamente (gestión de partidos)

#### **Panel Admin:**
```php
// ❌ NO tiene acceso al panel admin de Filament
// Su trabajo es 100% desde el dashboard interno
```

#### **Dashboard Interno (Inertia):**
**Ruta:** `/referee/dashboard`
```php
'features' => [
    'Partidos Asignados',     // Lista de partidos
    'Control de Partido',     // Solo si tiene partido activo
    'Historial de Arbitrajes',// Partidos dirigidos
    'Evaluaciones Recibidas', // Feedback de actuación
    'Capacitaciones',         // Cursos de arbitraje
],
```

#### **Control de Partido (Ruta especial):**
**Ruta:** `/referee/match-control/{match}`
```php
'permissions' => [
    'Iniciar Partido',        // ✅ Botón de inicio
    'Controlar Puntuación',   // ✅ Sumar/restar puntos
    'Gestionar Rotaciones',   // ✅ Rotaciones automáticas/manuales
    'Controlar Cambios',      // ✅ Entrada/salida jugadores
    'Gestionar Tiempos',      // ✅ Tiempos fuera
    'Aplicar Sanciones',      // ✅ Tarjetas y penalizaciones
    'Finalizar Set/Partido',  // ✅ Cierre oficial
],
```

#### **Restricciones:**
- ❌ **NO accede** al panel admin nunca
- ❌ **NO puede** crear/editar jugadoras, equipos, torneos
- ❌ **NO puede** aprobar traspasos o certificados
- ✅ **Control total** solo durante el partido asignado
- ✅ **Interfaz especializada** para arbitraje

---

### **🟠 JUGADORA**
**Scope:** Gestión de perfil personal y documentación  
**Acceso:** ❌ SIN acceso al panel admin  
**Dashboard Interno:** ✅ Exclusivamente (gestión personal)

#### **Panel Admin:**
```php
// ❌ NO tiene acceso al panel admin de Filament
// Su gestión es 100% desde el dashboard interno
```

#### **Dashboard Interno (Inertia):**
**Ruta:** `/player/dashboard`
```php
'features' => [
    'Mi Perfil',              // Editar información personal
    'Documentación',          // Subir certificados, documentos
    'Mi Carnet Digital',      // Ver/descargar carnet con QR
    'Mis Estadísticas',       // Rendimiento personal
    'Mis Partidos',           // Calendario y resultados
    'Mi Equipo',              // Información del equipo actual
    'Solicitar Traspaso',     // Iniciar proceso de traspaso
    'Estado Médico',          // Certificados y alertas
],
```

#### **Funcionalidades de Autogestión:**
```php
'self_management' => [
    'Actualizar Foto',        // ✅ Upload de foto personal
    'Editar Datos Personales',// ✅ Nombre, contacto, etc.
    'Subir Documentos',       // ✅ Certificados médicos, cédula
    'Renovar Certificados',   // ✅ Cuando estén por vencer
    'Solicitar Correcciones', // ✅ Si hay errores en sus datos
    'Ver Historial',          // ✅ Clubes anteriores, estadísticas
],
```

#### **Restricciones:**
- ❌ **NO accede** al panel admin nunca
- ❌ **NO puede** editar datos de otras jugadoras
- ❌ **NO puede** aprobar traspasos
- ❌ **NO puede** generar carnets (se generan automáticamente)
- ✅ **Autogestión completa** de su perfil y documentación
- ✅ **Interfaz simplificada** y user-friendly

---

## 🛠️ **IMPLEMENTACIÓN TÉCNICA**

### **1. Middleware de Menús Dinámicos**

```php
// app/Http/Middleware/FilterFilamentNavigation.php
<?php

namespace App\Http\Middleware;

use Closure;
use Filament\Facades\Filament;

class FilterFilamentNavigation
{
    public function handle($request, Closure $next)
    {
        if (!auth()->check()) {
            return $next($request);
        }

        $user = auth()->user();
        $role = $user->getRoleNames()->first();

        // Filtrar recursos según rol
        $this->filterNavigationByRole($role);

        return $next($request);
    }

    private function filterNavigationByRole(string $role): void
    {
        $allowedResources = $this->getAllowedResourcesByRole($role);
        
        // Registrar solo los recursos permitidos
        foreach (Filament::getResources() as $resource) {
            $resourceName = class_basename($resource);
            
            if (!in_array($resourceName, $allowedResources)) {
                Filament::resources()->forget($resource);
            }
        }
    }

    private function getAllowedResourcesByRole(string $role): array
    {
        return match($role) {
            'super_admin' => [
                'UserResource', 'PlayerResource', 'ClubResource', 
                'LeagueResource', 'TournamentResource', 'TeamResource',
                'PaymentResource', 'RoleResource', 'SystemConfigurationResource',
                'MedicalCertificateResource', 'NotificationResource',
            ],
            'league_admin' => [
                'PlayerResource', 'ClubResource', 'LeagueResource', 
                'TournamentResource', 'TeamResource', 'PaymentResource',
                'MedicalCertificateResource', 'NotificationResource',
                'UserResource', // Solo crear usuarios de menor rango
            ],
            'club_director' => [
                'ClubResource', 'TeamResource', 'PlayerResource',
                'PaymentResource', 'NotificationResource',
            ],
            'coach' => [
                'TeamResource', 'PlayerResource', 'NotificationResource',
            ],
            'referee' => [], // Sin acceso al admin
            'player' => [], // Sin acceso al admin
            default => [],
        };
    }
}
```

### **2. Filtros Automáticos por Resource**

```php
// app/Filament/Resources/PlayerResource.php
<?php

namespace App\Filament\Resources;

use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;

class PlayerResource extends Resource
{
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();

        return match($user->getRoleNames()->first()) {
            'super_admin' => $query, // Ve todo
            
            'league_admin' => $query->whereHas('currentClub.league', function($q) use ($user) {
                $q->where('id', $user->league_id);
            }),
            
            'club_director' => $query->whereHas('currentClub', function($q) use ($user) {
                $q->where('id', $user->club_id);
            }),
            
            'coach' => $query->whereHas('teams', function($q) use ($user) {
                $q->where('coach_id', $user->id);
            }),
            
            default => $query->whereRaw('1 = 0'), // No ve nada
        };
    }
    
    public static function canCreate(): bool
    {
        return auth()->user()->hasAnyRole(['super_admin', 'league_admin', 'club_director']);
    }
    
    public static function canEdit(Model $record): bool
    {
        $user = auth()->user();
        
        return match($user->getRoleNames()->first()) {
            'super_admin' => true,
            'league_admin' => $record->currentClub->league_id === $user->league_id,
            'club_director' => $record->current_club_id === $user->club_id,
            default => false,
        };
    }
}
```

### **3. Grupos de Navegación Dinámicos**

```php
// app/Providers/FilamentServiceProvider.php
<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Filament\Facades\Filament;
use Filament\Navigation\NavigationGroup;

class FilamentServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Filament::serving(function () {
            $this->registerNavigationGroupsByRole();
        });
    }

    private function registerNavigationGroupsByRole(): void
    {
        if (!auth()->check()) return;

        $role = auth()->user()->getRoleNames()->first();
        $groups = $this->getNavigationGroupsByRole($role);

        foreach ($groups as $group) {
            Filament::registerNavigationGroups([$group]);
        }
    }

    private function getNavigationGroupsByRole(string $role): array
    {
        $allGroups = [
            NavigationGroup::make('Gestión Deportiva')
                ->label('Gestión Deportiva')
                ->icon('heroicon-o-trophy'),
            NavigationGroup::make('Gestión Médica y Documentos')
                ->label('Gestión Médica y Documentos')
                ->icon('heroicon-o-document-check'),
            NavigationGroup::make('Finanzas y Pagos')
                ->label('Finanzas y Pagos')
                ->icon('heroicon-o-credit-card'),
            NavigationGroup::make('Comunicación')
                ->label('Comunicación')
                ->icon('heroicon-o-chat-bubble-left-right'),
            NavigationGroup::make('Administración del Sistema')
                ->label('Administración del Sistema')
                ->icon('heroicon-o-cog-6-tooth'),
        ];

        return match($role) {
            'super_admin' => $allGroups,
            'league_admin' => array_slice($allGroups, 0, 4), // Sin "Administración del Sistema"
            'club_director' => array_slice($allGroups, 0, 3), // Sin "Comunicación" ni "Administración"
            'coach' => [$allGroups[0], $allGroups[3]], // Solo "Deportiva" y "Comunicación"
            default => [],
        };
    }
}
```

### **4. Redirección Post-Login**

```php
// app/Services/RoleRedirectionService.php
<?php

namespace App\Services;

class RoleRedirectionService
{
    public static function getRedirectPath($user): string
    {
        $role = $user->getRoleNames()->first();

        return match($role) {
            'super_admin', 'league_admin', 'club_director', 'coach' => '/admin',
            'referee' => '/referee/dashboard',
            'player' => '/player/dashboard',
            default => '/',
        };
    }
}
```

### **5. Middleware de Acceso al Admin**

```php
// app/Http/Middleware/CheckAdminPanelAccess.php
<?php

namespace App\Http\Middleware;

use Closure;

class CheckAdminPanelAccess
{
    public function handle($request, Closure $next)
    {
        if (!auth()->check()) {
            return redirect('/login');
        }

        $allowedRoles = ['super_admin', 'league_admin', 'club_director', 'coach'];
        
        if (!auth()->user()->hasAnyRole($allowedRoles)) {
            abort(403, 'No tienes permisos para acceder al panel administrativo.');
        }

        return $next($request);
    }
}
```

---

## 📊 **MATRIZ DE PERMISOS RESUMIDA**

| **Funcionalidad** | **Super Admin** | **Liga Admin** | **Club Director** | **Entrenador** | **Árbitro** | **Jugadora** |
|-------------------|-----------------|----------------|-------------------|----------------|-------------|--------------|
| **Panel Admin** | ✅ Completo | ✅ Filtrado | ⚠️ Muy limitado | ⚠️ Mínimo | ❌ Sin acceso | ❌ Sin acceso |
| **Dashboard Interno** | ❌ No aplica | ❌ No aplica | ❌ No aplica | ✅ Sí | ✅ Sí | ✅ Sí |
| **Crear Usuarios** | ✅ Todos | ⚠️ Solo menores | ❌ No | ❌ No | ❌ No | ❌ No |
| **Gestionar Ligas** | ✅ Todas | ⚠️ Solo suya | ❌ No | ❌ No | ❌ No | ❌ No |
| **Gestionar Clubes** | ✅ Todos | ✅ De su liga | ⚠️ Solo suyo | ❌ No | ❌ No | ❌ No |
| **Gestionar Jugadoras** | ✅ Todas | ✅ De su liga | ✅ De su club | ⚠️ Solo vista | ❌ No | ⚠️ Solo perfil |
| **Control Partidos** | ✅ Todos | ✅ De su liga | ❌ No | ❌ No | ✅ Asignados | ❌ No |
| **Configuraciones** | ✅ Todas | ❌ No | ❌ No | ❌ No | ❌ No | ❌ No |

---

## 🎯 **BENEFICIOS DEL SISTEMA**

### **✅ Seguridad Granular:**
- Cada rol ve solo lo que puede/debe gestionar
- Filtros automáticos a nivel de base de datos
- Prevención de escalación de privilegios

### **✅ UX Optimizada:**
- Menús limpios sin opciones inútiles
- Interfaces especializadas por rol
- Menor fricción en tareas cotidianas

### **✅ Mantenimiento Simplificado:**
- Configuración centralizada de permisos
- Fácil auditoría de accesos
- Escalabilidad para nuevos roles

### **✅ Separación de Contextos:**
- Admin para gestión y configuración
- Dashboards internos para uso operativo
- Interfaces especializadas (árbitros, jugadoras)

Este sistema garantiza que cada actor del voleibol tenga exactamente las herramientas que necesita, sin complejidad innecesaria, manteniendo la seguridad y la eficiencia operativa.
