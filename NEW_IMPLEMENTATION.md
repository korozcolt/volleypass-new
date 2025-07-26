# VolleyPass - Sistema de Autenticación Triple y UI/UX Frontend

## 🔐 ARQUITECTURA DE AUTENTICACIÓN

VolleyPass implementa **tres sistemas de autenticación independientes** para diferentes tipos de usuarios:

### 1. **Sistema Web (Jugadoras)**
- **Guard**: `web` (session-based)
- **Login**: `/login`
- **Dashboard**: `/player/dashboard`
- **Roles**: `player`
- **Propósito**: Interface simplificada para jugadoras

### 2. **Panel Admin (Gestores)**
- **Guard**: `web` (session-based, mismo provider)
- **Login**: `/admin/login`
- **Dashboard**: `/admin`
- **Roles**: `admin`, `super_admin`, `league_director`, `club_director`, `coach`, `referee`
- **Propósito**: Gestión completa del sistema

### 3. **API Móvil (Verificadores)**
- **Guard**: `sanctum` (token-based)
- **Login**: `POST /api/v1/auth/login`
- **Authentication**: Bearer tokens
- **Roles**: `Verifier`, `LeagueAdmin`, `SuperAdmin`
- **Propósito**: Apps móviles para verificación QR

---

## 🛠️ CONFIGURACIÓN TÉCNICA

### Guards Configurados (`config/auth.php`)

```php
'guards' => [
    // Guard para usuarios web (jugadoras)
    'web' => [
        'driver' => 'session',
        'provider' => 'users',
    ],
    
    // Guard para API móvil (verificadores)
    'sanctum' => [
        'driver' => 'sanctum',
        'provider' => 'users',
    ],
    
    // NOTA: Filament usará 'web' guard
],
```

### Middleware Personalizado

#### CheckAdminPanelAccess.php
```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAdminPanelAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        // Excluir rutas de login/logout de Filament
        if ($request->routeIs('filament.admin.auth.login') || 
            $request->routeIs('filament.admin.auth.logout') ||
            str_starts_with($request->route()->getName() ?? '', 'filament.admin.auth.')) {
            return $next($request);
        }

        // Verificar autenticación web
        if (!auth('web')->check()) {
            return redirect()->route('filament.admin.auth.login');
        }

        $user = auth('web')->user();
        
        // Roles permitidos en panel admin
        $adminRoles = [
            'admin', 
            'super_admin', 
            'league_director', 
            'club_director', 
            'coach', 
            'referee'
        ];
        
        $hasAdminRole = false;
        foreach ($adminRoles as $role) {
            if ($user->hasRole($role)) {
                $hasAdminRole = true;
                break;
            }
        }

        if (!$hasAdminRole) {
            // Redirigir jugadoras a su dashboard
            if ($user->hasRole('player')) {
                return redirect()->route('player.dashboard');
            }
            
            abort(403, 'No tienes permisos para acceder al panel administrativo.');
        }

        return $next($request);
    }
}
```

#### ApiRoleMiddleware.php
```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiRoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Verificar autenticación Sanctum
        if (!auth('sanctum')->check()) {
            return response()->json([
                'error' => 'Token de acceso requerido',
                'message' => 'Debes autenticarte para acceder a este endpoint'
            ], 401);
        }

        $user = auth('sanctum')->user();
        
        // Verificar roles requeridos
        $hasRole = false;
        foreach ($roles as $role) {
            if ($user->hasRole($role)) {
                $hasRole = true;
                break;
            }
        }

        if (!$hasRole) {
            return response()->json([
                'error' => 'Permisos insuficientes',
                'message' => 'No tienes permisos para acceder a este recurso',
                'required_roles' => $roles,
                'user_roles' => $user->getRoleNames()
            ], 403);
        }

        return $next($request);
    }
}
```

### Configuración Filament (`AdminPanelProvider.php`)

```php
public function panel(Panel $panel): Panel
{
    return $panel
        ->default()
        ->id('admin')
        ->path('admin')
        ->login()
        ->authGuard('web') // USAR GUARD WEB
        ->colors([
            'primary' => Color::Amber,
        ])
        ->brandName('VolleyPass Admin')
        ->brandLogo(asset('images/logo.png'))
        ->favicon(asset('favicon.ico'))
        ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
        ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
        ->pages([
            Pages\Dashboard::class,
        ])
        ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
        ->widgets([
            Widgets\AccountWidget::class,
            Widgets\FilamentInfoWidget::class,
        ])
        ->middleware([
            EncryptCookies::class,
            AddQueuedCookiesToResponse::class,
            StartSession::class,
            AuthenticateSession::class,
            ShareErrorsFromSession::class,
            VerifyCsrfToken::class,
            SubstituteBindings::class,
            DisableBladeIconComponents::class,
            DispatchServingFilamentEvent::class,
        ])
        ->authMiddleware([
            Authenticate::class,
            CheckAdminPanelAccess::class, // VERIFICACIÓN DE ROLES
        ]);
}
```

### Registro de Middleware (`bootstrap/app.php`)

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'role' => RoleMiddleware::class,
        'admin.access' => CheckAdminPanelAccess::class,
        'api.role' => ApiRoleMiddleware::class,
    ]);
})
```

---

## 🛣️ ESTRUCTURA DE RUTAS

### Rutas Web (`routes/web.php`)

```php
// RUTAS PÚBLICAS (NO REQUIEREN AUTENTICACIÓN)
Route::get('/', PublicTournaments::class)->name('home');

Route::prefix('public')->name('public.')->group(function () {
    Route::get('/tournaments', PublicTournaments::class)->name('tournaments');
    Route::get('/tournament/{tournament}', TournamentDetails::class)->name('tournament.show');
    Route::get('/team/{team}', TeamPublicProfile::class)->name('team.show');
    Route::get('/standings/{tournament}', TournamentStandings::class)->name('standings');
    Route::get('/schedule/{tournament}', TournamentSchedule::class)->name('schedule');
    Route::get('/results/{tournament}', TournamentResults::class)->name('results');
});

// AUTENTICACIÓN WEB (SOLO USUARIOS FINALES)
require __DIR__.'/auth.php';

// DASHBOARD INTELIGENTE - REDIRIGE SEGÚN ROL
Route::get('/dashboard', function () {
    if (!auth('web')->check()) {
        return redirect()->route('login');
    }
    
    $user = auth('web')->user();
    
    // Roles administrativos → Panel admin
    $adminRoles = ['admin', 'super_admin', 'league_director', 'club_director', 'coach', 'referee'];
    
    foreach ($adminRoles as $role) {
        if ($user->hasRole($role)) {
            return redirect('/admin');
        }
    }
    
    // Jugadoras → Dashboard específico
    if ($user->hasRole('player')) {
        return redirect()->route('player.dashboard');
    }
    
    // Sin rol definido → Home
    return redirect()->route('home');
})->middleware(['auth:web', 'verified'])->name('dashboard');

