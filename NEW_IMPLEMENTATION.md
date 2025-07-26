# 🏐 VolleyPass - Documentación Completa del Proyecto

## Arquitectura y Estructura del Sistema

### Inspiración y Diseño

La plataforma está inspirada en plataformas deportivas modernas como **ESPN**, **Liga MX App**, y **UEFA.com**, adaptada específicamente para voleibol amateur/regional. El diseño es profesional pero accesible, priorizando la claridad de información sobre efectos visuales complejos.

### Características del Diseño

- **Sistema de design tokens consistente** con colores de la bandera colombiana
- **Tipografía legible** en todos los tamaños de pantalla
- **Esquema de colores** que refleja la identidad del voleibol colombiano
- **Interfaz funcional sin JavaScript** para usuarios con conectividad limitada
- **Experiencia mejorada** cuando JavaScript está disponible
- **Componentes en tiempo real** con Livewire y Alpine.js

## Estructura de Archivos del Proyecto

### 1. Layouts Principales

```
resources/views/layouts/
├── app.blade.php           # Layout principal con Alpine stores
├── public.blade.php        # Layout para páginas públicas
├── auth.blade.php          # Layout para autenticación
└── dashboard.blade.php     # Layout base para dashboards
```

### 2. Vistas Principales

```
resources/views/
├── welcome.blade.php       # Página de inicio con información general
├── pages/
│   ├── admin/
│   │   └── dashboard.blade.php    # Dashboard administrativo
│   ├── coach/
│   │   └── dashboard.blade.php    # Dashboard de entrenador
│   ├── player/
│   │   └── dashboard.blade.php    # Dashboard de jugadora
│   ├── referee/
│   │   └── dashboard.blade.php    # Dashboard de árbitro
│   └── medical/
│       └── dashboard.blade.php    # Dashboard médico
```

### 3. Componentes de Navegación

```
resources/views/components/navigation/
├── header.blade.php         # Header principal con menús responsive
├── footer.blade.php         # Footer con información de contacto
├── public-header.blade.php  # Header para páginas públicas
├── public-footer.blade.php  # Footer para páginas públicas
├── auth-menu.blade.php      # Menú para usuarios autenticados
├── guest-menu.blade.php     # Menú para invitados
├── mobile-menu.blade.php    # Menú móvil responsive
└── user-dropdown.blade.php  # Dropdown del usuario
```

### 4. CSS y Design Tokens

```css
/* resources/css/app.css */

/* Variables CSS para design tokens */
:root {
  /* Colores Primarios - Bandera Colombiana */
  --vp-primary-50: #fef3c7;
  --vp-primary-500: #f59e0b;  /* Amarillo Colombia */
  --vp-primary-600: #d97706;
  
  /* Colores Secundarios - Azul Voleibol */
  --vp-secondary-500: #3b82f6;
  --vp-secondary-600: #2563eb;
  
  /* Colores de Acento - Rojo Pasión */
  --vp-accent-500: #ef4444;   /* Rojo Colombia */
  --vp-accent-600: #dc2626;
  
  /* Estados */
  --vp-success: #10b981;
  --vp-warning: #f59e0b;
  --vp-error: #ef4444;
  --vp-live: #ff0000;
}

/* Fuentes del Sistema */
.font-primary {
  font-family: "Inter", system-ui, -apple-system, sans-serif;
}

.font-display {
  font-family: "Poppins", system-ui, -apple-system, sans-serif;
}
```

## Componentes Livewire por Desarrollar

### Componentes Públicos
- `public.live-matches` - Muestra partidos en vivo en la página principal
- `public.recent-results` - Últimos resultados de partidos
- `public.league-stats` - Estadísticas generales de la liga

### Componentes Administrativos
- `admin.stats-overview` - Overview estadístico para administradores
- `admin.live-matches-management` - Gestión de partidos en vivo
- `admin.recent-activity` - Actividad reciente del sistema
- `admin.tournament-management` - Gestión de torneos
- `admin.quick-actions` - Acciones rápidas administrativas
- `admin.system-status` - Estado del sistema
- `admin.pending-approvals` - Aprobaciones pendientes

### Componentes de Jugadora
- `player.profile-header` - Header del perfil de jugadora
- `player.upcoming-matches` - Próximos partidos de la jugadora
- `player.performance-stats` - Estadísticas de rendimiento
- `player.recent-activity` - Actividad reciente de la jugadora
- `player.medical-status` - Estado médico actual
- `player.team-info` - Información del equipo

### Componentes de Entrenador
- `coach.team-overview` - Overview del equipo
- `coach.team-schedule` - Calendario del equipo
- `coach.player-management` - Gestión de jugadoras
- `coach.team-statistics` - Estadísticas del equipo
- `coach.team-health-status` - Estado de salud del equipo
- `coach.notifications` - Notificaciones del entrenador

### Componentes de Árbitro
- `referee.stats-overview` - Estadísticas del árbitro
- `referee.assigned-matches` - Partidos asignados
- `referee.match-reports` - Reportes de partidos
- `referee.performance-history` - Historial de rendimiento
- `referee.certification-status` - Estado de certificación
- `referee.recent-assignments` - Asignaciones recientes

### Componentes Médicos
- `medical.stats-overview` - Estadísticas médicas
- `medical.active-injuries` - Lesiones activas
- `medical.medical-reports` - Reportes médicos
- `medical.player-health-monitoring` - Monitoreo de salud de jugadoras
- `medical.emergency-contacts` - Contactos de emergencia
- `medical.medical-alerts` - Alertas médicas

### Componentes Compartidos
- `shared.notifications-dropdown` - Dropdown de notificaciones
- `shared.search-component` - Componente de búsqueda global
- `shared.match-card` - Tarjeta de partido reutilizable
- `shared.player-card` - Tarjeta de jugadora reutilizable

## Funcionalidades en Tiempo Real

### Con Livewire + Alpine.js
- **Actualizaciones de marcador en vivo** durante partidos
- **Notificaciones en tiempo real** de eventos importantes
- **Estado de salud de jugadoras** actualizado instantáneamente
- **Cambios de alineación** reflejados inmediatamente
- **Alertas médicas** con prioridad alta
- **Verificación QR** con validación instantánea

### Alpine.js Stores Globales
```javascript
// En app.blade.php
Alpine.store('app', {
  darkMode: false,
  sidebarOpen: false,
  toggleDarkMode() { /* lógica */ },
  toggleSidebar() { /* lógica */ }
});

Alpine.store('notifications', {
  items: [],
  add(notification) { /* lógica */ },
  remove(id) { /* lógica */ }
});
```

## Responsive Design

### Breakpoints
- **Mobile**: 320px - 768px
- **Tablet**: 768px - 1024px  
- **Desktop**: 1024px+

### Estrategia Mobile-First
Todos los componentes están diseñados primero para móvil y luego se expanden para pantallas más grandes usando clases de Tailwind CSS.

## Estados de Desarrollo

### ✅ Completado
- Estructura de layouts base
- Sistema de design tokens
- Navegación responsive
- Configuración de Alpine.js stores

### 🔄 En Desarrollo
- Componentes Livewire individuales
- Implementación de tiempo real
- Integración con base de datos

### 📋 Por Desarrollar
- Todos los componentes Livewire listados
- Sistema de notificaciones push
- Optimizaciones de rendimiento
- Tests unitarios y de integración

# 🏐 VolleyPass - Vistas Principales y Layouts

## 1. Vista Principal (welcome.blade.php)

### Estructura y Contenido

```php
<x-public-layout>
    @section('title', 'Inicio - Liga de Voleibol de Sucre')
    @section('description', 'Plataforma oficial de la Liga de Voleibol de Sucre.')

    <div x-data="welcomeData()" x-init="init()">
        <!-- Hero Section con gradiente de colores colombianos -->
        <section class="relative bg-gradient-to-br from-vp-primary-500 via-vp-secondary-500 to-vp-accent-500">
            <!-- Patrón de voleibol en SVG -->
            <div class="absolute inset-0 opacity-10">
                <!-- SVG con líneas de cancha de voleibol -->
            </div>
            
            <!-- Contenido principal del hero -->
            <div class="relative max-w-7xl mx-auto px-4 py-24">
                <h1 class="font-display font-bold text-4xl lg:text-6xl text-white">
                    Liga de Voleibol
                    <span class="block text-vp-primary-200">de Sucre</span>
                </h1>
                <p class="text-xl text-white/90 mb-8">
                    La plataforma digital oficial para seguir todos los partidos, 
                    estadísticas y noticias del voleibol sucreño
                </p>
                <!-- CTAs principales -->
                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="{{ route('register') }}" class="btn-primary">Únete Ahora</a>
                    <a href="#live-matches" class="btn-outline">Ver Partidos en Vivo</a>
                </div>
            </div>
        </section>

        <!-- Sección de Partidos en Vivo -->
        <section id="live-matches" class="py-16 bg-white">
            <div class="max-w-7xl mx-auto px-4">
                <h2 class="font-display font-bold text-3xl text-center mb-12">
                    Partidos en Vivo
                </h2>
                @livewire('public.live-matches')
            </div>
        </section>

        <!-- Sección de Últimos Resultados -->
        <section class="py-16 bg-gray-50">
            <div class="max-w-7xl mx-auto px-4">
                <h2 class="font-display font-bold text-3xl text-center mb-12">
                    Últimos Resultados
                </h2>
                @livewire('public.recent-results')
            </div>
        </section>

        <!-- Sección de Estadísticas -->
        <section class="py-16 bg-white">
            <div class="max-w-7xl mx-auto px-4">
                <h2 class="font-display font-bold text-3xl text-center mb-12">
                    Estadísticas de la Liga
                </h2>
                @livewire('public.league-stats')
            </div>
        </section>

        <!-- CTA Final -->
        <section class="py-16 bg-gradient-to-r from-vp-secondary-600 to-vp-primary-600">
            <div class="max-w-7xl mx-auto px-4 text-center">
                <h2 class="font-display font-bold text-3xl text-white mb-4">
                    ¿Listo para formar parte?
                </h2>
                <p class="text-xl text-white/90 mb-8">
                    Únete a la comunidad de voleibol más grande de Sucre
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('register') }}" class="btn-secondary">Registrarse Gratis</a>
                    <a href="{{ route('login') }}" class="btn-outline">Iniciar Sesión</a>
                </div>
            </div>
        </section>
    </div>

    <script>
        function welcomeData() {
            return {
                init() {
                    // Smooth scroll para enlaces de anclaje
                    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                        anchor.addEventListener('click', function (e) {
                            e.preventDefault();
                            const target = document.querySelector(this.getAttribute('href'));
                            if (target) {
                                target.scrollIntoView({ behavior: 'smooth' });
                            }
                        });
                    });
                }
            }
        }
    </script>
</x-public-layout>
```

## 2. Layout Principal (app.blade.php)

### Estructura Completa

```php
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" x-data="appData()" :class="{ 'dark': $store.app.darkMode }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>{{ config('app.name', 'VolleyPass') }} - @yield('title', 'Plataforma Integral de Voleibol')</title>
    
    <!-- Fuentes optimizadas -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Estilos -->
    @vite(['resources/css/app.css'])
    @livewireStyles
    
    <!-- Alpine.js Stores -->
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('app', {
                darkMode: localStorage.getItem('darkMode') === 'true',
                sidebarOpen: false,
                
                toggleDarkMode() {
                    this.darkMode = !this.darkMode;
                    localStorage.setItem('darkMode', this.darkMode);
                },
                
                toggleSidebar() {
                    this.sidebarOpen = !this.sidebarOpen;
                }
            });
            
            Alpine.store('notifications', {
                items: [],
                
                add(notification) {
                    const id = Date.now();
                    this.items.push({ id, ...notification });
                    setTimeout(() => this.remove(id), 5000);
                },
                
                remove(id) {
                    this.items = this.items.filter(item => item.id !== id);
                }
            });
        });
    </script>
</head>
<body class="font-inter antialiased bg-gray-50 dark:bg-gray-900 transition-colors duration-200">
    <!-- Header Component -->
    @include('components.navigation.header')
    
    <!-- Main Content -->
    <main class="min-h-screen">
        {{ $slot }}
    </main>
    
    <!-- Footer Component -->
    @include('components.navigation.footer')
    
    <!-- Sistema de Notificaciones en Tiempo Real -->
    <div x-data class="fixed top-4 right-4 z-50 space-y-2" x-show="$store.notifications.items.length > 0">
        <template x-for="notification in $store.notifications.items" :key="notification.id">
            <div x-show="true" 
                 x-transition:enter="transform ease-out duration-300"
                 x-transition:enter-start="translate-y-2 opacity-0"
                 x-transition:enter-end="translate-y-0 opacity-100"
                 class="max-w-sm w-full bg-white dark:bg-gray-800 shadow-lg rounded-lg ring-1 ring-black ring-opacity-5">
                <div class="p-4">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <!-- Iconos dinámicos según tipo de notificación -->
                            <svg x-show="notification.type === 'success'" class="h-6 w-6 text-green-400">
                                <!-- Icono de éxito -->
                            </svg>
                            <svg x-show="notification.type === 'error'" class="h-6 w-6 text-red-400">
                                <!-- Icono de error -->
                            </svg>
                        </div>
                        <div class="ml-3 w-0 flex-1">
                            <p class="text-sm font-medium text-gray-900 dark:text-white" x-text="notification.title"></p>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400" x-text="notification.message"></p>
                        </div>
                        <button @click="$store.notifications.remove(notification.id)" class="ml-4 flex-shrink-0">
                            <!-- Icono de cerrar -->
                        </button>
                    </div>
                </div>
            </div>
        </template>
    </div>
    
    <!-- Scripts -->
    @livewireScripts
    @vite(['resources/js/app.js'])
    
    <script>
        function appData() {
            return {
                init() {
                    // Inicializar modo oscuro desde localStorage
                    if (localStorage.getItem('darkMode') === 'true') {
                        document.documentElement.classList.add('dark');
                    }
                }
            }
        }
    </script>
</body>
</html>
```

## 3. Layout Público (public.blade.php)

### Para páginas sin autenticación

```php
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" x-data="publicData()">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>{{ config('app.name', 'VolleyPass') }} - @yield('title', 'Liga de Voleibol de Sucre')</title>
    <meta name="description" content="@yield('description', 'Plataforma integral para la gestión de la Liga de Voleibol de Sucre, Colombia.')">
    
    <!-- Fuentes y estilos -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css'])
    @livewireStyles
</head>
<body class="font-inter antialiased bg-gray-50">
    <!-- Public Header -->
    @include('components.navigation.public-header')
    
    <!-- Main Content -->
    <main>
        {{ $slot }}
    </main>
    
    <!-- Public Footer -->
    @include('components.navigation.public-footer')
    
    <!-- Scripts -->
    @livewireScripts
    @vite(['resources/js/app.js'])
    
    <script>
        function publicData() {
            return {
                mobileMenuOpen: false,
                
                toggleMobileMenu() {
                    this.mobileMenuOpen = !this.mobileMenuOpen;
                }
            }
        }
    </script>
</body>
</html>
```

## 4. Layout de Autenticación (auth.blade.php)

### Para páginas de login/registro

