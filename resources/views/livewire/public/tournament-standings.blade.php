<div>
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
        <div class="p-6">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">
                        Tabla de Posiciones
                    </h2>
                    @if($tournament)
                        <p class="text-gray-600 dark:text-gray-400">
                            {{ $tournament->name }} - {{ $tournament->category->name ?? 'Sin categoría' }}
                        </p>
                    @endif
                </div>
                
                <div class="flex items-center space-x-3">
                    <!-- Filtro por Grupo -->
                    @if($groups->count() > 1)
                        <select wire:model.live="selectedGroup" 
                                class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                            <option value="">Todos los grupos</option>
                            @foreach($groups as $group)
                                <option value="{{ $group->id }}">{{ $group->name }}</option>
                            @endforeach
                        </select>
                    @endif
                    
                    <!-- Botón de Actualizar -->
                    <button wire:click="refreshStandings" 
                            class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Actualizar
                    </button>
                </div>
            </div>

            <!-- Leyenda -->
            <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                <h3 class="text-sm font-medium text-gray-900 dark:text-white mb-2">Leyenda:</h3>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 text-xs">
                    <div class="flex items-center">
                        <span class="w-3 h-3 bg-green-500 rounded-full mr-2"></span>
                        <span class="text-gray-600 dark:text-gray-400">Clasificado</span>
                    </div>
                    <div class="flex items-center">
                        <span class="w-3 h-3 bg-yellow-500 rounded-full mr-2"></span>
                        <span class="text-gray-600 dark:text-gray-400">Repechaje</span>
                    </div>
                    <div class="flex items-center">
                        <span class="w-3 h-3 bg-red-500 rounded-full mr-2"></span>
                        <span class="text-gray-600 dark:text-gray-400">Eliminado</span>
                    </div>
                    <div class="flex items-center">
                        <span class="text-gray-600 dark:text-gray-400">PJ: Partidos Jugados</span>
                    </div>
                </div>
            </div>

            <!-- Tabla de Posiciones -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Pos
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Equipo
                            </th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                PJ
                            </th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                G
                            </th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                P
                            </th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Sets G
                            </th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Sets P
                            </th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Ratio
                            </th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Pts
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($standings as $index => $standing)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-150">
                                <!-- Posición -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <!-- Indicador de clasificación -->
                                        <div class="w-3 h-3 rounded-full mr-3 {{ $this->getPositionColor($index + 1) }}"></div>
                                        <span class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $index + 1 }}
                                        </span>
                                    </div>
                                </td>
                                
                                <!-- Equipo -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        @if($standing['team']['logo'])
                                            <img class="h-8 w-8 rounded-full mr-3" 
                                                 src="{{ Storage::url($standing['team']['logo']) }}" 
                                                 alt="{{ $standing['team']['name'] }}">
                                        @else
                                            <div class="h-8 w-8 rounded-full bg-gray-300 dark:bg-gray-600 flex items-center justify-center mr-3">
                                                <span class="text-xs font-medium text-gray-700 dark:text-gray-300">
                                                    {{ substr($standing['team']['name'], 0, 2) }}
                                                </span>
                                            </div>
                                        @endif
                                        <div>
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $standing['team']['name'] }}
                                            </div>
                                            @if($standing['team']['club'])
                                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                                    {{ $standing['team']['club']['name'] }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                
                                <!-- Partidos Jugados -->
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="text-sm text-gray-900 dark:text-white">
                                        {{ $standing['matches_played'] }}
                                    </span>
                                </td>
                                
                                <!-- Ganados -->
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="text-sm font-medium text-green-600 dark:text-green-400">
                                        {{ $standing['matches_won'] }}
                                    </span>
                                </td>
                                
                                <!-- Perdidos -->
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="text-sm font-medium text-red-600 dark:text-red-400">
                                        {{ $standing['matches_lost'] }}
                                    </span>
                                </td>
                                
                                <!-- Sets Ganados -->
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="text-sm text-gray-900 dark:text-white">
                                        {{ $standing['sets_won'] }}
                                    </span>
                                </td>
                                
                                <!-- Sets Perdidos -->
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="text-sm text-gray-900 dark:text-white">
                                        {{ $standing['sets_lost'] }}
                                    </span>
                                </td>
                                
                                <!-- Ratio -->
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="text-sm text-gray-900 dark:text-white">
                                        {{ number_format($standing['set_ratio'], 2) }}
                                    </span>
                                </td>
                                
                                <!-- Puntos -->
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="text-sm font-bold text-gray-900 dark:text-white">
                                        {{ $standing['points'] }}
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

            <!-- Información adicional -->
            @if($standings->count() > 0)
                <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Estadísticas generales -->
                    <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                        <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-2">Estadísticas</h4>
                        <div class="space-y-1 text-xs text-gray-600 dark:text-gray-400">
                            <div class="flex justify-between">
                                <span>Total de equipos:</span>
                                <span>{{ $standings->count() }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Partidos jugados:</span>
                                <span>{{ $totalMatches }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Sets totales:</span>
                                <span>{{ $totalSets }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Próximos partidos -->
                    <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                        <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-2">Próximos Partidos</h4>
                        <div class="space-y-2">
                            @forelse($upcomingMatches->take(3) as $match)
                                <div class="text-xs">
                                    <div class="font-medium text-gray-900 dark:text-white">
                                        {{ $match->team_a->name }} vs {{ $match->team_b->name }}
                                    </div>
                                    <div class="text-gray-600 dark:text-gray-400">
                                        {{ $match->scheduled_at->format('d/m H:i') }}
                                    </div>
                                </div>
                            @empty
                                <p class="text-xs text-gray-500 dark:text-gray-400">No hay partidos programados</p>
                            @endforelse
                        </div>
                    </div>
                    
                    <!-- Última actualización -->
                    <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                        <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-2">Información</h4>
                        <div class="space-y-1 text-xs text-gray-600 dark:text-gray-400">
                            <div>Última actualización:</div>
                            <div class="font-medium">{{ now()->format('d/m/Y H:i') }}</div>
                            <div class="mt-2">Sistema de puntos:</div>
                            <div>Victoria: 3 pts | Derrota: 0 pts</div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>