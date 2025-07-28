# 📋 **VolleyPass - Documentación Completa con Inertia.js + React**

## 🎯 **ARQUITECTURA GENERAL**

### **Stack Tecnológico:**
- **Backend:** Laravel 12.x + Filament 3.x
- **Frontend:** Inertia.js + React + TypeScript  
- **Styling:** Tailwind CSS
- **Base de Datos:** MySQL 8.0+
- **Tiempo Real:** Laravel WebSockets + Pusher

### **Estructura del Proyecto:**
```
volleyball-project/
├── app/
│   ├── Filament/              # Admin Panel (intacto)
│   ├── Http/Controllers/      # Controllers para Inertia
│   └── Models/               # Modelos Eloquent
├── resources/
│   ├── js/
│   │   ├── Pages/            # Componentes React (páginas)
│   │   ├── Layouts/          # Layouts reutilizables
│   │   ├── Components/       # Componentes reutilizables
│   │   └── Hooks/           # Custom hooks React
│   └── views/
│       └── app.blade.php    # Root template Inertia
└── routes/
    ├── web.php              # Rutas Inertia
    └── admin.php            # Rutas Filament
```

---

## 🌐 **1. VISTAS PÚBLICAS (Sin Autenticación)**

### **🏠 Welcome Principal**
**Ruta:** `/`  
**Controller:** `PublicController@welcome`  
**Componente:** `resources/js/Pages/Public/Welcome.tsx`

#### **Props del Controller:**
```php
// app/Http/Controllers/PublicController.php
public function welcome()
{
    return Inertia::render('Public/Welcome', [
        'projectStats' => [
            'totalPlayers' => Player::count(),
            'activeLeagues' => League::active()->count(),
            'liveTournaments' => Tournament::live()->count(),
            'completedTournaments' => Tournament::completed()->count(),
        ],
        'featuredTournaments' => Tournament::featured()
            ->with(['league', 'teams'])
            ->limit(3)
            ->get(),
        'technologies' => [
            ['name' => 'Laravel', 'version' => '12.x', 'color' => 'bg-red-600'],
            ['name' => 'Filament', 'version' => '3.x', 'color' => 'bg-yellow-600'],
            ['name' => 'Livewire', 'version' => '3.x', 'color' => 'bg-purple-600'],
            ['name' => 'MySQL', 'version' => '8.0+', 'color' => 'bg-blue-600'],
        ],
    ]);
}
```

#### **Funcionalidades del Componente:**
- **Hero Section** con información del proyecto
- **Estadísticas en tiempo real** (95% completado, 45+ tablas BD, etc.)
- **Grid de características** principales
- **Stack tecnológico** con badges dinámicos
- **Call-to-actions** hacia otras vistas
- **Footer informativo** con contacto

---

### **🏐 Partidos en Vivo**
**Ruta:** `/partidos`  
**Controller:** `PublicController@matches`  
**Componente:** `resources/js/Pages/Public/Matches.tsx`

#### **Props del Controller:**
```php
public function matches()
{
    return Inertia::render('Public/Matches', [
        'liveMatches' => Match::live()
            ->with(['teamA', 'teamB', 'referee', 'tournament', 'sets'])
            ->get(),
        'upcomingMatches' => Match::upcoming()
            ->with(['teamA', 'teamB', 'tournament'])
            ->limit(10)
            ->get(),
        'recentResults' => Match::finished()
            ->with(['teamA', 'teamB', 'tournament'])
            ->latest()
            ->limit(15)
            ->get(),
        'standings' => StandingsService::getPublicStandings(),
        'currentTime' => now()->toISOString(),
    ]);
}
```

#### **Funcionalidades React:**
- **Timer en vivo** con `useEffect` para actualización automática
- **WebSocket integration** para marcadores en tiempo real
- **Featured match hero** con marcador destacado
- **Grid de partidos** en vivo con estados visuales
- **Tabla de posiciones** lateral con racha de resultados
- **Estadísticas rápidas** (partidos hoy, espectadores, etc.)

#### **Custom Hooks:**
```typescript
// resources/js/Hooks/useRealTimeMatches.ts
const useRealTimeMatches = () => {
  const [matches, setMatches] = useState([]);
  
  useEffect(() => {
    Echo.channel('live-matches')
      .listen('ScoreUpdated', (e) => {
        setMatches(prev => prev.map(match => 
          match.id === e.matchId ? { ...match, ...e.data } : match
        ));
      });
  }, []);
  
  return matches;
};
```

