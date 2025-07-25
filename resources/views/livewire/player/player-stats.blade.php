<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Estadísticas del Jugador</h2>
                <p class="text-gray-600 dark:text-gray-400">Análisis detallado de tu rendimiento</p>
            </div>
            <div class="flex space-x-2">
                <button wire:click="updatePeriod('season')" 
                        class="px-4 py-2 rounded-lg font-medium transition-colors {{ $selectedPeriod === 'season' ? 'bg-blue-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                    Temporada
                </button>
                <button wire:click="updatePeriod('month')" 
                        class="px-4 py-2 rounded-lg font-medium transition-colors {{ $selectedPeriod === 'month' ? 'bg-blue-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                    Mes
                </button>
                <button wire:click="updatePeriod('week')" 
                        class="px-4 py-2 rounded-lg font-medium transition-colors {{ $selectedPeriod === 'week' ? 'bg-blue-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                    Semana
                </button>
            </div>
        </div>
    </div>

    <!-- Season Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Points -->
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                </div>
                <span class="text-sm font-medium {{ strpos($seasonStats['points']['change'], '+') === 0 ? 'text-green-600' : 'text-red-600' }}">
                    {{ $seasonStats['points']['change'] }}
                </span>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">Puntos</h3>
            <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $seasonStats['points']['current'] }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Anterior: {{ $seasonStats['points']['previous'] }}</p>
        </div>

        <!-- Aces -->
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                    </svg>
                </div>
                <span class="text-sm font-medium {{ strpos($seasonStats['aces']['change'], '+') === 0 ? 'text-green-600' : 'text-red-600' }}">
                    {{ $seasonStats['aces']['change'] }}
                </span>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">Aces</h3>
            <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $seasonStats['aces']['current'] }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Anterior: {{ $seasonStats['aces']['previous'] }}</p>
        </div>

        <!-- Blocks -->
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-red-100 dark:bg-red-900 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728"></path>
                    </svg>
                </div>
                <span class="text-sm font-medium {{ strpos($seasonStats['blocks']['change'], '+') === 0 ? 'text-green-600' : 'text-red-600' }}">
                    {{ $seasonStats['blocks']['change'] }}
                </span>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">Bloqueos</h3>
            <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $seasonStats['blocks']['current'] }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Anterior: {{ $seasonStats['blocks']['previous'] }}</p>
        </div>

        <!-- Attacks -->
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-orange-100 dark:bg-orange-900 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
                <span class="text-sm font-medium {{ strpos($seasonStats['attacks']['change'], '+') === 0 ? 'text-green-600' : 'text-red-600' }}">
                    {{ $seasonStats['attacks']['change'] }}
                </span>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">Ataques</h3>
            <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $seasonStats['attacks']['current'] }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Anterior: {{ $seasonStats['attacks']['previous'] }}</p>
        </div>

        <!-- Reception -->
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16l-4-4m0 0l4-4m-4 4h18"></path>
                    </svg>
                </div>
                <span class="text-sm font-medium {{ strpos($seasonStats['reception']['change'], '+') === 0 ? 'text-green-600' : 'text-red-600' }}">
                    {{ $seasonStats['reception']['change'] }}
                </span>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">Recepción</h3>
            <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $seasonStats['reception']['current'] }}%</p>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Anterior: {{ $seasonStats['reception']['previous'] }}%</p>
        </div>

        <!-- Efficiency -->
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-yellow-100 dark:bg-yellow-900 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <span class="text-sm font-medium {{ strpos($seasonStats['efficiency']['change'], '+') === 0 ? 'text-green-600' : 'text-red-600' }}">
                    {{ $seasonStats['efficiency']['change'] }}
                </span>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">Eficiencia</h3>
            <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $seasonStats['efficiency']['current'] }}%</p>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Anterior: {{ $seasonStats['efficiency']['previous'] }}%</p>
        </div>
    </div>

    <!-- Team Comparison -->
    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Comparación con el Equipo</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($teamComparison as $stat => $data)
            <div class="text-center">
                <div class="mb-2">
                    <span class="text-2xl font-bold text-gray-900 dark:text-white">{{ $data['player'] }}</span>
                    <span class="text-sm text-gray-500 dark:text-gray-400 ml-1">vs {{ $data['team_avg'] }}</span>
                </div>
                <div class="mb-2">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                        {{ $data['rank'] <= 2 ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 
                           ($data['rank'] <= 4 ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : 
                            'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200') }}">
                        #{{ $data['rank'] }} en equipo
                    </span>
                </div>
                <p class="text-sm font-medium text-gray-700 dark:text-gray-300 capitalize">{{ str_replace('_', ' ', $stat) }}</p>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Position Stats -->
    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Estadísticas por Posición</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach($positionStats as $position => $data)
            <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-4">
                <h4 class="font-medium text-gray-900 dark:text-white mb-3 capitalize">
                    {{ str_replace('_', ' ', $position) }}
                </h4>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600 dark:text-gray-300">Partidos</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $data['matches'] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600 dark:text-gray-300">Eficiencia</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $data['efficiency'] }}%</span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Evolution Chart Placeholder -->
    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Evolución de Rendimiento</h3>
        <div class="h-64 flex items-center justify-center bg-gray-50 dark:bg-gray-700 rounded-lg">
            <div class="text-center">
                <svg class="w-12 h-12 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                <p class="text-gray-500 dark:text-gray-400">Gráfico de evolución</p>
                <p class="text-sm text-gray-400 dark:text-gray-500">Datos: {{ implode(', ', $evolutionData['labels']) }}</p>
            </div>
        </div>
    </div>
</div>