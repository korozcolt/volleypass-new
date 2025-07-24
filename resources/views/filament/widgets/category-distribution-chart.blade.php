<x-filament-widgets::widget>
    <x-filament::section>
        <div class="space-y-6">
            <!-- Chart Type Selector -->
            <div class="flex flex-wrap gap-2 mb-6">
                <button 
                    wire:click="setChartType('categories')" 
                    class="px-4 py-2 rounded-lg text-sm font-medium transition-colors
                           {{ $chartType === 'categories' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600' }}">
                    Distribución por Categorías
                </button>
                <button 
                    wire:click="setChartType('medical')" 
                    class="px-4 py-2 rounded-lg text-sm font-medium transition-colors
                           {{ $chartType === 'medical' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600' }}">
                    Estados Médicos
                </button>
                <button 
                    wire:click="setChartType('federation')" 
                    class="px-4 py-2 rounded-lg text-sm font-medium transition-colors
                           {{ $chartType === 'federation' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600' }}">
                    Tendencia Federación
                </button>
                <button 
                    wire:click="setChartType('league')" 
                    class="px-4 py-2 rounded-lg text-sm font-medium transition-colors
                           {{ $chartType === 'league' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600' }}">
                    Performance por Liga
                </button>
            </div>

            <!-- Chart Container -->
            <div class="bg-white dark:bg-gray-800 rounded-lg p-6 border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        {{ $this->getChartTitle() }}
                    </h3>
                    <div class="flex items-center space-x-2">
                        <span class="text-xs text-gray-500 dark:text-gray-400">
                            Actualizado: {{ now()->format('H:i') }}
                        </span>
                        <button 
                            wire:click="refreshChart" 
                            class="p-1 rounded hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                
                <div class="h-80">
                    <canvas id="distributionChart" width="400" height="320"></canvas>
                </div>

                <!-- Chart Legend/Stats -->
                <div class="mt-6 grid grid-cols-2 md:grid-cols-4 gap-4">
                    @foreach($this->getChartStats() as $stat)
                        <div class="text-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stat['value'] }}</div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">{{ $stat['label'] }}</div>
                            @if(isset($stat['change']))
                                <div class="text-xs {{ $stat['change'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $stat['change'] >= 0 ? '+' : '' }}{{ $stat['change'] }}%
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Additional Insights -->
            @if($chartType === 'categories')
                <div class="bg-white dark:bg-gray-800 rounded-lg p-6 border border-gray-200 dark:border-gray-700">
                    <h4 class="text-md font-semibold text-gray-900 dark:text-white mb-3">Insights de Categorías</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                            <div class="text-sm font-medium text-blue-800 dark:text-blue-200">Categoría Dominante</div>
                            <div class="text-lg font-bold text-blue-900 dark:text-blue-100">{{ $insights['dominant_category'] ?? 'N/A' }}</div>
                        </div>
                        <div class="p-4 bg-green-50 dark:bg-green-900/20 rounded-lg">
                            <div class="text-sm font-medium text-green-800 dark:text-green-200">Crecimiento</div>
                            <div class="text-lg font-bold text-green-900 dark:text-green-100">{{ $insights['growth_category'] ?? 'N/A' }}</div>
                        </div>
                        <div class="p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg">
                            <div class="text-sm font-medium text-yellow-800 dark:text-yellow-200">Necesita Atención</div>
                            <div class="text-lg font-bold text-yellow-900 dark:text-yellow-100">{{ $insights['attention_category'] ?? 'N/A' }}</div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </x-filament::section>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        let distributionChart;
        
        document.addEventListener('DOMContentLoaded', function() {
            initChart();
        });

        // Listen for Livewire updates
        document.addEventListener('livewire:updated', function() {
            if (distributionChart) {
                distributionChart.destroy();
            }
            initChart();
        });

        function initChart() {
            const ctx = document.getElementById('distributionChart').getContext('2d');
            const chartData = @json($this->getChartData());
            const chartType = '{{ $chartType }}';
            
            let config = {
                type: getChartType(chartType),
                data: {
                    labels: chartData.labels,
                    datasets: chartData.datasets
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 20,
                                usePointStyle: true
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.parsed || context.parsed.y || 0;
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = ((value / total) * 100).toFixed(1);
                                    return `${label}: ${value} (${percentage}%)`;
                                }
                            }
                        }
                    },
                    scales: getScalesConfig(chartType)
                }
            };

            distributionChart = new Chart(ctx, config);
        }

        function getChartType(type) {
            switch(type) {
                case 'categories':
                case 'medical':
                    return 'doughnut';
                case 'federation':
                case 'league':
                    return 'line';
                default:
                    return 'bar';
            }
        }

        function getScalesConfig(type) {
            if (type === 'categories' || type === 'medical') {
                return {}; // No scales for doughnut charts
            }
            
            return {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(156, 163, 175, 0.1)'
                    },
                    ticks: {
                        color: 'rgba(156, 163, 175, 0.8)'
                    }
                },
                x: {
                    grid: {
                        color: 'rgba(156, 163, 175, 0.1)'
                    },
                    ticks: {
                        color: 'rgba(156, 163, 175, 0.8)'
                    }
                }
            };
        }

        // Auto-refresh every 5 minutes
        setInterval(function() {
            @this.call('refreshChart');
        }, 300000);
    </script>
    @endpush
</x-filament-widgets::widget>