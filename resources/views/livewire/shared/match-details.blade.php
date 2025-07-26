<div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Detalles del Partido</h1>
            <p class="mt-2 text-gray-600 dark:text-gray-400">Información completa del partido</p>
        </div>

        <!-- Match Info -->
        @if(isset($match))
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6">
            <div class="text-center">
                <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-4">
                    Partido #{{ $match->id }}
                </h2>
                <div class="flex items-center justify-center space-x-8">
                    <div class="text-center">
                        <p class="text-lg font-medium text-gray-900 dark:text-white">Equipo Local</p>
                        <p class="text-gray-600 dark:text-gray-400">vs</p>
                    </div>
                    <div class="text-center">
                        <p class="text-lg font-medium text-gray-900 dark:text-white">Equipo Visitante</p>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Content Placeholder -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">Detalles del Partido</h3>
                <p class="mt-2 text-gray-500 dark:text-gray-400">Componente en desarrollo</p>
            </div>
        </div>
    </div>
</div>