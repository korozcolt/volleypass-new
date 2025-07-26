<div class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-gray-100 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900">
    <!-- Tournament Hero Header - UEFA Style -->
    <div class="relative bg-gradient-to-r from-blue-900 via-blue-800 to-purple-900 overflow-hidden">
        <div class="absolute inset-0 bg-black/20"></div>
        <div class="absolute inset-0">
            <div class="absolute inset-0 bg-gradient-to-r from-blue-600/20 to-purple-600/20 animate-pulse"></div>
        </div>
        
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                <div class="flex-1">
                    <!-- Tournament Badge -->
                    <div class="inline-flex items-center mb-4">
                        <div class="sports-divider mr-3"></div>
                        <span class="text-white/80 text-sm font-semibold tracking-wide uppercase">Torneo Oficial</span>
                    </div>
                    
                    <h1 class="text-4xl lg:text-5xl font-black text-white mb-4 tracking-tight">
                        {{ $tournament->name }}
                    </h1>
                    
                    <!-- Status and Info Grid -->
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mt-6">
                        <div class="glass-effect rounded-xl p-4">
                            <div class="flex items-center">
                                <div class="w-3 h-3 rounded-full {{ $tournament->status === \App\Enums\TournamentStatus::InProgress ? 'bg-green-400 live-indicator' : ($tournament->status === \App\Enums\TournamentStatus::RegistrationOpen ? 'bg-blue-400' : 'bg-gray-400') }} mr-2"></div>
                                <span class="text-white text-sm font-medium">{{ $tournament->status->getLabel() }}</span>
                            </div>
                        </div>
                        
                        @if($tournament->start_date)
                        <div class="glass-effect rounded-xl p-4">
                            <div class="text-white/80 text-xs uppercase tracking-wide">Fechas</div>
                            <div class="text-white text-sm font-semibold">{{ $tournament->start_date->format('M d') }} - {{ $tournament->end_date->format('M d') }}</div>
                        </div>
                        @endif
                        
                        @if($tournament->venue)
                        <div class="glass-effect rounded-xl p-4">
                            <div class="text-white/80 text-xs uppercase tracking-wide">Sede</div>
                            <div class="text-white text-sm font-semibold">{{ $tournament->venue }}</div>
                        </div>
                        @endif
                        
                        <div class="glass-effect rounded-xl p-4">
                            <div class="text-white/80 text-xs uppercase tracking-wide">Equipos</div>
                            <div class="text-white text-sm font-semibold">{{ $tournament->teams->count() }} participantes</div>
                        </div>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="mt-8 lg:mt-0 flex flex-col space-y-3">
                    <button class="inline-flex items-center justify-center px-6 py-3 bg-white/10 backdrop-blur-sm border border-white/20 rounded-xl text-white font-semibold hover:bg-white/20 transition-all duration-300 group">
                        <svg class="w-5 h-5 mr-2 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"></path>
                        </svg>
                        Compartir
                    </button>
                    
                    <button class="inline-flex items-center justify-center px-6 py-3 bg-red-600 hover:bg-red-700 rounded-xl text-white font-semibold transition-all duration-300 group">
                        <svg class="w-5 h-5 mr-2 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                        </svg>
                        Ver en Vivo
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Sticky Navigation Tabs - Sports Style -->
    <div class="sticky top-0 z-40 bg-white/95 dark:bg-gray-800/95 backdrop-blur-sm border-b border-gray-200 dark:border-gray-700 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav class="flex space-x-0" aria-label="Tournament Navigation">
                <button wire:click="setActiveTab('overview')" 
                        class="relative py-4 px-6 font-semibold text-sm transition-all duration-300 group
                               {{ $activeTab === 'overview' ? 'text-blue-600 dark:text-blue-400' : 'text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400' }}">
                    <span class="relative z-10">📊 Resumen</span>
                    @if($activeTab === 'overview')
                        <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-blue-500 to-purple-500 rounded-t-full"></div>
                        <div class="absolute inset-0 bg-blue-50 dark:bg-blue-900/20 rounded-t-lg"></div>
                    @endif
                </button>
                
                <button wire:click="setActiveTab('standings')" 
                        class="relative py-4 px-6 font-semibold text-sm transition-all duration-300 group
                               {{ $activeTab === 'standings' ? 'text-blue-600 dark:text-blue-400' : 'text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400' }}">
                    <span class="relative z-10">🏆 Posiciones</span>
                    @if($activeTab === 'standings')
                        <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-blue-500 to-purple-500 rounded-t-full"></div>
                        <div class="absolute inset-0 bg-blue-50 dark:bg-blue-900/20 rounded-t-lg"></div>
                    @endif
                </button>
                
                <button wire:click="setActiveTab('schedule')" 
                        class="relative py-4 px-6 font-semibold text-sm transition-all duration-300 group
                               {{ $activeTab === 'schedule' ? 'text-blue-600 dark:text-blue-400' : 'text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400' }}">
                    <span class="relative z-10">📅 Calendario</span>
                    @if($activeTab === 'schedule')
                        <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-blue-500 to-purple-500 rounded-t-full"></div>
                        <div class="absolute inset-0 bg-blue-50 dark:bg-blue-900/20 rounded-t-lg"></div>
                    @endif
                </button>
                
                <button wire:click="setActiveTab('results')" 
                        class="relative py-4 px-6 font-semibold text-sm transition-all duration-300 group
                               {{ $activeTab === 'results' ? 'text-blue-600 dark:text-blue-400' : 'text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400' }}">
                    <span class="relative z-10">⚽ Resultados</span>
                    @if($activeTab === 'results')
                        <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-blue-500 to-purple-500 rounded-t-full"></div>
                        <div class="absolute inset-0 bg-blue-50 dark:bg-blue-900/20 rounded-t-lg"></div>
                    @endif
                </button>
                
                <button wire:click="setActiveTab('teams')" 
                        class="relative py-4 px-6 font-semibold text-sm transition-all duration-300 group
                               {{ $activeTab === 'teams' ? 'text-blue-600 dark:text-blue-400' : 'text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400' }}">
                    <span class="relative z-10">👥 Equipos</span>
                    @if($activeTab === 'teams')
                        <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-blue-500 to-purple-500 rounded-t-full"></div>
                        <div class="absolute inset-0 bg-blue-50 dark:bg-blue-900/20 rounded-t-lg"></div>
                    @endif
                </button>
            </nav>
        </div>
    </div>

    <!-- Contenido de Tabs -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if($activeTab === 'overview')
            <!-- Resumen del Torneo -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Información Principal -->
                <div class="lg:col-span-2 space-y-6">
                    @if($tournament->description)
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Descripción</h3>
                            <p class="text-gray-600 dark:text-gray-400">{{ $tournament->description }}</p>
                        </div>
                    @endif

                    <!-- Próximos Partidos -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Próximos Partidos</h3>
                        <div class="space-y-4">
                            @forelse($tournament->matches->where('status', 'scheduled')->take(3) as $match)
                                <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    <div class="flex items-center space-x-4">
                                        <div class="text-center">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $match->homeTeam->name }}</div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">vs</div>
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $match->awayTeam->name }}</div>
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

                <!-- Estadísticas -->
                <div class="space-y-6">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Estadísticas</h3>
                        <div class="space-y-4">
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Equipos</span>
                                <span class="font-semibold text-gray-900 dark:text-white">{{ $tournament->teams->count() }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Partidos Jugados</span>
                                <span class="font-semibold text-gray-900 dark:text-white">{{ $tournament->matches->where('status', 'finished')->count() }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Partidos Pendientes</span>
                                <span class="font-semibold text-gray-900 dark:text-white">{{ $tournament->matches->where('status', 'scheduled')->count() }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Formato del Torneo -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Formato</h3>
                        <div class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                            @if($tournament->format)
                                <p><span class="font-medium">Tipo:</span> {{ ucfirst($tournament->format) }}</p>
                            @endif
                            @if($tournament->max_teams)
                                <p><span class="font-medium">Máximo equipos:</span> {{ $tournament->max_teams }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if($activeTab === 'standings')
            <!-- Professional Standings Table -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden border border-gray-100 dark:border-gray-700">
                <div class="bg-gradient-to-r from-blue-600 to-purple-600 px-6 py-4">
                    <h3 class="text-xl font-bold text-white flex items-center">
                        <span class="mr-2">🏆</span>
                        Tabla de Posiciones
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-gray-50 dark:bg-gray-700/50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Pos</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Equipo</th>
                                <th class="px-6 py-4 text-center text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider">PJ</th>
                                <th class="px-6 py-4 text-center text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider">PG</th>
                                <th class="px-6 py-4 text-center text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider">PE</th>
                                <th class="px-6 py-4 text-center text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider">PP</th>
                                <th class="px-6 py-4 text-center text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider">GF</th>
                                <th class="px-6 py-4 text-center text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider">GC</th>
                                <th class="px-6 py-4 text-center text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider">DG</th>
                                <th class="px-6 py-4 text-center text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Pts</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach($tournament->teams->sortByDesc('points') as $index => $team)
                                <tr class="hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors duration-200 group">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold
                                                {{ $index < 3 ? 'bg-gradient-to-r from-yellow-400 to-yellow-600 text-white' : 
                                                   ($index < 8 ? 'bg-gradient-to-r from-blue-400 to-blue-600 text-white' : 'bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-300') }}">
                                                {{ $index + 1 }}
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center group-hover:scale-105 transition-transform duration-200">
                                            @if($team->logo)
                                                <img class="h-10 w-10 rounded-full mr-4 border-2 border-gray-200 dark:border-gray-600" src="{{ Storage::url($team->logo) }}" alt="{{ $team->name }}">
                                            @else
                                                <div class="h-10 w-10 rounded-full bg-gradient-to-br from-blue-400 to-purple-500 mr-4 flex items-center justify-center border-2 border-gray-200 dark:border-gray-600">
                                                    <span class="text-sm font-bold text-white">{{ substr($team->name, 0, 2) }}</span>
                                                </div>
                                            @endif
                                            <div>
                                                <div class="text-sm font-bold text-gray-900 dark:text-white">{{ $team->name }}</div>
                                                @if($team->club)
                                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ $team->club->name }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900 dark:text-white text-center">
                                        {{ $team->matches_played ?? 0 }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-green-600 dark:text-green-400 text-center">
                                        {{ $team->matches_won ?? 0 }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-yellow-600 dark:text-yellow-400 text-center">
                                        {{ $team->matches_drawn ?? 0 }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-red-600 dark:text-red-400 text-center">
                                        {{ $team->matches_lost ?? 0 }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900 dark:text-white text-center">
                                        {{ $team->goals_for ?? 0 }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900 dark:text-white text-center">
                                        {{ $team->goals_against ?? 0 }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-center
                                        {{ ($team->goals_for ?? 0) - ($team->goals_against ?? 0) > 0 ? 'text-green-600 dark:text-green-400' : 
                                           (($team->goals_for ?? 0) - ($team->goals_against ?? 0) < 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-600 dark:text-gray-400') }}">
                                        {{ ($team->goals_for ?? 0) - ($team->goals_against ?? 0) > 0 ? '+' : '' }}{{ ($team->goals_for ?? 0) - ($team->goals_against ?? 0) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold bg-gradient-to-r from-blue-500 to-purple-500 text-white">
                                            {{ $team->points ?? 0 }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Legend -->
                <div class="bg-gray-50 dark:bg-gray-700/50 px-6 py-4 border-t border-gray-200 dark:border-gray-600">
                    <div class="flex flex-wrap gap-4 text-xs text-gray-600 dark:text-gray-400">
                        <div class="flex items-center">
                            <div class="w-3 h-3 rounded-full bg-gradient-to-r from-yellow-400 to-yellow-600 mr-2"></div>
                            <span>Posiciones 1-3: Clasificación directa</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-3 h-3 rounded-full bg-gradient-to-r from-blue-400 to-blue-600 mr-2"></div>
                            <span>Posiciones 4-8: Playoffs</span>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if($activeTab === 'teams')
            <!-- Lista de Equipos -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($tournament->teams as $team)
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 hover:shadow-lg transition-shadow duration-200">
                        <div class="flex items-center space-x-4 mb-4">
                            @if($team->logo)
                                <img class="h-12 w-12 rounded-full" src="{{ Storage::url($team->logo) }}" alt="{{ $team->name }}">
                            @else
                                <div class="h-12 w-12 rounded-full bg-gray-300 dark:bg-gray-600 flex items-center justify-center">
                                    <span class="text-lg font-medium text-gray-700 dark:text-gray-300">{{ substr($team->name, 0, 2) }}</span>
                                </div>
                            @endif
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $team->name }}</h3>
                                @if($team->club)
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $team->club->name }}</p>
                                @endif
                            </div>
                        </div>
                        
                        <div class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                            <div class="flex justify-between">
                                <span>Jugadoras:</span>
                                <span class="font-medium">{{ $team->players->count() }}</span>
                            </div>
                            @if($team->coach)
                                <div class="flex justify-between">
                                    <span>Entrenador:</span>
                                    <span class="font-medium">{{ $team->coach->user->name }}</span>
                                </div>
                            @endif
                        </div>
                        
                        @if($team->is_public)
                            <div class="mt-4">
                                <a href="{{ route('public.team.show', $team) }}" 
                                   class="inline-flex items-center text-sm text-blue-600 dark:text-blue-400 hover:text-blue-500">
                                    Ver perfil
                                    <svg class="ml-1 w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                    </svg>
                                </a>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>