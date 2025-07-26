<div>
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
        <div class="p-6">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">
                        Resultados
                    </h2>
                    @if($tournament)
                        <p class="text-gray-600 dark:text-gray-400">
                            {{ $tournament->name }} - {{ $tournament->category->name ?? 'Sin categoría' }}
                        </p>
                    @endif
                </div>
                
                <div class="flex flex-col sm:flex-row items-start sm:items-center space-y-2 sm:space-y-0 sm:space-x-3">
                    <!-- Filtros -->
                    <div class="flex flex-wrap gap-2">
                        <!-- Filtro por grupo -->
                        @if($groups->count() > 1)
                            <select wire:model.live="selectedGroup" 
                                    class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm text-sm">
                                <option value="">Todos los grupos</option>
                                @foreach($groups as $group)
                                    <option value="{{ $group->id }}">{{ $group->name }}</option>
                                @endforeach
                            </select>
                        @endif
                        
                        <!-- Filtro por jornada -->
                        @if($rounds->count() > 1)
                            <select wire:model.live="selectedRound" 
                                    class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm text-sm">
                                <option value="">Todas las jornadas</option>
                                @foreach($rounds as $round)
                                    <option value="{{ $round }}">Jornada {{ $round }}</option>
                                @endforeach
                            </select>
                        @endif
                        
                        <!-- Filtro por equipo -->
                        <select wire:model.live="selectedTeam" 
                                class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm text-sm">
                            <option value="">Todos los equipos</option>
                            @foreach($teams as $team)
                                <option value="{{ $team->id }}">{{ $team->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- Ordenamiento -->
                    <select wire:model.live="sortBy" 
                            class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm text-sm">
                        <option value="date_desc">Más recientes</option>
                        <option value="date_asc">Más antiguos</option>
                        <option value="round_desc">Jornada (desc)</option>
                        <option value="round_asc">Jornada (asc)</option>
                    </select>
                </div>
            </div>

            <!-- Estadísticas rápidas -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $totalMatches }}</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">Partidos jugados</div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $totalSets }}</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">Sets jugados</div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $averageMatchDuration }}</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">Duración promedio</div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $completionPercentage }}%</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">Completado</div>
                </div>
            </div>

            <!-- Lista de resultados -->
            <div class="space-y-4">
                @forelse($matches as $match)
                    <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-6 hover:shadow-md transition-shadow duration-150">
                        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                            <!-- Información del partido -->
                            <div class="flex-1">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="flex items-center space-x-3">
                                        <!-- Fecha y hora -->
                                        <div class="text-sm text-gray-600 dark:text-gray-400">
                                            {{ $match->scheduled_at->format('d/m/Y H:i') }}
                                        </div>
                                        
                                        @if($match->group)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200">
                                                {{ $match->group->name }}
                                            </span>
                                        @endif
                                        
                                        @if($match->round)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                                                Jornada {{ $match->round }}
                                            </span>
                                        @endif
                                    </div>
                                    
                                    <!-- Duración del partido -->
                                    @if($match->duration)
                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                            Duración: {{ $match->duration }} min
                                        </div>
                                    @endif
                                </div>
                                
                                <!-- Equipos y resultado -->
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-6">
                                        <!-- Equipo A -->
                                        <div class="flex items-center space-x-3">
                                            @if($match->team_a->logo)
                                                <img class="h-10 w-10 rounded-full" 
                                                     src="{{ Storage::url($match->team_a->logo) }}" 
                                                     alt="{{ $match->team_a->name }}">
                                            @else
                                                <div class="h-10 w-10 rounded-full bg-gray-300 dark:bg-gray-600 flex items-center justify-center">
                                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                                        {{ substr($match->team_a->name, 0, 2) }}
                                                    </span>
                                                </div>
                                            @endif
                                            <div>
                                                <div class="font-medium text-gray-900 dark:text-white {{ $match->team_a_sets > $match->team_b_sets ? 'font-bold' : '' }}">
                                                    {{ $match->team_a->name }}
                                                </div>
                                                @if($match->team_a->club)
                                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                                        {{ $match->team_a->club->name }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        
                                        <!-- Resultado principal -->
                                        <div class="text-center">
                                            <div class="text-2xl font-bold text-gray-900 dark:text-white">
                                                <span class="{{ $match->team_a_sets > $match->team_b_sets ? 'text-green-600 dark:text-green-400' : '' }}">
                                                    {{ $match->team_a_sets }}
                                                </span>
                                                <span class="text-gray-400 mx-2">-</span>
                                                <span class="{{ $match->team_b_sets > $match->team_a_sets ? 'text-green-600 dark:text-green-400' : '' }}">
                                                    {{ $match->team_b_sets }}
                                                </span>
                                            </div>
                                            
                                            <!-- Resultado por sets -->
                                            @if($match->sets && count($match->sets) > 0)
                                                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                    @foreach($match->sets as $set)
                                                        <span class="inline-block mx-1">
                                                            {{ $set['team_a_score'] }}-{{ $set['team_b_score'] }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                        
                                        <!-- Equipo B -->
                                        <div class="flex items-center space-x-3">
                                            <div class="text-right">
                                                <div class="font-medium text-gray-900 dark:text-white {{ $match->team_b_sets > $match->team_a_sets ? 'font-bold' : '' }}">
                                                    {{ $match->team_b->name }}
                                                </div>
                                                @if($match->team_b->club)
                                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                                        {{ $match->team_b->club->name }}
                                                    </div>
                                                @endif
                                            </div>
                                            @if($match->team_b->logo)
                                                <img class="h-10 w-10 rounded-full" 
                                                     src="{{ Storage::url($match->team_b->logo) }}" 
                                                     alt="{{ $match->team_b->name }}">
                                            @else
                                                <div class="h-10 w-10 rounded-full bg-gray-300 dark:bg-gray-600 flex items-center justify-center">
                                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                                        {{ substr($match->team_b->name, 0, 2) }}
                                                    </span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Información adicional -->
                                <div class="mt-4 flex flex-wrap items-center gap-4 text-sm text-gray-600 dark:text-gray-400">
                                    @if($match->venue)
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            </svg>
                                            {{ $match->venue }}{{ $match->court ? ' - Cancha ' . $match->court : '' }}
                                        </div>
                                    @endif
                                    
                                    @if($match->referee)
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                            </svg>
                                            Árbitro: {{ $match->referee->name }}
                                        </div>
                                    @endif
                                    
                                    @if($match->mvp)
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-1 text-yellow-500" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"></path>
                                            </svg>
                                            MVP: {{ $match->mvp->name }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Acciones -->
                            <div class="mt-4 lg:mt-0 lg:ml-6 flex items-center space-x-2">
                                <button wire:click="viewMatchDetails({{ $match->id }})" 
                                        class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    Ver detalles
                                </button>
                                
                                <button wire:click="shareMatch({{ $match->id }})" 
                                        class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No hay resultados disponibles</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Los resultados aparecerán cuando se completen los partidos.</p>
                    </div>
                @endforelse
            </div>

            <!-- Paginación -->
            @if($matches->hasPages())
                <div class="mt-6">
                    {{ $matches->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Modal de detalles del partido -->
    @if($selectedMatch)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeMatchDetails"></div>
                
                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                    <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">
                                Detalles del Partido
                            </h3>
                            <button wire:click="closeMatchDetails" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                        
                        <!-- Contenido del modal con detalles del partido -->
                        <div class="space-y-6">
                            <!-- Información básica -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <h4 class="font-medium text-gray-900 dark:text-white mb-2">Información del Partido</h4>
                                    <div class="space-y-1 text-sm text-gray-600 dark:text-gray-400">
                                        <div>Fecha: {{ $selectedMatch->scheduled_at->format('d/m/Y H:i') }}</div>
                                        <div>Duración: {{ $selectedMatch->duration ?? 'N/A' }} min</div>
                                        <div>Lugar: {{ $selectedMatch->venue ?? 'N/A' }}</div>
                                        @if($selectedMatch->court)
                                            <div>Cancha: {{ $selectedMatch->court }}</div>
                                        @endif
                                    </div>
                                </div>
                                
                                <div>
                                    <h4 class="font-medium text-gray-900 dark:text-white mb-2">Resultado Final</h4>
                                    <div class="text-3xl font-bold text-gray-900 dark:text-white">
                                        {{ $selectedMatch->team_a_sets }} - {{ $selectedMatch->team_b_sets }}
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Resultado por sets -->
                            @if($selectedMatch->sets && count($selectedMatch->sets) > 0)
                                <div>
                                    <h4 class="font-medium text-gray-900 dark:text-white mb-2">Resultado por Sets</h4>
                                    <div class="overflow-x-auto">
                                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                            <thead>
                                                <tr>
                                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Set</th>
                                                    <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ $selectedMatch->team_a->name }}</th>
                                                    <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ $selectedMatch->team_b->name }}</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                                @foreach($selectedMatch->sets as $index => $set)
                                                    <tr>
                                                        <td class="px-4 py-2 text-sm font-medium text-gray-900 dark:text-white">Set {{ $index + 1 }}</td>
                                                        <td class="px-4 py-2 text-center text-sm {{ $set['team_a_score'] > $set['team_b_score'] ? 'font-bold text-green-600 dark:text-green-400' : 'text-gray-900 dark:text-white' }}">
                                                            {{ $set['team_a_score'] }}
                                                        </td>
                                                        <td class="px-4 py-2 text-center text-sm {{ $set['team_b_score'] > $set['team_a_score'] ? 'font-bold text-green-600 dark:text-green-400' : 'text-gray-900 dark:text-white' }}">
                                                            {{ $set['team_b_score'] }}
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>