<div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Historial Médico</h1>
            <p class="mt-2 text-gray-600 dark:text-gray-400">Historial médico de la jugadora</p>
        </div>

        <!-- Player Info -->
        @if(isset($player))
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6">
            <div class="flex items-center">
                <div class="h-16 w-16 rounded-full bg-gray-300 dark:bg-gray-600 flex items-center justify-center">
                    <svg class="h-8 w-8 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <div class="ml-4">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white">{{ $player->name }}</h2>
                    <p class="text-gray-600 dark:text-gray-400">{{ $player->email }}</p>
                </div>
            </div>
        </div>
        @endif

        <!-- Content Placeholder -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">Historial Médico</h3>
                <p class="mt-2 text-gray-500 dark:text-gray-400">Componente en desarrollo</p>
            </div>
        </div>
    </div>
</div>