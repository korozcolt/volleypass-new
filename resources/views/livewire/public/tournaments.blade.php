<div x-data="{
    autoRefresh: true,
    refreshInterval: null,
    init() {
        if (this.autoRefresh) {
            this.refreshInterval = setInterval(() => {
                $wire.call('refreshData');
            }, 30000); // Refresh every 30 seconds
        }
    },
    destroy() {
        if (this.refreshInterval) {
            clearInterval(this.refreshInterval);
        }
    }
}" class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

    <!-- Hero Section -->
    <div class="bg-gradient-to-r from-blue-600 to-purple-600 rounded-2xl p-8 text-white">
        <div class="text-center">
            <h1 class="text-4xl font-bold mb-4">🏐 Torneos en Vivo</h1>
            <p class="text-xl opacity-90 mb-6">
                Sigue todos los partidos de la Liga de Voleibol de Sucre en tiempo real
            </p>
            <div class="flex items-center justify-center space-x-6 text-sm">
                <div class="flex items-center space-x-2">
                    <div class="w-3 h-3 bg-green-400 rounded-full animate-pulse"></div>
                    <span>{{ count($liveMatches) }} partidos en vivo</span>
                </div>
                <div class="flex items-center space-x-2">
                    <div class="w-3 h-3 bg-blue-400 rounded-full"></div>
                    <span>{{ count($tournaments) }} torneos activos</span>
                </div>
                <div class="flex items-center space-x-2">
                    <div class="w-3 h-3 bg-yellow-400 rounded-full"></div>
                    <span>{{ count($upcomingMatches) }} próximos partidos</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Tournament Selector -->
    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="flex flex-wrap gap-4 justify-center">
            @foreach($tournaments as $tournament)
            <button
                wire:click="selectTournament({{ $tournament['id'] }})"
                class="px-6 py-3 rounded-lg font-medium transition-all transform hover:scale-105 {{ $selectedTournament == $tournament['id'] ? 'bg-blue-600 text-white shadow-lg' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                <div class="text-center">
                    <div class="font-bold">{{ $tournament['name'] }}</div>
                    <div class="text-xs opacity-75">{{ $tournament['category'] }} • {{ $tournament['teams_count'] }} equipos</div>
                </div>
            </button>
            @endforeach
        </div>
    </div>

    <!-- Live Matches -->
    @if(count($liveMatches) > 0)
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center">
                <div class="w-4 h-4 bg-red-500 rounded-full animate-pulse mr-3"></div>
                Partidos en Vivo
            </h2>
            <div class="flex items-center space-x-2 text-sm text-gray-500 dark:text-gray-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                <span>Actualización automática cada 30s</span>
            </div>
        </div>

        <div class="grid lg:grid-cols-2 gap-6">
            @foreach($liveMatches as $match)
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg border border-gray-200 dark:border-gray-700 relative overflow-hidden">
                <!-- Live indicator -->
                <div class="absolute top-4 right-4">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                        <div class="w-2 h-2 bg-red-500 rounded-full animate-pulse mr-1"></div>
                        EN VIVO
                    </span>
                </div>

                <!-- Teams and Score -->
                <div class="mb-4">
                    <div class="flex items-center justify-between mb-2">
                        <div class="text-lg font-bold text-gray-900 dark:text-white">{{ $match['team_a'] }}</div>
                        <div class="text-3xl font-bold text-blue-600">{{ $match['score_a'] }}</div>
                    </div>
                    <div class="flex items-center justify-center py-2">
                        <div class="text-sm text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-700 px-3 py-1 rounded-full">
                            VS
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="text-lg font-bold text-gray-900 dark:text-white">{{ $match['team_b'] }}</div>
                        <div class="text-3xl font-bold text-purple-600">{{ $match['score_b'] }}</div>
                    </div>
                </div>

                <!-- Set Scores -->
                <div class="mb-4">
                    <div class="text-sm text-gray-600 dark:text-gray-400 mb-2">Sets ganados:</div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            <span class="text-lg font-bold text-blue-600">{{ $match['sets_a'] }}</span>
                            <span class="text-sm text-gray-500 dark:text-gray-400">{{ $match['team_a'] }}</span>
                        </div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">-</div>
                        <div class="flex items-center space-x-2">
                            <span class="text-sm text-gray-500 dark:text-gray-400">{{ $match['team_b'] }}</span>
                            <span class="text-lg font-bold text-purple-600">{{ $match['sets_b'] }}</span>
                        </div>
                    </div>
                </div>

                <!-- Match Info -->
                <div class="flex items-center justify-between text-sm text-gray-500 dark:text-gray-400">
                    <div class="flex items-center space-x-4">
                        <span>Set {{ $match['current_set']['number'] }}</span>
                        <span>⏱️ {{ $match['time_elapsed'] }}</span>
                    </div>
                    <button class="text-blue-600 dark:text-blue-400 hover:underline font-medium">
                        Ver detalles
                    </button>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Tournament Content Grid -->
    <div class="grid lg:grid-cols-3 gap-8">
        <!-- Left Column: Standings -->
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6 flex items-center">
                    <svg class="w-6 h-6 mr-2 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                    </svg>
                    Tabla de Posiciones
                </h3>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-200 dark:border-gray-600">
                                <th class="text-left py-3 px-2 font-medium text-gray-900 dark:text-white">#</th>
                                <th class="text-left py-3 px-4 font-medium text-gray-900 dark:text-white">Equipo</th>
                                <th class="text-center py-3 px-2 font-medium text-gray-900 dark:text-white">PJ</th>
                                <th class="text-center py-3 px-2 font-medium text-gray-900 dark:text-white">PG</th>
                                <th class="text-center py-3 px-2 font-medium text-gray-900 dark:text-white">PP</th>
                                <th class="text-center py-3 px-2 font-medium text-gray-900 dark:text-white">Pts</th>
                                <th class="text-center py-3 px-2 font-medium text-gray-900 dark:text-white">Dif</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
                            @foreach($standings as $team)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                <td class="py-3 px-2">
                                    <div class="flex items-center">
                                        <span class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold
                                            {{ $team['position'] <= 3 ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300' }}">
                                            {{ $team['position'] }}
                                        </span>
                                        @if($team['position'] == 1)
                                            <svg class="w-4 h-4 text-yellow-500 ml-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                                            </svg>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-3 px-4 font-medium text-gray-900 dark:text-white">{{ $team['team'] }}</td>
                                <td class="py-3 px-2 text-center text-gray-600 dark:text-gray-300">{{ $team['matches'] }}</td>
                                <td class="py-3 px-2 text-center font-medium text-green-600">{{ $team['wins'] }}</td>
                                <td class="py-3 px-2 text-center font-medium text-red-600">{{ $team['losses'] }}</td>
                                <td class="py-3 px-2 text-center font-bold text-blue-600">{{ $team['points'] }}</td>
                                <td class="py-3 px-2 text-center font-medium {{ str_starts_with($team['sets_diff'], '+') ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $team['sets_diff'] }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Right Column: Upcoming Matches -->
        <div class="space-y-6">
            <!-- Upcoming Matches -->
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    Próximos Partidos
                </h3>
                <div class="space-y-4">
                    @foreach($upcomingMatches as $match)
                    <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-4 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <div class="flex items-center justify-between mb-2">
                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ $match['team_a'] }} vs {{ $match['team_b'] }}
                            </div>
                            <div class="text-xs text-blue-600 dark:text-blue-400 bg-blue-100 dark:bg-blue-900 px-2 py-1 rounded">
                                {{ date('d M', strtotime($match['date'])) }}
                            </div>
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400 space-y-1">
                            <div class="flex items-center">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ $match['time'] }}
                            </div>
                            <div class="flex items-center">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                {{ $match['venue'] }}
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Tournament Stats -->
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    Estadísticas del Torneo
                </h3>
                <div class="space-y-3">
                    @php
                        $currentTournament = collect($tournaments)->firstWhere('id', $selectedTournament);
                    @endphp
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-300">Equipos participantes</span>
                        <span class="font-medium text-gray-900 dark:text-white">{{ $currentTournament['teams_count'] ?? 0 }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-300">Partidos jugados</span>
                        <span class="font-medium text-gray-900 dark:text-white">{{ $currentTournament['matches_played'] ?? 0 }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-300">Partidos restantes</span>
                        <span class="font-medium text-gray-900 dark:text-white">{{ $currentTournament['matches_remaining'] ?? 0 }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-300">Progreso</span>
                        <span class="font-medium text-blue-600">
                            {{ $currentTournament ? round(($currentTournament['matches_played'] / ($currentTournament['matches_played'] + $currentTournament['matches_remaining'])) * 100) : 0 }}%
                        </span>
                    </div>
                </div>

                @if($currentTournament)
                <div class="mt-4">
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                        <div class="bg-blue-600 h-2 rounded-full transition-all duration-500"
                             style="width: {{ round(($currentTournament['matches_played'] / ($currentTournament['matches_played'] + $currentTournament['matches_remaining'])) * 100) }}%">
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Quick Actions -->
            <div class="bg-gradient-to-br from-blue-50 to-purple-50 dark:from-blue-900/20 dark:to-purple-900/20 rounded-xl p-6 border border-blue-200 dark:border-blue-800">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Acciones Rápidas</h3>
                <div class="space-y-3">
                    <button class="w-full flex items-center justify-center px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Descargar Calendario
                    </button>

                    <button class="w-full flex items-center justify-center px-4 py-3 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-lg font-medium transition-colors border border-gray-300 dark:border-gray-600">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"></path>
                        </svg>
                        Compartir Resultados
                    </button>

                    @guest
                    <a href="/login" class="w-full flex items-center justify-center px-4 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                        </svg>
                        Iniciar Sesión
                    </a>
                    @endguest
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Results -->
    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6 flex items-center">
            <svg class="w-6 h-6 mr-2 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
            </svg>
            Resultados Recientes
        </h3>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
            @php
                $recentResults = [
                    ['team_a' => 'Halcones FC', 'team_b' => 'Águilas Doradas', 'score_a' => 3, 'score_b' => 1, 'date' => '2024-02-08'],
                    ['team_a' => 'Tigres del Norte', 'team_b' => 'Leones FC', 'score_a' => 2, 'score_b' => 3, 'date' => '2024-02-07'],
                    ['team_a' => 'Cóndores', 'team_b' => 'Pumas', 'score_a' => 3, 'score_b' => 0, 'date' => '2024-02-06'],
                    ['team_a' => 'Jaguares', 'team_b' => 'Panteras', 'score_a' => 1, 'score_b' => 3, 'date' => '2024-02-05'],
                    ['team_a' => 'Lobos', 'team_b' => 'Halcones FC', 'score_a' => 0, 'score_b' => 3, 'date' => '2024-02-04'],
                    ['team_a' => 'Águilas Doradas', 'team_b' => 'Tigres del Norte', 'score_a' => 3, 'score_b' => 2, 'date' => '2024-02-03'],
                ];
            @endphp

            @foreach($recentResults as $result)
            <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-4 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                <div class="flex items-center justify-between mb-2">
                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                        {{ $result['team_a'] }}
                    </div>
                    <div class="text-lg font-bold {{ $result['score_a'] > $result['score_b'] ? 'text-green-600' : 'text-gray-500' }}">
                        {{ $result['score_a'] }}
                    </div>
                </div>
                <div class="text-center text-xs text-gray-500 dark:text-gray-400 mb-2">vs</div>
                <div class="flex items-center justify-between mb-3">
                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                        {{ $result['team_b'] }}
                    </div>
                    <div class="text-lg font-bold {{ $result['score_b'] > $result['score_a'] ? 'text-green-600' : 'text-gray-500' }}">
                        {{ $result['score_b'] }}
                    </div>
                </div>
                <div class="text-xs text-gray-500 dark:text-gray-400 text-center">
                    {{ date('d M Y', strtotime($result['date'])) }}
                </div>
            </div>
            @endforeach
        </div>
    </div>
    </div>
</div>
