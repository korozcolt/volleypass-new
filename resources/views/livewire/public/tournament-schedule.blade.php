<div>
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
        <div class="p-6">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">
                        Calendario de Partidos
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
                        <!-- Filtro por fecha -->
                        <input type="date" 
                               wire:model.live="selectedDate"
                               class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm text-sm">
                        
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
                        
                        <!-- Filtro por estado -->
                        <select wire:model.live="selectedStatus" 
                                class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm text-sm">
                            <option value="">Todos los estados</option>
                            <option value="scheduled">Programados</option>
                            <option value="in_progress">En curso</option>
                            <option value="finished">Finalizados</option>
                            <option value="postponed">Pospuestos</option>
                        </select>
                    </div>
                    
                    <!-- Botones de vista -->
                    <div class="flex rounded-lg border border-gray-300 dark:border-gray-600 overflow-hidden">
                        <button wire:click="$set('viewMode', 'list')" 
                                class="px-3 py-2 text-sm font-medium {{ $viewMode === 'list' ? 'bg-indigo-600 text-white' : 'bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                            </svg>
                        </button>
                        <button wire:click="$set('viewMode', 'calendar')" 
                                class="px-3 py-2 text-sm font-medium {{ $viewMode === 'calendar' ? 'bg-indigo-600 text-white' : 'bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            @if($viewMode === 'list')
                <!-- Vista de Lista -->
                <div class="space-y-6">
                    @forelse($matchesByDate as $date => $dayMatches)
                        <div class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                            <!-- Encabezado del día -->
                            <div class="bg-gray-50 dark:bg-gray-700 px-6 py-3 border-b border-gray-200 dark:border-gray-600">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                    {{ \Carbon\Carbon::parse($date)->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY') }}
                                </h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    {{ $dayMatches->count() }} {{ $dayMatches->count() === 1 ? 'partido' : 'partidos' }}
                                </p>
                            </div>
                            
                            <!-- Partidos del día -->
                            <div class="divide-y divide-gray-200 dark:divide-gray-600">
                                @foreach($dayMatches as $match)
                                    <div class="p-6 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-150">
                                        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                                            <!-- Información del partido -->
                                            <div class="flex-1">
                                                <div class="flex items-center justify-between mb-2">
                                                    <div class="flex items-center space-x-3">
                                                        <!-- Estado del partido -->
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $this->getMatchStatusClass($match->status) }}">
                                                            {{ $this->getMatchStatusText($match->status) }}
                                                        </span>
                                                        
                                                        @if($match->group)
                                                            <span class="text-xs text-gray-500 dark:text-gray-400">
                                                                {{ $match->group->name }}
                                                            </span>
                                                        @endif
                                                        
                                                        @if($match->round)
                                                            <span class="text-xs text-gray-500 dark:text-gray-400">
                                                                Jornada {{ $match->round }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                    
                                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                        {{ $match->scheduled_at->format('H:i') }}
                                                    </div>
                                                </div>
                                                
                                                <!-- Equipos -->
                                                <div class="flex items-center justify-between">
                                                    <div class="flex items-center space-x-4">
                                                        <!-- Equipo A -->
                                                        <div class="flex items-center space-x-3">
                                                            @if($match->team_a->logo)
                                                                <img class="h-8 w-8 rounded-full" 
                                                                     src="{{ Storage::url($match->team_a->logo) }}" 
                                                                     alt="{{ $match->team_a->name }}">
                                                            @else
                                                                <div class="h-8 w-8 rounded-full bg-gray-300 dark:bg-gray-600 flex items-center justify-center">
                                                                    <span class="text-xs font-medium text-gray-700 dark:text-gray-300">
                                                                        {{ substr($match->team_a->name, 0, 2) }}
                                                                    </span>
                                                                </div>
                                                            @endif
                                                            <span class="font-medium text-gray-900 dark:text-white">
                                                                {{ $match->team_a->name }}
                                                            </span>
                                                        </div>
                                                        
                                                        <!-- Resultado -->
                                                        <div class="flex items-center space-x-2">
                                                            @if($match->status === 'finished')
                                                                <div class="text-lg font-bold text-gray-900 dark:text-white">
                                                                    {{ $match->team_a_sets }} - {{ $match->team_b_sets }}
                                                                </div>
                                                            @else
                                                                <div class="text-gray-400 dark:text-gray-500">
                                                                    vs
                                                                </div>
                                                            @endif
                                                        </div>
                                                        
                                                        <!-- Equipo B -->
                                                        <div class="flex items-center space-x-3">
                                                            <span class="font-medium text-gray-900 dark:text-white">
                                                                {{ $match->team_b->name }}
                                                            </span>
                                                            @if($match->team_b->logo)
                                                                <img class="h-8 w-8 rounded-full" 
                                                                     src="{{ Storage::url($match->team_b->logo) }}" 
                                                                     alt="{{ $match->team_b->name }}">
                                                            @else
                                                                <div class="h-8 w-8 rounded-full bg-gray-300 dark:bg-gray-600 flex items-center justify-center">
                                                                    <span class="text-xs font-medium text-gray-700 dark:text-gray-300">
                                                                        {{ substr($match->team_b->name, 0, 2) }}
                                                                    </span>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <!-- Información adicional -->
                                                @if($match->venue || $match->court)
                                                    <div class="mt-2 flex items-center text-sm text-gray-600 dark:text-gray-400">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                        </svg>
                                                        {{ $match->venue }}{{ $match->court ? ' - Cancha ' . $match->court : '' }}
                                                    </div>
                                                @endif
                                            </div>
                                            
                                            <!-- Acciones -->
                                            <div class="mt-4 lg:mt-0 lg:ml-6 flex items-center space-x-2">
                                                @if($match->status === 'finished')
                                                    <button wire:click="viewMatchDetails({{ $match->id }})" 
                                                            class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                                        Ver detalles
                                                    </button>
                                                @endif
                                                
                                                @if($match->status === 'in_progress')
                                                    <span class="inline-flex items-center px-3 py-2 rounded-md text-sm font-medium bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">
                                                        <span class="w-2 h-2 bg-green-400 rounded-full mr-2 animate-pulse"></span>
                                                        En vivo
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No hay partidos programados</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Los partidos aparecerán cuando sean programados por los organizadores.</p>
                        </div>
                    @endforelse
                </div>
            @else
                <!-- Vista de Calendario -->
                <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                    <!-- Encabezado del calendario -->
                    <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
                        <button wire:click="previousMonth" 
                                class="p-2 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                        </button>
                        
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                            {{ $currentMonth->locale('es')->isoFormat('MMMM YYYY') }}
                        </h3>
                        
                        <button wire:click="nextMonth" 
                                class="p-2 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </button>
                    </div>
                    
                    <!-- Días de la semana -->
                    <div class="grid grid-cols-7 border-b border-gray-200 dark:border-gray-700">
                        @foreach(['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'] as $day)
                            <div class="p-3 text-center text-sm font-medium text-gray-500 dark:text-gray-400 border-r border-gray-200 dark:border-gray-700 last:border-r-0">
                                {{ $day }}
                            </div>
                        @endforeach
                    </div>
                    
                    <!-- Días del calendario -->
                    <div class="grid grid-cols-7">
                        @foreach($calendarDays as $day)
                            <div class="min-h-[100px] p-2 border-r border-b border-gray-200 dark:border-gray-700 last:border-r-0 {{ $day['isCurrentMonth'] ? 'bg-white dark:bg-gray-800' : 'bg-gray-50 dark:bg-gray-900' }}">
                                <div class="text-sm {{ $day['isCurrentMonth'] ? 'text-gray-900 dark:text-white' : 'text-gray-400 dark:text-gray-600' }}">
                                    {{ $day['date']->format('j') }}
                                </div>
                                
                                @if($day['matches']->count() > 0)
                                    <div class="mt-1 space-y-1">
                                        @foreach($day['matches']->take(3) as $match)
                                            <div class="text-xs p-1 rounded {{ $this->getMatchStatusClass($match->status, true) }} truncate">
                                                {{ $match->team_a->name }} vs {{ $match->team_b->name }}
                                            </div>
                                        @endforeach
                                        
                                        @if($day['matches']->count() > 3)
                                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                                +{{ $day['matches']->count() - 3 }} más
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>