// RUTAS DE JUGADORAS
Route::middleware(['auth:web', 'role:player'])->group(function () {
    Route::prefix('player')->name('player.')->group(function () {
        Route::get('/dashboard', PlayerDashboard::class)->name('dashboard');
        Route::get('/profile', function () {
            return view('player.profile');
        })->name('profile');
        Route::post('/profile/update', [PlayerController::class, 'updateProfile'])->name('profile.update');
        Route::post('/profile/photo', [PlayerController::class, 'updatePhoto'])->name('profile.photo');
        Route::get('/card', DigitalCard::class)->name('card');
        Route::get('/card/download', [PlayerController::class, 'downloadCard'])->name('card.download');
        Route::get('/stats', PlayerStats::class)->name('stats');
        Route::get('/tournaments', MyTournaments::class)->name('tournaments');
        Route::get('/tournaments/{tournament}', MyTournamentDetails::class)->name('tournaments.show');
        Route::get('/settings', PlayerSettings::class)->name('settings');
        Route::get('/notifications', PlayerNotifications::class)->name('notifications');
    });
});
```

### Rutas API (`routes/api.php`)

```php
// RUTAS PÚBLICAS API (NO REQUIEREN TOKEN)
Route::prefix('v1')->group(function () {
    
    // Health check
    Route::get('/health', function () {
        return response()->json([
            'status' => 'ok',
            'timestamp' => now()->toISOString(),
            'version' => '1.0.0',
            'environment' => app()->environment()
        ]);
    })->name('api.health');

    // Verificación QR (público para verificadores)
    Route::post('/verify-qr', [QrVerificationController::class, 'verify'])->name('api.verify-qr');
    Route::post('/qr-info', [QrVerificationController::class, 'getQrInfo'])->name('api.qr-info');

    // Autenticación para verificadores
    Route::prefix('auth')->name('api.auth.')->group(function () {
        Route::post('/login', [AuthController::class, 'login'])->name('login');
        Route::post('/check-email', function (Request $request) {
            $request->validate(['email' => 'required|email']);
            $exists = \App\Models\User::where('email', $request->email)
                ->whereHas('roles', function($q) {
                    $q->whereIn('name', ['Verifier', 'LeagueAdmin', 'SuperAdmin']);
                })
                ->exists();
            return response()->json(['exists' => $exists]);
        })->name('check-email');
    });
});

// RUTAS PROTEGIDAS API (REQUIEREN TOKEN)
Route::prefix('v1')->middleware(['auth:sanctum'])->group(function () {

    // Gestión de tokens y usuario
    Route::prefix('auth')->name('api.auth.')->group(function () {
        Route::get('/user', [AuthController::class, 'user'])->name('user');
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
        Route::post('/logout-all', [AuthController::class, 'logoutAll'])->name('logout-all');
        Route::get('/tokens', [AuthController::class, 'listTokens'])->name('tokens.list');
        Route::delete('/tokens/{tokenId}', [AuthController::class, 'revokeToken'])->name('tokens.revoke');
    });
    
    // Verificadores avanzados
    Route::middleware(['api.role:Verifier,LeagueAdmin,SuperAdmin'])->group(function () {
        Route::post('/verify-batch', [QrVerificationController::class, 'verifyBatch'])->name('api.verify-batch');
        Route::get('/stats/dashboard', [QrVerificationController::class, 'getStats'])->name('api.stats.dashboard');
    });
});
```

---

## 🎨 UI/UX ESPECIFICACIONES FRONTEND PÚBLICO

### Diseño General del Sistema Público

#### **Principios de Diseño**
- **Mobile First**: Diseño prioritario para dispositivos móviles
- **Performance First**: Carga rápida y optimizada
- **Accessibility**: WCAG 2.1 AA compliant
- **Progressive Enhancement**: Funciona sin JavaScript, mejora con él

#### **Paleta de Colores**
```css
:root {
  /* Colores Principales */
  --primary-blue: #1e40af;     /* Azul profesional */
  --primary-orange: #ea580c;   /* Naranja voleibol */
  --success-green: #16a34a;    /* Verde éxito */
  --warning-yellow: #ca8a04;   /* Amarillo advertencia */
  --error-red: #dc2626;        /* Rojo error */
  
  /* Colores Neutrales */
  --gray-50: #f9fafb;
  --gray-100: #f3f4f6;
  --gray-200: #e5e7eb;
  --gray-300: #d1d5db;
  --gray-400: #9ca3af;
  --gray-500: #6b7280;
  --gray-600: #4b5563;
  --gray-700: #374151;
  --gray-800: #1f2937;
  --gray-900: #111827;
  
  /* Modo Oscuro */
  --dark-bg: #0f172a;
  --dark-surface: #1e293b;
  --dark-border: #334155;
  --dark-text: #e2e8f0;
  
  /* Colores Deportivos Específicos */
  --volley-court: #d97706;    /* Naranja cancha */
  --volley-net: #374151;      /* Gris red */
  --winner-gold: #f59e0b;     /* Oro ganador */
  --colombia-yellow: #fbbf24; /* Amarillo Colombia */
  --colombia-blue: #1d4ed8;   /* Azul Colombia */
  --colombia-red: #dc2626;    /* Rojo Colombia */
}
```

#### **Tipografía**
```css
/* Fuentes */
--font-sans: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
--font-display: 'Poppins', sans-serif;
--font-mono: 'JetBrains Mono', 'Cascadia Code', monospace;

/* Tamaños Responsivos */
--text-xs: clamp(0.75rem, 0.7rem + 0.2vw, 0.875rem);   /* 12-14px */
--text-sm: clamp(0.875rem, 0.8rem + 0.3vw, 1rem);      /* 14-16px */
--text-base: clamp(1rem, 0.9rem + 0.4vw, 1.125rem);    /* 16-18px */
--text-lg: clamp(1.125rem, 1rem + 0.5vw, 1.25rem);     /* 18-20px */
--text-xl: clamp(1.25rem, 1.1rem + 0.6vw, 1.5rem);     /* 20-24px */
--text-2xl: clamp(1.5rem, 1.3rem + 0.8vw, 1.875rem);   /* 24-30px */
--text-3xl: clamp(1.875rem, 1.6rem + 1vw, 2.25rem);    /* 30-36px */
--text-4xl: clamp(2.25rem, 2rem + 1.2vw, 3rem);        /* 36-48px */
--text-5xl: clamp(3rem, 2.5rem + 2vw, 4rem);           /* 48-64px */

/* Pesos y espaciado */
--font-light: 300;
--font-normal: 400;
--font-medium: 500;
--font-semibold: 600;
--font-bold: 700;
--font-extrabold: 800;

