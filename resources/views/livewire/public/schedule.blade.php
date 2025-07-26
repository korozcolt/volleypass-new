<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <!-- Header -->
    <div class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Calendario de Partidos</h1>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">Consulta los horarios y resultados de todos los partidos</p>
                </div>
                <div class="mt-4 sm:mt-0 flex items-center space-x-4">
                    <div class="text-sm text-gray-500 dark:text-gray-400">
                        <span class="font-medium">{{ $this->todayMatchesCount }}</span> partidos hoy
                    </div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">
                        <span class="font-medium">{{ $this->upcomingMatchesCount }}</span> próximos
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    @if($todayMatches->count() > 0)
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
            <h3 class="text-lg font-semibold text-blue-900 dark:text-blue-100 mb-3">Partidos de Hoy</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($todayMatches->take(3) as $match)
                    <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-blue-200 dark:border-blue-700">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-blue-600 dark:text-blue-400">{{ $match->match_time ?? 'Hora TBD' }}</span>
                            <span class="px-2 py-1 text-xs rounded-full {{ $match->status->getColor() }} text-white">
                                {{ $match->status->getLabel() }}
                            </span>
                        </div>
                        <div class="text-center">
                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ $match->homeTeam->name ?? 'Equipo Local' }}
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400 my-1">vs</div>
                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ $match->awayTeam->name ?? 'Equipo Visitante' }}
                            </div>
                        </div>
                        @if($match->venue)
                            <div class="mt-2 text-xs text-gray-500 dark:text-gray-400 text-center">
                                📍 {{ $match->venue }}
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- Filters -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <!-- Tournament Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Torneo</label>
                    <select wire:model.live="selectedTournament" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                        <option value="all">Todos los torneos</option>
                        @foreach($tournaments as $tournament)
                            <option value="{{ $tournament->id }}">{{ $tournament->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Date Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Fecha</label>
                    <input 
                        type="date" 
                        wire:model.live="selectedDate" 
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                    >
                </div>

                <!-- Status Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Estado</label>
                    <select wire:model.live="selectedStatus" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                        <option value="all">Todos los estados</option>
                        @foreach($matchStatuses as $status)
                            <option value="{{ $status->value }}">{{ $status->getLabel() }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Venue Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Sede</label>
                    <select wire:model.live="selectedVenue" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                        <option value="all">Todas las sedes</option>
                        @foreach($venues as $venue)
                            <option value="{{ $venue->id }}">{{ $venue->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Actions -->
                <div class="flex items-end space-x-2">
                    <button 
                        wire:click="clearFilters" 
                        class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                    >
                        Limpiar
                    </button>
                    <button 
                        wire:click="refreshData" 
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                    >
                        Actualizar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Matches List -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-12">
        @if($matches->count() > 0)
            <div class="space-y-6">
                @foreach($matches as $match)
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 hover:shadow-md transition-shadow duration-200">
                        <div class="p-6">
                            <!-- Match Header -->
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4">
                                <div class="flex items-center space-x-4">
                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ $match->match_date ? $match->match_date->format('d/m/Y') : 'Fecha TBD' }}
                                    </div>
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $match->match_time ?? 'Hora TBD' }}
                                    </div>
                                    @if($match->tournament)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                            {{ $match->tournament->name }}
                                        </span>
                                    @endif
                                </div>
                                <div class="mt-2 sm:mt-0">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $match->status->getColor() }} text-white">
                                        {{ $match->status->getLabel() }}
                                    </span>
                                </div>
                            </div>

                            <!-- Teams and Score -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-center">
                                <!-- Home Team -->
                                <div class="text-center md:text-right">
                                    <div class="flex items-center justify-center md:justify-end space-x-3">
                                        @if($match->homeTeam && $match->homeTeam->logo)
                                            <img src="{{ $match->homeTeam->logo }}" alt="{{ $match->homeTeam->name }}" class="w-10 h-10 rounded-lg object-cover">
                                        @else
                                            <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center">
                                                <span class="text-blue-600 dark:text-blue-400 font-bold text-sm">{{ $match->homeTeam ? substr($match->homeTeam->name, 0, 2) : 'EL' }}</span>
                                            </div>
                                        @endif
                                        <div>
                                            <div class="text-lg font-semibold text-gray-900 dark:text-white">
                                                {{ $match->homeTeam->name ?? 'Equipo Local' }}
                                            </div>
                                            @if($match->homeTeam && $match->homeTeam->city)
                                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                                    {{ $match->homeTeam->city->name }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Score/VS -->
                                <div class="text-center">
                                    @if($match->status->value === 'finished' && ($match->home_sets || $match->away_sets))
                                        <div class="text-3xl font-bold text-gray-900 dark:text-white">
                                            {{ $match->home_sets ?? 0 }} - {{ $match->away_sets ?? 0 }}
                                        </div>
                                        @if($match->sets_detail)
                                            <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                                Sets: {{ implode(', ', $match->sets_detail) }}
                                            </div>
                                        @endif
                                    @elseif($match->status->value === 'in_progress')
                                        <div class="text-2xl font-bold text-green-600 dark:text-green-400">
                                            {{ $match->current_set_home ?? 0 }} - {{ $match->current_set_away ?? 0 }}
                                        </div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                            Set {{ $match->current_set ?? 1 }} • EN VIVO
                                        </div>
                                    @else
                                        <div class="text-2xl font-bold text-gray-400 dark:text-gray-500">
                                            VS
                                        </div>
                                    @endif
                                </div>

                                <!-- Away Team -->
                                <div class="text-center md:text-left">
                                    <div class="flex items-center justify-center md:justify-start space-x-3">
                                        <div>
                                            <div class="text-lg font-semibold text-gray-900 dark:text-white">
                                                {{ $match->awayTeam->name ?? 'Equipo Visitante' }}
                                            </div>
                                            @if($match->awayTeam && $match->awayTeam->city)
                                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                                    {{ $match->awayTeam->city->name }}
                                                </div>
                                            @endif
                                        </div>
                                        @if($match->awayTeam && $match->awayTeam->logo)
                                            <img src="{{ $match->awayTeam->logo }}" alt="{{ $match->awayTeam->name }}" class="w-10 h-10 rounded-lg object-cover">
                                        @else
                                            <div class="w-10 h-10 bg-red-100 dark:bg-red-900 rounded-lg flex items-center justify-center">
                                                <span class="text-red-600 dark:text-red-400 font-bold text-sm">{{ $match->awayTeam ? substr($match->awayTeam->name, 0, 2) : 'EV' }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Match Details -->
                            @if($match->venue || $match->phase || $match->group)
                                <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                                    <div class="flex flex-wrap items-center gap-4 text-sm text-gray-500 dark:text-gray-400">
                                        @if($match->venue)
                                            <div class="flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                </svg>
                                                {{ $match->venue }}
                                            </div>
                                        @endif
                                        @if($match->phase)
                                            <div>Fase: {{ $match->phase }}</div>
                                        @endif
                                        @if($match->group)
                                            <div>{{ $match->group }}</div>
                                        @endif
                                        @if($match->referee)
                                            <div>Árbitro: {{ $match->referee }}</div>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $matches->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-12">
                <div class="mx-auto w-24 h-24 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No se encontraron partidos</h3>
                <p class="text-gray-500 dark:text-gray-400">No hay partidos programados para los filtros seleccionados.</p>
                <div class="mt-4">
                    <button 
                        wire:click="clearFilters" 
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                    >
                        Ver todos los partidos
                    </button>
                </div>
            </div>
        @endif
    </div>

    <!-- Upcoming Matches Sidebar (Optional) -->
    @if($upcomingMatches->count() > 0)
    <div class="fixed bottom-4 right-4 w-80 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 p-4 hidden lg:block">
        <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Próximos Partidos</h4>
        <div class="space-y-2">
            @foreach($upcomingMatches->take(3) as $match)
                <div class="text-xs">
                    <div class="font-medium text-gray-900 dark:text-white">
                        {{ $match->homeTeam->name ?? 'TBD' }} vs {{ $match->awayTeam->name ?? 'TBD' }}
                    </div>
                    <div class="text-gray-500 dark:text-gray-400">
                        {{ $match->match_date ? $match->match_date->format('d/m H:i') : 'Fecha TBD' }}
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif
</div>

<script>
    // Auto-refresh for live matches
    document.addEventListener('livewire:init', () => {
        setInterval(() => {
            if (document.querySelector('[wire\\:poll]')) {
                window.dispatchEvent(new CustomEvent('refreshData'));
            }
        }, 30000); // Refresh every 30 seconds
    });
</script>