---

### **📞 Contacto**
**Ruta:** `/contacto`  
**Controller:** `PublicController@contact`  
**Componente:** `resources/js/Pages/Public/Contact.tsx`

#### **Props del Controller:**
```php
public function contact()
{
    return Inertia::render('Public/Contact', [
        'contactInfo' => [
            'email' => 'liga@volleypass.sucre.gov.co',
            'phone' => '+57 (5) 282-5555',
            'address' => 'Cra. 25 #16-50, Sincelejo, Sucre',
        ],
        'developers' => [
            ['name' => 'Equipo VolleyPass', 'role' => 'Desarrollo Full-Stack'],
        ],
        'faq' => [
            ['question' => '¿Cómo registro mi equipo?', 'answer' => 'El registro es interno...'],
            ['question' => '¿Cómo verifico un carnet?', 'answer' => 'Escanea el código QR...'],
        ],
    ]);
}
```

#### **Funcionalidades:**
- **Formulario de contacto** con React Hook Form
- **Validación en tiempo real** 
- **Información del equipo** de desarrollo
- **FAQ acordeón** interactivo
- **Mapa de ubicación** (Google Maps embed)

---

### **🔐 Iniciar Sesión**
**Ruta:** `/login`  
**Controller:** `AuthController@showLogin`  
**Componente:** `resources/js/Pages/Auth/Login.tsx`

#### **Props del Controller:**
```php
public function showLogin()
{
    return Inertia::render('Auth/Login', [
        'canResetPassword' => Route::has('password.request'),
        'status' => session('status'),
    ]);
}
```

#### **Funcionalidades:**
- **Formulario único** para todos los roles
- **Detección automática** de rol post-login
- **Redirección inteligente** según permisos
- **Validación en tiempo real** con feedback visual
- **Sin opción de registro** público
- **Mensaje claro** sobre registro interno

---

## 🏠 **2. DASHBOARDS POR ROL (Post-Autenticación)**

### **🎯 Dashboard Universal**
**Rutas:**
- `/player/dashboard` - Jugadoras
- `/coach/dashboard` - Entrenadores  
- `/referee/dashboard` - Árbitros

**Controllers:** 
- `PlayerController@dashboard`
- `CoachController@dashboard`
- `RefereeController@dashboard`

**Componente:** `resources/js/Pages/Dashboard/Index.tsx`

#### **Props Comunes:**
```php
// Ejemplo PlayerController@dashboard
public function dashboard()
{
    $player = auth()->user()->player;
    
    return Inertia::render('Dashboard/Index', [
        'userRole' => 'player',
        'profile' => [
            'user' => auth()->user(),
            'player' => $player,
            'team' => $player->currentTeam,
            'stats' => $player->seasonStats(),
        ],
        'relevantMatches' => Match::forPlayer($player)
            ->upcoming()
            ->with(['teamA', 'teamB', 'tournament'])
            ->limit(5)
            ->get(),
        'notifications' => auth()->user()
            ->notifications()
            ->unread()
            ->limit(5)
            ->get(),
        'quickActions' => [
            ['label' => 'Ver Estadísticas', 'route' => 'player.stats', 'icon' => 'BarChart3'],
            ['label' => 'Actualizar Perfil', 'route' => 'player.profile', 'icon' => 'User'],
        ],
    ]);
}
```

#### **Funcionalidades por Rol:**

##### **👩‍🏐 Jugadoras:**
- **Header personalizado** con foto y estadísticas básicas
- **Navegación por tabs** (Resumen, Estadísticas, Partidos, Médico)
- **Carnet digital** con QR code
- **Próximos partidos** de su equipo
- **Estadísticas de temporada** con barras de progreso
- **Estado médico** con alertas de vencimiento
- **Notificaciones** específicas

##### **👨‍🏫 Entrenadores:**
- **Vista de equipos** que dirige
- **Gestión de nóminas** y convocatorias
- **Estadísticas de equipo** y rendimiento
- **Solicitudes de transferencias** pendientes
- **Calendario de entrenamientos**

