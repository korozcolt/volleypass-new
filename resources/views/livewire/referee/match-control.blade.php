<div class="min-h-screen bg-gray-100 dark:bg-gray-900">
    <!-- Header del Partido -->
    <div class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{ $match['home_team'] }} vs {{ $match['away_team'] }}
                    </h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        {{ $match['tournament'] }} • {{ $match['venue'] }} • {{ $match['date'] }} {{ $match['time'] }}
                    </p>
                </div>
                <div class="flex items-center space-x-4">
                    @if($isLive)
                        <div class="flex items-center space-x-2">
                            <div class="w-3 h-3 bg-red-500 rounded-full animate-pulse"></div>
                            <span class="text-red-600 dark:text-red-400 font-semibold">EN VIVO</span>
                        </div>
                        <button wire:click="endMatch" 
                                class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                            Finalizar Partido
                        </button>
                    @else
                        <button wire:click="startMatch" 
                                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                            Iniciar Partido
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="grid lg:grid-cols-3 gap-6">
            <!-- Panel Principal de Marcador -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Marcador Principal -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <div class="text-center mb-6">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Set {{ $currentSet }}</h2>
                        <div class="flex items-center justify-center space-x-8">
                            <!-- Equipo Local -->
                            <div class="text-center">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">{{ $match['home_team'] }}</h3>
                                <div class="text-6xl font-bold text-blue-600 dark:text-blue-400 mb-4">{{ $homeScore }}</div>
                                <div class="flex space-x-2">
                                    <button wire:click="addPoint('home')" 
                                            @if(!$isLive) disabled @endif
                                            class="bg-green-600 hover:bg-green-700 disabled:bg-gray-400 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                                        +1
                                    </button>
                                    <button wire:click="removePoint('home')" 
                                            @if(!$isLive) disabled @endif
                                            class="bg-red-600 hover:bg-red-700 disabled:bg-gray-400 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                                        -1
                                    </button>
                                </div>
                            </div>

                            <!-- Separador -->
                            <div class="text-4xl font-light text-gray-400">-</div>

                            <!-- Equipo Visitante -->
                            <div class="text-center">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">{{ $match['away_team'] }}</h3>
                                <div class="text-6xl font-bold text-red-600 dark:text-red-400 mb-4">{{ $awayScore }}</div>
                                <div class="flex space-x-2">
                                    <button wire:click="addPoint('away')" 
                                            @if(!$isLive) disabled @endif
                                            class="bg-green-600 hover:bg-green-700 disabled:bg-gray-400 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                                        +1
                                    </button>
                                    <button wire:click="removePoint('away')" 
                                            @if(!$isLive) disabled @endif
                                            class="bg-red-600 hover:bg-red-700 disabled:bg-gray-400 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                                        -1
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Estadísticas del Set -->
                    @php $stats = $this->getMatchStats(); @endphp
                    <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                        <div class="flex justify-center space-x-8 text-sm">
                            <div class="text-center">
                                <div class="font-semibold text-gray-900 dark:text-white">{{ $stats['home_sets'] }}</div>
                                <div class="text-gray-500 dark:text-gray-400">Sets Ganados</div>
                            </div>
                            <div class="text-center">
                                <div class="font-semibold text-gray-900 dark:text-white">{{ $stats['total_points_home'] }}</div>
                                <div class="text-gray-500 dark:text-gray-400">Puntos Totales</div>
                            </div>
                            <div class="w-px bg-gray-200 dark:bg-gray-700"></div>
                            <div class="text-center">
                                <div class="font-semibold text-gray-900 dark:text-white">{{ $stats['total_points_away'] }}</div>
                                <div class="text-gray-500 dark:text-gray-400">Puntos Totales</div>
                            </div>
                            <div class="text-center">
                                <div class="font-semibold text-gray-900 dark:text-white">{{ $stats['away_sets'] }}</div>
                                <div class="text-gray-500 dark:text-gray-400">Sets Ganados</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Historial de Sets -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Historial de Sets</h3>
                    <div class="grid grid-cols-5 gap-4">
                        @foreach($sets as $setNumber => $set)
                            <div class="text-center p-3 rounded-lg 
                                {{ $setNumber === $currentSet ? 'bg-blue-100 dark:bg-blue-900 border-2 border-blue-500' : 'bg-gray-50 dark:bg-gray-700' }}
                                {{ $set['finished'] ? 'opacity-75' : '' }}">
                                <div class="text-sm font-medium text-gray-600 dark:text-gray-300 mb-1">Set {{ $setNumber }}</div>
                                <div class="text-lg font-bold text-gray-900 dark:text-white">
                                    {{ $set['home'] }} - {{ $set['away'] }}
                                </div>
                                @if($set['finished'])
                                    <div class="text-xs text-green-600 dark:text-green-400 mt-1">Finalizado</div>
                                @elseif($setNumber === $currentSet)
                                    <div class="text-xs text-blue-600 dark:text-blue-400 mt-1">En Curso</div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Controles de Partido -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Controles de Partido</h3>
                    <div class="grid md:grid-cols-2 gap-6">
                        <!-- Timeouts -->
                        <div>
                            <h4 class="font-medium text-gray-900 dark:text-white mb-3">Tiempos Fuera</h4>
                            <div class="space-y-2">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600 dark:text-gray-300">{{ $match['home_team'] }}</span>
                                    <div class="flex items-center space-x-2">
                                        <span class="text-sm font-medium">{{ $timeouts['home'] }}/2</span>
                                        <button wire:click="useTimeout('home')" 
                                                @if(!$isLive || $timeouts['home'] <= 0) disabled @endif
                                                class="bg-yellow-600 hover:bg-yellow-700 disabled:bg-gray-400 text-white px-3 py-1 rounded text-sm transition-colors">
                                            Usar
                                        </button>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600 dark:text-gray-300">{{ $match['away_team'] }}</span>
                                    <div class="flex items-center space-x-2">
                                        <span class="text-sm font-medium">{{ $timeouts['away'] }}/2</span>
                                        <button wire:click="useTimeout('away')" 
                                                @if(!$isLive || $timeouts['away'] <= 0) disabled @endif
                                                class="bg-yellow-600 hover:bg-yellow-700 disabled:bg-gray-400 text-white px-3 py-1 rounded text-sm transition-colors">
                                            Usar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Sustituciones -->
                        <div>
                            <h4 class="font-medium text-gray-900 dark:text-white mb-3">Sustituciones</h4>
                            <div class="space-y-2">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600 dark:text-gray-300">{{ $match['home_team'] }}</span>
                                    <div class="flex items-center space-x-2">
                                        <span class="text-sm font-medium">{{ $substitutions['home'] }}/6</span>
                                        <button wire:click="addSubstitution('home')" 
                                                @if(!$isLive || $substitutions['home'] <= 0) disabled @endif
                                                class="bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 text-white px-3 py-1 rounded text-sm transition-colors">
                                            Cambio
                                        </button>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600 dark:text-gray-300">{{ $match['away_team'] }}</span>
                                    <div class="flex items-center space-x-2">
                                        <span class="text-sm font-medium">{{ $substitutions['away'] }}/6</span>
                                        <button wire:click="addSubstitution('away')" 
                                                @if(!$isLive || $substitutions['away'] <= 0) disabled @endif
                                                class="bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 text-white px-3 py-1 rounded text-sm transition-colors">
                                            Cambio
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tarjetas -->
                    <div class="mt-6">
                        <h4 class="font-medium text-gray-900 dark:text-white mb-3">Tarjetas</h4>
                        <div class="grid md:grid-cols-2 gap-4">
                            <div>
                                <div class="text-sm text-gray-600 dark:text-gray-300 mb-2">{{ $match['home_team'] }}</div>
                                <div class="flex space-x-2">
                                    <button wire:click="addCard('home', 'yellow', 'Jugador')" 
                                            @if(!$isLive) disabled @endif
                                            class="bg-yellow-500 hover:bg-yellow-600 disabled:bg-gray-400 text-white px-3 py-1 rounded text-sm transition-colors">
                                        Amarilla
                                    </button>
                                    <button wire:click="addCard('home', 'red', 'Jugador')" 
                                            @if(!$isLive) disabled @endif
                                            class="bg-red-600 hover:bg-red-700 disabled:bg-gray-400 text-white px-3 py-1 rounded text-sm transition-colors">
                                        Roja
                                    </button>
                                </div>
                            </div>
                            <div>
                                <div class="text-sm text-gray-600 dark:text-gray-300 mb-2">{{ $match['away_team'] }}</div>
                                <div class="flex space-x-2">
                                    <button wire:click="addCard('away', 'yellow', 'Jugador')" 
                                            @if(!$isLive) disabled @endif
                                            class="bg-yellow-500 hover:bg-yellow-600 disabled:bg-gray-400 text-white px-3 py-1 rounded text-sm transition-colors">
                                        Amarilla
                                    </button>
                                    <button wire:click="addCard('away', 'red', 'Jugador')" 
                                            @if(!$isLive) disabled @endif
                                            class="bg-red-600 hover:bg-red-700 disabled:bg-gray-400 text-white px-3 py-1 rounded text-sm transition-colors">
                                        Roja
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Panel Lateral -->
            <div class="space-y-6">
                <!-- Rotaciones -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Rotaciones</h3>
                    <div class="space-y-4">
                        <div class="text-center">
                            <div class="text-sm text-gray-600 dark:text-gray-300 mb-1">{{ $match['home_team'] }}</div>
                            <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $rotations['home'] }}</div>
                        </div>
                        <div class="text-center">
                            <div class="text-sm text-gray-600 dark:text-gray-300 mb-1">{{ $match['away_team'] }}</div>
                            <div class="text-2xl font-bold text-red-600 dark:text-red-400">{{ $rotations['away'] }}</div>
                        </div>
                    </div>
                </div>

                <!-- Tarjetas Mostradas -->
                @if(count($cards) > 0)
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Tarjetas</h3>
                    <div class="space-y-2">
                        @foreach($cards as $card)
                            <div class="flex items-center justify-between p-2 rounded-lg bg-gray-50 dark:bg-gray-700">
                                <div class="flex items-center space-x-2">
                                    <div class="w-4 h-4 rounded {{ $card['type'] === 'yellow' ? 'bg-yellow-500' : 'bg-red-600' }}"></div>
                                    <span class="text-sm font-medium">{{ $card['player'] }}</span>
                                </div>
                                <span class="text-xs text-gray-500">Set {{ $card['set'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Eventos del Partido -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Eventos del Partido</h3>
                    <div class="space-y-2 max-h-96 overflow-y-auto">
                        @foreach(array_reverse($matchEvents) as $event)
                            <div class="p-2 rounded-lg bg-gray-50 dark:bg-gray-700">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $event['description'] }}</span>
                                    <span class="text-xs text-gray-500">{{ $event['time'] }}</span>
                                </div>
                                <div class="text-xs text-gray-500 mt-1">Set {{ $event['set'] }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>