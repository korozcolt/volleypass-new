<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 dark:from-gray-900 dark:via-slate-900 dark:to-gray-900">
    <!-- Sports Header with Live Ticker -->
    <div class="bg-gradient-to-r from-red-600 via-red-500 to-orange-500 text-white py-2 overflow-hidden">
        <div class="animate-marquee whitespace-nowrap">
            <span class="mx-8 font-semibold">🔴 EN VIVO:</span>
            @foreach($liveMatches->take(3) as $match)
                <span class="mx-8">{{ $match->homeTeam->name }} {{ $match->home_sets ?? 0 }}-{{ $match->away_sets ?? 0 }} {{ $match->awayTeam->name }}</span>
            @endforeach
            <span class="mx-8 font-semibold">📊 {{ $stats['active_tournaments'] }} Torneos Activos</span>
            <span class="mx-8">⚡ {{ $stats['live_matches'] }} Partidos en Vivo</span>
        </div>
    </div>

    <!-- Hero Section with ESPN-style Design -->
    <section class="relative bg-gradient-to-br from-slate-900 via-blue-900 to-indigo-900 text-white py-16 overflow-hidden">
        <!-- Background Pattern -->
        <div class="absolute inset-0 opacity-10">
            <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,<svg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"><g fill="none" fill-rule="evenodd"><g fill="%23ffffff" fill-opacity="0.1"><circle cx="30" cy="30" r="2"/></g></g></svg>'); background-size: 60px 60px;"></div>
        </div>
        
        <div class="container mx-auto px-4 lg:px-6 relative z-10">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <!-- Main Content -->
                <div>
                    <div class="inline-flex items-center px-4 py-2 bg-red-500/20 border border-red-400/30 rounded-full text-red-300 text-sm font-medium mb-6">
                        <span class="w-2 h-2 bg-red-400 rounded-full mr-2 animate-pulse"></span>
                        TRANSMISIÓN EN VIVO
                    </div>
                    <h1 class="text-4xl lg:text-6xl font-black mb-6 leading-tight">
                        <span class="bg-gradient-to-r from-white via-blue-100 to-indigo-200 bg-clip-text text-transparent">
                            VolleyPass
                        </span>
                        <span class="block text-3xl lg:text-5xl text-blue-300 font-bold">
                            Sucre 2024
                        </span>
                    </h1>
                    <p class="text-xl text-slate-300 mb-8 leading-relaxed">
                        La experiencia deportiva más completa. Resultados en vivo, estadísticas avanzadas y cobertura total de todos los torneos.
                    </p>
                    
                    <!-- Quick Actions -->
                    <div class="flex flex-wrap gap-4">
                        <button class="bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 px-6 py-3 rounded-lg font-semibold transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                            🔴 Ver en Vivo
                        </button>
                        <button class="bg-white/10 backdrop-blur-sm border border-white/20 hover:bg-white/20 px-6 py-3 rounded-lg font-semibold transition-all duration-200">
                            📊 Estadísticas
                        </button>
                    </div>
                </div>
                
                <!-- Live Stats Grid -->
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-white/10 backdrop-blur-sm border border-white/20 rounded-xl p-6 text-center hover:bg-white/15 transition-all duration-200">
                        <div class="text-3xl font-black text-blue-300">{{ $stats['active_tournaments'] }}</div>
                        <div class="text-slate-300 font-medium">Torneos Activos</div>
                        <div class="text-xs text-green-400 mt-1">↗ +2 esta semana</div>
                    </div>
                    <div class="bg-white/10 backdrop-blur-sm border border-white/20 rounded-xl p-6 text-center hover:bg-white/15 transition-all duration-200">
                        <div class="text-3xl font-black text-red-400">{{ $stats['live_matches'] }}</div>
                        <div class="text-slate-300 font-medium">En Vivo</div>
                        <div class="text-xs text-red-400 mt-1 animate-pulse">🔴 LIVE</div>
                    </div>
                    <div class="bg-white/10 backdrop-blur-sm border border-white/20 rounded-xl p-6 text-center hover:bg-white/15 transition-all duration-200">
                        <div class="text-3xl font-black text-yellow-400">{{ $stats['registered_teams'] }}</div>
                        <div class="text-slate-300 font-medium">Equipos</div>
                        <div class="text-xs text-slate-400 mt-1">Registrados</div>
                    </div>
                    <div class="bg-white/10 backdrop-blur-sm border border-white/20 rounded-xl p-6 text-center hover:bg-white/15 transition-all duration-200">
                        <div class="text-3xl font-black text-purple-400">{{ $stats['total_players'] }}</div>
                        <div class="text-slate-300 font-medium">Jugadoras</div>
                        <div class="text-xs text-slate-400 mt-1">Activas</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Live Matches Section - ESPN Style -->
    @if($liveMatches->count() > 0)
    <section class="py-12 bg-white dark:bg-slate-800">
        <div class="container mx-auto px-4 lg:px-6">
            <!-- Section Header -->
            <div class="flex items-center justify-between mb-8">
                <div class="flex items-center space-x-3">
                    <div class="w-1 h-8 bg-gradient-to-b from-red-500 to-red-600 rounded-full"></div>
                    <h2 class="text-2xl font-black text-slate-900 dark:text-white">
                        PARTIDOS EN VIVO
                    </h2>
                    <div class="flex items-center space-x-2 px-3 py-1 bg-red-500 text-white text-xs font-bold rounded-full animate-pulse">
                        <span class="w-2 h-2 bg-white rounded-full"></span>
                        <span>LIVE</span>
                    </div>
                </div>
                <button class="text-sm font-semibold text-blue-600 hover:text-blue-700 flex items-center space-x-1">
                    <span>Ver todos</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>
            </div>

            <!-- Live Matches Grid -->
            <div class="grid lg:grid-cols-2 xl:grid-cols-3 gap-6">
                @foreach($liveMatches as $match)
                <div class="group bg-gradient-to-br from-white to-slate-50 dark:from-slate-800 dark:to-slate-900 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden border border-slate-200 dark:border-slate-700 hover:border-red-300 dark:hover:border-red-600">
                    <!-- Tournament Badge -->
                    <div class="bg-gradient-to-r from-slate-800 to-slate-900 text-white px-4 py-2">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold uppercase tracking-wide">{{ Str::limit($match->tournament->name, 20) }}</span>
                            <div class="flex items-center space-x-1 text-red-400">
                                <span class="w-1.5 h-1.5 bg-red-400 rounded-full animate-pulse"></span>
                                <span class="text-xs font-bold">LIVE</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Match Content -->
                    <div class="p-6">
                        <!-- Teams and Score -->
                        <div class="space-y-4">
                            <!-- Home Team -->
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3 flex-1">
                                    <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center text-white font-bold text-sm">
                                        {{ substr($match->homeTeam->name, 0, 2) }}
                                    </div>
                                    <span class="font-bold text-slate-900 dark:text-white text-lg">{{ $match->homeTeam->name }}</span>
                                </div>
                                <div class="text-3xl font-black text-slate-900 dark:text-white">
                                    {{ $match->home_sets ?? 0 }}
                                </div>
                            </div>
                            
                            <!-- VS Divider -->
                            <div class="flex items-center justify-center">
                                <div class="h-px bg-gradient-to-r from-transparent via-slate-300 to-transparent flex-1"></div>
                                <span class="px-4 text-xs font-bold text-slate-500 dark:text-slate-400 bg-slate-100 dark:bg-slate-700 rounded-full">VS</span>
                                <div class="h-px bg-gradient-to-r from-transparent via-slate-300 to-transparent flex-1"></div>
                            </div>
                            
                            <!-- Away Team -->
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3 flex-1">
                                    <div class="w-8 h-8 bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg flex items-center justify-center text-white font-bold text-sm">
                                        {{ substr($match->awayTeam->name, 0, 2) }}
                                    </div>
                                    <span class="font-bold text-slate-900 dark:text-white text-lg">{{ $match->awayTeam->name }}</span>
                                </div>
                                <div class="text-3xl font-black text-slate-900 dark:text-white">
                                    {{ $match->away_sets ?? 0 }}
                                </div>
                            </div>
                        </div>
                        
                        <!-- Match Info -->
                        <div class="mt-6 pt-4 border-t border-slate-200 dark:border-slate-700">
                            <div class="flex items-center justify-between">
                                <div class="text-xs text-slate-500 dark:text-slate-400">
                                    Set {{ ($match->home_sets ?? 0) + ($match->away_sets ?? 0) + 1 }}
                                </div>
                                <a href="{{ route('public.tournament.show', $match->tournament) }}"
                                   class="inline-flex items-center space-x-1 text-sm font-semibold text-blue-600 hover:text-blue-700 group-hover:translate-x-1 transition-all duration-200">
                                    <span>Ver detalles</span>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- Featured Tournaments -->
    <section class="py-16 bg-white dark:bg-gray-800">
        <div class="container mx-auto px-4 lg:px-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
                <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-4 md:mb-0">
                    Torneos Destacados
                </h2>

                <!-- Filters -->
                <div class="flex flex-wrap gap-3">
                    <select wire:model.live="statusFilter" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        <option value="all">Todos los estados</option>
                        <option value="in_progress">En Progreso</option>
                        <option value="registration_open">Inscripciones Abiertas</option>
                        <option value="registration_closed">Inscripciones Cerradas</option>
                        <option value="finished">Finalizados</option>
                    </select>

                    <input type="text"
                           wire:model.live.debounce.300ms="searchTerm"
                           placeholder="Buscar torneos..."
                           class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500">
                </div>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($featuredTournaments as $tournament)
                <div class="bg-gray-50 dark:bg-gray-900 rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
                    <div class="p-6">
                        <div class="flex items-start justify-between mb-4">
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white">
                                {{ $tournament->name }}
                            </h3>
                            <span class="px-3 py-1 text-xs font-medium rounded-full {{ $tournament->status->getColorHtml() }}">
                                {{ $tournament->status->getLabel() }}
                            </span>
                        </div>

                        <div class="space-y-2 text-sm text-gray-600 dark:text-gray-400 mb-4">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                {{ $tournament->city ?? 'Ubicación por definir' }}
                            </div>
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                {{ $tournament->start_date ? $tournament->start_date->format('d M Y') : 'Fecha por definir' }}
                            </div>
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                {{ $tournament->teams->count() }} equipos
                            </div>
                        </div>

                        <div class="flex items-center justify-between">
                            <a href="{{ route('public.tournament.show', $tournament) }}"
                               class="text-blue-600 hover:text-blue-700 font-medium text-sm">
                                Ver detalles →
                            </a>

                            @if($tournament->status === \App\Enums\TournamentStatus::RegistrationOpen)
                            <span class="text-green-600 text-sm font-medium">
                                ¡Inscripciones abiertas!
                            </span>
                            @endif
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-span-full text-center py-12">
                    <div class="text-gray-400 text-lg">No hay torneos disponibles</div>
                </div>
                @endforelse
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $featuredTournaments->links() }}
            </div>
        </div>
    </section>

    <!-- Upcoming Matches -->
    @if($upcomingMatches->count() > 0)
    <section class="py-16">
        <div class="container mx-auto px-4 lg:px-6">
            <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-8 text-center">
                Próximos Partidos
            </h2>

            <div class="max-w-4xl mx-auto space-y-4">
                @foreach($upcomingMatches as $match)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <div class="text-sm text-gray-500 dark:text-gray-400 mb-1">
                                {{ $match->tournament->name }}
                            </div>
                            <div class="flex items-center space-x-4">
                                <span class="font-semibold text-gray-900 dark:text-white">
                                    {{ $match->homeTeam->name }}
                                </span>
                                <span class="text-gray-400">vs</span>
                                <span class="font-semibold text-gray-900 dark:text-white">
                                    {{ $match->awayTeam->name }}
                                </span>
                            </div>
                        </div>

                        <div class="text-right">
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                {{ $match->scheduled_at ? $match->scheduled_at->format('d M Y') : 'Fecha por definir' }}
                            </div>
                            <div class="font-semibold text-gray-900 dark:text-white">
                                {{ $match->scheduled_at ? $match->scheduled_at->format('H:i') : 'Hora por definir' }}
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- Call to Action -->
    <section class="py-16 bg-gradient-to-r from-blue-600 to-purple-600 text-white">
        <div class="container mx-auto px-4 lg:px-6 text-center">
            <h2 class="text-3xl font-bold mb-4">¿Eres jugadora de voleibol?</h2>
            <p class="text-xl text-blue-100 mb-8">
                Únete a nuestra plataforma y accede a tu carnet digital, estadísticas personales y mucho más
            </p>
            <a href="{{ route('login') }}"
               class="inline-flex items-center px-8 py-3 bg-white text-blue-600 font-semibold rounded-lg hover:bg-gray-100 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                </svg>
                Iniciar Sesión
            </a>
        </div>
    </section>
</div>
