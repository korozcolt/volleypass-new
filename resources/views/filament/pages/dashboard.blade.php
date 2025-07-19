<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Bienvenida -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-primary-500 rounded-full flex items-center justify-center">
                            <x-heroicon-o-home class="w-5 h-5 text-white" />
                        </div>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                            Bienvenido al Panel de Administración
                        </h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Sistema de Gestión de Federación Deportiva
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Widgets -->
        <x-filament-widgets::widgets
            :widgets="$this->getWidgets()"
            :columns="$this->getColumns()"
        />

        <!-- Accesos Rápidos -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                    Accesos Rápidos
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Crear Usuario -->
                    <a href="{{ route('filament.admin.resources.users.create') }}"
                       class="block p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors">
                        <div class="flex items-center">
                            <x-heroicon-o-user-plus class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                            <span class="ml-3 text-sm font-medium text-blue-900 dark:text-blue-100">
                                Crear Usuario
                            </span>
                        </div>
                    </a>

                    <!-- Crear Jugadora -->
                    <a href="{{ route('filament.admin.resources.players.create') }}"
                       class="block p-4 bg-green-50 dark:bg-green-900/20 rounded-lg hover:bg-green-100 dark:hover:bg-green-900/30 transition-colors">
                        <div class="flex items-center">
                            <x-heroicon-o-user-group class="w-6 h-6 text-green-600 dark:text-green-400" />
                            <span class="ml-3 text-sm font-medium text-green-900 dark:text-green-100">
                                Crear Jugadora
                            </span>
                        </div>
                    </a>

                    <!-- Crear Club -->
                    <a href="{{ route('filament.admin.resources.clubs.create') }}"
                       class="block p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg hover:bg-yellow-100 dark:hover:bg-yellow-900/30 transition-colors">
                        <div class="flex items-center">
                            <x-heroicon-o-building-office class="w-6 h-6 text-yellow-600 dark:text-yellow-400" />
                            <span class="ml-3 text-sm font-medium text-yellow-900 dark:text-yellow-100">
                                Crear Club
                            </span>
                        </div>
                    </a>

                    <!-- Ver Pagos -->
                    <a href="{{ route('filament.admin.resources.payments.index') }}"
                       class="block p-4 bg-purple-50 dark:bg-purple-900/20 rounded-lg hover:bg-purple-100 dark:hover:bg-purple-900/30 transition-colors">
                        <div class="flex items-center">
                            <x-heroicon-o-credit-card class="w-6 h-6 text-purple-600 dark:text-purple-400" />
                            <span class="ml-3 text-sm font-medium text-purple-900 dark:text-purple-100">
                                Ver Pagos
                            </span>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <!-- Información del Sistema -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                    Información del Sistema
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-gray-900 dark:text-white">
                            {{ \App\Models\SystemConfiguration::get('app.version', '1.0.0') }}
                        </div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                            Versión del Sistema
                        </div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-gray-900 dark:text-white">
                            {{ \App\Models\User::count() }}
                        </div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                            Usuarios Registrados
                        </div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-gray-900 dark:text-white">
                            {{ \App\Models\Club::count() }}
                        </div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                            Clubes Activos
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