--line-height-tight: 1.25;
--line-height-normal: 1.5;
--line-height-relaxed: 1.75;
```

### Especificaciones por Componente

#### **1. Header Principal**
```html
<header class="sticky top-0 z-50 bg-white/95 backdrop-blur-sm border-b border-gray-200 dark:bg-dark-bg/95 dark:border-dark-border transition-all duration-200">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center h-16">
      <!-- Logo y Navegación -->
      <div class="flex items-center space-x-8">
        <div class="flex-shrink-0">
          <img class="h-10 w-auto" src="/images/volleypass-logo.svg" alt="VolleyPass">
          <span class="sr-only">VolleyPass - Sistema de Gestión de Voleibol</span>
        </div>
        <nav class="hidden md:flex space-x-6" role="navigation" aria-label="Navegación principal">
          <a href="/" 
             class="text-gray-900 hover:text-primary-blue font-medium transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-primary-blue focus:ring-offset-2 rounded-md px-2 py-1"
             aria-current="page">
            Torneos
          </a>
          <a href="/public/standings" 
             class="text-gray-600 hover:text-primary-blue transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-primary-blue focus:ring-offset-2 rounded-md px-2 py-1">
            Posiciones
          </a>
          <a href="/public/schedule" 
             class="text-gray-600 hover:text-primary-blue transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-primary-blue focus:ring-offset-2 rounded-md px-2 py-1">
            Calendario
          </a>
          <a href="/public/results" 
             class="text-gray-600 hover:text-primary-blue transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-primary-blue focus:ring-offset-2 rounded-md px-2 py-1">
            Resultados
          </a>
        </nav>
      </div>
      
      <!-- Acciones -->
      <div class="flex items-center space-x-3">
        <!-- Toggle modo oscuro -->
        <button 
          class="p-2 text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white transition-colors duration-200 rounded-lg hover:bg-gray-100 dark:hover:bg-dark-surface focus:outline-none focus:ring-2 focus:ring-primary-blue"
          aria-label="Cambiar tema">
          <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
            <!-- Sun/Moon icon -->
            <path d="M10 2L13.09 8.26L20 9L14 14.74L15.18 21.02L10 17.77L4.82 21.02L6 14.74L0 9L6.91 8.26L10 2Z"/>
          </svg>
        </button>
        
        <!-- CTA Login -->
        <a href="/login" 
           class="bg-primary-blue text-white px-4 py-2 rounded-lg hover:bg-blue-700 font-medium transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-primary-blue focus:ring-offset-2 shadow-sm hover:shadow-md">
          Iniciar Sesión
        </a>
        
        <!-- Menú móvil -->
        <button class="md:hidden p-2 text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white"
                aria-label="Abrir menú de navegación">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
          </svg>
        </button>
      </div>
    </div>
    
    <!-- Menú móvil desplegable -->
    <div class="md:hidden border-t border-gray-200 dark:border-dark-border mt-2 py-3 space-y-2">
      <a href="/" class="block px-3 py-2 text-gray-900 dark:text-white font-medium">Torneos</a>
      <a href="/public/standings" class="block px-3 py-2 text-gray-600 dark:text-gray-300">Posiciones</a>
      <a href="/public/schedule" class="block px-3 py-2 text-gray-600 dark:text-gray-300">Calendario</a>
      <a href="/public/results" class="block px-3 py-2 text-gray-600 dark:text-gray-300">Resultados</a>
    </div>
  </div>
</header>
```

**Características:**
- **Sticky positioning** para navegación siempre visible
- **Backdrop blur** para efecto moderno tipo ESPN/UEFA
- **Responsive collapse** en móviles con menú hamburguesa
- **Theme toggle** para modo oscuro/claro
- **CTA prominente** para login
- **Accessibility compliant** con aria-labels y focus states

#### **2. Hero Section (Página Principal)**
```html
<section class="relative overflow-hidden bg-gradient-to-br from-colombia-blue via-primary-blue to-blue-900 min-h-[70vh] flex items-center">
  <!-- Patrón de fondo sutil -->
  <div class="absolute inset-0 bg-[url('/images/volleyball-pattern.svg')] opacity-5"></div>
  
  <!-- Elementos decorativos -->
  <div class="absolute top-20 left-10 w-32 h-32 bg-primary-orange/10 rounded-full blur-xl"></div>
  <div class="absolute bottom-20 right-10 w-48 h-48 bg-colombia-yellow/10 rounded-full blur-xl"></div>
  
  <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="lg:grid lg:grid-cols-12 lg:gap-12 items-center">
      <div class="lg:col-span-7">
        <!-- Badge superior -->
        <div class="inline-flex items-center px-4 py-2 bg-white/10 backdrop-blur-sm rounded-full text-white mb-6">
          <span class="w-2 h-2 bg-green-400 rounded-full mr-2 animate-pulse"></span>
          <span class="text-sm font-medium">En vivo: 12 partidos activos</span>
        </div>
        
        <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold text-white mb-6 leading-tight">
          Voleibol de 
          <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary-orange to-colombia-yellow">
            Sucre
          </span>
          <br>en Tiempo Real
        </h1>
        
        <p class="text-xl text-blue-100 mb-8 leading-relaxed max-w-2xl">
          Sigue todos los torneos, resultados y estadísticas del voleibol departamental. 
          Información oficial actualizada al instante desde las canchas.
        </p>
        
        <!-- Stats rápidas -->
        <div class="grid grid-cols-3 gap-4 mb-8">
          <div class="text-center">
            <div class="text-2xl sm:text-3xl font-bold text-white">24</div>
            <div class="text-sm text-blue-200">Equipos Activos</div>
          </div>
          <div class="text-center">
            <div class="text-2xl sm:text-3xl font-bold text-white">147</div>
            <div class="text-sm text-blue-200">Jugadoras</div>
          </div>
          <div class="text-center">
            <div class="text-2xl sm:text-3xl font-bold text-white">5</div>
            <div class="text-sm text-blue-200">Torneos</div>
          </div>
        </div>
        
        <!-- CTAs -->
        <div class="flex flex-col sm:flex-row gap-4">
          <a href="#torneos-activos" 
             class="bg-primary-orange text-white px-8 py-4 rounded-xl font-semibold hover:bg-orange-600 transition-all duration-200 text-center shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
            <span class="flex items-center justify-center">
              <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z"/>
              </svg>
              Ver Torneos en Vivo
            </span>
          </a>
          <a href="/public/schedule" 
             class="border-2 border-white text-white px-8 py-4 rounded-xl font-semibold hover:bg-white hover:text-primary-blue transition-all duration-200 text-center">
            Ver Calendario Completo
          </a>
        </div>
      </div>
      
      <div class="lg:col-span-5 mt-12 lg:mt-0">
        <div class="relative">
          <!-- Imagen principal -->
          <div class="relative z-10">
            <img src="/images/hero-volleyball-team.webp" 
                 alt="Equipo de voleibol femenino de Sucre celebrando" 
                 class="w-full h-auto rounded-2xl shadow-2xl">
          </div>
          
          <!-- Cards flotantes con estadísticas -->
          <div class="absolute -bottom-6 -left-6 bg-white dark:bg-dark-surface rounded-2xl p-6 shadow-2xl z-20 max-w-xs">
            <div class="flex items-center space-x-3">
              <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
              </div>
              <div>
                <div class="text-2xl font-bold text-gray-900 dark:text-white">98%</div>
                <div class="text-sm text-gray-600 dark:text-gray-400">Partidos Verificados</div>
              </div>
            </div>
          </div>
          
          <div class="absolute -top-6 -right-6 bg-gradient-to-r from-primary-orange to-yellow-500 rounded-2xl p-6 shadow-2xl text-white z-20">
            <div class="text-center">
              <div class="text-3xl font-bold">⭐</div>
              <div class="text-sm font-medium mt-1">Liga Oficial</div>
              <div class="text-xs opacity-90">Sucre 2024</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