```php
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>{{ config('app.name', 'VolleyPass') }} - @yield('title', 'Autenticación')</title>
    
    <!-- Fuentes -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css'])
    @livewireStyles
</head>
<body class="font-inter antialiased bg-gradient-to-br from-vp-primary-50 to-vp-secondary-50 min-h-screen">
    <!-- Patrón de fondo de voleibol -->
    <div class="absolute inset-0 opacity-5">
        <div class="w-full h-full" style="background-image: url('data:image/svg+xml,<svg xmlns=&quot;http://www.w3.org/2000/svg&quot; width=&quot;60&quot; height=&quot;60&quot; viewBox=&quot;0 0 60 60&quot;><circle cx=&quot;30&quot; cy=&quot;30&quot; r=&quot;25&quot; fill=&quot;none&quot; stroke=&quot;%23000&quot; stroke-width=&quot;1&quot;/><path d=&quot;M5 30 L55 30 M30 5 L30 55 M15 15 L45 45 M45 15 L15 45&quot; stroke=&quot;%23000&quot; stroke-width=&quot;0.5&quot;/></svg>'); background-size: 60px 60px;"></div>
    </div>
    
    <div class="relative min-h-screen flex flex-col justify-center py-12 sm:px-6 lg:px-8">
        <!-- Logo -->
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <div class="flex justify-center">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 bg-vp-primary-500 rounded-xl flex items-center justify-center">
                        <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <!-- Icono de voleibol -->
                        </svg>
                    </div>
                    <div>
                        <h1 class="font-display font-bold text-2xl text-gray-900">VolleyPass</h1>
                        <p class="text-sm text-gray-600">Liga de Voleibol de Sucre</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Contenido del formulario -->
        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
            <div class="bg-white py-8 px-4 shadow-xl sm:rounded-xl sm:px-10">
                {{ $slot }}
            </div>
        </div>
        
        <!-- Footer -->
        <div class="mt-8 text-center">
            <p class="text-sm text-gray-600">
                © {{ date('Y') }} VolleyPass Software. Todos los derechos reservados.
            </p>
        </div>
    </div>
    
    @livewireScripts
    @vite(['resources/js/app.js'])
</body>
</html>
```

## CSS Personalizado Requerido

### Ubicación: `resources/css/app.css`

```css
@import "tailwindcss/base";
@import "tailwindcss/components";
@import "tailwindcss/utilities";

/* VolleyPass Design Tokens */
:root {
  /* Colores Primarios - Bandera Colombiana */
  --vp-primary-50: #fef3c7;
  --vp-primary-100: #fde68a;
  --vp-primary-500: #f59e0b; /* Amarillo Colombia */
  --vp-primary-600: #d97706;
  --vp-primary-900: #78350f;

  /* Colores Secundarios - Azul Voleibol */
  --vp-secondary-50: #eff6ff;
  --vp-secondary-500: #3b82f6;
  --vp-secondary-600: #2563eb;
  --vp-secondary-900: #1e3a8a;

  /* Colores de Acento - Rojo Pasión */
  --vp-accent-500: #ef4444;
  --vp-accent-600: #dc2626;

  /* Estados */
  --vp-success: #10b981;
  --vp-warning: #f59e0b;
  --vp-error: #ef4444;
  --vp-live: #ff0000;
}

/* Componentes Base */
@layer components {
  .btn-primary {
    @apply bg-vp-primary-500 text-white px-8 py-4 rounded-xl font-semibold text-lg hover:bg-vp-primary-600 transition-colors shadow-lg;
  }

  .btn-secondary {
    @apply bg-white text-vp-secondary-600 px-8 py-4 rounded-xl font-semibold text-lg hover:bg-gray-50 transition-colors shadow-lg;
  }

  .btn-outline {
    @apply border-2 border-white text-white px-8 py-4 rounded-xl font-semibold text-lg hover:bg-white/10 transition-colors;
  }

  .live-indicator {
    @apply inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800;
  }

  .live-indicator::before {
    content: "";
    @apply w-2 h-2 bg-red-500 rounded-full mr-2 animate-pulse;
  }
}

/* Animaciones personalizadas */
@keyframes pulse-live {
  0%, 100% { opacity: 1; }
  50% { opacity: 0.5; }
}

.animate-pulse-live {
  animation: pulse-live 1.5s ease-in-out infinite;
}
```

## JavaScript Alpine.js Stores

### Configuración Global

```javascript
// En app.blade.php dentro del <script>
document.addEventListener('alpine:init', () => {
    // Store principal de la aplicación
    Alpine.store('app', {
        darkMode: localStorage.getItem('darkMode') === 'true',
        sidebarOpen: false,
        
        toggleDarkMode() {
            this.darkMode = !this.darkMode;
            localStorage.setItem('darkMode', this.darkMode);
        },
        
        toggleSidebar() {
            this.sidebarOpen = !this.sidebarOpen;
        }
    });
    
    // Store de notificaciones en tiempo real
    Alpine.store('notifications', {
        items: [],
        
        add(notification) {
            const id = Date.now();
            this.items.push({ id, ...notification });
            setTimeout(() => this.remove(id), 5000);
        },
        
        remove(id) {
            this.items = this.items.filter(item => item.id !== id);
        }
    });
    
    // Store para partidos en vivo
    Alpine.store('liveMatches', {
        matches: [],
        
        updateMatch(matchId, data) {
            const index = this.matches.findIndex(m => m.id === matchId);
            if (index !== -1) {
                this.matches[index] = { ...this.matches[index], ...data };
            }
        }
    });
});
```
# 🏐 VolleyPass - Dashboards de Usuarios Finales

## 1. Dashboard de Jugadora

### Ubicación: `resources/views/pages/player/dashboard.blade.php`

```php
<x-app-layout>
    @section('title', 'Mi Dashboard - Jugadora')

    <div class="py-6" x-data="playerDashboard()" x-init="init()">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header del perfil de jugadora con gradiente -->
            <div class="bg-gradient-to-r from-vp-primary-500 to-vp-secondary-500 rounded-xl shadow-lg overflow-hidden mb-8">
                @livewire('player.profile-header')
            </div>

            <!-- Grid principal del dashboard -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Contenido principal (2/3 del ancho) -->
                <div class="lg:col-span-2 space-y-8">
                    <!-- Próximos Partidos -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
                        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                            <div class="flex items-center justify-between">
                                <h3 class="font-semibold text-lg text-gray-900 dark:text-white">
                                    Próximos Partidos
                                </h3>
                                <span class="bg-vp-primary-100 text-vp-primary-800 px-3 py-1 rounded-full text-sm font-medium">
                                    {{ auth()->user()->upcomingMatches()->count() }} partidos
                                </span>
                            </div>
                        </div>
                        <div class="p-6">
                            @livewire('player.upcoming-matches')
                        </div>
                    </div>

                    <!-- Estadísticas de Rendimiento -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
                        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="font-semibold text-lg text-gray-900 dark:text-white">
                                Estadísticas de Rendimiento
                            </h3>
                        </div>
                        <div class="p-6">
                            @livewire('player.performance-stats')
                        </div>
                    </div>

                    <!-- Actividad Reciente -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
                        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="font-semibold text-lg text-gray-900 dark:text-white">
                                Actividad Reciente
                            </h3>
                        </div>
                        <div class="p-6">
                            @livewire('player.recent-activity')
                        </div>
                    </div>
                </div>

                <!-- Sidebar (1/3 del ancho) -->
                <div class="space-y-8">
                    <!-- Acciones Rápidas -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
                        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="font-semibold text-lg text-gray-900 dark:text-white">
                                Acciones Rápidas
                            </h3>
                        </div>
                        <div class="p-6 space-y-3">
                            <button @click="updateAvailability()" 
                                    class="w-full bg-vp-primary-500 text-white px-4 py-3 rounded-lg hover:bg-vp-primary-600 transition-colors text-left">
                                <svg class="w-5 h-5 inline mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                Actualizar Disponibilidad
                            </button>
                            <button @click="viewTeammates()" 
                                    class="w-full bg-vp-secondary-500 text-white px-4 py-3 rounded-lg hover:bg-vp-secondary-600 transition-colors text-left">
                                <svg class="w-5 h-5 inline mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                Ver Compañeras de Equipo
                            </button>
                            <button @click="reportInjury()" 
                                    class="w-full bg-red-500 text-white px-4 py-3 rounded-lg hover:bg-red-600 transition-colors text-left">
                                <svg class="w-5 h-5 inline mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                </svg>
                                Reportar Lesión
                            </button>
                        </div>
                    </div>

                    <!-- Estado Médico -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
                        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="font-semibold text-lg text-gray-900 dark:text-white">
                                Estado Médico
                            </h3>
                        </div>
                        <div class="p-6">
                            @livewire('player.medical-status')
                        </div>
                    </div>

                    <!-- Información del Equipo -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
                        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="font-semibold text-lg text-gray-900 dark:text-white">
                                Mi Equipo
                            </h3>
                        </div>
                        <div class="p-6">
                            @livewire('player.team-info')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function playerDashboard() {
            return {
                init() {
                    console.log('Player dashboard initialized');
                },
                
                updateAvailability() {
                    Livewire.dispatch('open-availability-modal');
                },
                
                viewTeammates() {
                    window.location.href = '/player/teammates';
                },
                
                reportInjury() {
                    Livewire.dispatch('open-injury-modal');
                }
            }
        }
    </script>
</x-app-layout>
```

## 2. Dashboard de Entrenador

### Ubicación: `resources/views/pages/coach/dashboard.blade.php`

```php
<x-app-layout>
    @section('title', 'Dashboard - Entrenador')

    <div class="py-6" x-data="coachDashboard()" x-init="init()">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header con configuración del equipo -->
            <div class="mb-8">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h1 class="font-display font-bold text-3xl text-gray-900 dark:text-white">
                            Panel de Entrenador
                        </h1>
                        <p class="mt-2 text-gray-600 dark:text-gray-400">
                            Gestiona tu equipo y jugadoras
                        </p>
                    </div>
                    <div class="mt-4 sm:mt-0 flex space-x-3">
                        <button @click="openTeamSettings()" 
                                class="bg-vp-secondary-500 text-white px-4 py-2 rounded-lg hover:bg-vp-secondary-600 transition-colors">
                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            Configurar Equipo
                        </button>
                    </div>
                </div>
            </div>

            <!-- Overview del Equipo con gradiente -->
            <div class="bg-gradient-to-r from-vp-secondary-500 to-vp-primary-500 rounded-xl shadow-lg overflow-hidden mb-8">
                @livewire('coach.team-overview')
            </div>

            <!-- Grid principal del dashboard -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Contenido principal -->
                <div class="lg:col-span-2 space-y-8">
                    <!-- Calendario del Equipo -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
                        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="font-semibold text-lg text-gray-900 dark:text-white">
                                Calendario del Equipo
                            </h3>
                        </div>
                        <div class="p-6">
                            @livewire('coach.team-schedule')
                        </div>
                    </div>

                    <!-- Gestión de Jugadoras -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
                        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                            <div class="flex items-center justify-between">
                                <h3 class="font-semibold text-lg text-gray-900 dark:text-white">
                                    Gestión de Jugadoras
                                </h3>
                                <button @click="addPlayer()" 
                                        class="bg-vp-primary-500 text-white px-4 py-2 rounded-lg hover:bg-vp-primary-600 transition-colors text-sm">
                                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                    Agregar Jugadora
                                </button>
                            </div>
                        </div>
                        <div class="p-6">
                            @livewire('coach.player-management')
                        </div>
                    </div>

                    <!-- Estadísticas del Equipo -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
                        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="font-semibold text-lg text-gray-900 dark:text-white">
                                Estadísticas del Equipo
                            </h3>
                        </div>
                        <div class="p-6">
                            @livewire('coach.team-statistics')
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-8">
                    <!-- Acciones Rápidas -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
                        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="font-semibold text-lg text-gray-900 dark:text-white">
                                Acciones Rápidas
                            </h3>
                        </div>
                        <div class="p-6 space-y-3">
                            <button @click="scheduleTraining()" 
                                    class="w-full bg-vp-primary-500 text-white px-4 py-3 rounded-lg hover:bg-vp-primary-600 transition-colors text-left">
                                <svg class="w-5 h-5 inline mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                Programar Entrenamiento
                            </button>
                            <button @click="createLineup()" 
                                    class="w-full bg-vp-secondary-500 text-white px-4 py-3 rounded-lg hover:bg-vp-secondary-600 transition-colors text-left">
                                <svg class="w-5 h-5 inline mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                                Crear Alineación
                            </button>
                            <button @click="sendMessage()" 
                                    class="w-full bg-green-500 text-white px-4 py-3 rounded-lg hover:bg-green-600 transition-colors text-left">
                                <svg class="w-5 h-5 inline mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                </svg>
                                Mensaje al Equipo
                            </button>
                        </div>
                    </div>

                    <!-- Estado de Salud del Equipo -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
                        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="font-semibold text-lg text-gray-900 dark:text-white">
                                Estado de Salud del Equipo
                            </h3>
                        </div>
                        <div class="p-6">
                            @livewire('coach.team-health-status')
                        </div>
                    </div>

                    <!-- Notificaciones -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
                        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="font-semibold text-lg text-gray-900 dark:text-white">
                                Notificaciones
                            </h3>
                        </div>
                        <div class="p-6">
                            @livewire('coach.notifications')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function coachDashboard() {
            return {
                init() {
                    console.log('Coach dashboard initialized');
                },
                
                openTeamSettings() {
                    Livewire.dispatch('open-team-settings');
                },
                
                addPlayer() {
                    Livewire.dispatch('open-add-player-modal');
                },
                
                scheduleTraining() {
                    Livewire.dispatch('open-training-modal');
                },
                
                createLineup() {
                    window.location.href = '/coach/lineup';
                },
                
                sendMessage() {
                    Livewire.dispatch('open-message-modal');
                }
            }
        }
    </script>
</x-app-layout>
```

## 3. Dashboard de Árbitro

### Ubicación: `resources/views/pages/referee/dashboard.blade.php`

```php
<x-app-layout>
    @section('title', 'Dashboard - Árbitro')

    <div class="py-6" x-data="refereeDashboard()" x-init="init()">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h1 class="font-display font-bold text-3xl text-gray-900 dark:text-white">
                            Panel de Árbitro
                        </h1>
                        <p class="mt-2 text-gray-600 dark:text-gray-400">
                            Gestiona tus asignaciones y reportes de partidos
                        </p>
                    </div>
                    <div class="mt-4 sm:mt-0 flex space-x-3">
                        <button @click="updateAvailability()" 
                                class="bg-vp-primary-500 text-white px-4 py-2 rounded-lg hover:bg-vp-primary-600 transition-colors">
                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            Actualizar Disponibilidad
                        </button>
                    </div>
                </div>
            </div>

            <!-- Estadísticas del Árbitro -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                @livewire('referee.stats-overview')
            </div>

            <!-- Grid principal del dashboard -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Contenido principal -->
                <div class="lg:col-span-2 space-y-8">
                    <!-- Partidos Asignados -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
                        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                            <div class="flex items-center justify-between">
                                <h3 class="font-semibold text-lg text-gray-900 dark:text-white">
                                    Partidos Asignados
                                </h3>
                                <span class="bg-vp-primary-100 text-vp-primary-800 px-3 py-1 rounded-full text-sm font-medium">
                                    {{ auth()->user()->assignedMatches()->upcoming()->count() }} próximos
                                </span>
                            </div>
                        </div>
                        <div class="p-6">
                            @livewire('referee.assigned-matches')
                        </div>
                    </div>

                    <!-- Reportes de Partidos -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
                        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                            <div class="flex items-center justify-between">
                                <h3 class="font-semibold text-lg text-gray-900 dark:text-white">
                                    Reportes de Partidos
                                </h3>
                                <button @click="createReport()" 
                                        class="bg-vp-secondary-500 text-white px-4 py-2 rounded-lg hover:bg-vp-secondary-600 transition-colors text-sm">
                                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                    Nuevo Reporte
                                </button>
                            </div>
                        </div>
                        <div class="p-6">
                            @livewire('referee.match-reports')
                        </div>
                    </div>

                    <!-- Historial de Rendimiento -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
                        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="font-semibold text-lg text-gray-900 dark:text-white">
                                Historial de Rendimiento
                            </h3>
                        </div>
                        <div class="p-6">
                            @livewire('referee.performance-history')
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-8">
                    <!-- Acciones Rápidas -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
                        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="font-semibold text-lg text-gray-900 dark:text-white">
                                Acciones Rápidas
                            </h3>
                        </div>
                        <div class="p-6 space-y-3">
                            <button @click="startMatch()" 
                                    class="w-full bg-green-500 text-white px-4 py-3 rounded-lg hover:bg-green-600 transition-colors text-left">
                                <svg class="w-5 h-5 inline mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h8m-5-9V3m0 0V1m0 2h4M7 21h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                Iniciar Partido
                            </button>
                            <button @click="submitScore()" 
                                    class="w-full bg-vp-primary-500 text-white px-4 py-3 rounded-lg hover:bg-vp-primary-600 transition-colors text-left">
                                <svg class="w-5 h-5 inline mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                </svg>
                                Registrar Marcador
                            </button>
                            <button @click="reportIncident()" 
                                    class="w-full bg-red-500 text-white px-4 py-3 rounded-lg hover:bg-red-600 transition-colors text-left">
                                <svg class="w-5 h-5 inline mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                </svg>
                                Reportar Incidente
                            </button>
                        </div>
                    </div>

                    <!-- Estado de Certificación -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
                        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="font-semibold text-lg text-gray-900 dark:text-white">
                                Estado de Certificación
                            </h3>
                        </div>
                        <div class="p-6">
                            @livewire('referee.certification-status')
                        </div>
                    </div>

                    <!-- Asignaciones Recientes -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
                        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="font-semibold text-lg text-gray-900 dark:text-white">
                                Asignaciones Recientes
                            </h3>
                        </div>
                        <div class="p-6">
                            @livewire('referee.recent-assignments')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function refereeDashboard() {
            return {
                init() {
                    console.log('Referee dashboard initialized');
                },
                
                updateAvailability() {
                    Livewire.dispatch('open-availability-modal');
                },
                
                createReport() {
                    Livewire.dispatch('open-report-modal');
                },
                
                startMatch() {
                    Livewire.dispatch('open-match-control');
                },
                
                submitScore() {
                    Livewire.dispatch('open-score-modal');
                },
                
                reportIncident() {
                    Livewire.dispatch('open-incident-modal');
                }
            }
        }
    </script>
</x-app-layout>
```

