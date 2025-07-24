<x-filament-widgets::widget>
    <x-filament::section>
        @if($club)
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Estadísticas Rápidas -->
                <div class="lg:col-span-1">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-success-50 dark:bg-success-900/20 p-4 rounded-lg">
                            <div class="text-2xl font-bold text-success-600 dark:text-success-400">
                                {{ $stats['total_jugadoras'] }}
                            </div>
                            <div class="text-sm text-success-600 dark:text-success-400">
                                Total Jugadoras
                            </div>
                        </div>
                        
                        <div class="bg-primary-50 dark:bg-primary-900/20 p-4 rounded-lg">
                            <div class="text-2xl font-bold text-primary-600 dark:text-primary-400">
                                {{ $stats['jugadoras_federadas'] }}
                            </div>
                            <div class="text-sm text-primary-600 dark:text-primary-400">
                                Federadas
                            </div>
                        </div>
                        
                        <div class="bg-warning-50 dark:bg-warning-900/20 p-4 rounded-lg">
                            <div class="text-2xl font-bold text-warning-600 dark:text-warning-400">
                                {{ $stats['directivos_activos'] }}
                            </div>
                            <div class="text-sm text-warning-600 dark:text-warning-400">
                                Directivos
                            </div>
                        </div>
                        
                        <div class="bg-info-50 dark:bg-info-900/20 p-4 rounded-lg">
                            <div class="text-2xl font-bold text-info-600 dark:text-info-400">
                                {{ $stats['torneos_participados'] }}
                            </div>
                            <div class="text-sm text-info-600 dark:text-info-400">
                                Torneos
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Jugadoras Recientes -->
                <div class="lg:col-span-1">
                    <h3 class="text-lg font-semibold mb-4">Jugadoras Recientes</h3>
                    <div class="space-y-2 max-h-64 overflow-y-auto">
                        @forelse($jugadoras as $jugadora)
                            <div class="flex items-center justify-between p-2 bg-gray-50 dark:bg-gray-800 rounded">
                                <div>
                                    <div class="font-medium">{{ $jugadora->user->name ?? 'N/A' }}</div>
                                    <div class="text-sm text-gray-500">
                                        {{ $jugadora->posicion ?? 'Sin posición' }}
                                    </div>
                                </div>
                                <div class="text-right">
                                    @if($jugadora->es_federada)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-success-100 text-success-800 dark:bg-success-900 dark:text-success-200">
                                            Federada
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200">
                                            No Federada
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-gray-500 py-4">
                                No hay jugadoras registradas
                            </div>
                        @endforelse
                    </div>
                </div>
                
                <!-- Directivos y Torneos -->
                <div class="lg:col-span-1">
                    <!-- Directivos Activos -->
                    <h3 class="text-lg font-semibold mb-4">Directivos Activos</h3>
                    <div class="space-y-2 mb-6">
                        @forelse($directivos as $directivo)
                            <div class="flex items-center justify-between p-2 bg-gray-50 dark:bg-gray-800 rounded">
                                <div>
                                    <div class="font-medium">{{ $directivo->name }}</div>
                                    <div class="text-sm text-gray-500">
                                        {{ ucfirst($directivo->pivot->rol ?? 'Directivo') }}
                                    </div>
                                </div>
                                <div class="text-xs text-gray-400">
                                    @if($directivo->pivot->fecha_inicio)
                                        Desde {{ \Carbon\Carbon::parse($directivo->pivot->fecha_inicio)->format('M Y') }}
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-gray-500 py-2">
                                No hay directivos activos
                            </div>
                        @endforelse
                    </div>
                    
                    <!-- Torneos Recientes -->
                    <h3 class="text-lg font-semibold mb-4">Torneos Recientes</h3>
                    <div class="space-y-2">
                        @forelse($torneos as $participacion)
                            <div class="p-2 bg-gray-50 dark:bg-gray-800 rounded">
                                <div class="font-medium text-sm">{{ $participacion->torneo->nombre ?? 'Torneo' }}</div>
                                <div class="text-xs text-gray-500">
                                    {{ $participacion->created_at->format('M Y') }}
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-gray-500 py-2">
                                No ha participado en torneos
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        @else
            <div class="text-center text-gray-500 py-8">
                No hay información del club disponible
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>