```

**Características:**
- **Gradiente patriótico** con colores de Colombia
- **Elementos decorativos** sutiles pero modernos
- **Live badge** para mostrar actividad en tiempo real
- **Stats rápidas** para credibilidad inmediata
- **CTAs contrastantes** para diferentes acciones
- **Responsive images** con lazy loading
- **Cards flotantes** tipo ESPN para información destacada

#### **3. Cards de Torneos**
```html
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
  <article class="group bg-white dark:bg-dark-surface rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden border border-gray-100 dark:border-dark-border">
    <!-- Header con imagen y estado -->
    <div class="relative h-48 overflow-hidden">
      <img src="/images/tournament-banner.webp" 
           alt="Copa Departamental Sucre - Voleibol Femenino" 
           class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
      
      <!-- Overlay gradient -->
      <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/20 to-transparent"></div>
      
      <!-- Estado del torneo -->
      <div class="absolute top-4 right-4 flex space-x-2">
        <span class="bg-green-500 text-white px-3 py-1 rounded-full text-sm font-semibold flex items-center">
          <span class="w-2 h-2 bg-white rounded-full mr-2 animate-pulse"></span>
          En Vivo
        </span>
        <button class="bg-white/20 backdrop-blur-sm text-white p-2 rounded-full hover:bg-white/30 transition-colors">
          <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
            <path d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z"/>
          </svg>
        </button>
      </div>
      
      <!-- Información superpuesta -->
      <div class="absolute bottom-4 left-4 text-white">
        <div class="text-xs font-medium bg-black/30 backdrop-blur-sm px-2 py-1 rounded-md mb-1">
          Femenino • Categoría A
        </div>
        <div class="text-sm font-semibold">15 equipos participantes</div>
      </div>
    </div>
    
    <!-- Contenido principal -->
    <div class="p-6">
      <div class="flex items-start justify-between mb-3">
        <div>
          <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-1 group-hover:text-primary-blue transition-colors line-clamp-2">
            Copa Departamental Sucre 2024
          </h3>
          <p class="text-sm text-gray-500 dark:text-gray-400">Liga Departamental de Sucre</p>
        </div>
        <div class="flex-shrink-0 ml-3">
          <img src="/images/liga-sucre-logo.png" alt="Liga Sucre" class="w-10 h-10 rounded-lg">
        </div>
      </div>
      
      <p class="text-gray-600 dark:text-gray-300 text-sm mb-4 line-clamp-2">
        Torneo oficial con la participación de los mejores equipos de voleibol femenino del departamento.
      </p>
      
      <!-- Progreso del torneo -->
      <div class="mb-4">
        <div class="flex justify-between items-center text-sm text-gray-600 dark:text-gray-400 mb-2">
          <span class="font-medium">Fase de Grupos</span>
          <span>12 de 20 partidos</span>
        </div>
        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
          <div class="bg-gradient-to-r from-primary-blue to-primary-orange h-2 rounded-full transition-all duration-500" 
               style="width: 60%">
          </div>
        </div>
        <div class="text-xs text-gray-500 mt-1">60% completado</div>
      </div>
      
      <!-- Información clave en grid -->
      <div class="grid grid-cols-2 gap-4 mb-4 text-sm">
        <div class="flex items-center text-gray-600 dark:text-gray-400">
          <svg class="w-4 h-4 mr-2 text-primary-orange" fill="currentColor" viewBox="0 0 20 20">
            <path d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zM4 9h12v7H4V9z"/>
          </svg>
          <span>15 Dic - 22 Dic</span>
        </div>
        <div class="flex items-center text-gray-600 dark:text-gray-400">
          <svg class="w-4 h-4 mr-2 text-primary-orange" fill="currentColor" viewBox="0 0 20 20">
            <path d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z"/>
          </svg>
          <span>Sincelejo</span>
        </div>
        <div class="flex items-center text-gray-600 dark:text-gray-400">
          <svg class="w-4 h-4 mr-2 text-primary-orange" fill="currentColor" viewBox="0 0 20 20">
            <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
          </svg>
          <span>15 equipos</span>
        </div>
        <div class="flex items-center text-gray-600 dark:text-gray-400">
          <svg class="w-4 h-4 mr-2 text-primary-orange" fill="currentColor" viewBox="0 0 20 20">
            <path d="M2 10a8 8 0 018-8v8h8a8 8 0 11-16 0z"/>
            <path d="M12 2.252A8.014 8.014 0 0117.748 8H12V2.252z"/>
          </svg>
          <span>$50.000 premio</span>
        </div>
      </div>
      
      <!-- Próximo partido destacado -->
      <div class="bg-gray-50 dark:bg-gray-800/50 rounded-lg p-3 mb-4">
        <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">Próximo partido</div>
        <div class="flex items-center justify-between text-sm">
          <div class="flex items-center space-x-2">
            <img src="/images/team-logo-1.png" alt="Águilas" class="w-6 h-6 rounded">
            <span class="font-medium">Águilas vs Panteras</span>
            <img src="/images/team-logo-2.png" alt="Panteras" class="w-6 h-6 rounded">
          </div>
          <span class="text-primary-blue font-medium">Hoy 7:00 PM</span>
        </div>
      </div>
      
      <!-- Acciones -->
      <div class="flex gap-2">
        <a href="/public/tournament/123" 
           class="flex-1 bg-primary-blue text-white text-center py-3 rounded-lg hover:bg-blue-700 transition-colors font-medium">
          Ver Detalles
        </a>
        <button class="px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors group">
          <svg class="w-4 h-4 text-gray-400 group-hover:text-red-500 transition-colors" fill="currentColor" viewBox="0 0 20 20">
            <path d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z"/>
          </svg>
        </button>
        <button class="px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
          <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
            <path d="M15 8a3 3 0 10-2.977-2.63l-4.94 2.47a3 3 0 100 4.319l4.94 2.47a3 3 0 10.895-1.789l-4.94-2.47a3.027 3.027 0 000-.74l4.94-2.47C13.456 7.68 14.19 8 15 8z"/>
          </svg>
        </button>
      </div>
    </div>
  </article>