## 4. Dashboard Médico Deportivo

### Ubicación: `resources/views/pages/medical/dashboard.blade.php`

```php
<x-app-layout>
    @section('title', 'Dashboard - Médico Deportivo')

    <div class="py-6" x-data="medicalDashboard()" x-init="init()">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h1 class="font-display font-bold text-3xl text-gray-900 dark:text-white">
                            Panel Médico Deportivo
                        </h1>
                        <p class="mt-2 text-gray-600 dark:text-gray-400">
                            Monitorea la salud y bienestar de las jugadoras
                        </p>
                    </div>
                    <div class="mt-4 sm:mt-0 flex space-x-3">
                        <button @click="emergencyProtocol()" 
                                class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition-colors">
                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
                            </svg>
                            Protocolo de Emergencia
                        </button>
                    </div>
                </div>
            </div>

            <!-- Estadísticas Médicas -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                @livewire('medical.stats-overview')
            </div>

            <!-- Grid principal del dashboard -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Contenido principal -->
                <div class="lg:col-span-2 space-y-8">
                    <!-- Lesiones Activas -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
                        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                            <div class="flex items-center justify-between">
                                <h3 class="font-semibold text-lg text-gray-900 dark:text-white">
                                    Lesiones Activas
                                </h3>
                                <span class="bg-red-100 text-red-800 px-3 py-1 rounded-full text-sm font-medium">
                                    {{ auth()->user()->activeInjuries()->count() }} casos
                                </span>
                            </div>
                        </div>
                        <div class="p-6">
                            @livewire('medical.active-injuries')
                        </div>
                    </div>

                    <!-- Reportes Médicos -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
                        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                            <div class="flex items-center justify-between">
                                <h3 class="font-semibold text-lg text-gray-900 dark:text-white">
                                    Reportes Médicos
                                </h3>
                                <button @click="createReport()" 
                                        class="bg-vp-primary-500 text-white px-4 py-2 rounded-lg hover:bg-vp-primary-600 transition-colors text-sm">
                                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                    Nuevo Reporte
                                </button>
                            </div>
                        </div>
                        <div class="p-6">
                            @livewire('medical.medical-reports')
                        </div>
                    </div>

                    <!-- Monitoreo de Salud de Jugadoras -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
                        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="font-semibold text-lg text-gray-900 dark:text-white">
                                Monitoreo de Salud de Jugadoras
                            </h3>
                        </div>
                        <div class="p-6">
                            @livewire('medical.player-health-monitoring')
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-8">
                    <!-- Acciones Rápidas -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
                        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="font-semibold text-lg text-gray-900 dark:text-white">
                                Acciones Rápidas
                            </h3>
                        </div>
                        <div class="p-6 space-y-3">
                            <button @click="recordInjury()" 
                                    class="w-full bg-red-500 text-white px-4 py-3 rounded-lg hover:bg-red-600 transition-colors text-left">
                                <svg class="w-5 h-5 inline mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                </svg>
                                Registrar Lesión
                            </button>
                            <button @click="clearPlayer()" 
                                    class="w-full bg-green-500 text-white px-4 py-3 rounded-lg hover:bg-green-600 transition-colors text-left">
                                <svg class="w-5 h-5 inline mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Dar Alta Médica
                            </button>
                            <button @click="scheduleCheckup()" 
                                    class="w-full bg-vp-secondary-500 text-white px-4 py-3 rounded-lg hover:bg-vp-secondary-600 transition-colors text-left">
                                <svg class="w-5 h-5 inline mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                Programar Revisión
                            </button>
                        </div>
                    </div>

                    <!-- Contactos de Emergencia -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
                        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="font-semibold text-lg text-gray-900 dark:text-white">
                                Contactos de Emergencia
                            </h3>
                        </div>
                        <div class="p-6">
                            @livewire('medical.emergency-contacts')
                        </div>
                    </div>

                    <!-- Alertas Médicas -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
                        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="font-semibold text-lg text-gray-900 dark:text-white">
                                Alertas Médicas
                            </h3>
                        </div>
                        <div class="p-6">
                            @livewire('medical.medical-alerts')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function medicalDashboard() {
            return {
                init() {
                    console.log('Medical dashboard initialized');
                },
                
                emergencyProtocol() {
                    Livewire.dispatch('activate-emergency-protocol');
                },
                
                createReport() {
                    Livewire.dispatch('open-medical-report-modal');
                },
                
                recordInjury() {
                    Livewire.dispatch('open-injury-record-modal');
                },
                
                clearPlayer() {
                    Livewire.dispatch('open-medical-clearance-modal');
                },
                
                scheduleCheckup() {
                    Livewire.dispatch('open-checkup-schedule-modal');
                }
            }
        }
    </script>
</x-app-layout>
```

## Características Comunes de los Dashboards

### 1. Estructura Visual Consistente
- **Header**: Título del rol + descripción + botones de acción principales
- **Stats Overview**: Tarjetas con métricas clave (4 columnas en desktop)
- **Grid Layout**: 2/3 contenido principal + 1/3 sidebar
- **Acciones Rápidas**: Sidebar con botones de funciones frecuentes

### 2. Componentes Livewire en Tiempo Real
- **Actualizaciones automáticas**: Sin refresh manual
- **Notificaciones push**: Para eventos críticos
- **Estados dinámicos**: Cambian según la actividad

### 3. Responsive Design
- **Mobile-first**: Funciona perfecto en móviles
- **Tablet optimizado**: Layout adaptativo
- **Desktop completo**: Máximo aprovechamiento del espacio

### 4. Alpine.js para Interactividad
- **Stores globales**: Estado compartido entre componentes
- **Eventos personalizados**: Comunicación entre componentes
- **Transiciones suaves**: Mejor experiencia de usuario

### 5. Accesibilidad
- **Navegación por teclado**: Totalmente accesible
- **Lectores de pantalla**: Semántica correcta
- **Contraste adecuado**: Colores legibles

## Estados de Desarrollo por Dashboard

### ✅ Estructuras Completadas
- Layouts base de todos los dashboards
- Sistemas de navegación responsive
- Integración con Alpine.js stores

### 🔄 En Desarrollo
- Componentes Livewire individuales
- Funcionalidades en tiempo real
- Modales y formularios

### 📋 Por Desarrollar
- Todos los componentes `@livewire()` referenciados
- Sistema de notificaciones push
- Integración completa con WebSockets
- Tests de cada dashboard

# 🏐 VolleyPass - Componentes de Navegación

## 1. Header Principal (header.blade.php)

### Ubicación: `resources/views/components/navigation/header.blade.php`

```php
<header class="bg-white dark:bg-gray-900 shadow-sm sticky top-0 z-50 transition-colors duration-200"
        x-data="headerData()"
        x-init="init()">
    
    <!-- Navegación Desktop -->
    <nav class="hidden lg:block max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <!-- Logo -->
            <div class="flex items-center">
                <a href="{{ route('home') }}" class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-vp-primary-500 to-vp-secondary-500 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10" fill="none" stroke="currentColor" stroke-width="2"/>
                            <path d="M2 12h20M12 2v20M7 7l10 10M17 7L7 17" stroke="currentColor" stroke-width="1"/>
                        </svg>
                    </div>
                    <div>
                        <span class="font-display font-bold text-xl text-gray-900 dark:text-white">
                            VolleyPass
                        </span>
                        <div class="text-xs text-gray-500 dark:text-gray-400 -mt-1">
                            Liga de Sucre
                        </div>
                    </div>
                </a>
            </div>

            <!-- Navegación Principal -->
            <div class="hidden lg:flex space-x-8">
                @auth
                    @include('components.navigation.auth-menu')
                @else
                    @include('components.navigation.guest-menu')
                @endauth
            </div>

            <!-- Acciones del lado derecho -->
            <div class="flex items-center space-x-4">
                <!-- Toggle Modo Oscuro -->
                <button @click="$store.app.toggleDarkMode()" 
                        class="p-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors">
                    <svg x-show="!$store.app.darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                    </svg>
                    <svg x-show="$store.app.darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </button>

                @auth
                    <!-- Notificaciones -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" 
                                class="p-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors relative">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM10.07 2.82l-.03.03a1.51 1.51 0 000 2.13l1.06 1.06 8.49 8.48a1.51 1.51 0 002.12 0l.03-.03a1.51 1.51 0 000-2.12L12.25 2.88a1.51 1.51 0 00-2.13 0l-.05.05z" />
                            </svg>
                            <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                                3
                            </span>
                        </button>
                        
                        <!-- Dropdown de Notificaciones -->
                        <div x-show="open" 
                             @click.away="open = false"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             class="absolute right-0 mt-2 w-80 bg-white dark:bg-gray-800 rounded-lg shadow-lg ring-1 ring-black ring-opacity-5">
                            @livewire('shared.notifications-dropdown')
                        </div>
                    </div>

                    <!-- Menú de Usuario -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" 
                                class="flex items-center space-x-3 p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                            <img class="h-8 w-8 rounded-full object-cover" 
                                 src="{{ auth()->user()->avatar_url ?? '/placeholder.svg?height=32&width=32' }}" 
                                 alt="{{ auth()->user()->name }}">
                            <div class="hidden sm:block text-left">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ auth()->user()->name }}
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ ucfirst(auth()->user()->role) }}
                                </div>
                            </div>
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        
                        <!-- Dropdown de Usuario -->
                        <div x-show="open" 
                             @click.away="open = false"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg ring-1 ring-black ring-opacity-5">
                            @include('components.navigation.user-dropdown')
                        </div>
                    </div>
                @else
                    <!-- Acciones para Invitados -->
                    <div class="flex items-center space-x-3">
                        <a href="{{ route('login') }}" 
                           class="text-gray-700 dark:text-gray-300 hover:text-vp-primary-600 dark:hover:text-vp-primary-400 font-medium transition-colors">
                            Iniciar Sesión
                        </a>
                        <a href="{{ route('register') }}" 
                           class="bg-vp-primary-500 text-white px-4 py-2 rounded-lg hover:bg-vp-primary-600 transition-colors font-medium">
                            Registrarse
                        </a>
                    </div>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Navegación Móvil -->
    <div class="lg:hidden">
        <div class="flex items-center justify-between h-16 px-4">
            <!-- Logo Móvil -->
            <a href="{{ route('home') }}" class="flex items-center space-x-2">
                <div class="w-8 h-8 bg-gradient-to-br from-vp-primary-500 to-vp-secondary-500 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10" fill="none" stroke="currentColor" stroke-width="2"/>
                        <path d="M2 12h20M12 2v20M7 7l10 10M17 7L7 17" stroke="currentColor" stroke-width="1"/>
                    </svg>
                </div>
                <span class="font-display font-bold text-lg text-gray-900 dark:text-white">
                    VolleyPass
                </span>
            </a>

            <!-- Botón Menú Móvil -->
            <button @click="mobileMenuOpen = !mobileMenuOpen" 
                    class="p-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                <svg x-show="!mobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
                <svg x-show="mobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Menú Móvil -->
        <div x-show="mobileMenuOpen" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-1"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-1"
             class="border-t border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900">
            @include('components.navigation.mobile-menu')
        </div>
    </div>

    <script>
        function headerData() {
            return {
                mobileMenuOpen: false,
                
                init() {
                    // Cerrar menú móvil al hacer clic fuera
                    document.addEventListener('click', (e) => {
                        if (!this.$el.contains(e.target)) {
                            this.mobileMenuOpen = false;
                        }
                    });
                }
            }
        }
    </script>
</header>
```

## 2. Footer Principal (footer.blade.php)

### Ubicación: `resources/views/components/navigation/footer.blade.php`

```php
<footer class="bg-gray-900 text-white relative overflow-hidden">
    <!-- Patrón de fondo de voleibol -->
    <div class="absolute inset-0 opacity-5">
        <div class="w-full h-full" style="background-image: url('data:image/svg+xml,<svg xmlns=&quot;http://www.w3.org/2000/svg&quot; width=&quot;60&quot; height=&quot;60&quot; viewBox=&quot;0 0 60 60&quot;><circle cx=&quot;30&quot; cy=&quot;30&quot; r=&quot;25&quot; fill=&quot;none&quot; stroke=&quot;%23fff&quot; stroke-width=&quot;1&quot;/><path d=&quot;M5 30 L55 30 M30 5 L30 55 M15 15 L45 45 M45 15 L15 45&quot; stroke=&quot;%23fff&quot; stroke-width=&quot;0.5&quot;/></svg>'); background-size: 60px 60px;"></div>
    </div>
    
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <!-- Grid de contenido del footer -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12">
            <!-- Sección de Marca -->
            <div class="lg:col-span-2">
                <div class="flex items-center space-x-3 mb-6">
                    <div class="w-12 h-12 bg-gradient-to-br from-vp-primary-500 to-vp-secondary-500 rounded-xl flex items-center justify-center">
                        <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10" fill="none" stroke="currentColor" stroke-width="2"/>
                            <path d="M2 12h20M12 2v20M7 7l10 10M17 7L7 17" stroke="currentColor" stroke-width="1"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-display font-bold text-2xl">VolleyPass</h3>
                        <p class="text-gray-400">Liga de Voleibol de Sucre</p>
                    </div>
                </div>
                <p class="text-gray-300 mb-6 max-w-md">
                    La plataforma digital oficial para la gestión integral de la Liga de Voleibol de Sucre, Colombia. 
                    Conectando jugadoras, equipos y aficionados en una sola plataforma.
                </p>
                <div class="flex space-x-4">
                    <a href="#" class="text-gray-400 hover:text-white transition-colors">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                            <!-- Icono de Twitter -->
                            <path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/>
                        </svg>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-white transition-colors">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                            <!-- Icono de Facebook -->
                            <path d="M22.46 6c-.77.35-1.6.58-2.46.69.88-.53 1.56-1.37 1.88-2.38-.83.5-1.75.85-2.72 1.05C18.37 4.5 17.26 4 16 4c-2.35 0-4.27 1.92-4.27 4.29 0 .34.04.67.11.98C8.28 9.09 5.11 7.38 3 4.79c-.37.63-.58 1.37-.58 2.15 0 1.49.75 2.81 1.91 3.56-.71 0-1.37-.2-1.95-.5v.03c0 2.08 1.48 3.82 3.44 4.21a4.22 4.22 0 0 1-1.93.07 4.28 4.28 0 0 0 4 2.98 8.521 8.521 0 0 1-5.33 1.84c-.34 0-.68-.02-1.02-.06C3.44 20.29 5.7 21 8.12 21 16 21 20.33 14.46 20.33 8.79c0-.19 0-.37-.01-.56.84-.6 1.56-1.36 2.14-2.23z"/>
                        </svg>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-white transition-colors">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                            <!-- Icono de Instagram -->
                            <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                        </svg>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-white transition-colors">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                            <!-- Icono de YouTube -->
                            <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                        </svg>
                    </a>
                </div>
            </div>
            
            <!-- Enlaces Rápidos -->
            <div>
                <h4 class="font-semibold text-lg mb-6">Enlaces Rápidos</h4>
                <ul class="space-y-3">
                    <li><a href="{{ route('home') }}" class="text-gray-300 hover:text-white transition-colors">Inicio</a></li>
                    <li><a href="#" class="text-gray-300 hover:text-white transition-colors">Partidos en Vivo</a></li>
                    <li><a href="#" class="text-gray-300 hover:text-white transition-colors">Resultados</a></li>
                    <li><a href="#" class="text-gray-300 hover:text-white transition-colors">Estadísticas</a></li>
                    <li><a href="#" class="text-gray-300 hover:text-white transition-colors">Equipos</a></li>
                    <li><a href="#" class="text-gray-300 hover:text-white transition-colors">Jugadoras</a></li>
                </ul>
            </div>
            
            <!-- Información de Contacto -->
            <div>
                <h4 class="font-semibold text-lg mb-6">Contacto</h4>
                <div class="space-y-3">
                    <div class="flex items-center space-x-3">
                        <svg class="w-5 h-5 text-vp-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span class="text-gray-300">Sucre, Colombia</span>
                    </div>
                    <div class="flex items-center space-x-3">
                        <svg class="w-5 h-5 text-vp-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        <span class="text-gray-300">info@volleypass.co</span>
                    </div>
                    <div class="flex items-center space-x-3">
                        <svg class="w-5 h-5 text-vp-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                        </svg>
                        <span class="text-gray-300">+57 300 123 4567</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Barra inferior -->
        <div class="border-t border-gray-800 mt-12 pt-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                <div class="text-gray-400 text-sm">
                    © {{ date('Y') }} VolleyPass Software. Todos los derechos reservados.
                </div>
                <div class="mt-4 md:mt-0 flex space-x-6">
                    <a href="#" class="text-gray-400 hover:text-white text-sm transition-colors">Política de Privacidad</a>
                    <a href="#" class="text-gray-400 hover:text-white text-sm transition-colors">Términos de Servicio</a>
                    <a href="#" class="text-gray-400 hover:text-white text-sm transition-colors">Soporte</a>
                </div>
            </div>
        </div>
    </div>
</footer>
```

