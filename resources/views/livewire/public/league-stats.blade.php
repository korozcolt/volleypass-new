<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Estadísticas de Liga</h2>
            <p class="text-gray-600 mt-1">Estadísticas generales y clasificación de equipos</p>
        </div>
        
        <!-- Tournament Selector -->
        @if($tournaments && $tournaments->count() > 1)
        <div class="mt-4 sm:mt-0">
            <select wire:model.live="selectedTournament" 
                    class="block w-full sm:w-auto rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                @foreach($tournaments as $tournament)
                    <option value="{{ $tournament->id }}">{{ $tournament->name }}</option>
                @endforeach
            </select>
        </div>
        @endif
    </div>

    <!-- Loading State -->
    <div wire:loading class="flex items-center justify-center py-8">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
        <span class="ml-2 text-gray-600">Cargando estadísticas...</span>
    </div>

    <!-- Content -->
    <div wire:loading.remove>
        @if($selectedTournament && !empty($leagueStats))
            <!-- Tournament Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                <!-- Total Teams -->
                <div class="bg-blue-50 rounded-lg p-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-blue-600">Equipos</p>
                            <p class="text-2xl font-bold text-blue-900">{{ $leagueStats['total_teams'] }}</p>
                        </div>
                    </div>
                </div>

                <!-- Total Matches -->
                <div class="bg-green-50 rounded-lg p-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-600">Partidos Jugados</p>
                            <p class="text-2xl font-bold text-green-900">{{ $leagueStats['played_matches'] }}/{{ $leagueStats['total_matches'] }}</p>
                        </div>
                    </div>
                </div>

                <!-- Completion Percentage -->
                <div class="bg-purple-50 rounded-lg p-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-8 w-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-purple-600">Progreso</p>
                            <p class="text-2xl font-bold text-purple-900">{{ $leagueStats['completion_percentage'] }}%</p>
                        </div>
                    </div>
                </div>

                <!-- Total Sets -->
                <div class="bg-orange-50 rounded-lg p-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-8 w-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-orange-600">Sets Totales</p>
                            <p class="text-2xl font-bold text-orange-900">{{ $leagueStats['total_sets'] }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Progress Bar -->
            <div class="mb-8">
                <div class="flex justify-between text-sm text-gray-600 mb-2">
                    <span>Progreso del Torneo: {{ $leagueStats['tournament_name'] }}</span>
                    <span>{{ $leagueStats['completion_percentage'] }}% completado</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" 
                         style="width: {{ $leagueStats['completion_percentage'] }}%"></div>
                </div>
            </div>

            <!-- Top Teams Table -->
            @if($topTeams && $topTeams->count() > 0)
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Top 5 Equipos</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Posición</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Equipo</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">PJ</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">G</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">P</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Sets G/P</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Puntos</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">% Victoria</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($topTeams as $index => $teamData)
                            <tr class="hover:bg-gray-50 transition-colors duration-150">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        @if($index < 3)
                                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full text-xs font-bold
                                                {{ $index === 0 ? 'bg-yellow-100 text-yellow-800' : '' }}
                                                {{ $index === 1 ? 'bg-gray-100 text-gray-800' : '' }}
                                                {{ $index === 2 ? 'bg-orange-100 text-orange-800' : '' }}">
                                                {{ $index + 1 }}
                                            </span>
                                        @else
                                            <span class="text-sm font-medium text-gray-900">{{ $index + 1 }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $teamData['team']->name }}</div>
                                    @if($teamData['team']->city)
                                        <div class="text-sm text-gray-500">{{ $teamData['team']->city }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-900">{{ $teamData['matches_played'] }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium text-green-600">{{ $teamData['wins'] }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium text-red-600">{{ $teamData['losses'] }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-900">{{ $teamData['sets_won'] }}/{{ $teamData['sets_lost'] }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $teamData['points'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-900">{{ $teamData['win_percentage'] }}%</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        @else
            <!-- Empty State -->
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No hay estadísticas disponibles</h3>
                <p class="mt-1 text-sm text-gray-500">No se encontraron torneos activos o datos para mostrar.</p>
            </div>
        @endif
    </div>

    <!-- Auto-refresh script -->
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('leagueStatsRefresh', () => ({
                init() {
                    // Auto-refresh every 30 seconds
                    setInterval(() => {
                        if (document.visibilityState === 'visible') {
                            @this.call('refreshStats');
                        }
                    }, 30000);
                }
            }));
        });
    </script>
</div>