</div>
```

**Características:**
- **Visual hierarchy** clara inspirada en ESPN
- **Live indicators** prominentes
- **Progress bars** para mostrar avance
- **Next match preview** tipo Liga MX App
- **Hover effects** sutiles pero atractivos
- **Information density** balanceada
- **Action buttons** claros y accesibles

#### **4. Marcadores en Vivo (Estilo UEFA.com)**
```html
<div class="bg-gradient-to-r from-green-500 via-green-600 to-emerald-600 rounded-2xl text-white p-6 mb-8 shadow-xl">
  <!-- Header del partido en vivo -->
  <div class="flex items-center justify-between mb-6">
    <div class="flex items-center space-x-3">
      <div class="flex items-center space-x-2">
        <div class="w-3 h-3 bg-red-500 rounded-full animate-pulse"></div>
        <span class="font-bold text-lg">EN VIVO</span>
      </div>
      <div class="hidden sm:block w-px h-6 bg-white/30"></div>
      <div class="hidden sm:flex items-center space-x-2 text-sm">
        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
          <path d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z"/>
        </svg>
        <span>Copa Departamental • Final</span>
      </div>
    </div>
    <div class="text-right">
      <div class="text-sm opacity-90">Set 3</div>
      <div class="text-lg font-bold">12:45 min</div>
    </div>
  </div>
  
  <div class="grid grid-cols-1 lg:grid-cols-5 gap-6 items-center">
    <!-- Equipo Local -->
    <div class="lg:col-span-2">
      <div class="flex items-center justify-center lg:justify-end space-x-4">
        <div class="text-center lg:text-right order-2 lg:order-1">
          <h3 class="text-xl lg:text-2xl font-bold mb-1">Águilas Doradas</h3>
          <p class="text-sm opacity-90 mb-2">Sincelejo FC</p>
          <div class="flex justify-center lg:justify-end space-x-1">
            <span class="px-2 py-1 bg-white/20 rounded text-xs">Local</span>
            <span class="px-2 py-1 bg-yellow-500/80 rounded text-xs">Líder</span>
          </div>
        </div>
        <div class="order-1 lg:order-2">
          <img src="/images/team-logo-aguilas.png" 
               alt="Escudo Águilas Doradas" 
               class="w-16 h-16 lg:w-20 lg:h-20 rounded-full border-4 border-white/30 shadow-lg">
        </div>
      </div>
    </div>
    
    <!-- Marcador Central -->
    <div class="lg:col-span-1">
      <div class="text-center">
        <!-- Sets ganados -->
        <div class="bg-white/15 backdrop-blur-sm rounded-2xl p-4 mb-4 border border-white/20">
          <div class="flex justify-center items-center space-x-8">
            <div class="text-center">
              <div class="text-4xl lg:text-5xl font-bold">2</div>
              <div class="text-xs opacity-75 mt-1">SETS</div>
            </div>
            <div class="text-3xl font-light opacity-60">-</div>
            <div class="text-center">
              <div class="text-4xl lg:text-5xl font-bold">1</div>
              <div class="text-xs opacity-75 mt-1">SETS</div>
            </div>
          </div>
        </div>
        
        <!-- Puntos del set actual -->
        <div class="flex justify-center items-center space-x-4">
          <div class="bg-white/20 backdrop-blur-sm px-4 py-2 rounded-xl font-bold text-xl">18</div>
          <div class="text-sm opacity-75 px-3">Set 3</div>
          <div class="bg-white/20 backdrop-blur-sm px-4 py-2 rounded-xl font-bold text-xl">15</div>
        </div>
        
        <!-- Indicador de saque -->
        <div class="mt-3 flex justify-center">
          <div class="bg-yellow-400 text-gray-900 px-3 py-1 rounded-full text-xs font-bold">
            ← Saque Águilas
          </div>
        </div>
      </div>
    </div>
    
    <!-- Equipo Visitante -->
    <div class="lg:col-span-2">
      <div class="flex items-center justify-center lg:justify-start space-x-4">
        <img src="/images/team-logo-panteras.png" 
             alt="Escudo Panteras FC" 
             class="w-16 h-16 lg:w-20 lg:h-20 rounded-full border-4 border-white/30 shadow-lg">
        <div class="text-center lg:text-left">
          <h3 class="text-xl lg:text-2xl font-bold mb-1">Panteras FC</h3>
          <p class="text-sm opacity-90 mb-2">Corozal</p>
          <div class="flex justify-center lg:justify-start space-x-1">
            <span class="px-2 py-1 bg-white/20 rounded text-xs">Visitante</span>
            <span class="px-2 py-1 bg-orange-500/80 rounded text-xs">Defensor</span>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <!-- Historial de sets -->
  <div class="mt-6 pt-4 border-t border-white/20">
    <div class="grid grid-cols-3 lg:grid-cols-5 gap-4 text-center">
      <div class="lg:col-start-2">
        <div class="text-xs opacity-75 mb-1">SET 1</div>
        <div class="bg-white/10 rounded-lg py-2 px-3">
          <span class="font-bold">25</span>
          <span class="mx-1 opacity-60">-</span>
          <span class="font-bold">22</span>
        </div>
      </div>
      <div>
        <div class="text-xs opacity-75 mb-1">SET 2</div>
        <div class="bg-white/10 rounded-lg py-2 px-3">
          <span class="font-bold">23</span>
          <span class="mx-1 opacity-60">-</span>
          <span class="font-bold">25</span>
        </div>
      </div>
      <div>
        <div class="text-xs opacity-75 mb-1">SET 3</div>
        <div class="bg-yellow-400/20 border border-yellow-400/50 rounded-lg py-2 px-3">
          <span class="font-bold">18</span>
          <span class="mx-1 opacity-60">-</span>
          <span class="font-bold">15</span>
        </div>
      </div>
    </div>
  </div>
  
  <!-- Acciones rápidas -->
  <div class="mt-4 flex justify-center space-x-3">
    <button class="bg-white/20 hover:bg-white/30 backdrop-blur-sm px-4 py-2 rounded-lg text-sm font-medium transition-colors">
      📊 Estadísticas
    </button>
    <button class="bg-white/20 hover:bg-white/30 backdrop-blur-sm px-4 py-2 rounded-lg text-sm font-medium transition-colors">
      📱 Compartir
    </button>
    <button class="bg-white/20 hover:bg-white/30 backdrop-blur-sm px-4 py-2 rounded-lg text-sm font-medium transition-colors">
      🔔 Alertas
    </button>
  </div>
</div>
```

#### **5. Tabla de Posiciones (Estilo Liga MX)**
```html
<div class="bg-white dark:bg-dark-surface rounded-2xl shadow-lg overflow-hidden border border-gray-100 dark:border-dark-border">
  <!-- Header con controles -->
  <div class="p-6 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
      <div>
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-1">Tabla de Posiciones</h2>
        <p class="text-sm text-gray-600 dark:text-gray-400">Actualizada hace 2 minutos</p>
      </div>
      <div class="flex flex-col sm:flex-row items-start sm:items-center space-y-2 sm:space-y-0 sm:space-x-3">
        <select class="border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm bg-white dark:bg-dark-surface">
          <option>Grupo A</option>
          <option>Grupo B</option>
          <option>Clasificación General</option>
        </select>
        <button class="p-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
          <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
            <path d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z"/>
          </svg>
        </button>
      </div>
    </div>
  </div>
  
  <!-- Tabla responsive -->
  <div class="overflow-x-auto">
    <table class="w-full">
      <thead class="bg-gray-50 dark:bg-gray-700">
        <tr>
          <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
            Pos
          </th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
            Equipo
          </th>
          <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
            PJ
          </th>
          <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
            PG
          </th>
          <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
            PP
          </th>
          <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
            Sets
          </th>
          <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
            Pts
          </th>
          <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
            Racha
          </th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
        <!-- Fila 1 - Líder -->
        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
          <td class="px-4 py-4">
            <div class="flex items-center">
              <div class="w-8 h-8 bg-yellow-500 text-white rounded-full flex items-center justify-center text-sm font-bold">
                1
              </div>
              <div class="ml-2 w-1 h-8 bg-green-500 rounded-full"></div>
            </div>
          </td>
          <td class="px-6 py-4">
            <div class="flex items-center space-x-3">
              <img src="/images/team-logo-aguilas.png" alt="Águilas" class="w-10 h-10 rounded-full">
              <div>
                <div class="text-sm font-medium text-gray-900 dark:text-white">Águilas Doradas</div>
                <div class="text-xs text-gray-500 dark:text-gray-400">Sincelejo FC</div>
              </div>
            </div>
          </td>
          <td class="px-3 py-4 text-center text-sm text-gray-900 dark:text-white font-medium">8</td>
          <td class="px-3 py-4 text-center text-sm text-gray-900 dark:text-white font-medium">7</td>
          <td class="px-3 py-4 text-center text-sm text-gray-900 dark:text-white font-medium">1</td>
          <td class="px-3 py-4 text-center text-sm text-gray-600 dark:text-gray-400">21-8</td>
          <td class="px-3 py-4 text-center">
            <span class="text-lg font-bold text-gray-900 dark:text-white">21</span>
          </td>
          <td class="px-3 py-4 text-center">
            <div class="flex justify-center space-x-1">
              <span class="w-2 h-2 bg-green-500 rounded-full" title="Victoria"></span>
              <span class="w-2 h-2 bg-green-500 rounded-full" title="Victoria"></span>
              <span class="w-2 h-2 bg-green-500 rounded-full" title="Victoria"></span>
              <span class="w-2 h-2 bg-red-500 rounded-full" title="Derrota"></span>
              <span class="w-2 h-2 bg-green-500 rounded-full" title="Victoria"></span>
            </div>
          </td>
        </tr>
        
        <!-- Fila 2 - Clasificado -->
        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
          <td class="px-4 py-4">
            <div class="flex items-center">
              <div class="w-8 h-8 bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-full flex items-center justify-center text-sm font-bold">
                2
              </div>
              <div class="ml-2 w-1 h-8 bg-green-500 rounded-full"></div>
            </div>
          </td>
          <td class="px-6 py-4">
            <div class="flex items-center space-x-3">
              <img src="/images/team-logo-panteras.png" alt="Panteras" class="w-10 h-10 rounded-full">
              <div>
                <div class="text-sm font-medium text-gray-900 dark:text-white">Panteras FC</div>
                <div class="text-xs text-gray-500 dark:text-gray-400">Corozal</div>
              </div>
            </div>
          </td>
          <td class="px-3 py-4 text-center text-sm text-gray-900 dark:text-white font-medium">8</td>
          <td class="px-3 py-4 text-center text-sm text-gray-900 dark:text-white font-medium">6</td>
          <td class="px-3 py-4 text-center text-sm text-gray-900 dark:text-white font-medium">2</td>
          <td class="px-3 py-4 text-center text-sm text-gray-600 dark:text-gray-400">19-12</td>
          <td class="px-3 py-4 text-center">
            <span class="text-lg font-bold text-gray-900 dark:text-white">18</span>
          </td>
          <td class="px-3 py-4 text-center">
            <div class="flex justify-center space-x-1">
              <span class="w-2 h-2 bg-green-500 rounded-full"></span>
              <span class="w-2 h-2 bg-red-500 rounded-full"></span>
              <span class="w-2 h-2 bg-green-500 rounded-full"></span>
              <span class="w-2 h-2 bg-green-500 rounded-full"></span>
              <span class="w-2 h-2 bg-red-500 rounded-full"></span>
            </div>
          </td>
        </tr>
        
        <!-- Más filas... -->
      </tbody>
    </table>
  </div>
  
  <!-- Leyenda -->
  <div class="p-4 bg-gray-50 dark:bg-gray-800/50 border-t border-gray-200 dark:border-gray-700">
    <div class="flex flex-wrap items-center gap-4 text-xs text-gray-600 dark:text-gray-400">
      <div class="flex items-center">
        <div class="w-3 h-3 bg-green-500 rounded mr-2"></div>
        <span>Clasificación directa</span>
      </div>
      <div class="flex items-center">
        <div class="w-3 h-3 bg-yellow-500 rounded mr-2"></div>
        <span>Repechaje</span>
      </div>
      <div class="flex items-center">
        <div class="w-3 h-3 bg-red-500 rounded mr-2"></div>
        <span>Eliminado</span>
      </div>
      <div class="ml-auto text-right">
        <span>PJ: Partidos Jugados | PG: Partidos Ganados | PP: Partidos Perdidos | Pts: Puntos</span>
      </div>
    </div>
  </div>