##### **👨‍⚖️ Árbitros:**
- **Partidos asignados** con estados
- **Control de partido activo** (si tiene uno en curso)
- **Historial de arbitrajes** con evaluaciones
- **Próximas asignaciones** con detalles de equipos

---

### **👤 Perfil Universal**
**Rutas:**
- `/player/profile` - Jugadoras
- `/coach/profile` - Entrenadores
- `/referee/profile` - Árbitros

**Componente:** `resources/js/Pages/Profile/Index.tsx`

#### **Funcionalidades Comunes:**
- **Información personal** editable con formularios React
- **Upload de documentos** con drag & drop
- **Carnet digital** con QR único y estadísticas
- **Historial de actividades** con timeline
- **Configuraciones de privacidad**

#### **Específico por Rol:**

##### **👩‍🏐 Jugadoras:**
```php
'profileData' => [
    'personalInfo' => $player->user,
    'sportsInfo' => $player,
    'medicalRecords' => $player->medicalCertificates(),
    'transferHistory' => $player->transfers(),
    'achievements' => $player->achievements(),
    'seasonStats' => $player->detailedStats(),
],
```

##### **👨‍🏫 Entrenadores:**
```php
'profileData' => [
    'personalInfo' => $coach->user,
    'certifications' => $coach->certifications(),
    'teamsHistory' => $coach->teamsDirected(),
    'evaluations' => $coach->evaluations(),
],
```

---

## ⚖️ **3. TABLERO DE CONTROL DE ÁRBITRO**

### **🎮 Control de Partido**
**Ruta:** `/referee/match-control/{match}`  
**Controller:** `RefereeController@matchControl`  
**Componente:** `resources/js/Pages/Referee/MatchControl.tsx`

#### **Props del Controller:**
```php
public function matchControl(Match $match)
{
    $this->authorize('control', $match); // Solo árbitro asignado
    
    return Inertia::render('Referee/MatchControl', [
        'match' => $match->load([
            'teamA.players', 
            'teamB.players', 
            'tournament', 
            'sets',
            'rotations',
            'substitutions'
        ]),
        'matchState' => [
            'status' => $match->status,
            'currentSet' => $match->current_set,
            'score' => $match->current_score,
            'positions' => $match->current_positions,
        ],
        'gameRules' => $match->tournament->league->rules,
    ]);
}
```

#### **Estados del Partido:**
1. **Pre-Partido:** Configuración inicial
2. **En Curso:** Control activo con WebSockets
3. **Entre Sets:** Pausa entre sets
4. **Finalizado:** Cierre y resultados

#### **Custom Hooks para Control:**
```typescript
// resources/js/Hooks/useMatchControl.ts
const useMatchControl = (matchId: number) => {
  const [matchState, setMatchState] = useState({
    status: 'pre-match',
    currentSet: 1,
    score: { teamA: 0, teamB: 0 },
    positions: { teamA: [], teamB: [] },
    rotations: 0,
  });

  const startMatch = async () => {
    await router.post(`/referee/matches/${matchId}/start`);
  };

  const addPoint = async (team: 'A' | 'B') => {
    await router.post(`/referee/matches/${matchId}/point`, { team });
  };

  const endSet = async () => {
    await router.post(`/referee/matches/${matchId}/end-set`);
  };

  const makeSubstitution = async (playerId: number, substituteId: number) => {
    await router.post(`/referee/matches/${matchId}/substitute`, {
      player_id: playerId,
      substitute_id: substituteId,
    });
  };

  return {
    matchState,
    startMatch,
    addPoint,
    endSet,
    makeSubstitution,
  };
};
```

### **📋 Pre-Partido (Configuración)**
**Componente:** `resources/js/Components/Referee/PreMatchSetup.tsx`

#### **Funcionalidades:**
- **Drag & Drop** para posicionar jugadores en cancha
- **Configuración de libero** por equipo (activar/desactivar)
- **Verificación de nóminas** con validación de elegibilidad
- **Setup de rotaciones** iniciales
- **Botón de inicio oficial** con confirmaciones

### **⚡ Durante el Partido**
**Componentes:**
- `resources/js/Components/Referee/ScoreControl.tsx`
- `resources/js/Components/Referee/RotationControl.tsx`
- `resources/js/Components/Referee/SubstitutionControl.tsx`

#### **Funcionalidades Principales:**

