<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h2 class="text-2xl font-bold text-gray-900 mb-2">Mis Partidos</h2>
        <p class="text-gray-600">Gestiona tus próximos partidos y revisa tu historial de juegos</p>
    </div>

    <!-- Tabs -->
    <div class="bg-white rounded-lg shadow-sm">
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8 px-6" aria-label="Tabs">
                <button 
                    wire:click="setTab('upcoming')"
                    class="{{ $selectedTab === 'upcoming' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-200">
                    <i class="fas fa-calendar-plus mr-2"></i>
                    Próximos Partidos
                </button>
                <button 
                    wire:click="setTab('history')"
                    class="{{ $selectedTab === 'history' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-200">
                    <i class="fas fa-history mr-2"></i>
                    Historial
                </button>
            </nav>
        </div>

        <div class="p-6">
            @if($selectedTab === 'upcoming')
                <!-- Próximos Partidos -->
                <div class="space-y-4">
                    @forelse($upcomingMatches as $match)
                        <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow duration-200">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-4">
                                        <div class="flex-1">
                                            <h3 class="text-lg font-semibold text-gray-900">vs {{ $match['opponent'] }}</h3>
                                            <p class="text-sm text-gray-600">{{ $match['tournament'] }}</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-sm font-medium text-gray-900">{{ date('d/m/Y', strtotime($match['date'])) }}</p>
                                            <p class="text-sm text-gray-600">{{ $match['time'] }}</p>
                                        </div>
                                    </div>
                                    <div class="mt-3 flex items-center justify-between">
                                        <div class="flex items-center space-x-4">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                <i class="fas fa-map-marker-alt mr-1"></i>
                                                {{ $match['venue'] }}
                                            </span>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                                {{ $match['convocation_status'] === 'confirmed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                                {{ $match['convocation_status'] === 'confirmed' ? 'Confirmado' : 'Pendiente' }}
                                            </span>
                                        </div>
                                        <div class="text-sm text-gray-600">
                                            <strong>Lista:</strong> {{ $match['team_list'] }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <i class="fas fa-calendar-times text-4xl text-gray-400 mb-4"></i>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">No hay partidos próximos</h3>
                            <p class="text-gray-600">Cuando tengas partidos programados aparecerán aquí</p>
                        </div>
                    @endforelse
                </div>
            @else
                <!-- Historial de Partidos -->
                <div class="space-y-4">
                    @forelse($matchHistory as $match)
                        <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow duration-200">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-4">
                                        <div class="flex-1">
                                            <h3 class="text-lg font-semibold text-gray-900">vs {{ $match['opponent'] }}</h3>
                                            <p class="text-sm text-gray-600">{{ date('d/m/Y', strtotime($match['date'])) }}</p>
                                        </div>
                                        <div class="text-right">
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                                                {{ str_starts_with($match['result'], 'W') ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ $match['result'] }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="mt-3 grid grid-cols-2 md:grid-cols-5 gap-4 text-sm">
                                        <div class="text-center">
                                            <p class="font-medium text-gray-900">{{ $match['points'] }}</p>
                                            <p class="text-gray-600">Puntos</p>
                                        </div>
                                        <div class="text-center">
                                            <p class="font-medium text-gray-900">{{ $match['aces'] }}</p>
                                            <p class="text-gray-600">Aces</p>
                                        </div>
                                        <div class="text-center">
                                            <p class="font-medium text-gray-900">{{ $match['blocks'] }}</p>
                                            <p class="text-gray-600">Bloqueos</p>
                                        </div>
                                        <div class="text-center">
                                            <p class="font-medium text-gray-900">{{ $match['efficiency'] }}%</p>
                                            <p class="text-gray-600">Eficiencia</p>
                                        </div>
                                        <div class="text-center">
                                            <p class="font-medium text-gray-900">{{ $match['playing_time'] }}</p>
                                            <p class="text-gray-600">Tiempo</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <i class="fas fa-volleyball-ball text-4xl text-gray-400 mb-4"></i>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">No hay historial de partidos</h3>
                            <p class="text-gray-600">Cuando juegues partidos aparecerán aquí</p>
                        </div>
                    @endforelse
                </div>
            @endif
        </div>
    </div>
</div>