## 3. Menú de Usuario Autenticado (auth-menu.blade.php)

### Ubicación: `resources/views/components/navigation/auth-menu.blade.php`

```php
<nav class="flex space-x-8">
    <!-- Enlaces según el rol del usuario -->
    @if(auth()->user()->hasRole('player'))
        <a href="{{ route('player.dashboard') }}" 
           class="text-gray-700 dark:text-gray-300 hover:text-vp-primary-600 dark:hover:text-vp-primary-400 px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('player.*') ? 'text-vp-primary-600 dark:text-vp-primary-400' : '' }}">
            Mi Dashboard
        </a>
        <a href="{{ route('player.matches') }}" 
           class="text-gray-700 dark:text-gray-300 hover:text-vp-primary-600 dark:hover:text-vp-primary-400 px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('player.matches') ? 'text-vp-primary-600 dark:text-vp-primary-400' : '' }}">
            Mis Partidos
        </a>
        <a href="{{ route('player.team') }}" 
           class="text-gray-700 dark:text-gray-300 hover:text-vp-primary-600 dark:hover:text-vp-primary-400 px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('player.team') ? 'text-vp-primary-600 dark:text-vp-primary-400' : '' }}">
            Mi Equipo
        </a>
        <a href="{{ route('player.stats') }}" 
           class="text-gray-700 dark:text-gray-300 hover:text-vp-primary-600 dark:hover:text-vp-primary-400 px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('player.stats') ? 'text-vp-primary-600 dark:text-vp-primary-400' : '' }}">
            Estadísticas
        </a>
    @endif

    @if(auth()->user()->hasRole('coach'))
        <a href="{{ route('coach.dashboard') }}" 
           class="text-gray-700 dark:text-gray-300 hover:text-vp-primary-600 dark:hover:text-vp-primary-400 px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('coach.*') ? 'text-vp-primary-600 dark:text-vp-primary-400' : '' }}">
            Panel de Control
        </a>
        <a href="{{ route('coach.team') }}" 
           class="text-gray-700 dark:text-gray-300 hover:text-vp-primary-600 dark:hover:text-vp-primary-400 px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('coach.team') ? 'text-vp-primary-600 dark:text-vp-primary-400' : '' }}">
            Mi Equipo
        </a>
        <a href="{{ route('coach.matches') }}" 
           class="text-gray-700 dark:text-gray-300 hover:text-vp-primary-600 dark:hover:text-vp-primary-400 px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('coach.matches') ? 'text-vp-primary-600 dark:text-vp-primary-400' : '' }}">
            Calendario
        </a>
        <a href="{{ route('coach.training') }}" 
           class="text-gray-700 dark:text-gray-300 hover:text-vp-primary-600 dark:hover:text-vp-primary-400 px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('coach.training') ? 'text-vp-primary-600 dark:text-vp-primary-400' : '' }}">
            Entrenamientos
        </a>
    @endif

    @if(auth()->user()->hasRole('referee'))
        <a href="{{ route('referee.dashboard') }}" 
           class="text-gray-700 dark:text-gray-300 hover:text-vp-primary-600 dark:hover:text-vp-primary-400 px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('referee.*') ? 'text-vp-primary-600 dark:text-vp-primary-400' : '' }}">
            Panel de Árbitro
        </a>
        <a href="{{ route('referee.assignments') }}" 
           class="text-gray-700 dark:text-gray-300 hover:text-vp-primary-600 dark:hover:text-vp-primary-400 px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('referee.assignments') ? 'text-vp-primary-600 dark:text-vp-primary-400' : '' }}">
            Asignaciones
        </a>
        <a href="{{ route('referee.reports') }}" 
           class="text-gray-700 dark:text-gray-300 hover:text-vp-primary-600 dark:hover:text-vp-primary-400 px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('referee.reports') ? 'text-vp-primary-600 dark:text-vp-primary-400' : '' }}">
            Reportes
        </a>
    @endif

    @if(auth()->user()->hasRole('medical'))
        <a href="{{ route('medical.dashboard') }}" 
           class="text-gray-700 dark:text-gray-300 hover:text-vp-primary-600 dark:hover:text-vp-primary-400 px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('medical.*') ? 'text-vp-primary-600 dark:text-vp-primary-400' : '' }}">
            Panel Médico
        </a>
        <a href="{{ route('medical.players') }}" 
           class="text-gray-700 dark:text-gray-300 hover:text-vp-primary-600 dark:hover:text-vp-primary-400 px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('medical.players') ? 'text-vp-primary-600 dark:text-vp-primary-400' : '' }}">
            Jugadoras
        </a>
        <a href="{{ route('medical.reports') }}" 
           class="text-gray-700 dark:text-gray-300 hover:text-vp-primary-600 dark:hover:text-vp-primary-400 px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('medical.reports') ? 'text-vp-primary-600 dark:text-vp-primary-400' : '' }}">
            Reportes Médicos
        </a>
    @endif

    <!-- Enlaces comunes para todos los usuarios autenticados -->
    <a href="{{ route('public.matches') }}" 
       class="text-gray-700 dark:text-gray-300 hover:text-vp-primary-600 dark:hover:text-vp-primary-400 px-3 py-2 text-sm font-medium transition-colors">
        Partidos
    </a>
    <a href="{{ route('public.teams') }}" 
       class="text-gray-700 dark:text-gray-300 hover:text-vp-primary-600 dark:hover:text-vp-primary-400 px-3 py-2 text-sm font-medium transition-colors">
        Equipos
    </a>
</nav>
```

## 4. Menú para Invitados (guest-menu.blade.php)

### Ubicación: `resources/views/components/navigation/guest-menu.blade.php`

```php
<nav class="flex space-x-8">
    <a href="{{ route('home') }}" 
       class="text-gray-700 dark:text-gray-300 hover:text-vp-primary-600 dark:hover:text-vp-primary-400 px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('home') ? 'text-vp-primary-600 dark:text-vp-primary-400' : '' }}">
        Inicio
    </a>
    <a href="{{ route('public.matches') }}" 
       class="text-gray-700 dark:text-gray-300 hover:text-vp-primary-600 dark:hover:text-vp-primary-400 px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('public.matches') ? 'text-vp-primary-600 dark:text-vp-primary-400' : '' }}">
        Partidos en Vivo
    </a>
    <a href="{{ route('public.results') }}" 
       class="text-gray-700 dark:text-gray-300 hover:text-vp-primary-600 dark:hover:text-vp-primary-400 px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('public.results') ? 'text-vp-primary-600 dark:text-vp-primary-400' : '' }}">
        Resultados
    </a>
    <a href="{{ route('public.teams') }}" 
       class="text-gray-700 dark:text-gray-300 hover:text-vp-primary-600 dark:hover:text-vp-primary-400 px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('public.teams') ? 'text-vp-primary-600 dark:text-vp-primary-400' : '' }}">
        Equipos
    </a>
    <a href="{{ route('public.standings') }}" 
       class="text-gray-700 dark:text-gray-300 hover:text-vp-primary-600 dark:hover:text-vp-primary-400 px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('public.standings') ? 'text-vp-primary-600 dark:text-vp-primary-400' : '' }}">
        Tabla de Posiciones
    </a>
    <a href="{{ route('public.stats') }}" 
       class="text-gray-700 dark:text-gray-300 hover:text-vp-primary-600 dark:hover:text-vp-primary-400 px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('public.stats') ? 'text-vp-primary-600 dark:text-vp-primary-400' : '' }}">
        Estadísticas
    </a>
</nav>
```

## 5. Menú Móvil (mobile-menu.blade.php)

### Ubicación: `resources/views/components/navigation/mobile-menu.blade.php`

```php
<div class="px-4 py-6 space-y-1">
    @auth
        <!-- Información del usuario -->
        <div class="pb-6 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center space-x-3">
                <img class="h-10 w-10 rounded-full object-cover" 
                     src="{{ auth()->user()->avatar_url ?? '/placeholder.svg?height=40&width=40' }}" 
                     alt="{{ auth()->user()->name }}">
                <div>
                    <div class="text-base font-medium text-gray-900 dark:text-white">
                        {{ auth()->user()->name }}
                    </div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">
                        {{ ucfirst(auth()->user()->role) }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Enlaces según el rol -->
        <div class="mt-6 space-y-1">
            @if(auth()->user()->hasRole('player'))
                <a href="{{ route('player.dashboard') }}" 
                   class="block px-3 py-2 text-base font-medium text-gray-700 dark:text-gray-300 hover:text-vp-primary-600 dark:hover:text-vp-primary-400 hover:bg-gray-50 dark:hover:bg-gray-800 rounded-md transition-colors">
                    Mi Dashboard
                </a>
                <a href="{{ route('player.matches') }}" 
                   class="block px-3 py-2 text-base font-medium text-gray-700 dark:text-gray-300 hover:text-vp-primary-600 dark:hover:text-vp-primary-400 hover:bg-gray-50 dark:hover:bg-gray-800 rounded-md transition-colors">
                    Mis Partidos
                </a>
                <a href="{{ route('player.team') }}" 
                   class="block px-3 py-2 text-base font-medium text-gray-700 dark:text-gray-300 hover:text-vp-primary-600 dark:hover:text-vp-primary-400 hover:bg-gray-50 dark:hover:bg-gray-800 rounded-md transition-colors">
                    Mi Equipo
                </a>
            @endif

            @if(auth()->user()->hasRole('coach'))
                <a href="{{ route('coach.dashboard') }}" 
                   class="block px-3 py-2 text-base font-medium text-gray-700 dark:text-gray-300 hover:text-vp-primary-600 dark:hover:text-vp-primary-400 hover:bg-gray-50 dark:hover:bg-gray-800 rounded-md transition-colors">
                    Panel de Control
                </a>
                <a href="{{ route('coach.team') }}" 
                   class="block px-3 py-2 text-base font-medium text-gray-700 dark:text-gray-300 hover:text-vp-primary-600 dark:hover:text-vp-primary-400 hover:bg-gray-50 dark:hover:bg-gray-800 rounded-md transition-colors">
                    Mi Equipo
                </a>
                <a href="{{ route('coach.matches') }}" 
                   class="block px-3 py-2 text-base font-medium text-gray-700 dark:text-gray-300 hover:text-vp-primary-600 dark:hover:text-vp-primary-400 hover:bg-gray-50 dark:hover:bg-gray-800 rounded-md transition-colors">
                    Calendario
                </a>
            @endif

            <!-- Enlaces comunes -->
            <a href="{{ route('public.matches') }}" 
               class="block px-3 py-2 text-base font-medium text-gray-700 dark:text-gray-300 hover:text-vp-primary-600 dark:hover:text-vp-primary-400 hover:bg-gray-50 dark:hover:bg-gray-800 rounded-md transition-colors">
                Partidos
            </a>
            <a href="{{ route('public.teams') }}" 
               class="block px-3 py-2 text-base font-medium text-gray-700 dark:text-gray-300 hover:text-vp-primary-600 dark:hover:text-vp-primary-400 hover:bg-gray-50 dark:hover:bg-gray-800 rounded-md transition-colors">
                Equipos
            </a>
        </div>

        <!-- Acciones del usuario -->
        <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700 space-y-1">
            <a href="{{ route('profile.edit') }}" 
               class="block px-3 py-2 text-base font-medium text-gray-700 dark:text-gray-300 hover:text-vp-primary-600 dark:hover:text-vp-primary-400 hover:bg-gray-50 dark:hover:bg-gray-800 rounded-md transition-colors">
                Mi Perfil
            </a>
            <a href="{{ route('settings') }}" 
               class="block px-3 py-2 text-base font-medium text-gray-700 dark:text-gray-300 hover:text-vp-primary-600 dark:hover:text-vp-primary-400 hover:bg-gray-50 dark:hover:bg-gray-800 rounded-md transition-colors">
                Configuración
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" 
                        class="block w-full text-left px-3 py-2 text-base font-medium text-red-600 hover:text-red-700 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-md transition-colors">
                    Cerrar Sesión
                </button>
            </form>
        </div>
    @else
        <!-- Menú para invitados -->
        <div class="space-y-1">
            <a href="{{ route('home') }}" 
               class="block px-3 py-2 text-base font-medium text-gray-700 dark:text-gray-300 hover:text-vp-primary-600 dark:hover:text-vp-primary-400 hover:bg-gray-50 dark:hover:bg-gray-800 rounded-md transition-colors">
                Inicio
            </a>
            <a href="{{ route('public.matches') }}" 
               class="block px-3 py-2 text-base font-medium text-gray-700 dark:text-gray-300 hover:text-vp-primary-600 dark:hover:text-vp-primary-400 hover:bg-gray-50 dark:hover:bg-gray-800 rounded-md transition-colors">
                Partidos en Vivo
            </a>
            <a href="{{ route('public.results') }}" 
               class="block px-3 py-2 text-base font-medium text-gray-700 dark:text-gray-300 hover:text-vp-primary-600 dark:hover:text-vp-primary-400 hover:bg-gray-50 dark:hover:bg-gray-800 rounded-md transition-colors">
                Resultados
            </a>
            <a href="{{ route('public.teams') }}" 
               class="block px-3 py-2 text-base font-medium text-gray-700 dark:text-gray-300 hover:text-vp-primary-600 dark:hover:text-vp-primary-400 hover:bg-gray-50 dark:hover:bg-gray-800 rounded-md transition-colors">
                Equipos
            </a>
            <a href="{{ route('public.standings') }}" 
               class="block px-3 py-2 text-base font-medium text-gray-700 dark:text-gray-300 hover:text-vp-primary-600 dark:hover:text-vp-primary-400 hover:bg-gray-50 dark:hover:bg-gray-800 rounded-md transition-colors">
                Tabla de Posiciones
            </a>
        </div>

        <!-- Acciones para invitados -->
        <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700 space-y-3">
            <a href="{{ route('login') }}" 
               class="block bg-vp-primary-500 text-white text-center px-4 py-3 rounded-lg font-medium hover:bg-vp-primary-600 transition-colors">
                Iniciar Sesión
            </a>
            <a href="{{ route('register') }}" 
               class="block border-2 border-vp-primary-500 text-vp-primary-500 text-center px-4 py-3 rounded-lg font-medium hover:bg-vp-primary-50 transition-colors">
                Registrarse
            </a>
        </div>
    @endauth
</div>
```