##### **📊 Marcador en Tiempo Real:**
- **Puntos por set** con botones de +/- 
- **Set actual** destacado visualmente
- **Tiempo de juego** con cronómetro automático
- **Control de saques** alternado por equipo

##### **🔄 Sistema de Rotaciones:**
- **Detección automática** cuando corresponde rotar
- **Control manual** para forzar rotaciones
- **Vista visual** de posiciones actuales en cancha
- **Validaciones** para prevenir rotaciones incorrectas
- **Historial** de todas las rotaciones del partido

##### **🔄 Gestión de Cambios:**
- **Modal de cambios** con lista de jugadores disponibles
- **Control especial** para entrada/salida de libero
- **Límites por set** respetando reglas del torneo
- **Log automático** de todos los cambios
- **Validaciones** de elegibilidad

##### **⚠️ Amonestaciones y Sanciones:**
- **Sistema de tarjetas** (amarilla, roja)
- **Sanciones automáticas** (puntos perdidos, expulsiones)
- **Registro inmediato** en base de datos
- **Notificaciones** a federación para sanciones graves

### **🏁 Finalización de Sets**
**Componente:** `resources/js/Components/Referee/SetControl.tsx`

#### **Control Manual del Árbitro:**
- **Botón "Finalizar Set"** activo solo para árbitro asignado
- **Soporte para sets extendidos** más allá de 25/15 puntos
- **Confirmación doble** antes de cerrar set
- **Transición automática** al siguiente set o fin de partido
- **Generación de estadísticas** automática del set

---

## 🛠️ **4. IMPLEMENTACIÓN TÉCNICA**

### **📁 Estructura de Archivos React**
```
resources/js/
├── Pages/
│   ├── Public/
│   │   ├── Welcome.tsx           # Homepage con stats
│   │   ├── Matches.tsx           # Partidos en vivo + standings
│   │   ├── Tournaments.tsx       # Gestión de torneos
│   │   └── Contact.tsx           # Formulario de contacto
│   ├── Auth/
│   │   └── Login.tsx             # Login universal
│   ├── Dashboard/
│   │   └── Index.tsx             # Dashboard universal por rol
│   ├── Profile/
│   │   └── Index.tsx             # Perfil universal por rol
│   └── Referee/
│       ├── MatchControl.tsx      # Control completo de partido
│       └── MatchHistory.tsx      # Historial de arbitrajes
├── Layouts/
│   ├── PublicLayout.tsx          # Layout para vistas públicas
│   ├── DashboardLayout.tsx       # Layout para dashboards
│   └── RefereeLayout.tsx         # Layout específico para árbitros
├── Components/
│   ├── Common/                   # Componentes reutilizables
│   │   ├── Navigation.tsx
│   │   ├── SearchBar.tsx
│   │   └── NotificationBell.tsx
│   ├── Match/                    # Componentes de partidos
│   │   ├── LiveMatchCard.tsx
│   │   ├── MatchSchedule.tsx
│   │   └── ScoreBoard.tsx
│   ├── Player/                   # Componentes de jugadores
│   │   ├── PlayerCard.tsx
│   │   ├── StatsChart.tsx
│   │   └── DigitalID.tsx
│   └── Referee/                  # Componentes específicos árbitro
│       ├── PreMatchSetup.tsx
│       ├── ScoreControl.tsx
│       ├── RotationControl.tsx
│       └── SubstitutionControl.tsx
├── Hooks/
│   ├── useWebSocket.ts           # WebSocket personalizado
│   ├── useMatchControl.ts        # Lógica control partido
│   ├── useRealTimeUpdates.ts     # Updates automáticos
│   └── useAuth.ts               # Estado de autenticación
└── Services/
    ├── matchService.ts           # Servicios de partido
    ├── websocketService.ts       # Gestión WebSocket
    └── rotationService.ts        # Lógica rotaciones
```

### **🔌 WebSockets y Tiempo Real**

#### **Configuración Laravel WebSockets:**
```php
// config/broadcasting.php
'pusher' => [
    'driver' => 'pusher',
    'key' => env('PUSHER_APP_KEY'),
    'secret' => env('PUSHER_APP_SECRET'),
    'app_id' => env('PUSHER_APP_ID'),
    'options' => [
        'cluster' => env('PUSHER_APP_CLUSTER'),
        'host' => '127.0.0.1',
        'port' => 6001,
        'scheme' => 'http',
        'encrypted' => false,
    ],
],
```

