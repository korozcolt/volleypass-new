<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Header Section -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                        Dashboard de Federación
                    </h1>
                    <p class="text-gray-600 dark:text-gray-400 mt-1">
                        Monitoreo en tiempo real del sistema de carnetización
                    </p>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="text-sm text-gray-500 dark:text-gray-400">
                        Última actualización: <span id="last-update">{{ now()->format('d/m/Y H:i:s') }}</span>
                    </div>
                    <button 
                        onclick="refreshDashboard()" 
                        class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Actualizar
                    </button>
                </div>
            </div>
        </div>

        <!-- Stats Overview -->
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            @livewire(\App\Filament\Widgets\FederationStatsWidget::class)
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
            <!-- Left Column - Live Metrics -->
            <div class="xl:col-span-2">
                @livewire(\App\Filament\Widgets\LiveMetricsWidget::class)
            </div>
            
            <!-- Right Column - System Alerts -->
            <div class="xl:col-span-1">
                @livewire(\App\Filament\Widgets\SystemAlertsWidget::class)
            </div>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
            <!-- Category Distribution Chart -->
            <div class="xl:col-span-1">
                @livewire(\App\Filament\Widgets\CategoryDistributionChart::class)
            </div>
            
            <!-- Additional Chart Space -->
            <div class="xl:col-span-1">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                            Análisis Adicional
                        </h3>
                    </div>
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">
                            Próximamente
                        </h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            Análisis avanzados y métricas adicionales
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer Information -->
        <div class="bg-gray-50 dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700 p-4">
            <div class="flex items-center justify-between text-sm text-gray-600 dark:text-gray-400">
                <div class="flex items-center space-x-4">
                    <span class="flex items-center">
                        <div class="w-2 h-2 bg-green-500 rounded-full mr-2"></div>
                        Sistema Operativo
                    </span>
                    <span>Versión 2.0</span>
                </div>
                <div class="flex items-center space-x-4">
                    <span>Usuario: {{ auth()->user()->name ?? 'Sistema' }}</span>
                    <span>Federación Activa</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Auto-refresh Script -->
    <script>
        let refreshInterval;
        
        function refreshDashboard() {
            // Update timestamp
            document.getElementById('last-update').textContent = new Date().toLocaleString('es-ES', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });
            
            // Trigger Livewire refresh for all widgets
            Livewire.emit('refreshWidget');
            
            // Show refresh feedback
            const button = event.target.closest('button');
            const originalText = button.innerHTML;
            button.innerHTML = '<svg class="w-4 h-4 mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>Actualizando...';
            
            setTimeout(() => {
                button.innerHTML = originalText;
            }, 1000);
        }
        
        // Auto-refresh every 30 seconds
        function startAutoRefresh() {
            refreshInterval = setInterval(() => {
                refreshDashboard();
            }, 30000);
        }
        
        function stopAutoRefresh() {
            if (refreshInterval) {
                clearInterval(refreshInterval);
            }
        }
        
        // Start auto-refresh when page loads
        document.addEventListener('DOMContentLoaded', function() {
            startAutoRefresh();
        });
        
        // Stop auto-refresh when page is hidden
        document.addEventListener('visibilitychange', function() {
            if (document.hidden) {
                stopAutoRefresh();
            } else {
                startAutoRefresh();
            }
        });
    </script>
</x-filament-panels::page>