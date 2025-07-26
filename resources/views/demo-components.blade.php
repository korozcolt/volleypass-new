<!DOCTYPE html>
<html lang="es" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="VolleyPass - Demo de Componentes UI Deportivos">
    <title>VolleyPass - Demo Componentes</title>

    <!-- Critical CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Preload fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap"
        rel="stylesheet">
</head>

<body class="bg-gradient-to-br from-gray-50 via-white to-gray-100 font-sans antialiased">
    <!-- Skip to main content -->
    <a href="#main-content"
        class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 bg-primary-600 text-white px-4 py-2 rounded-lg z-50">
        Saltar al contenido principal
    </a>

    <!-- Sports Header -->
    <header class="sports-header" role="banner">
        <!-- Top Bar -->
        <div class="sports-header__top-bar">
            <div class="sports-header__container">
                <div class="sports-header__live-scores">
                    <div class="sports-header__live-score">
                        <span class="sports-header__live-dot"></span>
                        <span class="sports-header__score-text">EN VIVO</span>
                    </div>
                    <div class="sports-header__live-score">
                        <span class="sports-header__score-text">Colombia vs Brasil 2-1</span>
                    </div>
                    <div class="sports-header__live-score">
                        <span class="sports-header__score-text">Argentina vs Chile 0-0</span>
                    </div>
                </div>
                <div class="sports-header__quick-actions">
                    <button class="sports-header__action" aria-label="Cambiar idioma">
                        ES
                    </button>
                </div>
            </div>
        </div>

        <!-- Main Navigation -->
        <nav class="sports-header__main" role="navigation" aria-label="Navegación principal">
            <div class="sports-header__container">
                <a href="/" class="sports-header__brand">
                    <div class="sports-header__logo">
                        <svg width="32" height="32" viewBox="0 0 32 32" fill="none">
                            <circle cx="16" cy="16" r="14" fill="currentColor" opacity="0.1" />
                            <circle cx="16" cy="16" r="8" fill="currentColor" />
                        </svg>
                    </div>
                    <div class="sports-header__brand-text">
                        <span class="sports-header__brand-name">VolleyPass</span>
                        <span class="sports-header__brand-tagline">Sistema Deportivo</span>
                    </div>
                </a>

                <div class="sports-header__nav">
                    <a href="/" class="sports-header__nav-link sports-header__nav-link--active">Inicio</a>
                    <a href="/torneos" class="sports-header__nav-link">Torneos</a>
                    <a href="/equipos" class="sports-header__nav-link">Equipos</a>
                    <a href="/jugadores" class="sports-header__nav-link">Jugadores</a>
                    <a href="/estadisticas" class="sports-header__nav-link">Estadísticas</a>
                </div>

                <div class="sports-header__actions">
                    <div class="sports-header__search">
                        <input type="search" placeholder="Buscar..." class="sports-header__search-input">
                        <svg class="sports-header__search-icon" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <button class="sports-header__action" aria-label="Notificaciones">
                        <svg class="sports-header__notification-icon" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5z">
                            </path>
                        </svg>
                        <span class="sports-header__notification-badge">3</span>
                    </button>
                    <div class="sports-header__user">
                        <div class="sports-header__avatar">
                            <span>U</span>
                        </div>
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <!-- Sports Hero -->
    <section class="sports-hero relative overflow-hidden" role="banner">
        <div class="sports-hero__background">
            <div class="sports-hero__bg-overlay"></div>
        </div>
        <!-- Decorative gradient overlay -->
        <div
            class="absolute inset-0 bg-gradient-to-r from-blue-600/10 via-transparent to-yellow-500/10 pointer-events-none">
        </div>

        <div class="sports-hero__container">
            <div class="sports-hero__content">
                <div class="sports-hero__badge">
                    <span class="live-indicator">DEMOSTRACIÓN DE COMPONENTES</span>
                </div>

                <h1 class="sports-hero__title">
                    Sistema de UI Deportiva
                    <span
                        class="sports-hero__subtitle text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-yellow-500">VolleyPass</span>
                </h1>

                <p class="sports-hero__description">
                    Explora nuestros componentes diseñados específicamente para aplicaciones deportivas, con un enfoque
                    en la experiencia del usuario y la accesibilidad.
                </p>

                <div class="sports-hero__stats">
                    <div class="sports-hero__stat">
                        <span class="sports-hero__stat-number">3</span>
                        <span class="sports-hero__stat-label">Componentes</span>
                    </div>
                    <div class="sports-hero__stat">
                        <span class="sports-hero__stat-number">100%</span>
                        <span class="sports-hero__stat-label">Responsive</span>
                    </div>
                    <div class="sports-hero__stat">
                        <span class="sports-hero__stat-number">A11Y</span>
                        <span class="sports-hero__stat-label">Accesible</span>
                    </div>
                </div>

                <div class="sports-hero__actions">
                    <button class="btn-sports">
                        Ver Documentación
                    </button>
                    <button class="btn-sports btn-sports--secondary">
                        Descargar Componentes
                    </button>
                </div>
            </div>

            <div class="sports-hero__sidebar">
                <div class="sports-hero__upcoming">
                    <h3 class="sports-hero__sidebar-title">Componentes Incluidos</h3>
                    <div class="sports-hero__match-list">
                        <div class="sports-hero__match">
                            <div class="sports-hero__match-teams">
                                <span class="team-home">Tarjetas de Torneos</span>
                                <span class="sports-hero__vs">✓</span>
                                <span class="team-away">Listo</span>
                            </div>
                        </div>
                        <div class="sports-hero__match">
                            <div class="sports-hero__match-teams">
                                <span class="team-home">Marcadores en Vivo</span>
                                <span class="sports-hero__vs">✓</span>
                                <span class="team-away">Listo</span>
                            </div>
                        </div>
                        <div class="sports-hero__match">
                            <div class="sports-hero__match-teams">
                                <span class="team-home">Tabla de Posiciones</span>
                                <span class="sports-hero__vs">✓</span>
                                <span class="team-away">Listo</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="sports-hero__live-scores">
                    <h3 class="sports-hero__sidebar-title">Características</h3>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between p-3 bg-white/10 rounded-lg backdrop-blur-sm">
                            <span class="text-white/90 text-sm">Responsive Design</span>
                            <span class="text-green-400 font-bold">✓</span>
                        </div>
                        <div class="flex items-center justify-between p-3 bg-white/10 rounded-lg backdrop-blur-sm">
                            <span class="text-white/90 text-sm">Accesibilidad WCAG</span>
                            <span class="text-green-400 font-bold">✓</span>
                        </div>
                        <div class="flex items-center justify-between p-3 bg-white/10 rounded-lg backdrop-blur-sm">
                            <span class="text-white/90 text-sm">Optimizado</span>
                            <span class="text-green-400 font-bold">✓</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <main id="main-content" class="py-20" role="main">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Demo Components Showcase -->
            <div class="space-y-20">
                <!-- Torneos Section -->
                <section class="mb-12" aria-labelledby="tournaments-heading">
                    <h2 id="tournaments-heading" class="text-3xl font-bold text-gray-900 mb-8 text-center">
                        Torneos Destacados
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                        <!-- Tournament Card 1 -->
                        <article class="tournament-card tournament-card--featured">
                            <div class="tournament-card__header">
                                <div class="tournament-card__status">
                                    <span class="tournament-card__badge tournament-card__badge--live">EN VIVO</span>
                                </div>
                                <div class="tournament-card__image">
                                    <img src="/images/tournament-1.jpg" alt="Copa Nacional"
                                        class="tournament-card__img">
                                </div>
                            </div>

                            <div class="tournament-card__content">
                                <h3 class="tournament-card__title">Copa Nacional de Voleibol</h3>
                                <p class="tournament-card__description">El torneo más importante del país con los
                                    mejores equipos.</p>

                                <div class="tournament-card__stats">
                                    <div class="tournament-card__stat">
                                        <span class="tournament-card__stat-number">24</span>
                                        <span class="tournament-card__stat-label">Equipos</span>
                                    </div>
                                    <div class="tournament-card__stat">
                                        <span class="tournament-card__stat-number">8</span>
                                        <span class="tournament-card__stat-label">Ciudades</span>
                                    </div>
                                </div>

                                <div class="tournament-card__progress">
                                    <div class="tournament-card__progress-bar">
                                        <div class="tournament-card__progress-fill" style="width: 65%"></div>
                                    </div>
                                    <span class="tournament-card__progress-text">65% Completado</span>
                                </div>
                            </div>

                            <div class="tournament-card__footer">
                                <button class="tournament-card__action">Ver Detalles</button>
                            </div>
                        </article>

                        <!-- Tournament Card 2 -->
                        <article class="tournament-card">
                            <div class="tournament-card__header">
                                <div class="tournament-card__status">
                                    <span
                                        class="tournament-card__badge tournament-card__badge--upcoming">PRÓXIMO</span>
                                </div>
                                <div class="tournament-card__image">
                                    <img src="/images/tournament-2.jpg" alt="Liga Regional"
                                        class="tournament-card__img">
                                </div>
                            </div>

                            <div class="tournament-card__content">
                                <h3 class="tournament-card__title">Liga Regional Pacífico</h3>
                                <p class="tournament-card__description">Competencia regional de la costa pacífica
                                    colombiana.</p>

                                <div class="tournament-card__stats">
                                    <div class="tournament-card__stat">
                                        <span class="tournament-card__stat-number">12</span>
                                        <span class="tournament-card__stat-label">Equipos</span>
                                    </div>
                                    <div class="tournament-card__stat">
                                        <span class="tournament-card__stat-number">4</span>
                                        <span class="tournament-card__stat-label">Ciudades</span>
                                    </div>
                                </div>

                                <div class="tournament-card__date">
                                    <span class="tournament-card__date-label">Inicia:</span>
                                    <span class="tournament-card__date-value">15 Marzo 2024</span>
                                </div>
                            </div>

                            <div class="tournament-card__footer">
                                <button class="tournament-card__action tournament-card__action--secondary">Más
                                    Información</button>
                            </div>
                        </article>

                        <!-- Tournament Card 3 -->
                        <article class="tournament-card">
                            <div class="tournament-card__header">
                                <div class="tournament-card__status">
                                    <span
                                        class="tournament-card__badge tournament-card__badge--finished">FINALIZADO</span>
                                </div>
                                <div class="tournament-card__image">
                                    <img src="/images/tournament-3.jpg" alt="Copa Juvenil"
                                        class="tournament-card__img">
                                </div>
                            </div>

                            <div class="tournament-card__content">
                                <h3 class="tournament-card__title">Copa Juvenil Nacional</h3>
                                <p class="tournament-card__description">Torneo para promesas del voleibol colombiano.
                                </p>

                                <div class="tournament-card__winner">
                                    <span class="tournament-card__winner-label">Campeón:</span>
                                    <span class="tournament-card__winner-team">Bogotá Juvenil</span>
                                </div>

                                <div class="tournament-card__stats">
                                    <div class="tournament-card__stat">
                                        <span class="tournament-card__stat-number">16</span>
                                        <span class="tournament-card__stat-label">Equipos</span>
                                    </div>
                                    <div class="tournament-card__stat">
                                        <span class="tournament-card__stat-number">45</span>
                                        <span class="tournament-card__stat-label">Partidos</span>
                                    </div>
                                </div>
                            </div>

                            <div class="tournament-card__footer">
                                <button class="tournament-card__action tournament-card__action--secondary">Ver
                                    Resultados</button>
                            </div>
                        </article>
                    </div>
                </section>

            </div>
            </section>

            <!-- Marcadores Section -->
            <section class="mb-12 py-16 bg-gray-100" aria-labelledby="live-scores-heading">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <h2 id="live-scores-heading" class="text-3xl font-bold text-gray-900 mb-8 text-center">
                        Marcadores en Vivo
                    </h2>
                    <!-- Live Match -->
                    <div class="live-scoreboard live-scoreboard--entering">
                        <div class="live-scoreboard__header">
                            <span class="live-scoreboard__tournament">Copa Nacional - Semifinales</span>
                            <div class="live-scoreboard__status">
                                <div class="live-scoreboard__status-dot"></div>
                                <span>Set 3 - 18:42</span>
                            </div>
                        </div>

                        <div class="live-scoreboard__match">
                            <div class="live-scoreboard__team">
                                <div class="live-scoreboard__team-logo">BV</div>
                                <div class="live-scoreboard__team-info">
                                    <div class="live-scoreboard__team-name">Bogotá Volley</div>
                                    <div class="live-scoreboard__team-record">12-2 (Temporada)</div>
                                </div>
                            </div>

                            <div class="live-scoreboard__score">
                                <div class="live-scoreboard__score-main">
                                    <span class="live-scoreboard__score-home">21</span>
                                    <span class="live-scoreboard__score-separator">-</span>
                                    <span class="live-scoreboard__score-away">18</span>
                                </div>
                                <div class="live-scoreboard__set-score">Sets: 2-1</div>
                            </div>

                            <div class="live-scoreboard__team live-scoreboard__team--away">
                                <div class="live-scoreboard__team-info">
                                    <div class="live-scoreboard__team-name">Medellín Eagles</div>
                                    <div class="live-scoreboard__team-record">10-4 (Temporada)</div>
                                </div>
                                <div class="live-scoreboard__team-logo">ME</div>
                            </div>
                        </div>

                        <div class="live-scoreboard__sets">
                            <div class="live-scoreboard__set live-scoreboard__set--finished">
                                <div class="live-scoreboard__set-label">Set 1</div>
                                <div class="live-scoreboard__set-scores">
                                    <span>25</span>
                                    <span class="live-scoreboard__set-separator">-</span>
                                    <span>23</span>
                                </div>
                            </div>
                            <div class="live-scoreboard__set live-scoreboard__set--finished">
                                <div class="live-scoreboard__set-label">Set 2</div>
                                <div class="live-scoreboard__set-scores">
                                    <span>22</span>
                                    <span class="live-scoreboard__set-separator">-</span>
                                    <span>25</span>
                                </div>
                            </div>
                            <div class="live-scoreboard__set live-scoreboard__set--current">
                                <div class="live-scoreboard__set-label">Set 3</div>
                                <div class="live-scoreboard__set-scores">
                                    <span>21</span>
                                    <span class="live-scoreboard__set-separator">-</span>
                                    <span>18</span>
                                </div>
                            </div>
                        </div>

                        <div class="live-scoreboard__actions">
                            <a href="#" class="live-scoreboard__action live-scoreboard__action--primary">
                                <svg class="live-scoreboard__action-icon" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z">
                                    </path>
                                </svg>
                                Ver en Vivo
                            </a>
                            <a href="#" class="live-scoreboard__action">
                                <svg class="live-scoreboard__action-icon" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                                    </path>
                                </svg>
                                Estadísticas
                            </a>
                        </div>
                    </div>

                    <!-- Upcoming Match -->
                    <div class="live-scoreboard live-scoreboard--upcoming" style="margin-top: 2rem;">
                        <div class="live-scoreboard__header">
                            <span class="live-scoreboard__tournament">Copa Nacional - Semifinales</span>
                            <div class="live-scoreboard__status">
                                <div class="live-scoreboard__status-dot"></div>
                                <span>19:00</span>
                            </div>
                        </div>

                        <div class="live-scoreboard__match">
                            <div class="live-scoreboard__team">
                                <div class="live-scoreboard__team-logo">CT</div>
                                <div class="live-scoreboard__team-info">
                                    <div class="live-scoreboard__team-name">Cali Titans</div>
                                    <div class="live-scoreboard__team-record">8-6 (Temporada)</div>
                                </div>
                            </div>

                            <div class="live-scoreboard__score">
                                <div class="live-scoreboard__score-main">
                                    <span class="live-scoreboard__score-home">-</span>
                                    <span class="live-scoreboard__score-separator">vs</span>
                                    <span class="live-scoreboard__score-away">-</span>
                                </div>
                                <div class="live-scoreboard__set-score">Polideportivo Sur, Cali</div>
                            </div>

                            <div class="live-scoreboard__team live-scoreboard__team--away">
                                <div class="live-scoreboard__team-info">
                                    <div class="live-scoreboard__team-name">Atlántico Storm</div>
                                    <div class="live-scoreboard__team-record">7-7 (Temporada)</div>
                                </div>
                                <div class="live-scoreboard__team-logo">AS</div>
                            </div>
                        </div>

                        <div class="live-scoreboard__actions">
                            <a href="#" class="live-scoreboard__action live-scoreboard__action--primary">
                                <svg class="live-scoreboard__action-icon" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Recordatorio
                            </a>
                            <a href="#" class="live-scoreboard__action">
                                <svg class="live-scoreboard__action-icon" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Previa
                            </a>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Tabla Section -->
            <!-- Standings Table Section -->
            <section class="mb-12 py-16 bg-white" aria-labelledby="standings-heading" x-data="{ selectedGroup: 'todos', searchTerm: '' }">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

                    <!-- Section Header -->
                    <div class="text-center mb-8">
                        <h2 id="standings-heading" class="text-3xl font-bold text-gray-900 mb-2">
                            Tabla de Posiciones
                        </h2>
                        <p class="text-lg text-gray-600">Copa Departamental Sucre 2024 - Jornada 7</p>
                    </div>

                    <!-- Controls Panel -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6">
                        <div class="p-6 border-b border-gray-200">
                            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">

                                <!-- Tournament Info -->
                                <div class="flex items-center space-x-3">
                                    <div
                                        class="w-12 h-12 bg-gradient-to-br from-blue-600 to-blue-700 rounded-xl flex items-center justify-center">
                                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path
                                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-bold text-gray-900">Copa Departamental</h3>
                                        <p class="text-sm text-gray-600">Actualizado hace 2 minutos</p>
                                    </div>
                                </div>

                                <!-- Search and Filters -->
                                <div class="flex flex-col sm:flex-row gap-3">
                                    <!-- Search -->
                                    <div class="relative">
                                        <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                        </svg>
                                        <input type="text" placeholder="Buscar equipo..." x-model="searchTerm"
                                            class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 w-full sm:w-64" />
                                    </div>

                                    <!-- Group Filter -->
                                    <select x-model="selectedGroup"
                                        class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        <option value="todos">Todos los Grupos</option>
                                        <option value="A">Grupo A</option>
                                        <option value="B">Grupo B</option>
                                    </select>

                                    <!-- Export Button -->
                                    <button
                                        class="flex items-center space-x-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <span class="hidden sm:inline">Exportar</span>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Legend -->
                        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                            <div class="flex flex-wrap items-center gap-4 text-sm">
                                <div class="flex items-center space-x-2">
                                    <div class="w-4 h-4 bg-gradient-to-r from-green-500 to-green-600 rounded"></div>
                                    <span class="text-gray-700">Clasificados (1-2)</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <div class="w-4 h-4 bg-gradient-to-r from-yellow-500 to-orange-500 rounded"></div>
                                    <span class="text-gray-700">Playoff (3-4)</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <div class="w-4 h-4 bg-gradient-to-r from-red-500 to-red-600 rounded"></div>
                                    <span class="text-gray-700">Eliminados (7-8)</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Standings Table -->
                    <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gray-50 border-b border-gray-200">
                                    <tr>
                                        <th
                                            class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                            Pos</th>
                                        <th
                                            class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider min-w-[250px]">
                                            Equipo</th>
                                        <th
                                            class="px-4 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                            PJ</th>
                                        <th
                                            class="px-4 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                            G</th>
                                        <th
                                            class="px-4 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                            P</th>
                                        <th
                                            class="px-4 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                            Sets</th>
                                        <th
                                            class="px-4 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                            Pts</th>
                                        <th
                                            class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                            Últimos 5</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">

                                    <!-- 1st Place - Qualified -->
                                    <tr class="hover:bg-gray-50 transition-colors group">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center space-x-3">
                                                <div
                                                    class="w-8 h-8 bg-gradient-to-r from-green-500 to-green-600 rounded-full flex items-center justify-center text-white font-bold text-sm">
                                                    1</div>
                                                <div
                                                    class="w-1 h-8 bg-gradient-to-b from-green-500 to-green-600 rounded-full">
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center space-x-4">
                                                <div
                                                    class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center text-white font-bold shadow-sm">
                                                    AG
                                                </div>
                                                <div>
                                                    <div
                                                        class="font-semibold text-gray-900 group-hover:text-blue-600 transition-colors">
                                                        Águilas Doradas</div>
                                                    <div class="text-sm text-gray-500">Sincelejo FC</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-4 text-center text-sm font-medium text-gray-900">14</td>
                                        <td class="px-4 py-4 text-center text-sm font-medium text-green-600">12</td>
                                        <td class="px-4 py-4 text-center text-sm font-medium text-red-600">2</td>
                                        <td class="px-4 py-4 text-center text-sm text-gray-600">37-15</td>
                                        <td class="px-4 py-4 text-center">
                                            <span class="text-lg font-bold text-gray-900">36</span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex space-x-1 justify-center">
                                                <div class="w-6 h-6 bg-green-500 rounded flex items-center justify-center text-white text-xs font-medium"
                                                    title="Victoria">G</div>
                                                <div class="w-6 h-6 bg-green-500 rounded flex items-center justify-center text-white text-xs font-medium"
                                                    title="Victoria">G</div>
                                                <div class="w-6 h-6 bg-green-500 rounded flex items-center justify-center text-white text-xs font-medium"
                                                    title="Victoria">G</div>
                                        <td class="px-6 py-4">
                                            <div class="flex space-x-1 justify-center">
                                                <div class="w-6 h-6 bg-red-500 rounded flex items-center justify-center text-white text-xs font-medium"
                                                    title="Derrota">P</div>
                                                <div class="w-6 h-6 bg-red-500 rounded flex items-center justify-center text-white text-xs font-medium"
                                                    title="Derrota">P</div>
                                                <div class="w-6 h-6 bg-red-500 rounded flex items-center justify-center text-white text-xs font-medium"
                                                    title="Derrota">P</div>
                                                <div class="w-6 h-6 bg-green-500 rounded flex items-center justify-center text-white text-xs font-medium"
                                                    title="Victoria">G</div>
                                                <div class="w-6 h-6 bg-red-500 rounded flex items-center justify-center text-white text-xs font-medium"
                                                    title="Derrota">P</div>
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- 8th Place - Elimination Zone -->
                                    <tr class="hover:bg-gray-50 transition-colors group">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center space-x-3">
                                                <div
                                                    class="w-8 h-8 bg-gradient-to-r from-red-500 to-red-600 rounded-full flex items-center justify-center text-white font-bold text-sm">
                                                    8</div>
                                                <div
                                                    class="w-1 h-8 bg-gradient-to-b from-red-500 to-red-600 rounded-full">
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center space-x-4">
                                                <div
                                                    class="w-10 h-10 bg-gradient-to-br from-teal-500 to-teal-600 rounded-lg flex items-center justify-center text-white font-bold shadow-sm">
                                                    ES
                                                </div>
                                                <div>
                                                    <div
                                                        class="font-semibold text-gray-900 group-hover:text-blue-600 transition-colors">
                                                        Estrellas</div>
                                                    <div class="text-sm text-gray-500">Palmito</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-4 text-center text-sm font-medium text-gray-900">14</td>
                                        <td class="px-4 py-4 text-center text-sm font-medium text-green-600">1</td>
                                        <td class="px-4 py-4 text-center text-sm font-medium text-red-600">13</td>
                                        <td class="px-4 py-4 text-center text-sm text-gray-600">8-39</td>
                                        <td class="px-4 py-4 text-center">
                                            <span class="text-lg font-bold text-gray-900">3</span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex space-x-1 justify-center">
                                                <div class="w-6 h-6 bg-red-500 rounded flex items-center justify-center text-white text-xs font-medium"
                                                    title="Derrota">P</div>
                                                <div class="w-6 h-6 bg-red-500 rounded flex items-center justify-center text-white text-xs font-medium"
                                                    title="Derrota">P</div>
                                                <div class="w-6 h-6 bg-red-500 rounded flex items-center justify-center text-white text-xs font-medium"
                                                    title="Derrota">P</div>
                                                <div class="w-6 h-6 bg-red-500 rounded flex items-center justify-center text-white text-xs font-medium"
                                                    title="Derrota">P</div>
                                                <div class="w-6 h-6 bg-green-500 rounded flex items-center justify-center text-white text-xs font-medium"
                                                    title="Victoria">G</div>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Table Footer -->
                        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                            <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                                <div class="text-sm text-gray-600">
                                    Mostrando 8 equipos • Última actualización: hace 2 minutos
                                </div>
                                <div class="flex items-center space-x-2">
                                    <span class="text-sm text-gray-600">PJ: Partidos Jugados</span>
                                    <span class="text-gray-300">•</span>
                                    <span class="text-sm text-gray-600">G: Ganados</span>
                                    <span class="text-gray-300">•</span>
                                    <span class="text-sm text-gray-600">P: Perdidos</span>
                                    <span class="text-gray-300">•</span>
                                    <span class="text-sm text-gray-600">Pts: Puntos</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-8" role="contentinfo">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <p>&copy; 2024 VolleyPass. Todos los derechos reservados.</p>
            </div>
        </div>
    </footer>

    <!-- JavaScript for interactivity -->
    <script>
        // Progressive enhancement
        document.addEventListener('DOMContentLoaded', function() {
            // Add loaded class to lazy components
            const lazyComponents = document.querySelectorAll('.lazy-component');
            lazyComponents.forEach(component => {
                component.classList.add('loaded');
            });

            // Simulate live score updates
            const liveScores = document.querySelectorAll('.live-scoreboard__current-score');
            setInterval(() => {
                liveScores.forEach(score => {
                    if (Math.random() > 0.95) {
                        score.classList.add('animate-score-update');
                        setTimeout(() => {
                            score.classList.remove('animate-score-update');
                        }, 600);
                    }
                });
            }, 5000);

            // Table sorting
            const sortableHeaders = document.querySelectorAll('.sports-table__header-cell--sortable');
            sortableHeaders.forEach(header => {
                header.addEventListener('click', function() {
                    // Remove active state from other headers
                    sortableHeaders.forEach(h => h.classList.remove(
                        'sports-table__header-cell--active'));
                    // Add active state to clicked header
                    this.classList.add('sports-table__header-cell--active');
                });
            });
        });
    </script>
</body>

</html>