</div>
```

---

## 📱 REFERENCIAS DE ESTILO

### 🎯 **Inspiración en Plataformas Deportivas Modernas**

#### **ESPN.com**
- **Layout**: Grid dinámico con cards prominentes
- **Live scores**: Marcadores destacados con animaciones sutiles
- **Navigation**: Sticky header con navegación contextual
- **Typography**: Jerarquías claras, números grandes para marcadores

#### **Liga MX App**
- **Mobile First**: Diseño optimizado para móviles
- **Color coding**: Estados visuales claros (verde=en vivo, azul=programado)
- **Team branding**: Integración de logos y colores de equipos
- **Quick actions**: Botones de acción rápida siempre visibles

#### **UEFA.com**
- **Professional feel**: Diseño limpio y profesional
- **Match cards**: Cards de partidos con información densa pero organizada
- **Progressive disclosure**: Información básica primero, detalles bajo demanda
- **Responsive tables**: Tablas que se adaptan perfectamente a móviles

### 🇨🇴 **Identidad Visual del Voleibol Colombiano**

#### **Paleta de Colores Patrióticos**
```css
/* Colores Colombia adaptados para voleibol */
:root {
  --colombia-yellow: #fbbf24;    /* Amarillo vibrante */
  --colombia-blue: #1d4ed8;      /* Azul océano */
  --colombia-red: #dc2626;       /* Rojo pasión */
  
  /* Colores específicos del voleibol */
  --volley-court: #d97706;       /* Naranja cancha */
  --volley-net: #374151;         /* Gris red de voleibol */
  --volley-ball: #fef3c7;        /* Amarillo pelota */
  
  /* Colores regionales Sucre */
  --sucre-green: #059669;        /* Verde sabana */
  --sucre-sand: #f59e0b;         /* Arena costeña */
}
```

#### **Elementos Gráficos Característicos**
- **Iconografía deportiva**: Pelotas, redes, canchas estilizadas
- **Patrones geométricos**: Inspirados en las líneas de la cancha
- **Gradientes dinámicos**: Que evocan movimiento y energía
- **Fotografía auténtica**: Jugadoras reales, no stock photos

### 🎨 **Sistema de Design Tokens**

#### **Espaciado Consistente**
```css
:root {
  /* Escala de espaciado basada en 4px */
  --space-1: 0.25rem;   /* 4px */
  --space-2: 0.5rem;    /* 8px */
  --space-3: 0.75rem;   /* 12px */
  --space-4: 1rem;      /* 16px */
  --space-5: 1.25rem;   /* 20px */
  --space-6: 1.5rem;    /* 24px */
  --space-8: 2rem;      /* 32px */
  --space-10: 2.5rem;   /* 40px */
  --space-12: 3rem;     /* 48px */
  --space-16: 4rem;     /* 64px */
  --space-20: 5rem;     /* 80px */
  --space-24: 6rem;     /* 96px */
  
  /* Espaciado semántico */
  --space-section: var(--space-16);     /* Entre secciones */
  --space-component: var(--space-8);    /* Entre componentes */
  --space-element: var(--space-4);      /* Entre elementos */
  --space-inline: var(--space-2);       /* Elementos inline */
}
```

#### **Radios de Borde Consistentes**
```css
:root {
  --radius-sm: 0.375rem;    /* 6px - Elementos pequeños */
  --radius-md: 0.5rem;      /* 8px - Botones, inputs */
  --radius-lg: 0.75rem;     /* 12px - Cards, modales */
  --radius-xl: 1rem;        /* 16px - Containers grandes */
  --radius-2xl: 1.5rem;     /* 24px - Hero sections */
  --radius-full: 9999px;    /* Círculos, pills */
}
```

#### **Sombras Consistentes**
```css
:root {
  --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
  --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
  --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
  --shadow-xl: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
  --shadow-2xl: 0 25px 50px -12px rgb(0 0 0 / 0.25);
  
  /* Sombras de colores para estados */
  --shadow-primary: 0 10px 15px -3px rgb(30 64 175 / 0.3);
  --shadow-success: 0 10px 15px -3px rgb(22 163 74 / 0.3);
  --shadow-warning: 0 10px 15px -3px rgb(202 138 4 / 0.3);
  --shadow-error: 0 10px 15px -3px rgb(220 38 38 / 0.3);
}
```

### 📱 **Componentes Base del Sistema**

#### **6. Componente de Calendario/Fixtures**
```html
<div class="bg-white dark:bg-dark-surface rounded-2xl shadow-lg overflow-hidden">
  <!-- Header del calendario -->
  <div class="p-6 bg-gradient-to-r from-primary-blue to-blue-600 text-white">
    <div class="flex items-center justify-between">
      <div>
        <h2 class="text-2xl font-bold mb-1">Próximos Partidos</h2>
        <p class="text-blue-100">Esta semana en la Copa Departamental</p>
      </div>
      <div class="flex items-center space-x-2">
        <button class="p-2 hover:bg-white/20 rounded-lg transition-colors">
          <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
            <path d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"/>
          </svg>
        </button>
        <span class="text-lg font-semibold px-3">Dic 2024</span>
        <button class="p-2 hover:bg-white/20 rounded-lg transition-colors">
          <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
            <path d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"/>
          </svg>
        </button>
      </div>
    </div>
  </div>
  
  <!-- Lista de partidos -->
  <div class="p-6 space-y-4">
    <!-- Partido destacado (hoy) -->
    <div class="border-l-4 border-primary-orange bg-orange-50 dark:bg-orange-900/20 rounded-lg p-4">
      <div class="flex items-center justify-between mb-3">
        <span class="bg-primary-orange text-white px-3 py-1 rounded-full text-sm font-semibold">
          HOY
        </span>
        <span class="text-sm text-gray-600 dark:text-gray-400">19:00</span>
      </div>
      
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-center">
        <!-- Equipo local -->
        <div class="flex items-center justify-center md:justify-end space-x-3">
          <div class="text-center md:text-right">
            <div class="font-bold text-gray-900 dark:text-white">Águilas Doradas</div>
            <div class="text-sm text-gray-600 dark:text-gray-400">1° Grupo A</div>
          </div>
          <img src="/images/team-logo-aguilas.png" alt="Águilas" class="w-12 h-12 rounded-full">
        </div>
        
        <!-- VS y detalles -->
        <div class="text-center">
          <div class="text-2xl font-bold text-gray-400 mb-1">VS</div>
          <div class="text-sm text-gray-600 dark:text-gray-400">
            Cancha Principal<br>
            Copa Departamental
          </div>
        </div>
        
        <!-- Equipo visitante -->
        <div class="flex items-center justify-center md:justify-start space-x-3">
          <img src="/images/team-logo-panteras.png" alt="Panteras" class="w-12 h-12 rounded-full">
          <div class="text-center md:text-left">
            <div class="font-bold text-gray-900 dark:text-white">Panteras FC</div>
            <div class="text-sm text-gray-600 dark:text-gray-400">2° Grupo A</div>
          </div>
        </div>
      </div>
      
      <!-- Botón de seguimiento -->
      <div class="mt-4 text-center">
        <button class="bg-primary-orange text-white px-6 py-2 rounded-lg hover:bg-orange-600 transition-colors font-medium">
          🔔 Recibir Notificaciones
        </button>
      </div>
    </div>
    
    <!-- Partidos regulares -->
    <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
      <div class="flex items-center justify-between mb-3">
        <span class="text-sm text-gray-600 dark:text-gray-400 font-medium">Mañana • 20:30</span>
        <span class="bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 px-2 py-1 rounded text-xs">
          Semifinal
        </span>
      </div>
      
      <div class="flex items-center justify-between">
        <div class="flex items-center space-x-3">
          <img src="/images/team-logo-tiburones.png" alt="Tiburones" class="w-10 h-10 rounded-full">
          <span class="font-medium text-gray-900 dark:text-white">Tiburones del Mar</span>
        </div>
        
        <span class="text-gray-400 font-medium">vs</span>
        
        <div class="flex items-center space-x-3">
          <span class="font-medium text-gray-900 dark:text-white">Cóndores</span>
          <img src="/images/team-logo-condores.png" alt="Cóndores" class="w-10 h-10 rounded-full">
        </div>
      </div>
    </div>
    
    <!-- Más partidos... -->
  </div>
  
  <!-- Footer con enlace al calendario completo -->
  <div class="p-4 bg-gray-50 dark:bg-gray-800/50 border-t border-gray-200 dark:border-gray-700">
    <a href="/public/schedule" 
       class="block text-center text-primary-blue hover:text-blue-700 dark:text-blue-400 font-medium">
      Ver calendario completo →
    </a>
  </div>