## 6. Dropdown de Usuario (user-dropdown.blade.php)

### Ubicación: `resources/views/components/navigation/user-dropdown.blade.php`

```php
<div class="py-1">
    <!-- Información del usuario -->
    <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ auth()->user()->name }}</p>
        <p class="text-sm text-gray-500 dark:text-gray-400">{{ auth()->user()->email }}</p>
        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">{{ ucfirst(auth()->user()->role) }}</p>
    </div>

    <!-- Enlaces de perfil -->
    <div class="py-1">
        <a href="{{ route('profile.edit') }}" 
           class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white transition-colors">
            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
            Mi Perfil
        </a>
        
        <a href="{{ route('settings') }}" 
           class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white transition-colors">
            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            Configuración
        </a>

        <!-- Enlaces específicos por rol -->
        @if(auth()->user()->hasRole('player'))
            <a href="{{ route('player.medical') }}" 
               class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white transition-colors">
                <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Estado Médico
            </a>
        @endif

        @if(auth()->user()->hasRole('coach'))
            <a href="{{ route('coach.reports') }}" 
               class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white transition-colors">
                <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                Reportes
            </a>
        @endif

        <a href="{{ route('help') }}" 
           class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white transition-colors">
            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Ayuda
        </a>
    </div>

    <!-- Cerrar sesión -->
    <div class="py-1 border-t border-gray-200 dark:border-gray-700">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" 
                    class="flex items-center w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 hover:text-red-700 transition-colors">
                <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                Cerrar Sesión
            </button>
        </form>
    </div>
</div>
```

## Características de los Componentes de Navegación

### 1. Responsividad Completa
- **Desktop**: Navegación horizontal completa
- **Mobile**: Menú hamburguesa con overlay
- **Tablet**: Adaptación automática

### 2. Estados Activos
- **Indicadores visuales**: Enlaces activos resaltados
- **Breadcrumbs implícitos**: Usuario sabe dónde está
- **Transiciones suaves**: Entre estados

### 3. Modo Oscuro
- **Toggle integrado**: En el header principal
- **Persistencia**: Guarda preferencia en localStorage
- **Transiciones**: Cambios suaves entre modos

### 4. Notificaciones en Tiempo Real
- **Dropdown**: Notificaciones recientes
- **Contador**: Badge con número de notificaciones
- **Auto-refresh**: Actualizaciones automáticas

### 5. Seguridad y Roles
- **Menús dinámicos**: Según rol del usuario
- **Permisos**: Solo enlaces permitidos
- **Logout seguro**: CSRF protection

## JavaScript Alpine.js

### Store Global para Navegación

```javascript
// En app.blade.php
Alpine.store('navigation', {
    mobileMenuOpen: false,
    userMenuOpen: false,
    notificationsOpen: false,
    
    closeMobileMenu() {
        this.mobileMenuOpen = false;
    },
    
    closeAllDropdowns() {
        this.userMenuOpen = false;
        this.notificationsOpen = false;
    },
    
    toggleMobileMenu() {
        this.mobileMenuOpen = !this.mobileMenuOpen;
        if (this.mobileMenuOpen) {
            this.closeAllDropdowns();
        }
    }
});
```

## Estados de Desarrollo

### ✅ Completado
- Estructura de todos los componentes de navegación
- Responsive design mobile-first
- Sistema de roles y permisos
- Modo oscuro integrado

### 🔄 En Desarrollo
- Componente de notificaciones
- Dropdown de usuario
- Navegación breadcrumb

### 📋 Por Desarrollar
- Todas las rutas referenciadas
- Componente `@livewire('shared.notifications-dropdown')`
- Sistema de búsqueda global
- Navegación contextual por sección

# 🏐 VolleyPass - Componentes Livewire por Desarrollar

## 1. Componentes Públicos

### 1.1 public.live-matches
**Ubicación**: `app/Livewire/Public/LiveMatches.php`  
**Vista**: `resources/views/livewire/public/live-matches.blade.php`

**Funcionalidad**:
- Muestra partidos actualmente en curso
- Actualización en tiempo real de marcadores
- Estados: En vivo, Próximos, Finalizados
- Filtros por categoría y fecha

**Propiedades**:
```php
public $matches = [];
public $filter = 'live'; // live, upcoming, finished
public $refreshInterval = 30; // segundos
```

**Métodos en Tiempo Real**:
- `#[On('match-score-updated')]` - Actualiza marcador
- `#[On('match-status-changed')]` - Cambia estado del partido
- `refreshMatches()` - Refresco manual

### 1.2 public.recent-results
**Ubicación**: `app/Livewire/Public/RecentResults.php`

**Funcionalidad**:
- Últimos 10 resultados de partidos finalizados
- Información de equipos y marcadores finales
- Links a estadísticas detalladas

### 1.3 public.league-stats
**Ubicación**: `app/Livewire/Public/LeagueStats.php`

**Funcionalidad**:
- Estadísticas generales de la liga
- Goleadoras, mejores equipos, records
- Gráficos y métricas visuales

## 2. Componentes de Jugadora

### 2.1 player.profile-header
**Ubicación**: `app/Livewire/Player/ProfileHeader.php`

**Funcionalidad**:
- Header con foto, nombre, posición, número
- Estado médico actual
- Estadísticas destacadas de la temporada
- QR del carnet digital

**Propiedades**:
```php
public $player;
public $medicalStatus;
public $seasonStats;
public $qrCode;
```

### 2.2 player.upcoming-matches
**Ubicación**: `app/Livewire/Player/UpcomingMatches.php`

**Funcionalidad**:
- Próximos partidos de la jugadora
- Información del rival, fecha, lugar
- Estado de confirmación de asistencia

**Métodos**:
- `confirmAttendance($matchId)` - Confirmar asistencia
- `reportUnavailability($matchId, $reason)` - Reportar no disponibilidad

### 2.3 player.performance-stats
**Ubicación**: `app/Livewire/Player/PerformanceStats.php`

**Funcionalidad**:
- Estadísticas personales: puntos, aces, bloqueos, recepciones
- Gráficos de evolución temporal
- Comparación con promedios de la liga

### 2.4 player.recent-activity
**Ubicación**: `app/Livewire/Player/RecentActivity.php`

**Funcionalidad**:
- Timeline de actividades recientes
- Partidos jugados, entrenamientos, evaluaciones médicas
- Notificaciones importantes

### 2.5 player.medical-status
**Ubicación**: `app/Livewire/Player/MedicalStatus.php`

**Funcionalidad**:
- Estado médico actual: Apta/No apta/Observación
- Fecha de último chequeo médico
- Recordatorios de citas médicas
- Botón para reportar lesión

**Métodos**:
- `reportInjury()` - Modal para reportar lesión
- `requestMedicalCheckup()` - Solicitar cita médica

### 2.6 player.team-info
**Ubicación**: `app/Livewire/Player/TeamInfo.php`

**Funcionalidad**:
- Información del equipo actual
- Entrenador, compañeras de equipo
- Próximos entrenamientos
- Chat del equipo (básico)

## 3. Componentes de Entrenador

### 3.1 coach.team-overview
**Ubicación**: `app/Livewire/Coach/TeamOverview.php`

**Funcionalidad**:
- Header con información del equipo
- Estadísticas del equipo en la temporada
- Estado general del plantel (lesiones, disponibilidad)
- Record de victorias/derrotas

**Propiedades**:
```php
public $team;
public $seasonRecord;
public $playerAvailability;
public $injuredPlayers;
```

### 3.2 coach.team-schedule
**Ubicación**: `app/Livewire/Coach/TeamSchedule.php`

**Funcionalidad**:
- Calendario completo del equipo
- Partidos, entrenamientos, eventos
- Crear nuevos eventos
- Confirmar asistencia de jugadoras

**Métodos**:
- `createEvent()` - Modal para crear evento
- `editEvent($eventId)` - Editar evento existente
- `viewAttendance($eventId)` - Ver asistencia

### 3.3 coach.player-management
**Ubicación**: `app/Livewire/Coach/PlayerManagement.php`

**Funcionalidad**:
- Lista completa de jugadoras del equipo
- Estado médico, disponibilidad, estadísticas
- Agregar/remover jugadoras
- Gestión de rotaciones y posiciones

**Métodos**:
- `addPlayer()` - Modal para agregar jugadora
- `removePlayer($playerId)` - Remover del equipo
- `updatePosition($playerId, $position)` - Cambiar posición

### 3.4 coach.team-statistics
**Ubicación**: `app/Livewire/Coach/TeamStatistics.php`

**Funcionalidad**:
- Estadísticas completas del equipo
- Análisis de rendimiento individual y colectivo
- Gráficos comparativos
- Exportar reportes

### 3.5 coach.team-health-status
**Ubicación**: `app/Livewire/Coach/TeamHealthStatus.php`

**Funcionalidad**:
- Estado médico del plantel
- Jugadoras lesionadas, en recuperación
- Alertas médicas importantes
- Coordinación con staff médico

### 3.6 coach.notifications
**Ubicación**: `app/Livewire/Coach/Notifications.php`

**Funcionalidad**:
- Notificaciones específicas del entrenador
- Confirmaciones de asistencia
- Reportes de lesiones
- Mensajes de la liga

## 4. Componentes de Árbitro

### 4.1 referee.stats-overview
**Ubicación**: `app/Livewire/Referee/StatsOverview.php`

**Funcionalidad**:
- 4 tarjetas con métricas clave del árbitro
- Partidos dirigidos, calificación promedio
- Próximas asignaciones, certificaciones

### 4.2 referee.assigned-matches
**Ubicación**: `app/Livewire/Referee/AssignedMatches.php`

**Funcionalidad**:
- Lista de partidos asignados
- Información de equipos, fecha, lugar
- Confirmar disponibilidad
- Acceso a control del partido en vivo

**Métodos**:
- `confirmAssignment($matchId)` - Confirmar asignación
- `startMatchControl($matchId)` - Iniciar control en vivo
- `submitMatchReport($matchId)` - Enviar reporte post-partido

### 4.3 referee.match-reports
**Ubicación**: `app/Livewire/Referee/MatchReports.php`

**Funcionalidad**:
- Historial de reportes de partidos
- Crear nuevos reportes
- Incidentes, amonestaciones, observaciones
- Firmas digitales

### 4.4 referee.performance-history
**Ubicación**: `app/Livewire/Referee/PerformanceHistory.php`

**Funcionalidad**:
- Historial de evaluaciones
- Feedback de equipos y supervisores
- Evolución de calificaciones
- Áreas de mejora

### 4.5 referee.certification-status
**Ubicación**: `app/Livewire/Referee/CertificationStatus.php`

**Funcionalidad**:
- Estado de certificaciones actuales
- Fechas de vencimiento
- Cursos pendientes
- Renovaciones automáticas

### 4.6 referee.recent-assignments
**Ubicación**: `app/Livewire/Referee/RecentAssignments.php`

**Funcionalidad**:
- Últimas asignaciones completadas
- Resultados y reportes asociados
- Feedback recibido

## 5. Componentes Médicos

### 5.1 medical.stats-overview
**Ubicación**: `app/Livewire/Medical/StatsOverview.php`

**Funcionalidad**:
- Métricas médicas: jugadoras atendidas, lesiones activas
- Alertas críticas, citas programadas
- Estado general de salud de la liga

### 5.2 medical.active-injuries
**Ubicación**: `app/Livewire/Medical/ActiveInjuries.php`

**Funcionalidad**:
- Lista de lesiones actualmente en tratamiento
- Severidad, tiempo estimado de recuperación
- Seguimiento de tratamientos
- Actualizaciones de estado

**Métodos**:
- `updateInjuryStatus($injuryId, $status)` - Actualizar estado
- `addTreatmentNote($injuryId, $note)` - Agregar nota de tratamiento
- `clearForPlay($playerId)` - Dar alta médica

### 5.3 medical.medical-reports
**Ubicación**: `app/Livewire/Medical/MedicalReports.php`

**Funcionalidad**:
- Crear y gestionar reportes médicos
- Evaluaciones periódicas
- Certificados de aptitud física
- Historiales médicos digitales

### 5.4 medical.player-health-monitoring
**Ubicación**: `app/Livewire/Medical/PlayerHealthMonitoring.php`

**Funcionalidad**:
- Monitoreo continuo de jugadoras
- Programación de chequeos médicos
- Alertas preventivas
- Dashboard de salud general

### 5.5 medical.emergency-contacts
**Ubicación**: `app/Livewire/Medical/EmergencyContacts.php`

**Funcionalidad**:
- Lista de contactos de emergencia
- Hospitales, ambulancias, especialistas
- Acceso rápido durante emergencias
- Protocolos de activación

### 5.6 medical.medical-alerts
**Ubicación**: `app/Livewire/Medical/MedicalAlerts.php`

**Funcionalidad**:
- Alertas médicas activas
- Condiciones especiales de jugadoras
- Medicamentos, alergias, restricciones
- Notificaciones críticas

## 6. Componentes Compartidos

### 6.1 shared.notifications-dropdown
**Ubicación**: `app/Livewire/Shared/NotificationsDropdown.php`

**Funcionalidad**:
- Dropdown de notificaciones del usuario
- Notificaciones en tiempo real
- Marcar como leídas
- Filtros por tipo y fecha

**Propiedades**:
```php
public $notifications = [];
public $unreadCount = 0;
public $filter = 'all'; // all, unread, today
```

**Métodos en Tiempo Real**:
- `#[On('notification-received')]` - Nueva notificación
- `markAsRead($notificationId)` - Marcar como leída
- `markAllAsRead()` - Marcar todas como leídas

### 6.2 shared.search-component
**Ubicación**: `app/Livewire/Shared/SearchComponent.php`

**Funcionalidad**:
- Búsqueda global en la plataforma
- Jugadoras, equipos, partidos
- Autocompletado con resultados en tiempo real
- Filtros avanzados

### 6.3 shared.match-card
**Ubicación**: `app/Livewire/Shared/MatchCard.php`

**Funcionalidad**:
- Componente reutilizable para mostrar partidos
- Diferentes modos: live, upcoming, finished
- Información de equipos, marcador, estado
- Acciones contextuales según usuario

### 6.4 shared.player-card
**Ubicación**: `app/Livewire/Shared/PlayerCard.php`

**Funcionalidad**:
- Tarjeta reutilizable de jugadora
- Foto, nombre, posición, equipo
- Estado médico, estadísticas básicas
- Links a perfil completo

## Características Técnicas de los Componentes

### 1. Tiempo Real con Livewire
```php
// Ejemplo de implementación en LiveMatches
#[On('match-score-updated')]
public function updateMatchScore($matchId, $homeScore, $awayScore)
{
    $match = $this->matches->find($matchId);
    if ($match) {
        $match->update([
            'home_score' => $homeScore,
            'away_score' => $awayScore
        ]);
        $this->refreshMatches();
    }
}

// Polling automático cada 30 segundos
public function refresh()
{
    $this->refreshMatches();
}
```

### 2. Alpine.js para Interactividad
```php
// En las vistas blade
<div x-data="{ 
    showDetails: false,
    confirmAction(action) {
        if (confirm('¿Estás seguro?')) {
            $wire[action]();
        }
    }
}">
```

### 3. Eventos Personalizados
```php
// Dispatch events para comunicación entre componentes
$this->dispatch('player-status-changed', playerId: $player->id);
$this->dispatch('notification-created', [
    'title' => 'Estado Actualizado',
    'message' => 'El estado médico ha sido actualizado'
]);
```

