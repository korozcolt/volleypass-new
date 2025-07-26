<div>
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
        <!-- Header del torneo -->
        <div class="relative">
            <!-- Banner del torneo -->
            @if($tournament->banner)
                <div class="h-48 bg-cover bg-center" style="background-image: url('{{ Storage::url($tournament->banner) }}')"></div>
                <div class="absolute inset-0 bg-black bg-opacity-40"></div>
            @else
                <div class="h-48 bg-gradient-to-r from-indigo-600 to-purple-600"></div>
            @endif
            
            <!-- Información del torneo superpuesta -->
            <div class="absolute inset-0 flex items-end">
                <div class="w-full p-6 text-white">
                    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between">
                        <div class="flex-1">
                            <!-- Estado del torneo -->
                            <div class="mb-2">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $this->getTournamentStatusClass($tournament->status) }}">
                                    {{ $this->getTournamentStatusText($tournament->status) }}
                                </span>
                                @if($tournament->category)
                                    <span class="ml-2 inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-white bg-opacity-20 text-white">
                                        {{ $tournament->category->name }}
                                    </span>
                                @endif
                            </div>
                            
                            <!-- Nombre del torneo -->
                            <h1 class="text-3xl sm:text-4xl font-bold mb-2">{{ $tournament->name }}</h1>
                            
                            <!-- Información básica -->
                            <div class="flex flex-wrap items-center gap-4 text-sm opacity-90">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 002 2z"></path>
                                    </svg>
                                    {{ $tournament->start_date->format('d/m/Y') }} - {{ $tournament->end_date->format('d/m/Y') }}
                                </div>
                                
                                @if($tournament->venue)
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                        {{ $tournament->venue }}
                                    </div>
                                @endif
                                
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                    {{ $tournament->teams_count ?? 0 }} equipos
                                </div>
                            </div>
                        </div>
                        
                        <!-- Acciones -->
                        <div class="mt-4 sm:mt-0 flex items-center space-x-3">
                            <button wire:click="shareTournament" 
                                    class="inline-flex items-center px-4 py-2 border border-white border-opacity-30 rounded-md text-sm font-medium text-white hover:bg-white hover:bg-opacity-10 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-white focus:ring-opacity-50 transition-colors duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"></path>
                                </svg>
                                Compartir
                            </button>
                            
                            @if($tournament->registration_open && $tournament->status === 'upcoming')
                                <button wire:click="showRegistrationInfo" 
                                        class="inline-flex items-center px-4 py-2 bg-white bg-opacity-20 border border-transparent rounded-md text-sm font-medium text-white hover:bg-opacity-30 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-white focus:ring-opacity-50 transition-colors duration-150">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    Inscribirse
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Navegación por pestañas -->
        <div class="border-b border-gray-200 dark:border-gray-700">
            <nav class="-mb-px flex space-x-8 px-6" aria-label="Tabs">
                <button wire:click="$set('activeTab', 'overview')" 
                        class="{{ $activeTab === 'overview' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Resumen
                </button>
                
                <button wire:click="$set('activeTab', 'standings')" 
                        class="{{ $activeTab === 'standings' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Posiciones
                </button>
                
                <button wire:click="$set('activeTab', 'schedule')" 
                        class="{{ $activeTab === 'schedule' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Calendario
                </button>
                
                <button wire:click="$set('activeTab', 'results')" 
                        class="{{ $activeTab === 'results' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Resultados
                </button>
                
                <button wire:click="$set('activeTab', 'teams')" 
                        class="{{ $activeTab === 'teams' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Equipos
                </button>
            </nav>
        </div>
        
        <!-- Contenido de las pestañas -->
        <div class="p-6">
            @if($activeTab === 'overview')
                <!-- Pestaña de Resumen -->
                <div class="space-y-6">
                    <!-- Descripción del torneo -->
                    @if($tournament->description)
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-3">Descripción</h3>
                            <div class="prose dark:prose-invert max-w-none">
                                {!! nl2br(e($tournament->description)) !!}
                            </div>
                        </div>
                    @endif
                    
                    <!-- Próximos partidos destacados -->
                    @if($upcomingMatches->count() > 0)
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-3">Próximos Partidos</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($upcomingMatches->take(4) as $match)
                                    <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                                        <div class="flex items-center justify-between mb-2">
                                            <div class="text-sm text-gray-600 dark:text-gray-400">
                                                {{ $match->scheduled_at->format('d/m H:i') }}
                                            </div>
                                            @if($match->group)
                                                <span class="text-xs px-2 py-1 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 rounded">
                                                    {{ $match->group->name }}
                                                </span>
                                            @endif
                                        </div>
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-2">
                                                @if($match->team_a->logo)
                                                    <img class="h-6 w-6 rounded-full" src="{{ Storage::url($match->team_a->logo) }}" alt="{{ $match->team_a->name }}">
                                                @endif
                                                <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $match->team_a->name }}</span>
                                            </div>
                                            <div class="text-gray-400 dark:text-gray-500 text-sm">vs</div>
                                            <div class="flex items-center space-x-2">
                                                <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $match->team_b->name }}</span>
                                                @if($match->team_b->logo)
                                                    <img class="h-6 w-6 rounded-full" src="{{ Storage::url($match->team_b->logo) }}" alt="{{ $match->team_b->name }}">
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    
                    <!-- Estadísticas del torneo -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-3">Estadísticas</h3>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg text-center">
                                <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $tournament->teams_count ?? 0 }}</div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">Equipos</div>
                            </div>
                            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg text-center">
                                <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $totalMatches }}</div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">Partidos</div>
                            </div>
                            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg text-center">
                                <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $completedMatches }}</div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">Completados</div>
                            </div>
                            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg text-center">
                                <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $totalSets }}</div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">Sets jugados</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Información del organizador -->
                    @if($tournament->organizer)
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-3">Organizador</h3>
                            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                <div class="flex items-center space-x-3">
                                    @if($tournament->organizer->avatar)
                                        <img class="h-10 w-10 rounded-full" src="{{ Storage::url($tournament->organizer->avatar) }}" alt="{{ $tournament->organizer->name }}">
                                    @else
                                        <div class="h-10 w-10 rounded-full bg-gray-300 dark:bg-gray-600 flex items-center justify-center">
                                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                                {{ substr($tournament->organizer->name, 0, 2) }}
                                            </span>
                                        </div>
                                    @endif
                                    <div>
                                        <div class="font-medium text-gray-900 dark:text-white">{{ $tournament->organizer->name }}</div>
                                        @if($tournament->organizer->email)
                                            <div class="text-sm text-gray-600 dark:text-gray-400">{{ $tournament->organizer->email }}</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            @elseif($activeTab === 'standings')
                <!-- Componente de posiciones -->
                @livewire('public.tournament-standings', ['tournament' => $tournament])
            @elseif($activeTab === 'schedule')
                <!-- Componente de calendario -->
                @livewire('public.tournament-schedule', ['tournament' => $tournament])
            @elseif($activeTab === 'results')
                <!-- Componente de resultados -->
                @livewire('public.tournament-results', ['tournament' => $tournament])
            @elseif($activeTab === 'teams')
                <!-- Lista de equipos -->
                <div>
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Equipos Participantes</h3>
                        <div class="text-sm text-gray-600 dark:text-gray-400">
                            {{ $teams->count() }} equipos
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($teams as $team)
                            <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-6 hover:shadow-md transition-shadow duration-150">
                                <div class="flex items-center space-x-4">
                                    @if($team->logo)
                                        <img class="h-12 w-12 rounded-full" src="{{ Storage::url($team->logo) }}" alt="{{ $team->name }}">
                                    @else
                                        <div class="h-12 w-12 rounded-full bg-gray-300 dark:bg-gray-600 flex items-center justify-center">
                                            <span class="text-lg font-medium text-gray-700 dark:text-gray-300">
                                                {{ substr($team->name, 0, 2) }}
                                            </span>
                                        </div>
                                    @endif
                                    <div class="flex-1">
                                        <h4 class="font-medium text-gray-900 dark:text-white">{{ $team->name }}</h4>
                                        @if($team->club)
                                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $team->club->name }}</p>
                                        @endif
                                        @if($team->city)
                                            <p class="text-xs text-gray-500 dark:text-gray-500">{{ $team->city }}</p>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="mt-4 flex items-center justify-between text-sm">
                                    <div class="text-gray-600 dark:text-gray-400">
                                        {{ $team->players_count ?? 0 }} jugadores
                                    </div>
                                    <button wire:click="viewTeam({{ $team->id }})" 
                                            class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-500 dark:hover:text-indigo-300 font-medium">
                                        Ver perfil
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
    
    <!-- Modal de información de inscripción -->
    @if($showRegistrationModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeRegistrationModal"></div>
                
                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">
                                Información de Inscripción
                            </h3>
                            <button wire:click="closeRegistrationModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                        
                        <div class="space-y-4">
                            <div>
                                <h4 class="font-medium text-gray-900 dark:text-white mb-2">Fechas importantes</h4>
                                <div class="space-y-1 text-sm text-gray-600 dark:text-gray-400">
                                    <div>Inicio de inscripciones: {{ $tournament->registration_start_date?->format('d/m/Y') ?? 'No especificado' }}</div>
                                    <div>Fin de inscripciones: {{ $tournament->registration_end_date?->format('d/m/Y') ?? 'No especificado' }}</div>
                                    <div>Inicio del torneo: {{ $tournament->start_date->format('d/m/Y') }}</div>
                                </div>
                            </div>
                            
                            @if($tournament->registration_fee)
                                <div>
                                    <h4 class="font-medium text-gray-900 dark:text-white mb-2">Costo de inscripción</h4>
                                    <div class="text-lg font-bold text-gray-900 dark:text-white">
                                        ${{ number_format($tournament->registration_fee, 0, ',', '.') }}
                                    </div>
                                </div>
                            @endif
                            
                            @if($tournament->max_teams)
                                <div>
                                    <h4 class="font-medium text-gray-900 dark:text-white mb-2">Cupos disponibles</h4>
                                    <div class="text-sm text-gray-600 dark:text-gray-400">
                                        {{ $tournament->teams_count ?? 0 }} de {{ $tournament->max_teams }} equipos inscritos
                                    </div>
                                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2 mt-1">
                                        <div class="bg-indigo-600 h-2 rounded-full" style="width: {{ ($tournament->teams_count / $tournament->max_teams) * 100 }}%"></div>
                                    </div>
                                </div>
                            @endif
                            
                            <div class="bg-yellow-50 dark:bg-yellow-900 border border-yellow-200 dark:border-yellow-700 rounded-md p-4">
                                <div class="flex">
                                    <svg class="w-5 h-5 text-yellow-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                    </svg>
                                    <div class="text-sm text-yellow-800 dark:text-yellow-200">
                                        Para inscribirse en este torneo, debe contactar directamente al organizador.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button wire:click="contactOrganizer" 
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Contactar Organizador
                        </button>
                        <button wire:click="closeRegistrationModal" 
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>