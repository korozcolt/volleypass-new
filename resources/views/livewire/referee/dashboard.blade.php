<div class="space-y-6">
    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Partidos Arbitrados</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $arbitrageStats['total_matches'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Calificación Promedio</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $arbitrageStats['average_rating'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-yellow-100 dark:bg-yellow-900 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Tarjetas Amarillas</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $arbitrageStats['yellow_cards'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-red-100 dark:bg-red-900 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Tarjetas Rojas</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $arbitrageStats['red_cards'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Live Match Control (if active) -->
    @if($liveMatch)
    <div class="bg-gradient-to-r from-green-500 to-blue-600 rounded-xl p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-xl font-bold mb-2">Partido en Vivo</h3>
                <p class="opacity-90">Halcones FC vs Águilas Doradas</p>
            </div>
            <button class="bg-white text-green-600 px-6 py-3 rounded-lg font-medium hover:bg-gray-100 transition-colors">
                Abrir Tablero de Control
            </button>
        </div>
    </div>
    @endif

    <!-- Main Content Grid -->
    <div class="grid lg:grid-cols-3 gap-6">
        <!-- Left Column -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Assigned Matches -->
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Partidos Asignados</h3>
                <div class="space-y-4">
                    @foreach($assignedMatches as $match)
                    <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-4">
                        <div class="flex items-center justify-between mb-3">
                            <div>
                                <h4 class="font-medium text-gray-900 dark:text-white">
                                    {{ $match['team_a'] }} vs {{ $match['team_b'] }}
                                </h4>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ date('d M Y', strtotime($match['date'])) }} - {{ $match['time'] }}
                                </p>
                            </div>
                            <div class="text-right">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    {{ $match['type'] === 'main_referee' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200' }}">
                                    {{ $match['type'] === 'main_referee' ? 'Árbitro Principal' : 'Juez de Línea' }}
                                </span>
                            </div>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-gray-600 dark:text-gray-300">
                                <p>📍 {{ $match['venue'] }}</p>
                                <p>🏆 {{ $match['tournament'] }}</p>
                            </div>
                            <div class="flex space-x-2">
                                <button class="text-blue-600 dark:text-blue-400 hover:underline text-sm">
                                    Ver detalles
                                </button>
                                @if($match['status'] === 'scheduled')
                                <a href="{{ route('referee.match-control', $match['id']) }}"
                                   class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm transition-colors inline-block">
                                    Control de Partido
                                </a>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Recent Evaluations -->
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Evaluaciones Recientes</h3>
                <div class="space-y-4">
                    @foreach($evaluations as $evaluation)
                    <div class="flex items-center justify-between p-4 border border-gray-200 dark:border-gray-600 rounded-lg">
                        <div>
                            <p class="font-medium text-gray-900 dark:text-white">{{ $evaluation['match'] }}</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ date('d M Y', strtotime($evaluation['date'])) }}</p>
                            <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">{{ $evaluation['comments'] }}</p>
                        </div>
                        <div class="text-right">
                            <div class="flex items-center space-x-1">
                                <span class="text-lg font-bold text-yellow-500">{{ $evaluation['rating'] }}</span>
                                <svg class="w-5 h-5 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="space-y-6">
            <!-- Active Tournaments -->
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Torneos Activos</h3>
                <div class="space-y-3">
                    @foreach($activeTournaments as $tournament)
                    <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-3">
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-medium text-sm text-gray-900 dark:text-white">{{ $tournament['name'] }}</span>
                        </div>
                        <div class="text-xs text-gray-600 dark:text-gray-300 space-y-1">
                            <p>{{ $tournament['matches_today'] }} partidos hoy</p>
                            <p>{{ $tournament['my_matches'] }} asignados a mí</p>
                        </div>
                        <button class="mt-2 text-xs text-blue-600 dark:text-blue-400 hover:underline">Ver detalles</button>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Acciones Rápidas</h3>
                <div class="space-y-3">
                    @if(count($assignedMatches) > 0)
                        <a href="{{ route('referee.match-control', $assignedMatches[0]['id']) }}"
                           class="w-full flex items-center justify-center px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-2a2 2 0 011-1.732l-1 1.732zM12 18V16m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6-8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-2a2 2 0 011-1.732l-1 1.732z"></path>
                            </svg>
                            Tablero de Control
                        </a>
                    @else
                        <button disabled class="w-full flex items-center justify-center px-4 py-3 bg-gray-400 text-white rounded-lg font-medium">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-2a2 2 0 011-1.732l-1 1.732zM12 18V16m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6-8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-2a2 2 0 011-1.732l-1 1.732z"></path>
                            </svg>
                            Sin Partidos Asignados
                        </button>
                    @endif
                    
                    <button class="w-full flex items-center justify-center px-4 py-3 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-lg font-medium transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Generar Reporte
                    </button>
                    
                    <button class="w-full flex items-center justify-center px-4 py-3 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-lg font-medium transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        Ver Calendario
                    </button>
                </div>
            </div>

            <!-- Performance Summary -->
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Resumen de Temporada</h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-300">Partidos esta temporada</span>
                        <span class="font-medium text-gray-900 dark:text-white">{{ $arbitrageStats['this_season'] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-300">Decisiones disputadas</span>
                        <span class="font-medium text-gray-900 dark:text-white">{{ $arbitrageStats['disputed_calls'] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-300">Tasa de acierto</span>
                        <span class="font-medium text-green-600">96.8%</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Public Tournaments Integration -->
    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Torneos en Vivo</h3>
        @livewire('public.tournaments')
    </div>
</div>