### 4. Validación en Tiempo Real
```php
#[Rule('required|min:3')]
public $search = '';

#[Rule('required|in:available,injured,suspended')]
public $playerStatus = '';
```

### 5. Autorización por Roles
```php
public function mount()
{
    $this->authorize('view-medical-data');
}

public function updatePlayerStatus($playerId, $status)
{
    $this->authorize('update-player-status');
    // Lógica de actualización
}
```

## Estados de Desarrollo

### 📋 Por Desarrollar (Prioridad Alta)
1. **public.live-matches** - Crítico para página principal
2. **player.profile-header** - Esencial para experiencia de jugadora
3. **shared.notifications-dropdown** - Necesario para navegación
4. **coach.team-overview** - Core del dashboard de entrenador

### 📋 Por Desarrollar (Prioridad Media)
- Todos los componentes de estadísticas
- Sistema de reportes médicos
- Gestión de asignaciones de árbitros

### 📋 Por Desarrollar (Prioridad Baja)
- Componentes de chat y mensajería
- Exportación de reportes avanzados
- Integraciones con sistemas externos

## Consideraciones de Rendimiento

### 1. Lazy Loading
```php
// Solo cargar datos cuando el componente sea visible
public function loadData()
{
    $this->data = $this->getData();
    $this->loaded = true;
}
```

### 2. Caché Inteligente
```php
// Cache de consultas pesadas
public function getStatsProperty()
{
    return Cache::remember(
        "player-stats-{$this->player->id}", 
        now()->addMinutes(10), 
        fn() => $this->calculateStats()
    );
}
```

### 3. Polling Inteligente
```php
// Solo hacer polling cuando la página esté activa
public function startPolling()
{
    if (document.visibilityState === 'visible') {
        $this->refreshData();
    }
}
```

Componentes en Tiempo Real:

Marcadores en vivo durante partidos
Notificaciones push de eventos importantes
Estado médico actualizado instantáneamente
Verificación QR con validación en tiempo real

Dashboards por Rol:

Jugadora: Perfil, partidos, estadísticas, estado médico
Entrenador: Gestión de equipo, calendario, jugadoras
Árbitro: Asignaciones, reportes, certificaciones
Médico: Monitoreo de salud, lesiones, emergencias

Diseño Inspirado en:

ESPN, Liga MX App, UEFA.com
Profesional pero accesible para voleibol amateur/regional
Colores de la bandera colombiana (amarillo, azul, rojo)
Mobile-first con funcionalidad sin JavaScript


//resources/app.css

@import "tailwindcss/base";
@import "tailwindcss/components";
@import "tailwindcss/utilities";

/* VolleyPass Design Tokens */
:root {
  /* Colores Primarios - Inspirados en la bandera colombiana */
  --vp-primary-50: #fef3c7;
  --vp-primary-100: #fde68a;
  --vp-primary-500: #f59e0b; /* Amarillo Colombia */
  --vp-primary-600: #d97706;
  --vp-primary-900: #78350f;

  /* Colores Secundarios - Azul voleibol */
  --vp-secondary-50: #eff6ff;
  --vp-secondary-500: #3b82f6; /* Azul deportivo */
  --vp-secondary-600: #2563eb;
  --vp-secondary-900: #1e3a8a;

  /* Colores de Acento - Rojo pasión */
  --vp-accent-500: #ef4444; /* Rojo Colombia */
  --vp-accent-600: #dc2626;

  /* Sistema de Grises */
  --vp-gray-50: #f9fafb;
  --vp-gray-100: #f3f4f6;
  --vp-gray-500: #6b7280;
  --vp-gray-800: #1f2937;
  --vp-gray-900: #111827;

  /* Estados Especiales */
  --vp-success: #10b981; /* Verde éxito */
  --vp-warning: #f59e0b; /* Amarillo advertencia */
  --vp-error: #ef4444; /* Rojo error */
  --vp-live: #ff0000; /* Rojo transmisión en vivo */
}

/* Fuentes */
.font-primary {
  font-family: "Inter", system-ui, -apple-system, sans-serif;
}

.font-display {
  font-family: "Poppins", system-ui, -apple-system, sans-serif;
}

/* Componentes Base */
@layer components {
  .btn-primary {
    @apply bg-vp-primary-500 text-white px-4 py-2 rounded-lg hover:bg-vp-primary-600 transition-colors font-medium;
  }

  .btn-secondary {
    @apply bg-vp-secondary-500 text-white px-4 py-2 rounded-lg hover:bg-vp-secondary-600 transition-colors font-medium;
  }

  .btn-outline {
    @apply border-2 border-vp-primary-500 text-vp-primary-500 px-4 py-2 rounded-lg hover:bg-vp-primary-500 hover:text-white transition-colors font-medium;
  }

  .card {
    @apply bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden;
  }

  .card-header {
    @apply p-6 border-b border-gray-200 dark:border-gray-700;
  }

  .card-body {
    @apply p-6;
  }

  .card-footer {
    @apply p-6 border-t border-gray-200 dark:border-gray-700;
  }

  .live-indicator {
    @apply inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800;
  }

  .live-indicator::before {
    content: "";
    @apply w-2 h-2 bg-red-500 rounded-full mr-2 animate-pulse;
  }
}

/* Animaciones personalizadas */
@keyframes pulse-live {
  0%,
  100% {
    opacity: 1;
  }
  50% {
    opacity: 0.5;
  }
}

.animate-pulse-live {
  animation: pulse-live 1.5s ease-in-out infinite;
}

/* Responsive utilities */
@layer utilities {
  .text-balance {
    text-wrap: balance;
  }

  .scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
  }

  .scrollbar-hide::-webkit-scrollbar {
    display: none;
  }
}

/* Dark mode improvements */
.dark {
  color-scheme: dark;
}

/* Print styles */
@media print {
  .no-print {
    display: none !important;
  }
}

//resources/view/welcome.blade.php
<x-public-layout>
    @section('title', 'Inicio - Liga de Voleibol de Sucre')
    @section('description', 'Plataforma oficial de la Liga de Voleibol de Sucre. Sigue los partidos en vivo, consulta resultados y estadísticas.')

    <div x-data="welcomeData()" x-init="init()">
        <!-- Hero Section -->
        <section class="relative bg-gradient-to-br from-vp-primary-500 via-vp-secondary-500 to-vp-accent-500 overflow-hidden">
            <!-- Background Pattern -->
            <div class="absolute inset-0 opacity-10">
                <div class="w-full h-full" style="background-image: url('data:image/svg+xml,<svg xmlns=&quot;http://www.w3.org/2000/svg&quot; width=&quot;100&quot; height=&quot;100&quot; viewBox=&quot;0 0 100 100&quot;><circle cx=&quot;50&quot; cy=&quot;50&quot; r=&quot;40&quot; fill=&quot;none&quot; stroke=&quot;%23fff&quot; stroke-width=&quot;2&quot;/><path d=&quot;M10 50 L90 50 M50 10 L50 90 M25 25 L75 75 M75 25 L25 75&quot; stroke=&quot;%23fff&quot; stroke-width=&quot;1&quot;/></svg>'); background-size: 100px 100px;"></div>
            </div>
            
            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 lg:py-32">
                <div class="text-center">
                    <h1 class="font-display font-bold text-4xl sm:text-5xl lg:text-6xl text-white mb-6">
                        Liga de Voleibol
                        <span class="block text-vp-primary-200">de Sucre</span>
                    </h1>
                    <p class="text-xl sm:text-2xl text-white/90 mb-8 max-w-3xl mx-auto">
                        La plataforma digital oficial para seguir todos los partidos, estadísticas y noticias del voleibol sucreño
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <a href="{{ route('register') }}" 
                           class="bg-white text-vp-primary-600 px-8 py-4 rounded-xl font-semibold text-lg hover:bg-gray-50 transition-colors shadow-lg">
                            Únete Ahora
                        </a>
                        <a href="#live-matches" 
                           class="border-2 border-white text-white px-8 py-4 rounded-xl font-semibold text-lg hover:bg-white/10 transition-colors">
                            Ver Partidos en Vivo
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Live Matches Section -->
        <section id="live-matches" class="py-16 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <h2 class="font-display font-bold text-3xl sm:text-4xl text-gray-900 mb-4">
                        Partidos en Vivo
                    </h2>
                    <p class="text-xl text-gray-600">
                        Sigue la acción en tiempo real
                    </p>
                </div>
                
                @livewire('public.live-matches')
            </div>
        </section>

        <!-- Recent Results Section -->
        <section class="py-16 bg-gray-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <h2 class="font-display font-bold text-3xl sm:text-4xl text-gray-900 mb-4">
                        Últimos Resultados
                    </h2>
                    <p class="text-xl text-gray-600">
                        Revisa los resultados más recientes
                    </p>
                </div>
                
                @livewire('public.recent-results')
            </div>
        </section>

        <!-- Statistics Section -->
        <section class="py-16 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <h2 class="font-display font-bold text-3xl sm:text-4xl text-gray-900 mb-4">
                        Estadísticas de la Liga
                    </h2>
                    <p class="text-xl text-gray-600">
                        Números que cuentan la historia
                    </p>
                </div>
                
                @livewire('public.league-stats')
            </div>
        </section>

        <!-- CTA Section -->
        <section class="py-16 bg-gradient-to-r from-vp-secondary-600 to-vp-primary-600">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <h2 class="font-display font-bold text-3xl sm:text-4xl text-white mb-4">
                    ¿Listo para formar parte?
                </h2>
                <p class="text-xl text-white/90 mb-8 max-w-2xl mx-auto">
                    Únete a la comunidad de voleibol más grande de Sucre. Registra tu equipo, sigue tus estadísticas y conecta con otros jugadores.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('register') }}" 
                       class="bg-white text-vp-secondary-600 px-8 py-4 rounded-xl font-semibold text-lg hover:bg-gray-50 transition-colors shadow-lg">
                        Registrarse Gratis
                    </a>
                    <a href="{{ route('login') }}" 
                       class="border-2 border-white text-white px-8 py-4 rounded-xl font-semibold text-lg hover:bg-white/10 transition-colors">
                        Iniciar Sesión
                    </a>
                </div>
            </div>
        </section>
    </div>

    <script>
        function welcomeData() {
            return {
                init() {
                    // Smooth scroll for anchor links
                    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                        anchor.addEventListener('click', function (e) {
                            e.preventDefault();
                            const target = document.querySelector(this.getAttribute('href'));
                            if (target) {
                                target.scrollIntoView({
                                    behavior: 'smooth',
                                    block: 'start'
                                });
                            }
                        });
                    });
                }
            }
        }
    </script>
</x-public-layout>


//resources/views/layouts/public.blade.php
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" x-data="publicData()">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>{{ config('app.name', 'VolleyPass') }} - @yield('title', 'Liga de Voleibol de Sucre')</title>
    <meta name="description" content="@yield('description', 'Plataforma integral para la gestión de la Liga de Voleibol de Sucre, Colombia. Resultados en vivo, estadísticas y más.')">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Styles -->
    @vite(['resources/css/app.css'])
    @livewireStyles
</head>
<body class="font-inter antialiased bg-gray-50">
    <!-- Public Header -->
    @include('components.navigation.public-header')
    
    <!-- Main Content -->
    <main>
        {{ $slot }}
    </main>
    
    <!-- Public Footer -->
    @include('components.navigation.public-footer')
    
    <!-- Scripts -->
    @livewireScripts
    @vite(['resources/js/app.js'])
    
    <script>
        function publicData() {
            return {
                mobileMenuOpen: false,
                
                toggleMobileMenu() {
                    this.mobileMenuOpen = !this.mobileMenuOpen;
                }
            }
        }
    </script>
</body>
</html>


//resources/views/layouts/auth.blade.php
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>{{ config('app.name', 'VolleyPass') }} - @yield('title', 'Autenticación')</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Styles -->
    @vite(['resources/css/app.css'])
    @livewireStyles
</head>
<body class="font-inter antialiased bg-gradient-to-br from-vp-primary-50 to-vp-secondary-50 min-h-screen">
    <!-- Volleyball Pattern Background -->
    <div class="absolute inset-0 opacity-5">
        <div class="w-full h-full" style="background-image: url('data:image/svg+xml,<svg xmlns=&quot;http://www.w3.org/2000/svg&quot; width=&quot;60&quot; height=&quot;60&quot; viewBox=&quot;0 0 60 60&quot;><circle cx=&quot;30&quot; cy=&quot;30&quot; r=&quot;25&quot; fill=&quot;none&quot; stroke=&quot;%23000&quot; stroke-width=&quot;1&quot;/><path d=&quot;M5 30 L55 30 M30 5 L30 55 M15 15 L45 45 M45 15 L15 45&quot; stroke=&quot;%23000&quot; stroke-width=&quot;0.5&quot;/></svg>'); background-size: 60px 60px;"></div>
    </div>
    
    <div class="relative min-h-screen flex flex-col justify-center py-12 sm:px-6 lg:px-8">
        <!-- Logo -->
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <div class="flex justify-center">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 bg-vp-primary-500 rounded-xl flex items-center justify-center">
                        <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10" fill="none" stroke="currentColor" stroke-width="2"/>
                            <path d="M2 12h20M12 2v20M7 7l10 10M17 7L7 17" stroke="currentColor" stroke-width="1"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="font-display font-bold text-2xl text-gray-900">VolleyPass</h1>
                        <p class="text-sm text-gray-600">Liga de Voleibol de Sucre</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Content -->
        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
            <div class="bg-white py-8 px-4 shadow-xl sm:rounded-xl sm:px-10">
                {{ $slot }}
            </div>
        </div>
        
        <!-- Footer -->
        <div class="mt-8 text-center">
            <p class="text-sm text-gray-600">
                © {{ date('Y') }} VolleyPass Software. Todos los derechos reservados.
            </p>
        </div>
    </div>
    
    <!-- Scripts -->
    @livewireScripts
    @vite(['resources/js/app.js'])
</body>
</html>


//resources/views/layouts/app.blade.php
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" x-data="appData()" :class="{ 'dark': $store.app.darkMode }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>{{ config('app.name', 'VolleyPass') }} - @yield('title', 'Plataforma Integral de Voleibol')</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Styles -->
    @vite(['resources/css/app.css'])
    @livewireStyles
    
    <!-- Alpine.js Stores -->
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('app', {
                darkMode: localStorage.getItem('darkMode') === 'true',
                sidebarOpen: false,
                
                toggleDarkMode() {
                    this.darkMode = !this.darkMode;
                    localStorage.setItem('darkMode', this.darkMode);
                },
                
                toggleSidebar() {
                    this.sidebarOpen = !this.sidebarOpen;
                }
            });
            
            Alpine.store('notifications', {
                items: [],
                
                add(notification) {
                    const id = Date.now();
                    this.items.push({ id, ...notification });
                    setTimeout(() => this.remove(id), 5000);
                },
                
                remove(id) {
                    this.items = this.items.filter(item => item.id !== id);
                }
            });
        });
    </script>
</head>
<body class="font-inter antialiased bg-gray-50 dark:bg-gray-900 transition-colors duration-200">
    <!-- Header Component -->
    @include('components.navigation.header')
    
    <!-- Main Content -->
    <main class="min-h-screen">
        {{ $slot }}
    </main>
    
    <!-- Footer Component -->
    @include('components.navigation.footer')
    
    <!-- Notification System -->
    <div x-data class="fixed top-4 right-4 z-50 space-y-2" x-show="$store.notifications.items.length > 0">
        <template x-for="notification in $store.notifications.items" :key="notification.id">
            <div x-show="true" 
                 x-transition:enter="transform ease-out duration-300 transition"
                 x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
                 x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
                 x-transition:leave="transition ease-in duration-100"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="max-w-sm w-full bg-white dark:bg-gray-800 shadow-lg rounded-lg pointer-events-auto ring-1 ring-black ring-opacity-5 overflow-hidden">
                <div class="p-4">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg x-show="notification.type === 'success'" class="h-6 w-6 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <svg x-show="notification.type === 'error'" class="h-6 w-6 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-3 w-0 flex-1 pt-0.5">
                            <p class="text-sm font-medium text-gray-900 dark:text-white" x-text="notification.title"></p>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400" x-text="notification.message"></p>
                        </div>
                        <div class="ml-4 flex-shrink-0 flex">
                            <button @click="$store.notifications.remove(notification.id)" class="bg-white dark:bg-gray-800 rounded-md inline-flex text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <span class="sr-only">Cerrar</span>
                                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>
    
    <!-- Scripts -->
    @livewireScripts
    @vite(['resources/js/app.js'])
    
    <script>
        function appData() {
            return {
                init() {
                    // Initialize dark mode from localStorage
                    if (localStorage.getItem('darkMode') === 'true') {
                        document.documentElement.classList.add('dark');
                    }
                }
            }
        }
    </script>
