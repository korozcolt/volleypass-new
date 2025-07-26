<div class="bg-gradient-to-br from-gray-50 via-white to-gray-100 font-sans antialiased min-h-screen">
    <style>
        .lazy-component {
            opacity: 0;
            transform: translateY(20px);
            transition: all 0.6s ease;
        }
        .lazy-component.loaded {
            opacity: 1;
            transform: translateY(0);
        }
        .animate-score-update {
            animation: scoreUpdate 0.6s ease;
        }
        @keyframes scoreUpdate {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); color: #10b981; }
            100% { transform: scale(1); }
        }
        .gradient-text {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
    </style>
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
                    @foreach($liveMatches->take(2) as $match)
                    <div class="sports-header__live-score">
                        <span class="sports-header__score-text">
                            {{ $match['home_team']['name'] }} vs {{ $match['away_team']['name'] }} 
                            {{ $match['home_score'] }}-{{ $match['away_score'] }}
                        </span>
                    </div>
                    @endforeach
                    @if($liveMatches->isEmpty())
                    <div class="sports-header__live-score">
                        <span class="sports-header__score-text">No hay partidos en vivo</span>
                    </div>
                    @endif
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
                    <a href="/tournaments" class="sports-header__nav-link">Torneos</a>
                    <a href="/teams" class="sports-header__nav-link">Equipos</a>
                    <a href="/schedule" class="sports-header__nav-link">Calendario</a>
                    <a href="/standings" class="sports-header__nav-link">Posiciones</a>
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
                        <span class="sports-header__notification-badge">{{ $liveMatches->count() }}</span>
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
                    <span class="live-indicator">SISTEMA EN VIVO</span>
                </div>

                <h1 class="sports-hero__title">
                    Sistema de Gestión Deportiva
                    <span
                        class="sports-hero__subtitle text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-yellow-500">VolleyPass</span>
                </h1>

                <p class="sports-hero__description">
                    Plataforma completa para la gestión de torneos, equipos y jugadores de voleibol con datos en tiempo real.
                </p>

                <div class="sports-hero__stats">
                    <div class="sports-hero__stat">
                        <span class="sports-hero__stat-number">{{ $featuredTournaments->count() }}</span>
                        <span class="sports-hero__stat-label">Torneos Activos</span>
                    </div>
                    <div class="sports-hero__stat">
                        <span class="sports-hero__stat-number">{{ $liveMatches->count() }}</span>
                        <span class="sports-hero__stat-label">En Vivo</span>
                    </div>
                    <div class="sports-hero__stat">
                        <span class="sports-hero__stat-number">{{ $standingsData['standings']->count() }}</span>
                        <span class="sports-hero__stat-label">Equipos</span>
                    </div>
                </div>

                <div class="sports-hero__actions">
                    <a href="/tournaments" class="btn-sports">
                        Ver Torneos
                    </a>
                    <a href="/teams" class="btn-sports btn-sports--secondary">
                        Ver Equipos
                    </a>
                </div>
            </div>

            <div class="sports-hero__sidebar">
                <div class="sports-hero__upcoming">
                    <h3 class="sports-hero__sidebar-title">Funcionalidades</h3>
                    <div class="sports-hero__match-list">
                        <div class="sports-hero__match">
                            <div class="sports-hero__match-teams">
                                <span class="team-home">Gestión de Torneos</span>
                                <span class="sports-hero__vs">✓</span>
                                <span class="team-away">Activo</span>
                            </div>
                        </div>
                        <div class="sports-hero__match">
                            <div class="sports-hero__match-teams">
                                <span class="team-home">Marcadores en Vivo</span>
                                <span class="sports-hero__vs">✓</span>
                                <span class="team-away">Activo</span>
                            </div>
                        </div>
                        <div class="sports-hero__match">
                            <div class="sports-hero__match-teams">
                                <span class="team-home">Tabla de Posiciones</span>
                                <span class="sports-hero__vs">✓</span>
                                <span class="team-away">Activo</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="sports-hero__live-scores">
                    <h3 class="sports-hero__sidebar-title">Próximos Partidos</h3>
                    <div class="space-y-3">
                        @forelse($upcomingMatches->take(3) as $match)
                        <div class="flex items-center justify-between p-3 bg-white/10 rounded-lg backdrop-blur-sm">
                            <span class="text-white/90 text-sm">{{ $match['home_team']['name'] }} vs {{ $match['away_team']['name'] }}</span>
                            <span class="text-green-400 font-bold text-xs">{{ $match['match_date']->format('H:i') }}</span>
                        </div>
                        @empty
                        <div class="flex items-center justify-between p-3 bg-white/10 rounded-lg backdrop-blur-sm">
                            <span class="text-white/90 text-sm">No hay partidos programados</span>
                            <span class="text-yellow-400 font-bold">-</span>
                        </div>
                        @endforelse
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
                        @forelse($featuredTournaments as $tournament)
                        <!-- Tournament Card -->
                        <article class="tournament-card {{ $tournament['status'] === 'live' ? 'tournament-card--featured' : '' }}">
                            <div class="tournament-card__header">
                                <div class="tournament-card__status">
                                    <span class="tournament-card__badge tournament-card__badge--{{ $tournament['status'] === 'live' ? 'live' : ($tournament['status'] === 'upcoming' ? 'upcoming' : 'finished') }}">
                                        @if($tournament['status'] === 'live')
                                            EN VIVO
                                        @elseif($tournament['status'] === 'upcoming')
                                            PRÓXIMO
                                        @else
                                            FINALIZADO
                                        @endif
                                    </span>
                                </div>
                                <div class="tournament-card__image">
                                    <div class="w-full h-32 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center text-white font-bold text-2xl">
                                        {{ strtoupper(substr($tournament['name'], 0, 2)) }}
                                    </div>
                                </div>
                            </div>

                            <div class="tournament-card__content">
                                <h3 class="tournament-card__title">{{ $tournament['name'] }}</h3>
                                <p class="tournament-card__description">{{ $tournament['description'] }}</p>

                                <div class="tournament-card__stats">
                                    <div class="tournament-card__stat">
                                        <span class="tournament-card__stat-number">{{ $tournament['teams_count'] }}</span>
                                        <span class="tournament-card__stat-label">Equipos</span>
                                    </div>
                                    <div class="tournament-card__stat">
                                        <span class="tournament-card__stat-number">{{ $tournament['cities_count'] }}</span>
                                        <span class="tournament-card__stat-label">Ciudades</span>
                                    </div>
                                </div>

                                @if($tournament['status'] === 'finished' && $tournament['winner'])
                                <div class="tournament-card__winner">
                                    <span class="tournament-card__winner-label">Campeón:</span>
                                    <span class="tournament-card__winner-team">{{ $tournament['winner'] }}</span>
                                </div>
                                @elseif($tournament['status'] === 'upcoming')
                                <div class="tournament-card__date">
                                    <span class="tournament-card__date-label">Inicia:</span>
                                    <span class="tournament-card__date-value">{{ $tournament['start_date']->format('d M Y') }}</span>
                                </div>
                                @else
                                <div class="tournament-card__progress">
                                    <div class="tournament-card__progress-bar">
                                        <div class="tournament-card__progress-fill" style="width: {{ $tournament['progress'] }}%"></div>
                                    </div>
                                    <span class="tournament-card__progress-text">{{ $tournament['progress'] }}% Completado</span>
                                </div>
                                @endif
                            </div>

                            <div class="tournament-card__footer">
                                <a href="/tournaments/{{ $tournament['id'] }}" class="tournament-card__action">
                                    @if($tournament['status'] === 'finished')
                                        Ver Resultados
                                    @else
                                        Ver Detalles
                                    @endif
                                </a>
                            </div>
                        </article>
                        @empty
                        <div class="col-span-full text-center py-12">
                            <p class="text-gray-500 text-lg">No hay torneos disponibles en este momento.</p>
                        </div>
                        @endforelse
                    </div>
                </section>

                <!-- Marcadores Section -->
                <section class="mb-12 py-16 bg-gray-100" aria-labelledby="live-scores-heading">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <h2 id="live-scores-heading" class="text-3xl font-bold text-gray-900 mb-8 text-center">
                            Marcadores en Vivo
                        </h2>
                        
                        @forelse($liveMatches as $match)
                        <!-- Live Match -->
                        <div class="live-scoreboard live-scoreboard--entering mb-8">
                            <div class="live-scoreboard__header">
                                <span class="live-scoreboard__tournament">{{ $match['tournament_name'] }} - {{ $match['phase'] }}</span>
                                <div class="live-scoreboard__status">
                                    <div class="live-scoreboard__status-dot"></div>
                                    <span>Set {{ $match['current_set'] }} - {{ $match['time_elapsed'] }}</span>
                                </div>
                            </div>

                            <div class="live-scoreboard__match">
                                <div class="live-scoreboard__team">
                                    <div class="live-scoreboard__team-logo">{{ $match['home_team']['logo'] }}</div>
                                    <div class="live-scoreboard__team-info">
                                        <div class="live-scoreboard__team-name">{{ $match['home_team']['name'] }}</div>
                                        <div class="live-scoreboard__team-record">{{ $match['home_team']['record'] }}</div>
                                    </div>
                                </div>

                                <div class="live-scoreboard__score">
                                    <div class="live-scoreboard__score-main">
                                        <span class="live-scoreboard__score-home">{{ $match['home_score'] }}</span>
                                        <span class="live-scoreboard__score-separator">-</span>
                                        <span class="live-scoreboard__score-away">{{ $match['away_score'] }}</span>
                                    </div>
                                    <div class="live-scoreboard__set-score">Sets: {{ collect($match['sets'])->where('status', 'finished')->where('home_score', '>', 'away_score')->count() }}-{{ collect($match['sets'])->where('status', 'finished')->where('away_score', '>', 'home_score')->count() }}</div>
                                </div>

                                <div class="live-scoreboard__team live-scoreboard__team--away">
                                    <div class="live-scoreboard__team-info">
                                        <div class="live-scoreboard__team-name">{{ $match['away_team']['name'] }}</div>
                                        <div class="live-scoreboard__team-record">{{ $match['away_team']['record'] }}</div>
                                    </div>
                                    <div class="live-scoreboard__team-logo">{{ $match['away_team']['logo'] }}</div>
                                </div>
                            </div>

                            <div class="live-scoreboard__sets">
                                @foreach($match['sets'] as $set)
                                <div class="live-scoreboard__set live-scoreboard__set--{{ $set['status'] }}">
                                    <div class="live-scoreboard__set-label">Set {{ $set['set_number'] }}</div>
                                    <div class="live-scoreboard__set-scores">
                                        <span>{{ $set['home_score'] }}</span>
                                        <span class="live-scoreboard__set-separator">-</span>
                                        <span>{{ $set['away_score'] }}</span>
                                    </div>
                                </div>
                                @endforeach
                            </div>

                            <div class="live-scoreboard__actions">
                                <a href="/matches/{{ $match['id'] }}" class="live-scoreboard__action live-scoreboard__action--primary">
                                    <svg class="live-scoreboard__action-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                    </svg>
                                    Ver en Vivo
                                </a>
                                <a href="/matches/{{ $match['id'] }}/stats" class="live-scoreboard__action">
                                    <svg class="live-scoreboard__action-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                    </svg>
                                    Estadísticas
                                </a>
                            </div>
                        </div>
                        @empty
                        
                        @forelse($upcomingMatches->take(2) as $match)
                        <!-- Upcoming Match -->
                        <div class="live-scoreboard live-scoreboard--upcoming mb-8">
                            <div class="live-scoreboard__header">
                                <span class="live-scoreboard__tournament">{{ $match['tournament_name'] }} - {{ $match['phase'] }}</span>
                                <div class="live-scoreboard__status">
                                    <div class="live-scoreboard__status-dot"></div>
                                    <span>{{ $match['match_date']->format('H:i') }}</span>
                                </div>
                            </div>

                            <div class="live-scoreboard__match">
                                <div class="live-scoreboard__team">
                                    <div class="live-scoreboard__team-logo">{{ $match['home_team']['logo'] }}</div>
                                    <div class="live-scoreboard__team-info">
                                        <div class="live-scoreboard__team-name">{{ $match['home_team']['name'] }}</div>
                                        <div class="live-scoreboard__team-record">{{ $match['home_team']['record'] }}</div>
                                    </div>
                                </div>

                                <div class="live-scoreboard__score">
                                    <div class="live-scoreboard__score-main">
                                        <span class="live-scoreboard__score-home">-</span>
                                        <span class="live-scoreboard__score-separator">vs</span>
                                        <span class="live-scoreboard__score-away">-</span>
                                    </div>
                                    <div class="live-scoreboard__set-score">{{ $match['venue'] }}</div>
                                </div>

                                <div class="live-scoreboard__team live-scoreboard__team--away">
                                    <div class="live-scoreboard__team-info">
                                        <div class="live-scoreboard__team-name">{{ $match['away_team']['name'] }}</div>
                                        <div class="live-scoreboard__team-record">{{ $match['away_team']['record'] }}</div>
                                    </div>
                                    <div class="live-scoreboard__team-logo">{{ $match['away_team']['logo'] }}</div>
                                </div>
                            </div>

                            <div class="live-scoreboard__actions">
                                <a href="/matches/{{ $match['id'] }}" class="live-scoreboard__action live-scoreboard__action--primary">
                                    <svg class="live-scoreboard__action-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Recordatorio
                                </a>
                                <a href="/matches/{{ $match['id'] }}/preview" class="live-scoreboard__action">
                                    <svg class="live-scoreboard__action-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Previa
                                </a>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-12">
                            <p class="text-gray-500 text-lg">No hay partidos programados en este momento.</p>
                        </div>
                        @endforelse
                        @endforelse
                    </div>
                </section>

                <!-- Tabla Section -->
                @if($standingsData['tournament'])
                <section class="mb-12 py-16 bg-white" aria-labelledby="standings-heading">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <!-- Section Header -->
                        <div class="text-center mb-8">
                            <h2 id="standings-heading" class="text-3xl font-bold text-gray-900 mb-2">
                                Tabla de Posiciones
                            </h2>
                            <p class="text-lg text-gray-600">{{ $standingsData['tournament']->name }} - Actualizado</p>
                        </div>

                        <!-- Controls Panel -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6">
                            <div class="p-6 border-b border-gray-200">
                                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                                    <!-- Tournament Info -->
                                    <div class="flex items-center space-x-3">
                                        <div class="w-12 h-12 bg-gradient-to-br from-blue-600 to-blue-700 rounded-xl flex items-center justify-center">
                                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <h3 class="text-lg font-bold text-gray-900">{{ $standingsData['tournament']->name }}</h3>
                                            <p class="text-sm text-gray-600">Actualizado hace pocos minutos</p>
                                        </div>
                                    </div>

                                    <div class="flex flex-col sm:flex-row gap-3">
                                        <a href="/standings" class="flex items-center space-x-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                            </svg>
                                            <span class="hidden sm:inline">Ver Completa</span>
                                        </a>
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
                                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Pos</th>
                                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider min-w-[250px]">Equipo</th>
                                            <th class="px-4 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">PJ</th>
                                            <th class="px-4 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">G</th>
                                            <th class="px-4 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">P</th>
                                            <th class="px-4 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Sets</th>
                                            <th class="px-4 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Pts</th>
                                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Últimos 5</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        @foreach($standingsData['standings'] as $index => $standing)
                                        <tr class="hover:bg-gray-50 transition-colors group">
                                            <td class="px-6 py-4">
                                                <div class="flex items-center space-x-3">
                                                    <div class="w-8 h-8 bg-gradient-to-r 
                                                        @if($index < 2) from-green-500 to-green-600
                                                        @elseif($index < 4) from-yellow-500 to-orange-500
                                                        @elseif($index >= 6) from-red-500 to-red-600
                                                        @else from-gray-400 to-gray-500
                                                        @endif
                                                        rounded-full flex items-center justify-center text-white font-bold text-sm">
                                                        {{ $index + 1 }}
                                                    </div>
                                                    <div class="w-1 h-8 bg-gradient-to-b 
                                                        @if($index < 2) from-green-500 to-green-600
                                                        @elseif($index < 4) from-yellow-500 to-orange-500
                                                        @elseif($index >= 6) from-red-500 to-red-600
                                                        @else from-gray-400 to-gray-500
                                                        @endif
                                                        rounded-full">
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="flex items-center space-x-4">
                                                    <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center text-white font-bold shadow-sm">
                                                        {{ $standing['logo'] }}
                                                    </div>
                                                    <div>
                                                        <div class="font-semibold text-gray-900 group-hover:text-blue-600 transition-colors">
                                                            {{ $standing['team']->name }}
                                                        </div>
                                                        <div class="text-sm text-gray-500">{{ $standing['team']->city }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-4 py-4 text-center text-sm font-medium text-gray-900">{{ $standing['matches_played'] }}</td>
                                            <td class="px-4 py-4 text-center text-sm font-medium text-green-600">{{ $standing['wins'] }}</td>
                                            <td class="px-4 py-4 text-center text-sm font-medium text-red-600">{{ $standing['losses'] }}</td>
                                            <td class="px-4 py-4 text-center text-sm text-gray-600">{{ $standing['sets_won'] }}-{{ $standing['sets_lost'] }}</td>
                                            <td class="px-4 py-4 text-center">
                                                <span class="text-lg font-bold text-gray-900">{{ $standing['points'] }}</span>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="flex space-x-1 justify-center">
                                                    @foreach(array_slice($standing['recent_results'], 0, 5) as $result)
                                                    <div class="w-6 h-6 {{ $result === 'G' ? 'bg-green-500' : 'bg-red-500' }} rounded flex items-center justify-center text-white text-xs font-medium" title="{{ $result === 'G' ? 'Victoria' : 'Derrota' }}">
                                                        {{ $result }}
                                                    </div>
                                                    @endforeach
                                                    @for($i = count($standing['recent_results']); $i < 5; $i++)
                                                    <div class="w-6 h-6 bg-gray-300 rounded flex items-center justify-center text-gray-500 text-xs font-medium" title="Sin datos">
                                                        -
                                                    </div>
                                                    @endfor
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Table Footer -->
                            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                                <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                                    <div class="text-sm text-gray-600">
                                        Mostrando {{ $standingsData['standings']->count() }} equipos • Última actualización: hace pocos minutos
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
                @endif
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

            // Auto-refresh live data every 30 seconds
            setInterval(() => {
                if (window.Livewire) {
                    window.Livewire.emit('refreshData');
                }
            }, 30000);
        });
    </script>
</div>