#### **Eventos en Tiempo Real:**
- **ScoreUpdated** - Actualización de marcadores
- **PlayerSubstituted** - Cambios de jugadores  
- **RotationExecuted** - Rotaciones automáticas
- **MatchStatusChanged** - Estados del partido
- **SanctionApplied** - Amonestaciones

#### **React WebSocket Service:**
```typescript
// resources/js/Services/websocketService.ts
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
    wsHost: import.meta.env.VITE_PUSHER_HOST || '127.0.0.1',
    wsPort: import.meta.env.VITE_PUSHER_PORT || 6001,
    forceTLS: false,
    disableStats: true,
});

export const subscribeToMatch = (matchId: number, callback: Function) => {
    return window.Echo.channel(`match.${matchId}`)
        .listen('ScoreUpdated', callback)
        .listen('MatchStatusChanged', callback);
};
```

### **🎯 Custom Hooks React**

#### **useAuth Hook:**
```typescript
// resources/js/Hooks/useAuth.ts
import { usePage } from '@inertiajs/react';

interface User {
    id: number;
    name: string;
    email: string;
    role: string;
}

export const useAuth = () => {
    const { props } = usePage();
    const user = props.auth?.user as User;
    
    return {
        user,
        isAuthenticated: !!user,
        isPlayer: user?.role === 'player',
        isReferee: user?.role === 'referee',
        isCoach: user?.role === 'coach',
        isAdmin: user?.role === 'admin',
    };
};
```

#### **useRealTimeUpdates Hook:**
```typescript
// resources/js/Hooks/useRealTimeUpdates.ts
import { useState, useEffect } from 'react';
import { subscribeToMatch } from '../Services/websocketService';

export const useRealTimeUpdates = (matchId?: number) => {
    const [liveData, setLiveData] = useState({});
    
    useEffect(() => {
        if (!matchId) return;
        
        const unsubscribe = subscribeToMatch(matchId, (event: any) => {
            setLiveData(prevData => ({
                ...prevData,
                [event.type]: event.data,
            }));
        });
        
        return () => unsubscribe?.();
    }, [matchId]);
    
    return liveData;
};
```

### **📱 Responsive Design**

#### **Breakpoints Tailwind:**
- **sm:** 640px+ (móviles grandes)
- **md:** 768px+ (tablets)  
- **lg:** 1024px+ (laptops)
- **xl:** 1280px+ (desktops)

#### **Estrategia Mobile-First:**
- **Navegación colapsible** en móviles
- **Cards apilables** en tablets
- **Sidebar hidden** en pantallas pequeñas
- **Touch-friendly** botones y controles

---

## 🚀 **5. VISTAS ADICIONALES SUGERIDAS**

### **📈 Estadísticas Avanzadas**
**Ruta:** `/stats`  
**Componente:** `resources/js/Pages/Stats/Index.tsx`
- **Gráficos interactivos** con Recharts
- **Comparativas** entre jugadores/equipos
- **Filtros** por temporada, categoría, posición
- **Exportación** de reportes en PDF

### **📅 Calendario de Eventos**
**Ruta:** `/calendar`  
**Componente:** `resources/js/Pages/Calendar/Index.tsx`
- **Vista calendario** con FullCalendar.js
- **Filtros** por tipo de evento
- **Modal de detalles** para cada evento
- **Sincronización** con calendarios externos

### **🏆 Torneos Detallados**
**Ruta:** `/tournaments/{tournament}`  
**Componente:** `resources/js/Pages/Tournaments/Show.tsx`
- **Bracket interactivo** para eliminatorias
- **Tabla de posiciones** en tiempo real
- **Estadísticas** del torneo
- **Galería** de fotos y videos

### **📱 Centro de Notificaciones**
**Ruta:** `/notifications`  
**Componente:** `resources/js/Pages/Notifications/Index.tsx`
- **Lista completa** de notificaciones
- **Filtros** por tipo y estado
- **Marcado masivo** como leídas
- **Configuración** de preferencias

---

## 💡 **6. FLUJO DE NAVEGACIÓN COMPLETO**

