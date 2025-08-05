<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Header con información -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center space-x-4">
                <div class="flex-shrink-0">
                    <x-heroicon-o-flag class="h-8 w-8 text-primary-600" />
                </div>
                <div>
                    <h2 class="text-lg font-medium text-gray-900 dark:text-white">
                        Gestión de Selecciones Departamentales
                    </h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Crea y gestiona selecciones departamentales con jugadores de diferentes clubes del mismo departamento.
                    </p>
                </div>
            </div>
        </div>

        <!-- Estadísticas rápidas -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <x-heroicon-o-flag class="h-6 w-6 text-green-600" />
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Selecciones</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">
                            {{ \App\Models\Team::where('team_type', \App\Enums\TeamType::SELECTION)->count() }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <x-heroicon-o-users class="h-6 w-6 text-blue-600" />
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Jugadores en Selecciones</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">
                            {{ \App\Models\Team::where('team_type', \App\Enums\TeamType::SELECTION)->withCount('players')->get()->sum('players_count') }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <x-heroicon-o-map class="h-6 w-6 text-purple-600" />
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Departamentos Activos</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">
                            {{ \App\Models\Team::where('team_type', \App\Enums\TeamType::SELECTION)->distinct('department_id')->count('department_id') }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <x-heroicon-o-trophy class="h-6 w-6 text-yellow-600" />
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Ligas Participantes</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">
                            {{ \App\Models\Team::where('team_type', \App\Enums\TeamType::SELECTION)->distinct('league_id')->count('league_id') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla de selecciones -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
            {{ $this->table }}
        </div>
    </div>
</x-filament-panels::page>