</body>
</html>


//resources/views/components/navigation/header.blade.php
<header class="bg-white dark:bg-gray-900 shadow-sm sticky top-0 z-50 transition-colors duration-200"
        x-data="headerData()"
        x-init="init()">
    
    <!-- Desktop Navigation -->
    <nav class="hidden lg:block max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <!-- Logo -->
            <div class="flex items-center">
                <a href="{{ route('home') }}" class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-vp-primary-500 to-vp-secondary-500 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10" fill="none" stroke="currentColor" stroke-width="2"/>
                            <path d="M2 12h20M12 2v20M7 7l10 10M17 7L7 17" stroke="currentColor" stroke-width="1"/>
                        </svg>
                    </div>
                    <div>
                        <span class="font-display font-bold text-xl text-gray-900 dark:text-white">
                            VolleyPass
                        </span>
                        <div class="text-xs text-gray-500 dark:text-gray-400 -mt-1">
                            Liga de Sucre
                        </div>
                    </div>
                </a>
            </div>

            <!-- Main Navigation -->
            <div class="hidden lg:flex space-x-8">
                @auth
                    @include('components.navigation.auth-menu')
                @else
                    @include('components.navigation.guest-menu')
                @endauth
            </div>

            <!-- Right Side Actions -->
            <div class="flex items-center space-x-4">
                <!-- Dark Mode Toggle -->
                <button @click="$store.app.toggleDarkMode()" 
                        class="p-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors">
                    <svg x-show="!$store.app.darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                    </svg>
                    <svg x-show="$store.app.darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </button>

                @auth
                    <!-- Notifications -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" 
                                class="p-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors relative">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM10.07 2.82l-.03.03a1.51 1.51 0 000 2.13l1.06 1.06 8.49 8.48a1.51 1.51 0 002.12 0l.03-.03a1.51 1.51 0 000-2.12L12.25 2.88a1.51 1.51 0 00-2.13 0l-.05.05z" />
                            </svg>
                            <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                                3
                            </span>
                        </button>
                        
                        <!-- Notifications Dropdown -->
                        <div x-show="open" 
                             @click.away="open = false"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             class="absolute right-0 mt-2 w-80 bg-white dark:bg-gray-800 rounded-lg shadow-lg ring-1 ring-black ring-opacity-5">
                            @livewire('shared.notifications-dropdown')
                        </div>
                    </div>

                    <!-- User Menu -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" 
                                class="flex items-center space-x-3 p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                            <img class="h-8 w-8 rounded-full object-cover" 
                                 src="{{ auth()->user()->avatar_url ?? '/placeholder.svg?height=32&width=32' }}" 
                                 alt="{{ auth()->user()->name }}">
                            <div class="hidden sm:block text-left">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ auth()->user()->name }}
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ ucfirst(auth()->user()->role) }}
                                </div>
                            </div>
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        
                        <!-- User Dropdown -->
                        <div x-show="open" 
                             @click.away="open = false"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg ring-1 ring-black ring-opacity-5">
                            @include('components.navigation.user-dropdown')
                        </div>
                    </div>
                @else
                    <!-- Guest Actions -->
                    <div class="flex items-center space-x-3">
                        <a href="{{ route('login') }}" 
                           class="text-gray-700 dark:text-gray-300 hover:text-vp-primary-600 dark:hover:text-vp-primary-400 font-medium transition-colors">
                            Iniciar Sesión
                        </a>
                        <a href="{{ route('register') }}" 
                           class="bg-vp-primary-500 text-white px-4 py-2 rounded-lg hover:bg-vp-primary-600 transition-colors font-medium">
                            Registrarse
                        </a>
                    </div>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Mobile Navigation -->
    <div class="lg:hidden">
        <div class="flex items-center justify-between h-16 px-4">
            <!-- Mobile Logo -->
            <a href="{{ route('home') }}" class="flex items-center space-x-2">
                <div class="w-8 h-8 bg-gradient-to-br from-vp-primary-500 to-vp-secondary-500 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10" fill="none" stroke="currentColor" stroke-width="2"/>
                        <path d="M2 12h20M12 2v20M7 7l10 10M17 7L7 17" stroke="currentColor" stroke-width="1"/>
                    </svg>
                </div>
                <span class="font-display font-bold text-lg text-gray-900 dark:text-white">
                    VolleyPass
                </span>
            </a>

            <!-- Mobile Menu Button -->
            <button @click="mobileMenuOpen = !mobileMenuOpen" 
                    class="p-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                <svg x-show="!mobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
                <svg x-show="mobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Mobile Menu -->
        <div x-show="mobileMenuOpen" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-1"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-1"
             class="border-t border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900">
            @include('components.navigation.mobile-menu')
        </div>
    </div>

    <script>
        function headerData() {
            return {
                mobileMenuOpen: false,
                
                init() {
                    // Close mobile menu when clicking outside
                    document.addEventListener('click', (e) => {
                        if (!this.$el.contains(e.target)) {
                            this.mobileMenuOpen = false;
                        }
                    });
                }
            }
        }
    </script>
</header>


//resources/views/components/navigation/footer.blade.php
<footer class="bg-gray-900 text-white relative overflow-hidden">
    <!-- Volleyball Pattern Background -->
    <div class="absolute inset-0 opacity-5">
        <div class="w-full h-full" style="background-image: url('data:image/svg+xml,<svg xmlns=&quot;http://www.w3.org/2000/svg&quot; width=&quot;60&quot; height=&quot;60&quot; viewBox=&quot;0 0 60 60&quot;><circle cx=&quot;30&quot; cy=&quot;30&quot; r=&quot;25&quot; fill=&quot;none&quot; stroke=&quot;%23fff&quot; stroke-width=&quot;1&quot;/><path d=&quot;M5 30 L55 30 M30 5 L30 55 M15 15 L45 45 M45 15 L15 45&quot; stroke=&quot;%23fff&quot; stroke-width=&quot;0.5&quot;/></svg>'); background-size: 60px 60px;"></div>
    </div>
    
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <!-- Footer Content Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12">
            <!-- Brand Section -->
            <div class="lg:col-span-2">
                <div class="flex items-center space-x-3 mb-6">
                    <div class="w-12 h-12 bg-gradient-to-br from-vp-primary-500 to-vp-secondary-500 rounded-xl flex items-center justify-center">
                        <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10" fill="none" stroke="currentColor" stroke-width="2"/>
                            <path d="M2 12h20M12 2v20M7 7l10 10M17 7L7 17" stroke="currentColor" stroke-width="1"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-display font-bold text-2xl">VolleyPass</h3>
                        <p class="text-gray-400">Liga de Voleibol de Sucre</p>
                    </div>
                </div>
                <p class="text-gray-300 mb-6 max-w-md">
                    La plataforma digital oficial para la gestión integral de la Liga de Voleibol de Sucre, Colombia. 
                    Conectando jugadoras, equipos y aficionados en una sola plataforma.
                </p>
                <div class="flex space-x-4">
                    <a href="#" class="text-gray-400 hover:text-white transition-colors">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/>
                        </svg>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-white transition-colors">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M22.46 6c-.77.35-1.6.58-2.46.69.88-.53 1.56-1.37 1.88-2.38-.83.5-1.75.85-2.72 1.05C18.37 4.5 17.26 4 16 4c-2.35 0-4.27 1.92-4.27 4.29 0 .34.04.67.11.98C8.28 9.09 5.11 7.38 3 4.79c-.37.63-.58 1.37-.58 2.15 0 1.49.75 2.81 1.91 3.56-.71 0-1.37-.2-1.95-.5v.03c0 2.08 1.48 3.82 3.44 4.21a4.22 4.22 0 0 1-1.93.07 4.28 4.28 0 0 0 4 2.98 8.521 8.521 0 0 1-5.33 1.84c-.34 0-.68-.02-1.02-.06C3.44 20.29 5.7 21 8.12 21 16 21 20.33 14.46 20.33 8.79c0-.19 0-.37-.01-.56.84-.6 1.56-1.36 2.14-2.23z"/>
                        </svg>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-white transition-colors">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12.017 0C5.396 0 .029 5.367.029 11.987c0 5.079 3.158 9.417 7.618 11.174-.105-.949-.199-2.403.041-3.439.219-.937 1.406-5.957 1.406-5.957s-.359-.72-.359-1.781c0-1.663.967-2.911 2.168-2.911 1.024 0 1.518.769 1.518 1.688 0 1.029-.653 2.567-.992 3.992-.285 1.193.6 2.165 1.775 2.165 2.128 0 3.768-2.245 3.768-5.487 0-2.861-2.063-4.869-5.008-4.869-3.41 0-5.409 2.562-5.409 5.199 0 1.033.394 2.143.889 2.741.099.12.112.225.085.345-.09.375-.293 1.199-.334 1.363-.053.225-.172.271-.402.165-1.495-.69-2.433-2.878-2.433-4.646 0-3.776 2.748-7.252 7.92-7.252 4.158 0 7.392 2.967 7.392 6.923 0 4.135-2.607 7.462-6.233 7.462-1.214 0-2.357-.629-2.75-1.378l-.748 2.853c-.271 1.043-1.002 2.35-1.492 3.146C9.57 23.812 10.763 24.009 12.017 24.009c6.624 0 11.99-5.367 11.99-11.988C24.007 5.367 18.641.001.012.001z"/>
                        </svg>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-white transition-colors">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                        </svg>
                    </a>
                </div>
            </div>
            
            <!-- Quick Links -->
            <div>
                <h4 class="font-semibold text-lg mb-6">Enlaces Rápidos</h4>
                <ul class="space-y-3">
                    <li><a href="{{ route('home') }}" class="text-gray-300 hover:text-white transition-colors">Inicio</a></li>
                    <li><a href="#" class="text-gray-300 hover:text-white transition-colors">Partidos en Vivo</a></li>
                    <li><a href="#" class="text-gray-300 hover:text-white transition-colors">Resultados</a></li>
                    <li><a href="#" class="text-gray-300 hover:text-white transition-colors">Estadísticas</a></li>
                    <li><a href="#" class="text-gray-300 hover:text-white transition-colors">Equipos</a></li>
                    <li><a href="#" class="text-gray-300 hover:text-white transition-colors">Jugadoras</a></li>
                </ul>
            </div>
            
            <!-- Contact Info -->
            <div>
                <h4 class="font-semibold text-lg mb-6">Contacto</h4>
                <div class="space-y-3">
                    <div class="flex items-center space-x-3">
                        <svg class="w-5 h-5 text-vp-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span class="text-gray-300">Sucre, Colombia</span>
                    </div>
                    <div class="flex items-center space-x-3">
                        <svg class="w-5 h-5 text-vp-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        <span class="text-gray-300">info@volleypass.co</span>
                    </div>
                    <div class="flex items-center space-x-3">
                        <svg class="w-5 h-5 text-vp-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                        </svg>
                        <span class="text-gray-300">+57 300 123 4567</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Bottom Bar -->
        <div class="border-t border-gray-800 mt-12 pt-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                <div class="text-gray-400 text-sm">
                    © {{ date('Y') }} VolleyPass Software. Todos los derechos reservados.
                </div>
                <div class="mt-4 md:mt-0 flex space-x-6">
                    <a href="#" class="text-gray-400 hover:text-white text-sm transition-colors">Política de Privacidad</a>
                    <a href="#" class="text-gray-400 hover:text-white text-sm transition-colors">Términos de Servicio</a>
                    <a href="#" class="text-gray-400 hover:text-white text-sm transition-colors">Soporte</a>
                </div>
            </div>
        </div>
    </div>
</footer>

//resources/views/pages/admin/dashboard.blade.php
<x-app-layout>
    @section('title', 'Dashboard Administrativo')

    <div class="py-6" x-data="adminDashboard()" x-init="init()">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h1 class="font-display font-bold text-3xl text-gray-900 dark:text-white">
                            Panel Administrativo
                        </h1>
                        <p class="mt-2 text-gray-600 dark:text-gray-400">
                            Gestiona toda la liga desde un solo lugar
                        </p>
                    </div>
                    <div class="mt-4 sm:mt-0 flex space-x-3">
                        <button @click="refreshData()" 
                                class="bg-vp-primary-500 text-white px-4 py-2 rounded-lg hover:bg-vp-primary-600 transition-colors">
                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            Actualizar
                        </button>
                    </div>
                </div>
            </div>

            <!-- Stats Overview -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                @livewire('admin.stats-overview')
            </div>

            <!-- Main Dashboard Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-8">
                    <!-- Live Matches Management -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
                        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="font-semibold text-lg text-gray-900 dark:text-white">
                                Gestión de Partidos en Vivo
                            </h3>
                        </div>
                        <div class="p-6">
                            @livewire('admin.live-matches-management')
                        </div>
                    </div>

                    <!-- Recent Activity -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
                        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="font-semibold text-lg text-gray-900 dark:text-white">
                                Actividad Reciente
                            </h3>
                        </div>
                        <div class="p-6">
                            @livewire('admin.recent-activity')
                        </div>
                    </div>

                    <!-- Tournament Management -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
                        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="font-semibold text-lg text-gray-900 dark:text-white">
                                Gestión de Torneos
                            </h3>
                        </div>
                        <div class="p-6">
                            @livewire('admin.tournament-management')
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-8">
                    <!-- Quick Actions -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
                        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="font-semibold text-lg text-gray-900 dark:text-white">
                                Acciones Rápidas
                            </h3>
                        </div>
                        <div class="p-6">
                            @livewire('admin.quick-actions')
                        </div>
                    </div>

                    <!-- System Status -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
                        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="font-semibold text-lg text-gray-900 dark:text-white">
                                Estado del Sistema
                            </h3>
                        </div>
                        <div class="p-6">
                            @livewire('admin.system-status')
                        </div>
                    </div>

                    <!-- Pending Approvals -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
                        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="font-semibold text-lg text-gray-900 dark:text-white">
                                Aprobaciones Pendientes
                            </h3>
                        </div>
                        <div class="p-6">
                            @livewire('admin.pending-approvals')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function adminDashboard() {
            return {
                refreshInterval: null,
                
                init() {
                    // Auto-refresh every 30 seconds
                    this.refreshInterval = setInterval(() => {
                        if (document.visibilityState === 'visible') {
                            this.refreshData();
                        }
                    }, 30000);
                },
                
                refreshData() {
                    // Trigger refresh on all Livewire components
                    Livewire.dispatch('refresh-dashboard');
                    
                    // Show notification
                    this.$store.notifications.add({
                        type: 'success',
                        title: 'Datos actualizados',
                        message: 'La información del dashboard ha sido actualizada.'
                    });
                }
            }
        }
    </script>
</x-app-layout>