### **🌐 Usuario Anónimo:**
```
/ (Welcome) 
├── /partidos (Matches - Tiempo Real)
├── /torneos (Tournaments - Con filtros)
├── /contacto (Contact - Formulario)
└── /login (Auth - Universal)
```

### **🔐 Post-Login (Automático según rol):**
```
/login 
├── /player/dashboard → /player/profile → /player/stats
├── /coach/dashboard → /coach/teams → /coach/profile  
└── /referee/dashboard → /referee/match-control/{id} → /referee/profile
```

### **⚖️ Árbitro con Partido Activo:**
```
/referee/dashboard 
└── /referee/match-control/123
    ├── Pre-Match Setup (Posiciones, Libero)
    ├── Live Control (Puntos, Rotaciones, Cambios)
    └── Post-Match (Resultados, Estadísticas)
```

---

## 🛠️ **7. CONFIGURACIÓN DE INERTIA.JS**

### **Instalación:**
```bash
composer require inertiajs/inertia-laravel
php artisan inertia:install react
npm install
```

### **Root Template (app.blade.php):**
```html
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>VolleyPass - Liga de Voleibol Sucre</title>
    @viteReactRefresh
    @vite('resources/js/app.tsx')
    @inertiaHead
</head>
<body class="bg-gradient-to-br from-slate-900 via-blue-900 to-slate-800">
    @inertia
</body>
</html>
```

### **Middleware Setup:**
```php
// bootstrap/app.php
->withMiddleware(function (Middleware $middleware) {
    $middleware->web(append: [
        HandleInertiaRequests::class,
    ]);
})
```

### **Shared Data:**
```php
// app/Http/Middleware/HandleInertiaRequests.php
public function share(Request $request): array
{
    return array_merge(parent::share($request), [
        'auth' => [
            'user' => $request->user(),
        ],
        'flash' => [
            'message' => fn () => $request->session()->get('message'),
            'error' => fn () => $request->session()->get('error'),
        ],
        'currentTime' => now()->toISOString(),
    ]);
}
```

---

## ✅ **8. BENEFICIOS DE ESTA IMPLEMENTACIÓN**

### **🎯 Desarrollo Unificado:**
- **Un solo repositorio** Laravel con todo integrado
- **Shared state** entre frontend y backend
- **Autenticación nativa** Laravel sin APIs complejas
- **Hot reloading** durante desarrollo

### **🎯 Performance Optimizada:**
- **Server-Side Rendering** inicial con Inertia
- **SPA navigation** fluida después de la carga
- **Lazy loading** de componentes
- **Asset optimization** con Vite

### **🎯 Experiencia de Usuario:**
- **Tiempo real** sin refrescar página
- **Estados visuales** claros para cada acción
- **Responsive design** mobile-first
- **Navegación intuitiva** según roles

### **🎯 Mantenimiento Simplificado:**
- **Componentes reutilizables** React
- **Custom hooks** para lógica compartida
- **TypeScript** para type safety
- **Estructura modular** escalable

---

Esta arquitectura combina perfectamente la potencia del backend Laravel/Filament con la modernidad del frontend React, manteniendo todo en un solo proyecto mientras aprovecha al máximo las capacidades de cada tecnología según el rol del usuario.

🎯 Puntos Clave de la Documentación:
✅ Componentes Mapeados:

Welcome.tsx → Página principal con stats del proyecto
Matches.tsx → Partidos en vivo con tiempo real y WebSockets
Tournaments.tsx → Gestión completa de torneos con filtros
Dashboard.tsx → Dashboard universal adaptado por roles
Contact.tsx → Formulario de contacto
Login.tsx → Autenticación universal

✅ Arquitectura React + Inertia:

Custom Hooks para lógica reutilizable (useMatchControl, useRealTimeUpdates, useAuth)
WebSockets para actualizaciones en tiempo real
Layouts reutilizables por tipo de vista
TypeScript para type safety completo

✅ Funcionalidades Avanzadas:

Control de árbitro en tiempo real con rotaciones automáticas
Carnet digital con QR codes
Estadísticas interactivas con gráficos
Sistema de notificaciones en tiempo real

✅ Estructura Modular:

Separación clara entre vistas públicas y dashboards por rol
Componentes reutilizables organizados por dominio
Services y hooks para lógica de negocio
Props tipados desde controllers Laravel