</div>
```

#### **7. Sistema de Notificaciones Toast**
```html
<!-- Contenedor de notificaciones -->
<div id="toast-container" 
     class="fixed top-4 right-4 z-50 space-y-3"
     x-data="{ toasts: [] }">
  
  <!-- Toast de éxito -->
  <div x-show="toasts.includes('success')"
       x-transition:enter="transform transition ease-out duration-300"
       x-transition:enter-start="translate-x-full opacity-0"
       x-transition:enter-end="translate-x-0 opacity-100"
       x-transition:leave="transform transition ease-in duration-200"
       x-transition:leave-start="translate-x-0 opacity-100"
       x-transition:leave-end="translate-x-full opacity-0"
       class="bg-white dark:bg-dark-surface border border-green-200 dark:border-green-800 rounded-lg shadow-lg p-4 max-w-sm">
    <div class="flex items-start">
      <div class="flex-shrink-0">
        <svg class="w-6 h-6 text-green-500" fill="currentColor" viewBox="0 0 20 20">
          <path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
        </svg>
      </div>
      <div class="ml-3 flex-1">
        <p class="text-sm font-medium text-gray-900 dark:text-white">¡Gol confirmado!</p>
        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">El marcador se ha actualizado correctamente.</p>
      </div>
      <button @click="toasts = toasts.filter(t => t !== 'success')" 
              class="flex-shrink-0 ml-4 text-gray-400 hover:text-gray-600">
        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
          <path d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"/>
        </svg>
      </button>
    </div>
  </div>
  
  <!-- Toast de información -->
  <div class="bg-white dark:bg-dark-surface border border-blue-200 dark:border-blue-800 rounded-lg shadow-lg p-4 max-w-sm">
    <div class="flex items-start">
      <div class="flex-shrink-0">
        <svg class="w-6 h-6 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
          <path d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"/>
        </svg>
      </div>
      <div class="ml-3 flex-1">
        <p class="text-sm font-medium text-gray-900 dark:text-white">Partido iniciado</p>
        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Águilas vs Panteras ha comenzado en la Cancha Principal.</p>
      </div>
    </div>
  </div>
</div>
```

#### **8. Footer Completo**
```html
<footer class="bg-gray-900 text-white">
  <!-- Sección principal -->
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
      <!-- Branding y descripción -->
      <div class="lg:col-span-2">
        <div class="flex items-center space-x-3 mb-4">
          <img src="/images/volleypass-logo-white.svg" alt="VolleyPass" class="h-10 w-auto">
          <span class="text-xl font-bold">VolleyPass</span>
        </div>
        <p class="text-gray-300 mb-6 max-w-md">
          Sistema oficial de gestión y seguimiento del voleibol en el departamento de Sucre. 
          Conectando jugadoras, equipos y aficionados en tiempo real.
        </p>
        <div class="flex space-x-4">
          <a href="#" class="text-gray-400 hover:text-white transition-colors">
            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
              <!-- Facebook icon -->
            </svg>
          </a>
          <a href="#" class="text-gray-400 hover:text-white transition-colors">
            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
              <!-- Instagram icon -->
            </svg>
          </a>
          <a href="#" class="text-gray-400 hover:text-white transition-colors">
            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
              <!-- YouTube icon -->
            </svg>
          </a>
        </div>
      </div>
      
      <!-- Enlaces rápidos -->
      <div>
        <h3 class="text-lg font-semibold mb-4">Navegación</h3>
        <ul class="space-y-3">
          <li><a href="/" class="text-gray-300 hover:text-white transition-colors">Torneos</a></li>
          <li><a href="/public/standings" class="text-gray-300 hover:text-white transition-colors">Posiciones</a></li>
          <li><a href="/public/schedule" class="text-gray-300 hover:text-white transition-colors">Calendario</a></li>
          <li><a href="/public/results" class="text-gray-300 hover:text-white transition-colors">Resultados</a></li>
          <li><a href="/login" class="text-gray-300 hover:text-white transition-colors">Iniciar Sesión</a></li>
        </ul>
      </div>
      
      <!-- Soporte -->
      <div>
        <h3 class="text-lg font-semibold mb-4">Soporte</h3>
        <ul class="space-y-3">
          <li><a href="/ayuda" class="text-gray-300 hover:text-white transition-colors">Centro de Ayuda</a></li>
          <li><a href="/contacto" class="text-gray-300 hover:text-white transition-colors">Contacto</a></li>
          <li><a href="/privacidad" class="text-gray-300 hover:text-white transition-colors">Privacidad</a></li>
          <li><a href="/terminos" class="text-gray-300 hover:text-white transition-colors">Términos</a></li>
        </ul>
      </div>
    </div>
  </div>
  
  <!-- Sección inferior -->
  <div class="border-t border-gray-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
      <div class="flex flex-col md:flex-row items-center justify-between">
        <p class="text-gray-400 text-sm">
          © 2024 VolleyPass. Desarrollado para la Liga Departamental de Sucre.
        </p>
        <div class="flex items-center space-x-4 mt-4 md:mt-0">
          <span class="text-gray-400 text-sm">Con ❤️ desde Sincelejo</span>
          <div class="w-6 h-4 bg-gradient-to-r from-colombia-yellow via-colombia-blue to-colombia-red rounded-sm"></div>
        </div>
      </div>
    </div>
  </div>
