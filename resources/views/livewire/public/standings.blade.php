<div>
    <div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-purple-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Header -->
            <div class="text-center mb-8">
                <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-4">
                    🏆 Tabla de Posiciones
                </h1>
                <p class="text-lg text-gray-600 dark:text-gray-400">
                    Sigue las posiciones de todos los torneos en tiempo real
                </p>
            </div>

            <!-- Selector de Torneo -->
            @if($tournaments->count() > 1)
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-8">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between">
                        <div class="mb-4 sm:mb-0">
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                                Seleccionar Torneo
                            </h2>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                Elige el torneo para ver su tabla de posiciones
                            </p>
                        </div>
                        <div class="w-full sm:w-auto">
                            <select wire:model.live="selectedTournament" 
                                    class="w-full sm:w-64 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 dark:focus:border-blue-400 focus:ring-blue-500 dark:focus:ring-blue-400 rounded-lg shadow-sm">
                                @foreach($tournaments as $tournament)
                                    <option value="{{ $tournament->id }}">
                                        {{ $tournament->name }} - {{ $tournament->category->name ?? 'Sin categoría' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            @endif

            @if($selectedTournament && $standings)
                @php
                    $currentTournament = $tournaments->firstWhere('id', $selectedTournament);
                @endphp

                <!-- Información del Torneo -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden mb-8">
                    <div class="bg-gradient-to-r from-blue-600 to-purple-600 px-6 py-4">
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
                            <div>
                                <h2 class="text-xl font-bold text-white mb-1">
                                    {{ $currentTournament->name }}
                                </h2>
                                <p class="text-blue-100">
                                    {{ $currentTournament->category->name ?? 'Sin categoría' }}
                                </p>
                            </div>
                            <div class="text-sm text-blue-100 mt-2 sm:mt-0">
                                Última actualización: {{ now()->format('d/m/Y H:i') }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabla de Posiciones -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden mb-8">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                            Posiciones Actuales
                        </h3>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Pos
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Equipo
                                    </th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        PJ
                                    </th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        PG
                                    </th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        PP
                                    </th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        SG
                                    </th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        SP
                                    </th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Ratio
                                    </th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Pts
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($standings as $index => $standing)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors
                                        {{ $index < 4 ? 'bg-green-50 dark:bg-green-900/20' : '' }}
                                        {{ $index >= count($standings) - 2 ? 'bg-red-50 dark:bg-red-900/20' : '' }}">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <span class="text-lg font-bold text-gray-900 dark:text-white mr-2">{{ $index + 1 }}</span>
                                                @if($index < 4)
                                                    <div class="w-2 h-2 bg-green-500 rounded-full" title="Clasificado a playoffs"></div>
                                                @elseif($index >= count($standings) - 2)
                                                    <div class="w-2 h-2 bg-red-500 rounded-full" title="Zona de descenso"></div>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                @if($standing->team->logo)
                                                    <img class="h-8 w-8 rounded-full mr-3" 
                                                         src="{{ Storage::url($standing->team->logo) }}" 
                                                         alt="{{ $standing->team->name }}">
                                                @else
                                                    <div class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center mr-3">
                                                        <span class="text-xs font-bold text-white">
                                                            {{ substr($standing->team->name, 0, 2) }}
                                                        </span>
                                                    </div>
                                                @endif
                                                <div>
                                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                        {{ $standing->team->name }}
                                                    </div>
                                                    @if($standing->team->club)
                                                        <div class="text-xs text-gray-500 dark:text-gray-400">
                                                            {{ $standing->team->club->name }}
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-900 dark:text-white font-medium">
                                            {{ $standing->matches_played }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-green-600 dark:text-green-400 font-medium">
                                            {{ $standing->wins }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-red-600 dark:text-red-400 font-medium">
                                            {{ $standing->losses }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-900 dark:text-white">
                                            {{ $standing->sets_won }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-900 dark:text-white">
                                            {{ $standing->sets_lost }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-900 dark:text-white">
                                            {{ number_format($standing->set_ratio, 2) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <span class="text-lg font-bold text-blue-600 dark:text-blue-400">
                                                {{ $standing->points }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="px-6 py-12 text-center">
                                            <div class="text-gray-500 dark:text-gray-400">
                                                <svg class="mx-auto h-12 w-12 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                                </svg>
                                                <p class="text-sm">No hay datos de posiciones disponibles</p>
                                                <p class="text-xs mt-1">Los datos aparecerán cuando se jueguen partidos</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Leyenda -->
                    <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700 border-t border-gray-200 dark:border-gray-600">
                        <div class="flex flex-wrap items-center justify-between text-xs text-gray-600 dark:text-gray-300">
                            <div class="flex items-center space-x-6">
                                <div class="flex items-center">
                                    <div class="w-3 h-3 bg-green-500 rounded-full mr-2"></div>
                                    <span>Clasificado a Playoffs</span>
                                </div>
                                <div class="flex items-center">
                                    <div class="w-3 h-3 bg-red-500 rounded-full mr-2"></div>
                                    <span>Zona de Descenso</span>
                                </div>
                            </div>
                            <div class="mt-2 md:mt-0">
                                <span class="font-medium">PJ:</span> Partidos Jugados • 
                                <span class="font-medium">PG:</span> Partidos Ganados • 
                                <span class="font-medium">PP:</span> Partidos Perdidos • 
                                <span class="font-medium">SG:</span> Sets Ganados • 
                                <span class="font-medium">SP:</span> Sets Perdidos • 
                                <span class="font-medium">Pts:</span> Puntos
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Información adicional -->
                <div class="grid md:grid-cols-2 gap-6">
                    <!-- Próximos partidos importantes -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                            Próximos Partidos
                        </h3>
                        <div class="space-y-3">
                            @forelse($upcomingMatches as $match)
                                <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    <div>
                                        <div class="font-medium text-gray-900 dark:text-white">
                                            {{ $match->homeTeam->name }} vs {{ $match->awayTeam->name }}
                                        </div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ $match->scheduled_at ? $match->scheduled_at->format('d M • H:i') : 'Por programar' }}
                                        </div>
                                    </div>
                                    <span class="text-xs bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 px-2 py-1 rounded-full">
                                        Programado
                                    </span>
                                </div>
                            @empty
                                <div class="text-center text-gray-500 dark:text-gray-400 py-4">
                                    <p class="text-sm">No hay partidos programados</p>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Estadísticas del torneo -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                            Estadísticas del Torneo
                        </h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="text-center">
                                <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $tournamentStats['played_matches'] ?? 0 }}</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">Partidos Jugados</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $tournamentStats['total_teams'] ?? 0 }}</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">Equipos</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ $tournamentStats['total_sets'] ?? 0 }}</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">Sets Jugados</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-orange-600 dark:text-orange-400">{{ $tournamentStats['completion_percentage'] ?? 0 }}%</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">Completado</div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <!-- Estado vacío -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-12 text-center">
                    <div class="text-gray-500 dark:text-gray-400">
                        <svg class="mx-auto h-16 w-16 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No hay torneos disponibles</h3>
                        <p class="text-sm">Las tablas de posiciones aparecerán cuando haya torneos activos</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>