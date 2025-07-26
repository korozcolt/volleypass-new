<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <!-- Hero Section -->
    <section class="bg-gradient-to-br from-blue-600 via-purple-600 to-indigo-700 text-white py-20">
        <div class="container mx-auto px-4 lg:px-6">
            <div class="text-center max-w-4xl mx-auto">
                <h1 class="text-4xl md:text-6xl font-bold mb-6">
                    Torneos de Voleibol
                    <span class="block text-blue-200">en Vivo</span>
                </h1>
                <p class="text-xl md:text-2xl text-blue-100 mb-8">
                    Sigue todos los torneos, resultados y estadísticas en tiempo real
                </p>

                <!-- Stats -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mt-12">
                    <div class="text-center">
                        <div class="text-3xl font-bold">{{ $stats['active_tournaments'] }}</div>
                        <div class="text-blue-200">Torneos Activos</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold">{{ $stats['live_matches'] }}</div>
                        <div class="text-blue-200">Partidos en Vivo</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold">{{ $stats['registered_teams'] }}</div>
                        <div class="text-blue-200">Equipos</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold">{{ $stats['total_players'] }}</div>
                        <div class="text-blue-200">Jugadoras</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Live Matches Section -->
    @if($liveMatches->count() > 0)
    <section class="py-16">
        <div class="container mx-auto px-4 lg:px-6">
            <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-8 text-center">
                🔴 Partidos en Vivo
            </h2>

            <div class="grid md:grid-cols-3 gap-6">
                @foreach($liveMatches as $match)
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border-l-4 border-red-500">
                    <div class="text-sm text-gray-500 dark:text-gray-400 mb-2">
                        {{ $match->tournament->name }}
                    </div>

                    <div class="flex items-center justify-between mb-4">
                        <div class="text-center">
                            <div class="font-semibold text-gray-900 dark:text-white">
                                {{ $match->homeTeam->name }}
                            </div>
                        </div>

                        <div class="text-center px-4">
                            <div class="text-2xl font-bold text-gray-900 dark:text-white">
                                {{ $match->home_sets ?? 0 }} - {{ $match->away_sets ?? 0 }}
                            </div>
                            <div class="text-sm text-red-500 font-medium">EN VIVO</div>
                        </div>

                        <div class="text-center">
                            <div class="font-semibold text-gray-900 dark:text-white">
                                {{ $match->awayTeam->name }}
                            </div>
                        </div>
                    </div>

                    <div class="text-center">
                        <a href="{{ route('public.tournament.show', $match->tournament) }}"
                           class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                            Ver detalles →
                        </a>
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
