<x-filament-widgets::widget>
    <x-filament::section>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- QR Verifications -->
            <div class="bg-white dark:bg-gray-800 rounded-lg p-6 border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Verificaciones QR</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $qrVerifications['count'] }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Última hora</p>
                    </div>
                    <div class="p-3 rounded-full {{ $qrVerifications['trend'] >= 0 ? 'bg-green-100 dark:bg-green-900' : 'bg-red-100 dark:bg-red-900' }}">
                        @if($qrVerifications['trend'] >= 0)
                            <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                            </svg>
                        @else
                            <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path>
                            </svg>
                        @endif
                    </div>
                </div>
                <div class="mt-4">
                    <div class="flex items-center text-sm">
                        <span class="{{ $qrVerifications['trend'] >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                            {{ $qrVerifications['trend'] >= 0 ? '+' : '' }}{{ number_format($qrVerifications['trend'], 1) }}%
                        </span>
                        <span class="text-gray-500 dark:text-gray-400 ml-2">vs hora anterior</span>
                    </div>
                </div>
            </div>

            <!-- New Registrations -->
            <div class="bg-white dark:bg-gray-800 rounded-lg p-6 border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Nuevos Registros</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $newRegistrations['count'] }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Hoy</p>
                    </div>
                    <div class="p-3 rounded-full {{ $newRegistrations['trend'] >= 0 ? 'bg-blue-100 dark:bg-blue-900' : 'bg-orange-100 dark:bg-orange-900' }}">
                        <svg class="w-6 h-6 {{ $newRegistrations['trend'] >= 0 ? 'text-blue-600 dark:text-blue-400' : 'text-orange-600 dark:text-orange-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="flex items-center text-sm">
                        <span class="{{ $newRegistrations['trend'] >= 0 ? 'text-blue-600 dark:text-blue-400' : 'text-orange-600 dark:text-orange-400' }}">
                            {{ $newRegistrations['trend'] >= 0 ? '+' : '' }}{{ number_format($newRegistrations['trend'], 1) }}%
                        </span>
                        <span class="text-gray-500 dark:text-gray-400 ml-2">vs ayer</span>
                    </div>
                </div>
            </div>

            <!-- System Performance -->
            <div class="bg-white dark:bg-gray-800 rounded-lg p-6 border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Performance Sistema</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $systemPerformance['avg_response'] }}ms</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Promedio</p>
                    </div>
                    <div class="p-3 rounded-full {{ $this->getPerformanceColor($systemPerformance['avg_response']) }}">
                        <svg class="w-6 h-6 {{ $this->getPerformanceIconColor($systemPerformance['avg_response']) }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                        <div class="{{ $this->getPerformanceBarColor($systemPerformance['avg_response']) }} h-2 rounded-full transition-all duration-300" 
                             style="width: {{ min(100, (500 - $systemPerformance['avg_response']) / 5) }}%"></div>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Objetivo: < 200ms</p>
                </div>
            </div>

            <!-- Active Users -->
            <div class="bg-white dark:bg-gray-800 rounded-lg p-6 border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Usuarios Activos</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $activeUsers['count'] }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Ahora</p>
                    </div>
                    <div class="p-3 rounded-full bg-purple-100 dark:bg-purple-900">
                        <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                        <span>Pico: {{ $activeUsers['peak'] }}</span>
                        <span>{{ $activeUsers['peak_time'] }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- QR Verifications Chart -->
            <div class="bg-white dark:bg-gray-800 rounded-lg p-6 border border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Verificaciones QR (12h)</h3>
                <div class="h-64">
                    <canvas id="qrChart" width="400" height="200"></canvas>
                </div>
            </div>

            <!-- Registrations Chart -->
            <div class="bg-white dark:bg-gray-800 rounded-lg p-6 border border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Registros (7 días)</h3>
                <div class="h-64">
                    <canvas id="registrationsChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>
    </x-filament::section>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // QR Verifications Chart
            const qrCtx = document.getElementById('qrChart').getContext('2d');
            new Chart(qrCtx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($qrVerifications['chart_labels']) !!},
                    datasets: [{
                        label: 'Verificaciones QR',
                        data: {!! json_encode($qrVerifications['chart_data']) !!},
                        borderColor: 'rgb(34, 197, 94)',
                        backgroundColor: 'rgba(34, 197, 94, 0.1)',
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(156, 163, 175, 0.1)'
                            }
                        },
                        x: {
                            grid: {
                                color: 'rgba(156, 163, 175, 0.1)'
                            }
                        }
                    }
                }
            });

            // Registrations Chart
            const regCtx = document.getElementById('registrationsChart').getContext('2d');
            new Chart(regCtx, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($newRegistrations['chart_labels']) !!},
                    datasets: [{
                        label: 'Nuevos Registros',
                        data: {!! json_encode($newRegistrations['chart_data']) !!},
                        backgroundColor: 'rgba(59, 130, 246, 0.8)',
                        borderColor: 'rgb(59, 130, 246)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(156, 163, 175, 0.1)'
                            }
                        },
                        x: {
                            grid: {
                                color: 'rgba(156, 163, 175, 0.1)'
                            }
                        }
                    }
                }
            });

            // Auto-refresh every 30 seconds
            setInterval(function() {
                window.location.reload();
            }, 30000);
        });
    </script>
    @endpush
</x-filament-widgets::widget>