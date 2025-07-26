<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between py-4">
                <div class="flex items-center space-x-4">
                    <button wire:click="goBack" class="text-gray-500 hover:text-gray-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </button>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">{{ $tournament->name }}</h1>
                        <p class="text-sm text-gray-500">{{ $tournament->category }} • {{ $tournament->venue }}</p>
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="px-3 py-1 text-xs font-medium rounded-full
                        @if($tournament->status === 'upcoming') bg-blue-100 text-blue-800
                        @elseif($tournament->status === 'ongoing') bg-green-100 text-green-800
                        @elseif($tournament->status === 'finished') bg-gray-100 text-gray-800
                        @else bg-yellow-100 text-yellow-800
                        @endif">
                        {{ ucfirst($tournament->status) }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Tournament Info -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Tournament Information</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Start Date</label>
                            <p class="text-sm text-gray-900">{{ $tournament->start_date->format('M d, Y') }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">End Date</label>
                            <p class="text-sm text-gray-900">{{ $tournament->end_date->format('M d, Y') }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Registration Deadline</label>
                            <p class="text-sm text-gray-900">{{ $tournament->registration_deadline->format('M d, Y') }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Teams</label>
                            <p class="text-sm text-gray-900">{{ $tournament->teams_count }} / {{ $tournament->max_teams }}</p>
                        </div>
                    </div>
                    @if($tournament->description)
                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                            <p class="text-sm text-gray-600">{{ $tournament->description }}</p>
                        </div>
                    @endif
                </div>

                <!-- My Team Info -->
                @if($myTeam)
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">My Team</h2>
                        <div class="flex items-center space-x-4">
                            @if($myTeam->logo)
                                <img src="{{ Storage::url($myTeam->logo) }}" alt="{{ $myTeam->name }}" class="w-12 h-12 rounded-lg object-cover">
                            @else
                                <div class="w-12 h-12 bg-gray-200 rounded-lg flex items-center justify-center">
                                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-6-3a2 2 0 11-4 0 2 2 0 014 0zm-2 4a5 5 0 00-4.546 2.916A5.986 5.986 0 0010 16a5.986 5.986 0 004.546-2.084A5 5 0 0010 11z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                            @endif
                            <div>
                                <h3 class="font-medium text-gray-900">{{ $myTeam->name }}</h3>
                                <p class="text-sm text-gray-500">{{ $myTeam->club }}</p>
                            </div>
                        </div>
                        
                        <!-- Team Stats in Tournament -->
                        @if($teamStats)
                            <div class="mt-4 grid grid-cols-2 md:grid-cols-4 gap-4">
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-gray-900">{{ $teamStats['matches_played'] }}</div>
                                    <div class="text-xs text-gray-500">Matches</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-green-600">{{ $teamStats['wins'] }}</div>
                                    <div class="text-xs text-gray-500">Wins</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-red-600">{{ $teamStats['losses'] }}</div>
                                    <div class="text-xs text-gray-500">Losses</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-blue-600">{{ $teamStats['position'] ?? '-' }}</div>
                                    <div class="text-xs text-gray-500">Position</div>
                                </div>
                            </div>
                        @endif
                    </div>
                @endif

                <!-- My Matches -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">My Matches</h2>
                    @if($myMatches->count() > 0)
                        <div class="space-y-4">
                            @foreach($myMatches as $match)
                                <div class="border rounded-lg p-4 hover:bg-gray-50 transition-colors">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-4">
                                            <div class="text-center">
                                                <div class="text-sm font-medium text-gray-900">{{ $match->match_date->format('M d') }}</div>
                                                <div class="text-xs text-gray-500">{{ $match->match_date->format('H:i') }}</div>
                                            </div>
                                            <div class="flex items-center space-x-2">
                                                <span class="font-medium text-gray-900">{{ $match->team1->name }}</span>
                                                <span class="text-gray-500">vs</span>
                                                <span class="font-medium text-gray-900">{{ $match->team2->name }}</span>
                                            </div>
                                        </div>
                                        <div class="flex items-center space-x-4">
                                            @if($match->status === 'finished')
                                                <div class="text-right">
                                                    <div class="font-bold text-gray-900">{{ $match->team1_score }} - {{ $match->team2_score }}</div>
                                                    <div class="text-xs text-gray-500">Final</div>
                                                </div>
                                            @elseif($match->status === 'ongoing')
                                                <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">Live</span>
                                            @else
                                                <span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded-full">Upcoming</span>
                                            @endif
                                            <button wire:click="viewMatch({{ $match->id }})" class="text-blue-600 hover:text-blue-800">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No matches yet</h3>
                            <p class="mt-1 text-sm text-gray-500">Matches will appear here once the tournament schedule is published.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Quick Actions -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
                    <div class="space-y-3">
                        @if($tournament->status === 'upcoming')
                            <button wire:click="downloadSchedule" class="w-full flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Download Schedule
                            </button>
                        @endif
                        <button wire:click="viewStandings" class="w-full flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                            View Standings
                        </button>
                        <button wire:click="shareTournament" class="w-full flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"></path>
                            </svg>
                            Share Tournament
                        </button>
                    </div>
                </div>

                <!-- Tournament Progress -->
                @if($tournament->status !== 'upcoming')
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Tournament Progress</h3>
                        <div class="space-y-4">
                            <div>
                                <div class="flex justify-between text-sm font-medium text-gray-700 mb-1">
                                    <span>Matches Completed</span>
                                    <span>{{ $tournamentProgress['completed_matches'] }}/{{ $tournamentProgress['total_matches'] }}</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $tournamentProgress['completion_percentage'] }}%"></div>
                                </div>
                            </div>
                            <div class="text-sm text-gray-600">
                                <p>Current Round: {{ $tournamentProgress['current_round'] ?? 'Group Stage' }}</p>
                                @if($tournamentProgress['next_match_date'])
                                    <p>Next Match: {{ $tournamentProgress['next_match_date']->format('M d, H:i') }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Contact Info -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Contact</h3>
                    <div class="space-y-3">
                        @if($tournament->organizer_email)
                            <div class="flex items-center space-x-2">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                                <a href="mailto:{{ $tournament->organizer_email }}" class="text-sm text-blue-600 hover:text-blue-800">{{ $tournament->organizer_email }}</a>
                            </div>
                        @endif
                        @if($tournament->organizer_phone)
                            <div class="flex items-center space-x-2">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                </svg>
                                <a href="tel:{{ $tournament->organizer_phone }}" class="text-sm text-blue-600 hover:text-blue-800">{{ $tournament->organizer_phone }}</a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>