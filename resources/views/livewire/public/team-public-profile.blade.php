<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <!-- Header del Equipo -->
    <div class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex flex-col lg:flex-row lg:items-start lg:space-x-8">
                <!-- Logo y Info Principal -->
                <div class="flex items-center space-x-6 mb-6 lg:mb-0">
                    @if($team->logo)
                        <img class="h-24 w-24 lg:h-32 lg:w-32 rounded-full shadow-lg" 
                             src="{{ Storage::url($team->logo) }}" 
                             alt="{{ $team->name }}">
                    @else
                        <div class="h-24 w-24 lg:h-32 lg:w-32 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 shadow-lg flex items-center justify-center">
                            <span class="text-2xl lg:text-4xl font-bold text-white">{{ substr($team->name, 0, 2) }}</span>
                        </div>
                    @endif
                    
                    <div>
                        <h1 class="text-3xl lg:text-4xl font-bold text-gray-900 dark:text-white mb-2">
                            {{ $team->name }}
                        </h1>
                        @if($team->club)
                            <p class="text-lg text-gray-600 dark:text-gray-400 mb-2">{{ $team->club->name }}</p>
                        @endif
                        <div class="flex flex-wrap gap-4 text-sm text-gray-500 dark:text-gray-400">
                            @if($team->founded_year)
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                                    </svg>
                                    Fundado en {{ $team->founded_year }}
                                </div>
                            @endif
                            @if($team->city)
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ $team->city }}
                                </div>
                            @endif
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                {{ $team->players->count() }} jugadoras
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Estadísticas Rápidas -->
                <div class="flex-1 lg:max-w-md">
                    <div class="grid grid-cols-3 gap-4">
                        <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4 text-center">
                            <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $team->tournaments->count() }}</div>
                            <div class="text-xs text-blue-600 dark:text-blue-400">Torneos</div>
                        </div>
                        <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-4 text-center">
                            <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $team->matches->where('status', 'finished')->count() }}</div>
                            <div class="text-xs text-green-600 dark:text-green-400">Partidos</div>
                        </div>
                        <div class="bg-purple-50 dark:bg-purple-900/20 rounded-lg p-4 text-center">
                            <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ $team->wins ?? 0 }}</div>
                            <div class="text-xs text-purple-600 dark:text-purple-400">Victorias</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Navegación de Tabs -->
    <div class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav class="flex space-x-8" aria-label="Tabs">
                <button wire:click="setActiveTab('overview')" 
                        class="py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-200
                               {{ $activeTab === 'overview' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300' }}">
                    Resumen
                </button>
                <button wire:click="setActiveTab('players')" 
                        class="py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-200
                               {{ $activeTab === 'players' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300' }}">
                    Jugadoras ({{ $team->players->count() }})
                </button>
                <button wire:click="setActiveTab('matches')" 
                        class="py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-200
                               {{ $activeTab === 'matches' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300' }}">
                    Partidos
                </button>
                <button wire:click="setActiveTab('tournaments')" 
                        class="py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-200
                               {{ $activeTab === 'tournaments' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300' }}">
                    Torneos
                </button>
            </nav>
        </div>
    </div>

    <!-- Contenido de Tabs -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if($activeTab === 'overview')
            <!-- Resumen del Equipo -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Información Principal -->
                <div class="lg:col-span-2 space-y-6">
                    @if($team->description)
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Acerca del Equipo</h3>
                            <p class="text-gray-600 dark:text-gray-400">{{ $team->description }}</p>
                        </div>
                    @endif

                    <!-- Cuerpo Técnico -->
                    @if($team->coach)
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Cuerpo Técnico</h3>
                            <div class="flex items-center space-x-4">
                                @if($team->coach->user->profile_photo_path)
                                    <img class="h-12 w-12 rounded-full" src="{{ Storage::url($team->coach->user->profile_photo_path) }}" alt="{{ $team->coach->user->name }}">
                                @else
                                    <div class="h-12 w-12 rounded-full bg-gray-300 dark:bg-gray-600 flex items-center justify-center">
                                        <span class="text-lg font-medium text-gray-700 dark:text-gray-300">{{ substr($team->coach->user->name, 0, 1) }}</span>
                                    </div>
                                @endif
                                <div>
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $team->coach->user->name }}</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">Entrenador Principal</div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Próximos Partidos -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Próximos Partidos</h3>
                        <div class="space-y-4">
                            @forelse($team->matches->where('status', 'scheduled')->take(3) as $match)
                                <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    <div class="flex items-center space-x-4">
                                        <div class="text-center">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $match->homeTeam->id === $team->id ? $match->awayTeam->name : $match->homeTeam->name }}
                                            </div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ $match->homeTeam->id === $team->id ? 'vs' : 'en casa de' }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $match->scheduled_at->format('d M') }}
                                        </div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ $match->scheduled_at->format('H:i') }}
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <p class="text-gray-500 dark:text-gray-400 text-center py-4">No hay partidos programados</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Estadísticas Detalladas -->
                <div class="space-y-6">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Estadísticas</h3>
                        <div class="space-y-4">
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Partidos Jugados</span>
                                <span class="font-semibold text-gray-900 dark:text-white">{{ $team->matches->where('status', 'finished')->count() }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Victorias</span>
                                <span class="font-semibold text-green-600 dark:text-green-400">{{ $team->wins ?? 0 }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Derrotas</span>
                                <span class="font-semibold text-red-600 dark:text-red-400">{{ $team->losses ?? 0 }}</span>
                            </div>
                            @if(($team->wins ?? 0) + ($team->losses ?? 0) > 0)
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">% Victorias</span>
                                    <span class="font-semibold text-gray-900 dark:text-white">
                                        {{ round((($team->wins ?? 0) / (($team->wins ?? 0) + ($team->losses ?? 0))) * 100, 1) }}%
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Información del Club -->
                    @if($team->club)
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Club</h3>
                            <div class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                                <p><span class="font-medium">Nombre:</span> {{ $team->club->name }}</p>
                                @if($team->club->city)
                                    <p><span class="font-medium">Ciudad:</span> {{ $team->club->city }}</p>
                                @endif
                                @if($team->club->founded_year)
                                    <p><span class="font-medium">Fundado:</span> {{ $team->club->founded_year }}</p>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        @if($activeTab === 'players')
            <!-- Lista de Jugadoras -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Plantilla Actual</h3>
                </div>
                <div class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($team->players as $player)
                        <div class="px-6 py-4 hover:bg-gray-50 dark:hover:bg-gray-700">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-4">
                                    @if($player->user->profile_photo_path)
                                        <img class="h-10 w-10 rounded-full" src="{{ Storage::url($player->user->profile_photo_path) }}" alt="{{ $player->user->name }}">
                                    @else
                                        <div class="h-10 w-10 rounded-full bg-gray-300 dark:bg-gray-600 flex items-center justify-center">
                                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ substr($player->user->name, 0, 1) }}</span>
                                        </div>
                                    @endif
                                    <div>
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $player->user->name }}</div>
                                        @if($player->position)
                                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ $player->position }}</div>
                                        @endif
                                    </div>
                                </div>
                                <div class="text-right">
                                    @if($player->jersey_number)
                                        <div class="text-lg font-bold text-gray-900 dark:text-white">#{{ $player->jersey_number }}</div>
                                    @endif
                                    @if($player->height)
                                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $player->height }} cm</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        @if($activeTab === 'matches')
            <!-- Historial de Partidos -->
            <div class="space-y-6">
                <!-- Próximos Partidos -->
                @if($team->matches->where('status', 'scheduled')->count() > 0)
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Próximos Partidos</h3>
                        <div class="space-y-4">
                            @foreach($team->matches->where('status', 'scheduled') as $match)
                                <div class="flex items-center justify-between p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                                    <div class="flex items-center space-x-4">
                                        <div class="text-center">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $match->homeTeam->name }} vs {{ $match->awayTeam->name }}
                                            </div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ $match->tournament->name }}</div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $match->scheduled_at->format('d M Y') }}
                                        </div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ $match->scheduled_at->format('H:i') }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Partidos Finalizados -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Resultados Recientes</h3>
                    <div class="space-y-4">
                        @forelse($team->matches->where('status', 'finished')->sortByDesc('finished_at')->take(10) as $match)
                            <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <div class="flex items-center space-x-4">
                                    <div class="text-center">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $match->homeTeam->name }} vs {{ $match->awayTeam->name }}
                                        </div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $match->tournament->name }}</div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-lg font-bold text-gray-900 dark:text-white">
                                        {{ $match->home_score ?? 0 }} - {{ $match->away_score ?? 0 }}
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $match->finished_at->format('d M Y') }}
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-500 dark:text-gray-400 text-center py-4">No hay partidos finalizados</p>
                        @endforelse
                    </div>
                </div>
            </div>
        @endif

        @if($activeTab === 'tournaments')
            <!-- Torneos del Equipo -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($team->tournaments as $tournament)
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 hover:shadow-lg transition-shadow duration-200">
                        <div class="flex items-center justify-between mb-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $tournament->status === 'active' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 
                                   ($tournament->status === 'completed' ? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300' : 
                                    'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200') }}">
                                {{ ucfirst($tournament->status) }}
                            </span>
                        </div>
                        
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">{{ $tournament->name }}</h3>
                        
                        <div class="space-y-2 text-sm text-gray-600 dark:text-gray-400 mb-4">
                            @if($tournament->start_date)
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ $tournament->start_date->format('d M Y') }}
                                </div>
                            @endif
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                {{ $tournament->teams->count() }} equipos
                            </div>
                        </div>
                        
                        <a href="{{ route('public.tournament.show', $tournament) }}" 
                           class="inline-flex items-center text-sm text-blue-600 dark:text-blue-400 hover:text-blue-500">
                            Ver torneo
                            <svg class="ml-1 w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"/>
                            </svg>
                        </a>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>