//resources/views/pages/coach/dashboard.blade.php
<x-app-layout>
    @section('title', 'Dashboard - Entrenador')

    <div class="py-6" x-data="coachDashboard()" x-init="init()">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h1 class="font-display font-bold text-3xl text-gray-900 dark:text-white">
                            Panel de Entrenador
                        </h1>
                        <p class="mt-2 text-gray-600 dark:text-gray-400">
                            Gestiona tu equipo y jugadoras
                        </p>
                    </div>
                    <div class="mt-4 sm:mt-0 flex space-x-3">
                        <button @click="openTeamSettings()" 
                                class="bg-vp-secondary-500 text-white px-4 py-2 rounded-lg hover:bg-vp-secondary-600 transition-colors">
                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            Configurar Equipo
                        </button>
                    </div>
                </div>
            </div>

            <!-- Team Overview -->
            <div class="bg-gradient-to-r from-vp-secondary-500 to-vp-primary-500 rounded-xl shadow-lg overflow-hidden mb-8">
                @livewire('coach.team-overview')
            </div>

            <!-- Dashboard Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-8">
                    <!-- Team Schedule -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
                        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="font-semibold text-lg text-gray-900 dark:text-white">
                                Calendario del Equipo
                            </h3>
                        </div>
                        <div class="p-6">
                            @livewire('coach.team-schedule')
                        </div>
                    </div>

                    <!-- Player Management -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
                        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                            <div class="flex items-center justify-between">
                                <h3 class="font-semibold text-lg text-gray-900 dark:text-white">
                                    Gestión de Jugadoras
                                </h3>
                                <button @click="addPlayer()" 
                                        class="bg-vp-primary-500 text-white px-4 py-2 rounded-lg hover:bg-vp-primary-600 transition-colors text-sm">
                                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                    Agregar Jugadora
                                </button>
                            </div>
                        </div>
                        <div class="p-6">
                            @livewire('coach.player-management')
                        </div>
                    </div>

                    <!-- Team Statistics -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
                        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="font-semibold text-lg text-gray-900 dark:text-white">
                                Estadísticas del Equipo
                            </h3>
                        </div>
                        <div class="p-6">
                            @livewire('coach.team-statistics')
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-8">
                    <!-- Quick Actions -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
                        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="font-semibold text-lg text-gray-900 dark:text-white">
                                Acciones Rápidas
                            </h3>
                        </div>
                        <div class="p-6 space-y-3">
                            <button @click="scheduleTraining()" 
                                    class="w-full bg-vp-primary-500 text-white px-4 py-3 rounded-lg hover:bg-vp-primary-600 transition-colors text-left">
                                <svg class="w-5 h-5 inline mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                Programar Entrenamiento
                            </button>
                            <button @click="createLineup()" 
                                    class="w-full bg-vp-secondary-500 text-white px-4 py-3 rounded-lg hover:bg-vp-secondary-600 transition-colors text-left">
                                <svg class="w-5 h-5 inline mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                                Crear Alineación
                            </button>
                            <button @click="sendMessage()" 
                                    class="w-full bg-green-500 text-white px-4 py-3 rounded-lg hover:bg-green-600 transition-colors text-left">
                                <svg class="w-5 h-5 inline mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                </svg>
                                Mensaje al Equipo
                            </button>
                        </div>
                    </div>

                    <!-- Team Health Status -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
                        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="font-semibold text-lg text-gray-900 dark:text-white">
                                Estado de Salud del Equipo
                            </h3>
                        </div>
                        <div class="p-6">
                            @livewire('coach.team-health-status')
                        </div>
                    </div>

                    <!-- Recent Notifications -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
                        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="font-semibold text-lg text-gray-900 dark:text-white">
                                Notificaciones
                            </h3>
                        </div>
                        <div class="p-6">
                            @livewire('coach.notifications')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function coachDashboard() {
            return {
                init() {
                    console.log('Coach dashboard initialized');
                },
                
                openTeamSettings() {
                    Livewire.dispatch('open-team-settings');
                },
                
                addPlayer() {
                    Livewire.dispatch('open-add-player-modal');
                },
                
                scheduleTraining() {
                    Livewire.dispatch('open-training-modal');
                },
                
                createLineup() {
                    window.location.href = '/coach/lineup';
                },
                
                sendMessage() {
                    Livewire.dispatch('open-message-modal');
                }
            }
        }
    </script>
</x-app-layout>

//resources/views/pages/medical/dashboard.blade.php
<x-app-layout>
    @section('title', 'Dashboard - Médico Deportivo')

    <div class="py-6" x-data="medicalDashboard()" x-init="init()">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h1 class="font-display font-bold text-3xl text-gray-900 dark:text-white">
                            Panel Médico Deportivo
                        </h1>
                        <p class="mt-2 text-gray-600 dark:text-gray-400">
                            Monitorea la salud y bienestar de las jugadoras
                        </p>
                    </div>
                    <div class="mt-4 sm:mt-0 flex space-x-3">
                        <button @click="emergencyProtocol()" 
                                class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition-colors">
                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
                            </svg>
                            Protocolo de Emergencia
                        </button>
                    </div>
                </div>
            </div>

            <!-- Medical Stats -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                @livewire('medical.stats-overview')
            </div>

            <!-- Dashboard Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-8">
                    <!-- Active Injuries -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
                        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                            <div class="flex items-center justify-between">
                                <h3 class="font-semibold text-lg text-gray-900 dark:text-white">
                                    Lesiones Activas
                                </h3>
                                <span class="bg-red-100 text-red-800 px-3 py-1 rounded-full text-sm font-medium">
                                    {{ auth()->user()->activeInjuries()->count() }} casos
                                </span>
                            </div>
                        </div>
                        <div class="p-6">
                            @livewire('medical.active-injuries')
                        </div>
                    </div>

                    <!-- Medical Reports -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
                        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                            <div class="flex items-center justify-between">
                                <h3 class="font-semibold text-lg text-gray-900 dark:text-white">
                                    Reportes Médicos
                                </h3>
                                <button @click="createReport()" 
                                        class="bg-vp-primary-500 text-white px-4 py-2 rounded-lg hover:bg-vp-primary-600 transition-colors text-sm">
                                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                    Nuevo Reporte
                                </button>
                            </div>
                        </div>
                        <div class="p-6">
                            @livewire('medical.medical-reports')
                        </div>
                    </div>

                    <!-- Player Health Monitoring -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
                        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="font-semibold text-lg text-gray-900 dark:text-white">
                                Monitoreo de Salud de Jugadoras
                            </h3>
                        </div>
                        <div class="p-6">
                            @livewire('medical.player-health-monitoring')
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-8">
                    <!-- Quick Actions -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
                        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="font-semibold text-lg text-gray-900 dark:text-white">
                                Acciones Rápidas
                            </h3>
                        </div>
                        <div class="p-6 space-y-3">
                            <button @click="recordInjury()" 
                                    class="w-full bg-red-500 text-white px-4 py-3 rounded-lg hover:bg-red-600 transition-colors text-left">
                                <svg class="w-5 h-5 inline mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                </svg>
                                Registrar Lesión
                            </button>
                            <button @click="clearPlayer()" 
                                    class="w-full bg-green-500 text-white px-4 py-3 rounded-lg hover:bg-green-600 transition-colors text-left">
                                <svg class="w-5 h-5 inline mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Dar Alta Médica
                            </button>
                            <button @click="scheduleCheckup()" 
                                    class="w-full bg-vp-secondary-500 text-white px-4 py-3 rounded-lg hover:bg-vp-secondary-600 transition-colors text-left">
                                <svg class="w-5 h-5 inline mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                Programar Revisión
                            </button>
                        </div>
                    </div>

                    <!-- Emergency Contacts -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
                        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="font-semibold text-lg text-gray-900 dark:text-white">
                                Contactos de Emergencia
                            </h3>
                        </div>
                        <div class="p-6">
                            @livewire('medical.emergency-contacts')
                        </div>
                    </div>

                    <!-- Medical Alerts -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
                        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="font-semibold text-lg text-gray-900 dark:text-white">
                                Alertas Médicas
                            </h3>
                        </div>
                        <div class="p-6">
                            @livewire('medical.medical-alerts')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function medicalDashboard() {
            return {
                init() {
                    console.log('Medical dashboard initialized');
                },
                
                emergencyProtocol() {
                    Livewire.dispatch('activate-emergency-protocol');
                },
                
                createReport() {
                    Livewire.dispatch('open-medical-report-modal');
                },
                
                recordInjury() {
                    Livewire.dispatch('open-injury-record-modal');
                },
                
                clearPlayer() {
                    Livewire.dispatch('open-medical-clearance-modal');
                },
                
                scheduleCheckup() {
                    Livewire.dispatch('open-checkup-schedule-modal');
                }
            }
        }
    </script>
</x-app-layout>

//resources/views/pages/player/dashboard.blade.php
<x-app-layout>
    @section('title', 'Mi Dashboard - Jugadora')

    <div class="py-6" x-data="playerDashboard()" x-init="init()">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Player Profile Header -->
            <div class="bg-gradient-to-r from-vp-primary-500 to-vp-secondary-500 rounded-xl shadow-lg overflow-hidden mb-8">
                @livewire('player.profile-header')
            </div>

            <!-- Dashboard Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-8">
                    <!-- Upcoming Matches -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
                        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                            <div class="flex items-center justify-between">
                                <h3 class="font-semibold text-lg text-gray-900 dark:text-white">
                                    Próximos Partidos
                                </h3>
                                <span class="bg-vp-primary-100 text-vp-primary-800 px-3 py-1 rounded-full text-sm font-medium">
                                    {{ auth()->user()->upcomingMatches()->count() }} partidos
                                </span>
                            </div>
                        </div>
                        <div class="p-6">
                            @livewire('player.upcoming-matches')
                        </div>
                    </div>

                    <!-- Performance Stats -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
                        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="font-semibold text-lg text-gray-900 dark:text-white">
                                Estadísticas de Rendimiento
                            </h3>
                        </div>
                        <div class="p-6">
                            @livewire('player.performance-stats')
                        </div>
                    </div>

                    <!-- Recent Activity -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
                        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="font-semibold text-lg text-gray-900 dark:text-white">
                                Actividad Reciente
                            </h3>
                        </div>
                        <div class="p-6">
                            @livewire('player.recent-activity')
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-8">
                    <!-- Quick Actions -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
                        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="font-semibold text-lg text-gray-900 dark:text-white">
                                Acciones Rápidas
                            </h3>
                        </div>
                        <div class="p-6 space-y-3">
                            <button @click="updateAvailability()" 
                                    class="w-full bg-vp-primary-500 text-white px-4 py-3 rounded-lg hover:bg-vp-primary-600 transition-colors text-left">
                                <svg class="w-5 h-5 inline mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                Actualizar Disponibilidad
                            </button>
                            <button @click="viewTeammates()" 
                                    class="w-full bg-vp-secondary-500 text-white px-4 py-3 rounded-lg hover:bg-vp-secondary-600 transition-colors text-left">
                                <svg class="w-5 h-5 inline mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                Ver Compañeras de Equipo
                            </button>
                            <button @click="reportInjury()" 
                                    class="w-full bg-red-500 text-white px-4 py-3 rounded-lg hover:bg-red-600 transition-colors text-left">
                                <svg class="w-5 h-5 inline mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                </svg>
                                Reportar Lesión
                            </button>
                        </div>
                    </div>

                    <!-- Medical Status -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
                        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="font-semibold text-lg text-gray-900 dark:text-white">
                                Estado Médico
                            </h3>
                        </div>
                        <div class="p-6">
                            @livewire('player.medical-status')
                        </div>
                    </div>

                    <!-- Team Information -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
                        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="font-semibold text-lg text-gray-900 dark:text-white">
                                Mi Equipo
                            </h3>
                        </div>
                        <div class="p-6">
                            @livewire('player.team-info')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function playerDashboard() {
            return {
                init() {
                    // Initialize player dashboard
                    console.log('Player dashboard initialized');
                },
                
                updateAvailability() {
                    // Open availability modal
                    Livewire.dispatch('open-availability-modal');
                },
                
                viewTeammates() {
                    // Navigate to teammates page
                    window.location.href = '/player/teammates';
                },
                
                reportInjury() {
                    // Open injury report modal
                    Livewire.dispatch('open-injury-modal');
                }
            }
        }
    </script>
</x-app-layout>

//resources/views/pages/referee/dashboard.blade.php
<x-app-layout>
    @section('title', 'Dashboard - Árbitro')

    <div class="py-6" x-data="refereeDashboard()" x-init="init()">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h1 class="font-display font-bold text-3xl text-gray-900 dark:text-white">
                            Panel de Árbitro
                        </h1>
                        <p class="mt-2 text-gray-600 dark:text-gray-400">
                            Gestiona tus asignaciones y reportes de partidos
                        </p>
                    </div>
                    <div class="mt-4 sm:mt-0 flex space-x-3">
                        <button @click="updateAvailability()" 
                                class="bg-vp-primary-500 text-white px-4 py-2 rounded-lg hover:bg-vp-primary-600 transition-colors">
                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            Actualizar Disponibilidad
                        </button>
                    </div>
                </div>
            </div>

            <!-- Referee Stats -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                @livewire('referee.stats-overview')
            </div>

            <!-- Dashboard Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-8">
                    <!-- Assigned Matches -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
                        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                            <div class="flex items-center justify-between">
                                <h3 class="font-semibold text-lg text-gray-900 dark:text-white">
                                    Partidos Asignados
                                </h3>
                                <span class="bg-vp-primary-100 text-vp-primary-800 px-3 py-1 rounded-full text-sm font-medium">
                                    {{ auth()->user()->assignedMatches()->upcoming()->count() }} próximos
                                </span>
                            </div>
                        </div>
                        <div class="p-6">
                            @livewire('referee.assigned-matches')
                        </div>
                    </div>

                    <!-- Match Reports -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
                        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                            <div class="flex items-center justify-between">
                                <h3 class="font-semibold text-lg text-gray-900 dark:text-white">
                                    Reportes de Partidos
                                </h3>
                                <button @click="createReport()" 
                                        class="bg-vp-secondary-500 text-white px-4 py-2 rounded-lg hover:bg-vp-secondary-600 transition-colors text-sm">
                                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                    Nuevo Reporte
                                </button>
                            </div>
                        </div>
                        <div class="p-6">
                            @livewire('referee.match-reports')
                        </div>
                    </div>

                    <!-- Performance History -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
                        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="font-semibold text-lg text-gray-900 dark:text-white">
                                Historial de Rendimiento
                            </h3>
                        </div>
                        <div class="p-6">
                            @livewire('referee.performance-history')
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-8">
                    <!-- Quick Actions -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
                        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="font-semibold text-lg text-gray-900 dark:text-white">
                                Acciones Rápidas
                            </h3>
                        </div>
                        <div class="p-6 space-y-3">
                            <button @click="startMatch()" 
                                    class="w-full bg-green-500 text-white px-4 py-3 rounded-lg hover:bg-green-600 transition-colors text-left">
                                <svg class="w-5 h-5 inline mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h8m-5-9V3m0 0V1m0 2h4M7 21h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                Iniciar Partido
                            </button>
                            <button @click="submitScore()" 
                                    class="w-full bg-vp-primary-500 text-white px-4 py-3 rounded-lg hover:bg-vp-primary-600 transition-colors text-left">
                                <svg class="w-5 h-5 inline mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                </svg>
                                Registrar Marcador
                            </button>
                            <button @click="reportIncident()" 
                                    class="w-full bg-red-500 text-white px-4 py-3 rounded-lg hover:bg-red-600 transition-colors text-left">
                                <svg class="w-5 h-5 inline mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                </svg>
                                Reportar Incidente
                            </button>
                        </div>
                    </div>

                    <!-- Certification Status -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
                        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="font-semibold text-lg text-gray-900 dark:text-white">
                                Estado de Certificación
                            </h3>
                        </div>
                        <div class="p-6">
                            @livewire('referee.certification-status')
                        </div>
                    </div>

                    <!-- Recent Assignments -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
                        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="font-semibold text-lg text-gray-900 dark:text-white">
                                Asignaciones Recientes
                            </h3>
                        </div>
                        <div class="p-6">
                            @livewire('referee.recent-assignments')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function refereeDashboard() {
            return {
                init() {
                    console.log('Referee dashboard initialized');
                },
                
                updateAvailability() {
                    Livewire.dispatch('open-availability-modal');
                },
                
                createReport() {
                    Livewire.dispatch('open-report-modal');
                },
                
                startMatch() {
                    Livewire.dispatch('open-match-control');
                },
                
                submitScore() {
                    Livewire.dispatch('open-score-modal');
                },
                
                reportIncident() {
                    Livewire.dispatch('open-incident-modal');
                }
            }
        }
    </script>
</x-app-layout>
