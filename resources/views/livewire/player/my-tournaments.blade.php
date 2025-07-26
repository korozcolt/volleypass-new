<div>
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Mis Torneos</h1>
        <p class="text-gray-600 dark:text-gray-400">Torneos donde participas actualmente</p>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex flex-wrap gap-3">
                <select wire:model.live="selectedStatus" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                    <option value="all">Todos los estados</option>
                    <option value="in_progress">En Progreso</option>
                    <option value="finished">Finalizados</option>
                    <option value="registration_open">Inscripciones Abiertas</option>
                    <option value="registration_closed">Inscripciones Cerradas</option>
                </select>
            </div>

            <div class="flex items-center space-x-2">
                <input type="text"
                       wire:model.live.debounce.300ms="searchTerm"
                       placeholder="Buscar torneos..."
                       class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500">
            </div>
        </div>
    </div>

    <!-- Tournaments Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        @forelse($tournaments as $tournament)
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden hover:shadow-md transition-shadow">
            <div class="p-6">
                <div class="flex items-start justify-between mb-4">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">
                        {{ $tournament->name }}
                    </h3>
                    <span class="px-3 py-1 text-xs font-medium rounded-full {{ $tournament->status->getColorHtml() }}">
                        {{ $tournament->status->getLabel() }}
                    </span>
                </div>

                <div class="space-y-2 text-sm text-gray-600 dark:text-gray-400 mb-4">
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        {{ $tournament->start_date ? $tournament->start_date->format('d M Y') : 'Fecha por definir' }}
                        @if($tournament->end_date)
                        - {{ $tournament->end_date->format('d M Y') }}
                        @endif
                    </div>
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        {{ $tournament->teams->count() }} equipos participantes
                    </div>
                    @if($tournament->city)
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        {{ $tournament->city }}
                    </div>
                    @endif
                </div>

                <div class="flex items-center justify-between">
                    <a href="{{ route('player.tournaments.show', $tournament) }}"
                       class="text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 font-medium text-sm">
                        Ver detalles →
                    </a>

                    @if($tournament->status === \App\Enums\TournamentStatus::InProgress)
                    <span class="text-green-600 dark:text-green-400 text-sm font-medium">
                        En curso
                    </span>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-12 text-center">
                <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">
                    No tienes torneos
                </h3>
                <p class="text-gray-600 dark:text-gray-400 mb-4">
                    Aún no participas en ningún torneo. Contacta a tu club para más información.
                </p>
            </div>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($tournaments->hasPages())
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
        {{ $tournaments->links() }}
    </div>
    @endif

    <!-- Upcoming Matches Section -->
    @if($upcomingMatches->count() > 0)
    <div class="mt-8 bg-white dark:bg-gray-800 rounded-xl shadow-sm">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                Próximos Partidos
            </h2>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                @foreach($upcomingMatches as $match)
                <div class="flex items-center justify-between p-4 border border-gray-200 dark:border-gray-700 rounded-lg">
                    <div class="flex-1">
                        <p class="font-medium text-gray-900 dark:text-white">
                            {{ $match->homeTeam->name }} vs {{ $match->awayTeam->name }}
                        </p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            {{ $match->tournament->name }}
                        </p>
                    </div>
                    <div class="text-right">
                        <p class="font-medium text-gray-900 dark:text-white">
                            {{ $match->scheduled_at ? $match->scheduled_at->format('d M Y') : 'Fecha TBD' }}
                        </p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            {{ $match->scheduled_at ? $match->scheduled_at->format('H:i') : 'Hora TBD' }}
                        </p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- Recent Matches Section -->
    @if($recentMatches->count() > 0)
    <div class="mt-6 bg-white dark:bg-gray-800 rounded-xl shadow-sm">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                Resultados Recientes
            </h2>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                @foreach($recentMatches as $match)
                <div class="flex items-center justify-between p-4 border border-gray-200 dark:border-gray-700 rounded-lg">
                    <div class="flex-1">
                        <p class="font-medium text-gray-900 dark:text-white">
                            {{ $match->homeTeam->name }} vs {{ $match->awayTeam->name }}
                        </p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            {{ $match->tournament->name }}
                        </p>
                    </div>
                    <div class="text-right">
                        <p class="font-medium text-gray-900 dark:text-white">
                            {{ $match->home_sets ?? 0 }} - {{ $match->away_sets ?? 0 }}
                        </p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            {{ $match->finished_at ? $match->finished_at->format('d M Y') : 'Finalizado' }}
                        </p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif
</div>
