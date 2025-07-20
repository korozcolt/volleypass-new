<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Header con información de la liga -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center space-x-4">
                @if($this->record->getFirstMediaUrl('logo'))
                    <div class="w-16 h-16 rounded-full overflow-hidden bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                        <img src="{{ $this->record->getFirstMediaUrl('logo', 'thumb') }}" alt="{{ $this->record->name }}" class="w-full h-full object-cover">
                    </div>
                @else
                    <div class="w-16 h-16 bg-gray-200 dark:bg-gray-700 rounded-full flex items-center justify-center">
                        <x-heroicon-o-trophy class="w-8 h-8 text-gray-400" />
                    </div>
                @endif

                <div>
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
                        {{ $this->record->name }}
                    </h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        {{ $this->record->description ?? 'Configuraciones de reglas y políticas de la liga' }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Información importante -->
        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
            <div class="flex items-start space-x-3">
                <x-heroicon-o-information-circle class="w-5 h-5 text-blue-500 mt-0.5 flex-shrink-0" />
                <div class="text-sm text-blue-700 dark:text-blue-300">
                    <p class="font-medium mb-1">Configuraciones de Liga</p>
                    <p>Estas configuraciones definen las reglas específicas que se aplicarán a todos los clubes, jugadoras y torneos de esta liga. Los cambios se aplicarán inmediatamente.</p>
                </div>
            </div>
        </div>

        <!-- Formulario de configuraciones -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
            {{ $this->form }}
        </div>

        <!-- Estadísticas de configuración -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                <div class="flex items-center space-x-3">
                    <div class="p-2 bg-green-100 dark:bg-green-900/20 rounded-lg">
                        <x-heroicon-o-check-circle class="w-5 h-5 text-green-600 dark:text-green-400" />
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">Clubes Activos</p>
                        <p class="text-2xl font-bold text-green-600 dark:text-green-400">
                            {{ $this->record->clubs()->where('status', 'active')->count() }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                <div class="flex items-center space-x-3">
                    <div class="p-2 bg-blue-100 dark:bg-blue-900/20 rounded-lg">
                        <x-heroicon-o-users class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">Total Jugadoras</p>
                        <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                            {{ $this->record->clubs()->withCount('players')->get()->sum('players_count') }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                <div class="flex items-center space-x-3">
                    <div class="p-2 bg-purple-100 dark:bg-purple-900/20 rounded-lg">
                        <x-heroicon-o-trophy class="w-5 h-5 text-purple-600 dark:text-purple-400" />
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">Configuraciones</p>
                        <p class="text-2xl font-bold text-purple-600 dark:text-purple-400">
                            {{ $this->record->configurations()->count() }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
