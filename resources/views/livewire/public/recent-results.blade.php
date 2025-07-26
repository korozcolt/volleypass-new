<div class="card" 
     x-data="recentResultsData()"
     wire:poll.30s="loadRecentResults">
    
    <!-- Header -->
    <div class="gradient-primary p-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-white">Resultados Recientes</h3>
                    <p class="text-white/80 text-sm">Últimos partidos finalizados</p>
                </div>
            </div>
            
            @if($isLoading)
                <div class="animate-spin rounded-full h-6 w-6 border-2 border-white border-t-transparent"></div>
            @endif
        </div>
    </div>

    <!-- Results List -->
    <div class="p-6">
        @forelse($recentResults as $result)
            <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 mb-4 last:mb-0 hover:shadow-md transition-shadow">
                <!-- Tournament Info -->
                <div class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                    🏆 {{ $result['tournament']['name'] }}
                </div>
                
                <!-- Match Result -->
                <div class="grid grid-cols-3 items-center gap-4">
                    <!-- Home Team -->
                    <div class="text-right">
                        <div class="font-semibold text-gray-900 dark:text-white 
                                    {{ $result['winner'] === 'home' ? 'text-green-600 dark:text-green-400' : '' }}">
                            {{ $result['homeTeam']['name'] }}
                        </div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">
                            Local
                        </div>
                    </div>

                    <!-- Score -->
                    <div class="text-center">
                        <div class="text-2xl font-bold text-gray-900 dark:text-white">
                            {{ $result['home_sets'] }} - {{ $result['away_sets'] }}
                        </div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">
                            Sets
                        </div>
                    </div>

                    <!-- Away Team -->
                    <div class="text-left">
                        <div class="font-semibold text-gray-900 dark:text-white
                                    {{ $result['winner'] === 'away' ? 'text-green-600 dark:text-green-400' : '' }}">
                            {{ $result['awayTeam']['name'] }}
                        </div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">
                            Visitante
                        </div>
                    </div>
                </div>

                <!-- Additional Info -->
                <div class="mt-4 flex items-center justify-between text-sm">
                    <span class="text-gray-600 dark:text-gray-400">
                        Finalizado: {{ $result['finished_at'] ? \Carbon\Carbon::parse($result['finished_at'])->format('d/m/Y H:i') : 'N/A' }}
                    </span>
                    <button type="button" 
                            class="text-blue-600 hover:text-blue-700 font-medium"
                            @click="viewMatchDetails({{ $result['id'] }})">
                        Ver detalles
                    </button>
                </div>
            </div>
        @empty
            <div class="p-12 text-center">
                <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <h4 class="text-lg font-medium text-gray-900 dark:text-white mb-2">
                    No hay resultados recientes
                </h4>
                <p class="text-gray-600 dark:text-gray-400">
                    Los resultados aparecerán aquí cuando finalicen los partidos.
                </p>
            </div>
        @endforelse
    </div>

    <script>
        function recentResultsData() {
            return {
                viewMatchDetails(matchId) {
                    window.location.href = `/matches/${matchId}`;
                }
            }
        }
    </script>
</div>