</footer>
```

### 🎯 **Principios de Accesibilidad**

#### **WCAG 2.1 AA Compliance**
```css
/* Contraste mínimo asegurado */
:root {
  --text-high-contrast: #000000;      /* 21:1 ratio en blanco */
  --text-medium-contrast: #374151;    /* 7:1 ratio en blanco */
  --text-low-contrast: #6b7280;       /* 4.5:1 ratio en blanco */
  
  /* Estados de foco visibles */
  --focus-ring: 0 0 0 3px rgba(59, 130, 246, 0.5);
  --focus-ring-offset: 0 0 0 2px #ffffff;
}

/* Estados de foco consistentes */
.focus-visible {
  outline: none;
  box-shadow: var(--focus-ring-offset), var(--focus-ring);
}

/* Texto alternativo para imágenes */
.sr-only {
  position: absolute;
  width: 1px;
  height: 1px;
  padding: 0;
  margin: -1px;
  overflow: hidden;
  clip: rect(0, 0, 0, 0);
  white-space: nowrap;
  border: 0;
}
```

#### **Navegación por Teclado**
```html
<!-- Skip links para navegación por teclado -->
<a href="#main-content" 
   class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 bg-primary-blue text-white px-4 py-2 rounded-lg z-50">
  Saltar al contenido principal
</a>

<!-- Roles ARIA apropiados -->
<nav role="navigation" aria-label="Navegación principal">
  <!-- Navegación -->
</nav>

<main id="main-content" role="main">
  <!-- Contenido principal -->
</main>

<!-- Landmarks semánticos -->
<section aria-labelledby="tournaments-heading">
  <h2 id="tournaments-heading">Torneos Activos</h2>
  <!-- Contenido -->
</section>
```

### 📊 **Progressive Enhancement**

#### **Funcionalidad Base (Sin JavaScript)**
- **Navegación completa** funcional
- **Formularios** que envían datos correctamente
- **Contenido** completamente accesible
- **Enlaces** que funcionan sin JavaScript

#### **Mejoras con JavaScript**
- **Actualizaciones en tiempo real** de marcadores
- **Transiciones suaves** entre estados
- **Modales** y overlays interactivos
- **Filtrado dinámico** de tablas
- **Notificaciones** toast
- **Modo oscuro** toggle

### 🚀 **Optimización de Performance**

#### **Estrategias de Carga**
```html
<!-- Critical CSS inline -->
<style>
  /* CSS crítico para above-the-fold */
</style>

<!-- CSS no crítico diferido -->
<link rel="preload" href="/css/non-critical.css" as="style" onload="this.onload=null;this.rel='stylesheet'">

<!-- Imágenes optimizadas -->
<picture>
  <source srcset="/images/hero-volleyball.webp" type="image/webp">
  <source srcset="/images/hero-volleyball.avif" type="image/avif">
  <img src="/images/hero-volleyball.jpg" 
       alt="Equipo de voleibol" 
       loading="lazy"
       width="800" 
       height="600">
</picture>

<!-- Preload de recursos críticos -->
<link rel="preload" href="/fonts/inter-var.woff2" as="font" type="font/woff2" crossorigin>
```

#### **Métricas de Performance Objetivo**
- **First Contentful Paint**: < 1.5s
- **Largest Contentful Paint**: < 2.5s
- **Cumulative Layout Shift**: < 0.1
- **First Input Delay**: < 100ms
- **Time to Interactive**: < 3.5s

### 📱 **Responsive Breakpoints**

```css
/* Sistema de breakpoints móvil-first */
/* xs: 0px - 639px (móviles) */
/* sm: 640px - 767px (móviles grandes) */
/* md: 768px - 1023px (tablets) */
/* lg: 1024px - 1279px (laptops) */
/* xl: 1280px - 1535px (desktop) */
/* 2xl: 1536px+ (desktop grande) */

@media (min-width: 640px) {
  /* Estilos para móviles grandes */
}

@media (min-width: 768px) {
  /* Estilos para tablets */
}

@media (min-width: 1024px) {
  /* Estilos para laptops */
}
```

### 🎨 **Estados de Interacción**

```css
/* Estados hover, focus, active consistentes */
.button {
  transition: all 0.2s ease-in-out;
}

.button:hover {
  transform: translateY(-1px);
  box-shadow: var(--shadow-lg);
}

.button:active {
  transform: translateY(0);
  box-shadow: var(--shadow-md);
}

.button:focus-visible {
  outline: none;
  box-shadow: var(--focus-ring);
}

/* Estados de datos */
.loading {
  opacity: 0.6;
  pointer-events: none;
}

.error {
  border-color: var(--error-red);
  background-color: rgb(254 242 242);
}

.success {
  border-color: var(--success-green);
  background-color: rgb(240 253 244);
}
```

---

## 🎯 **RESUMEN EJECUTIVO**

### **Objetivos del Sistema UI/UX**

1. **Profesionalismo Accesible**: Diseño que se siente oficial pero no intimidante
2. **Performance Primero**: Carga rápida incluso con conectividad limitada
3. **Mobile First**: Optimizado para el uso principal en dispositivos móviles
4. **Identidad Regional**: Refleja la cultura deportiva de Sucre y Colombia
5. **Escalabilidad**: Sistema que puede crecer con nuevas funcionalidades

### **Diferenciadores Clave**

- **Tiempo Real**: Actualizaciones instantáneas de marcadores y eventos
- **Verificación QR**: Sistema único de autenticación de jugadoras
- **Multi-Plataforma**: Web responsive + APIs para futuras apps móviles
- **Personalización**: Experiencias adaptadas por rol de usuario
- **Accesibilidad**: Cumple estándares internacionales WCAG 2.1 AA

### **Próximos Pasos de Implementación**

1. **Crear sistema de design tokens** con variables CSS
2. **Implementar componentes base** usando Livewire + Tailwind
3. **Desarrollar sistema de grid** responsivo
4. **Configurar sistema de imágenes** optimizadas
5. **Implementar progressive enhancement** paso a paso

Este sistema UI/UX garantiza una experiencia moderna, accesible y escalable que posiciona a VolleyPass como la plataforma líder de gestión deportiva en la región, siguiendo las mejores prácticas internacionales adaptadas al contexto local colombiano.
