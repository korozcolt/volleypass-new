<x-layouts.player>
    <x-slot name="title">Mi Perfil</x-slot>

    <div class="max-w-4xl mx-auto">
        <!-- Profile Header -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 mb-6">
            <div class="flex items-center space-x-6">
                <div class="w-24 h-24 bg-blue-500 rounded-full flex items-center justify-center">
                    <span class="text-white font-bold text-2xl">
                        {{ substr(auth()->user()->first_name ?? auth()->user()->name, 0, 1) }}{{ substr(auth()->user()->last_name ?? '', 0, 1) }}
                    </span>
                </div>
                <div class="flex-1">
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{ auth()->user()->full_name }}
                    </h1>
                    <p class="text-gray-600 dark:text-gray-400">Jugadora de Voleibol</p>
                    <p class="text-sm text-gray-500 dark:text-gray-500">
                        Miembro desde {{ auth()->user()->created_at->format('M Y') }}
                    </p>
                </div>
                <div>
                    <button class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        Editar Perfil
                    </button>
                </div>
            </div>
        </div>

        <!-- Profile Information -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Personal Information -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                    Información Personal
                </h2>
                <div class="space-y-4">
                    <div>
                        <label class="text-sm font-medium text-gray-600 dark:text-gray-400">Email</label>
                        <p class="text-gray-900 dark:text-white">{{ auth()->user()->email }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600 dark:text-gray-400">Teléfono</label>
                        <p class="text-gray-900 dark:text-white">{{ auth()->user()->phone ?? 'No especificado' }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600 dark:text-gray-400">Fecha de Nacimiento</label>
                        <p class="text-gray-900 dark:text-white">
                            {{ auth()->user()->birth_date ? auth()->user()->birth_date->format('d/m/Y') : 'No especificada' }}
                        </p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600 dark:text-gray-400">Dirección</label>
                        <p class="text-gray-900 dark:text-white">{{ auth()->user()->address ?? 'No especificada' }}</p>
                    </div>
                </div>
            </div>

            <!-- Sports Information -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                    Información Deportiva
                </h2>
                <div class="space-y-4">
                    <div>
                        <label class="text-sm font-medium text-gray-600 dark:text-gray-400">Liga</label>
                        <p class="text-gray-900 dark:text-white">
                            {{ auth()->user()->league->name ?? 'No asignada' }}
                        </p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600 dark:text-gray-400">Club</label>
                        <p class="text-gray-900 dark:text-white">
                            {{ auth()->user()->club->name ?? 'No asignado' }}
                        </p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600 dark:text-gray-400">Estado</label>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ auth()->user()->status->getColorHtml() }}">
                            {{ auth()->user()->status->getLabel() }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="mt-6 bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                Acciones Rápidas
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <a href="{{ route('player.card') }}"
                   class="flex items-center p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    <svg class="w-8 h-8 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"></path>
                    </svg>
                    <div>
                        <h3 class="font-medium text-gray-900 dark:text-white">Ver Mi Carnet</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Carnet digital oficial</p>
                    </div>
                </a>

                <a href="{{ route('player.stats') }}"
                   class="flex items-center p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    <svg class="w-8 h-8 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    <div>
                        <h3 class="font-medium text-gray-900 dark:text-white">Mis Estadísticas</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Rendimiento deportivo</p>
                    </div>
                </a>

                <a href="{{ route('player.tournaments') }}"
                   class="flex items-center p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    <svg class="w-8 h-8 text-purple-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                    <div>
                        <h3 class="font-medium text-gray-900 dark:text-white">Mis Torneos</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Competencias activas</p>
                    </div>
                </a>
            </div>
        </div>
    </div>
</x-layouts.player>
