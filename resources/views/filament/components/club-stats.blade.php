@php
    $club = $record;
    
    // Calculate statistics
    $totalPlayers = $club->players()->where('is_active', true)->count();
    $federatedPlayers = $club->players()
        ->where('is_active', true)
        ->where('is_federated', true)
        ->count();
    $activeDirectors = $club->directors()
        ->wherePivot('activo', true)
        ->count();
    $tournaments = $club->tournaments()->count();
    
    // Get recent players
    $recentPlayers = $club->players()
        ->where('is_active', true)
        ->with(['user'])
        ->latest()
        ->limit(5)
        ->get();
    
    // Get active directors
    $directors = $club->directors()
        ->wherePivot('activo', true)
        ->with(['user'])
        ->get();
    
    // Get recent tournaments
    $recentTournaments = $club->tournaments()
        ->with(['tournament'])
        ->latest()
        ->limit(5)
        ->get();
@endphp

<div class="space-y-6">
    <!-- Quick Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg border border-blue-200 dark:border-blue-700 shadow-sm">
            <div class="text-2xl font-bold text-blue-900 dark:text-blue-100">
                {{ $totalPlayers }}
            </div>
            <div class="text-sm text-blue-700 dark:text-blue-300 font-medium">
                Total Players
            </div>
        </div>
        
        <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded-lg border border-green-200 dark:border-green-700 shadow-sm">
            <div class="text-2xl font-bold text-green-900 dark:text-green-100">
                {{ $federatedPlayers }}
            </div>
            <div class="text-sm text-green-700 dark:text-green-300 font-medium">
                Federated Players
            </div>
        </div>
        
        <div class="bg-purple-50 dark:bg-purple-900/20 p-4 rounded-lg border border-purple-200 dark:border-purple-700 shadow-sm">
            <div class="text-2xl font-bold text-purple-900 dark:text-purple-100">
                {{ $activeDirectors }}
            </div>
            <div class="text-sm text-purple-700 dark:text-purple-300 font-medium">
                Active Directors
            </div>
        </div>
        
        <div class="bg-orange-50 dark:bg-orange-900/20 p-4 rounded-lg border border-orange-200 dark:border-orange-700 shadow-sm">
            <div class="text-2xl font-bold text-orange-900 dark:text-orange-100">
                {{ $tournaments }}
            </div>
            <div class="text-sm text-orange-700 dark:text-orange-300 font-medium">
                Tournaments
            </div>
        </div>
    </div>
    
    <!-- Detailed Information -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Recent Players -->
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4 shadow-sm">
            <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-600 pb-2">Recent Players</h3>
            @if($recentPlayers->count() > 0)
                <div class="space-y-3">
                    @foreach($recentPlayers as $player)
                        <div class="flex items-center space-x-3 p-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center border border-blue-200 dark:border-blue-700">
                                <span class="text-sm font-semibold text-blue-700 dark:text-blue-300">
                                    {{ substr($player->user->name ?? 'N/A', 0, 2) }}
                                </span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">
                                    {{ $player->user->name ?? 'N/A' }}
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    @if($player->is_federated)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300 border border-green-200 dark:border-green-700">
                                            ✓ Federated
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-600">
                                            ○ Not Federated
                                        </span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-gray-500 dark:text-gray-400">No players found</p>
            @endif
        </div>
        
        <!-- Active Directors -->
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4 shadow-sm">
            <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-600 pb-2">Active Directors</h3>
            @if($directors->count() > 0)
                <div class="space-y-3">
                    @foreach($directors as $director)
                        <div class="flex items-center space-x-3 p-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/30 rounded-full flex items-center justify-center border border-purple-200 dark:border-purple-700">
                                <span class="text-sm font-semibold text-purple-700 dark:text-purple-300">
                                    {{ substr($director->user->name ?? 'N/A', 0, 2) }}
                                </span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">
                                    {{ $director->user->name ?? 'N/A' }}
                                </p>
                                <p class="text-xs text-gray-600 dark:text-gray-300 mt-1">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300 border border-purple-200 dark:border-purple-700 mr-2">
                                        {{ $director->pivot->role ?? 'Director' }}
                                    </span>
                                    Since {{ $director->pivot->start_date ? \Carbon\Carbon::parse($director->pivot->start_date)->format('M Y') : 'N/A' }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-gray-500 dark:text-gray-400">No active directors found</p>
            @endif
        </div>
        
        <!-- Recent Tournaments -->
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4 shadow-sm">
            <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-600 pb-2">Recent Tournaments</h3>
            @if($recentTournaments->count() > 0)
                <div class="space-y-3">
                    @foreach($recentTournaments as $tournamentParticipation)
                        <div class="border-l-4 border-orange-400 dark:border-orange-500 pl-4 py-2 rounded-r-lg bg-orange-50/50 dark:bg-orange-900/10 hover:bg-orange-50 dark:hover:bg-orange-900/20 transition-colors">
                            <p class="text-sm font-semibold text-gray-900 dark:text-white">
                                {{ $tournamentParticipation->tournament->name ?? 'Tournament' }}
                            </p>
                            <p class="text-xs text-orange-600 dark:text-orange-400 mt-1 font-medium">
                                📅 {{ $tournamentParticipation->created_at ? $tournamentParticipation->created_at->format('M d, Y') : 'N/A' }}
                            </p>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-gray-500 dark:text-gray-400">No tournaments found</p>
            @endif
        </div>
    </div>
    
    <!-- Federation Information -->
    @if($club->is_federated)
        <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
            <div class="flex items-start space-x-3">
                <x-heroicon-o-check-badge class="w-5 h-5 text-green-500 mt-0.5 flex-shrink-0" />
                <div class="text-sm text-green-700 dark:text-green-300">
                    <p class="font-medium mb-1">Federated Club</p>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-2">
                        <div>
                            <span class="font-medium">Type:</span> {{ $club->federation_type ?? 'N/A' }}
                        </div>
                        <div>
                            <span class="font-medium">Code:</span> {{ $club->federation_code ?? 'N/A' }}
                        </div>
                        <div>
                            <span class="font-medium">Expires:</span> 
                            {{ $club->federation_expiry ? $club->federation_expiry->format('M d, Y') : 'N/A' }}
                        </div>
                    </div>
                    @if($club->federation_notes)
                        <div class="mt-2">
                            <span class="font-medium">Notes:</span> {{ $club->federation_notes }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>