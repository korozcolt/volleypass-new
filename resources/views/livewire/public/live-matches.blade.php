<div class="card"
     x-data="liveMatchesData()"
     x-init="startPolling()"
     wire:poll.5s="loadLiveMatches">
    
    <!-- Header -->
    <div class="gradient-primary p-6">
        <div class="flex items-center justify-between">
            <h3 class="text-white font-semibold text-lg flex items-center">
                <div class="w-3 h-3 bg-white rounded-full animate-pulse mr-3"></div>
                Partidos en Vivo
            </h3>
            <span class="text-red-200 text-sm">
                {{ count($liveMatches) }} activos
            </span>
        </div>
    </div>

    <!-- Matches List -->
    <div class="divide-y divide-gray-200 dark:divide-gray-700">
        @forelse($liveMatches as $match)
            <div class="p-6 hover:bg-gray-50 dark:hover:bg-gray-750 transition-colors"
                 x-data="{ matchData: @js($match) }"
                 wire:key="match-{{ $match['id'] }}">
                
                <!-- Match Info -->
                <div class="flex items-center justify-between mb-4">
                    <div class="text-sm text-gray-600 dark:text-gray-400">
                        {{ $match['tournament']['name'] }}
                    </div>
                    <div class="live-indicator">
                        EN VIVO
                    </div>
                </div>

                <!-- Teams and Score -->
                <div class="grid grid-cols-3 gap-4 items-center">
                    <!-- Home Team -->
                    <div class="text-right">
                        <div class="font-semibold text-gray-900 dark:text-white">
                            {{ $match['homeTeam']['name'] }}
                        </div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">
                            Local
                        </div>
                    </div>

                    <!-- Score -->
                    <div class="text-center">
                        <div class="text-2xl font-bold text-gray-900 dark:text-white"
                             x-text="formatScore(matchData.home_score, matchData.away_score)">
                            {{ $match['home_score'] }} - {{ $match['away_score'] }}
                        </div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">
                            Set {{ $match['current_set'] }}
                        </div>
                    </div>

                    <!-- Away Team -->
                    <div class="text-left">
                        <div class="font-semibold text-gray-900 dark:text-white">
                            {{ $match['awayTeam']['name'] }}
                        </div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">
                            Visitante
                        </div>
                    </div>
                </div>

                <!-- Additional Info -->
                <div class="mt-4 flex items-center justify-between text-sm">
                    <span class="text-gray-600 dark:text-gray-400">
                        Tiempo: {{ $match['match_time'] }}
                    </span>
                    <button type="button" 
                            class="text-blue-600 hover:text-blue-700 font-medium"
                            @click="viewMatchDetails({{ $match['id'] }})">
                        Ver detalles
                    </button>
                </div>
            </div>
        @empty
            <div class="p-12 text-center">
                <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h4 class="text-lg font-medium text-gray-900 dark:text-white mb-2">
                    No hay partidos en vivo
                </h4>
                <p class="text-gray-600 dark:text-gray-400">
                    Mantente atento a los próximos encuentros.
                </p>
            </div>
        @endforelse
    </div>

    <script>
        function liveMatchesData() {
            return {
                pollingInterval: null,
                
                startPolling() {
                    this.pollingInterval = setInterval(() => {
                        if (document.visibilityState === 'visible') {
                            this.$wire.loadLiveMatches();
                        }
                    }, 3000);
                },
                
                formatScore(homeScore, awayScore) {
                    return `${homeScore || 0} - ${awayScore || 0}`;
                },
                
                viewMatchDetails(matchId) {
                    window.location.href = `/matches/${matchId}`;
                }
            }
        }
    </script>
</div>
