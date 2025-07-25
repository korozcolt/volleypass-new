<div class="space-y-6">
    <!-- Quick Stats Row -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Puntos Totales</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $personalStats['points'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Aces</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $personalStats['aces'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Bloqueos</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $personalStats['blocks'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-orange-100 dark:bg-orange-900 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">% Victorias</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $personalStats['win_rate'] }}%</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Player Card -->
            @livewire('player.player-card')

            <!-- Player Stats -->
            @livewire('player.player-stats')
        </div>

        <!-- Right Column -->
        <div class="space-y-6">
            <!-- Upcoming Matches -->
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Próximos Partidos</h3>
                <div class="space-y-4">
                    @foreach($upcomingMatches as $match)
                    <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-4">
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-medium text-gray-900 dark:text-white">vs {{ $match['opponent'] }}</span>
                            <span class="text-sm text-gray-500 dark:text-gray-400">{{ date('d M', strtotime($match['date'])) }}</span>
                        </div>
                        <div class="text-sm text-gray-600 dark:text-gray-300">
                            <p>{{ $match['time'] }} - {{ $match['venue'] }}</p>
                            <p class="text-xs text-blue-600 dark:text-blue-400">{{ $match['tournament'] }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Team Stats -->
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Estadísticas del Equipo</h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-300">Posición en Liga</span>
                        <span class="font-medium text-gray-900 dark:text-white">#{{ $teamStats['team_ranking'] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-300">Puntos del Equipo</span>
                        <span class="font-medium text-gray-900 dark:text-white">{{ $teamStats['team_points'] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-300">Partidos Ganados</span>
                        <span class="font-medium text-green-600">{{ $teamStats['matches_won'] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-300">Partidos Perdidos</span>
                        <span class="font-medium text-red-600">{{ $teamStats['matches_lost'] }}</span>
                    </div>
                </div>
            </div>

            <!-- Available Tournaments -->
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Torneos Disponibles</h3>
                <div class="space-y-3">
                    @foreach($availableTournaments as $tournament)
                    <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-3">
                        <div class="flex items-center justify-between mb-1">
                            <span class="font-medium text-sm text-gray-900 dark:text-white">{{ $tournament['name'] }}</span>
                            @if($tournament['my_team_playing'])
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                    Mi Equipo
                                </span>
                            @endif
                        </div>
                        <p class="text-xs text-gray-600 dark:text-gray-300">{{ $tournament['matches_today'] }} partidos hoy</p>
                        <button class="mt-2 text-xs text-blue-600 dark:text-blue-400 hover:underline">Ver detalles</button>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Player Matches Component -->
    @livewire('player.player-matches')

    <!-- Public Tournaments Integration -->
    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Torneos en Vivo</h3>
        @livewire('public.tournaments')
    </div>
</div>
