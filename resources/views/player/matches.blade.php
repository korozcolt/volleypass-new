<div class="space-y-6">
    <!-- Matches Component -->
    @livewire('player.player-matches')

    <!-- Match Calendar -->
    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Calendario de Partidos</h3>
        <div class="grid grid-cols-7 gap-1 mb-4">
            <div class="text-center text-sm font-medium text-gray-500 dark:text-gray-400 py-2">Dom</div>
            <div class="text-center text-sm font-medium text-gray-500 dark:text-gray-400 py-2">Lun</div>
            <div class="text-center text-sm font-medium text-gray-500 dark:text-gray-400 py-2">Mar</div>
            <div class="text-center text-sm font-medium text-gray-500 dark:text-gray-400 py-2">Mié</div>
            <div class="text-center text-sm font-medium text-gray-500 dark:text-gray-400 py-2">Jue</div>
            <div class="text-center text-sm font-medium text-gray-500 dark:text-gray-400 py-2">Vie</div>
            <div class="text-center text-sm font-medium text-gray-500 dark:text-gray-400 py-2">Sáb</div>
        </div>
        <div class="grid grid-cols-7 gap-1">
            <!-- Calendar days would be generated dynamically -->
            @for($i = 1; $i <= 28; $i++)
                <div class="aspect-square flex items-center justify-center text-sm border border-gray-200 dark:border-gray-600 rounded
                    {{ $i == 15 ? 'bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 font-bold' : 
                       ($i == 22 ? 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 font-bold' : 
                        'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700') }}">
                    {{ $i }}
                </div>
            @endfor
        </div>
        <div class="mt-4 flex items-center space-x-4 text-sm">
            <div class="flex items-center space-x-2">
                <div class="w-3 h-3 bg-blue-500 rounded"></div>
                <span class="text-gray-600 dark:text-gray-300">Próximo partido</span>
            </div>
            <div class="flex items-center space-x-2">
                <div class="w-3 h-3 bg-green-500 rounded"></div>
                <span class="text-gray-600 dark:text-gray-300">Entrenamiento</span>
            </div>
        </div>
    </div>

    <!-- Performance Trends -->
    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Tendencias de Rendimiento</h3>
        <div class="grid md:grid-cols-2 gap-6">
            <div>
                <h4 class="font-medium text-gray-900 dark:text-white mb-3">Últimos 5 Partidos</h4>
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600 dark:text-gray-300">Puntos promedio</span>
                        <span class="font-medium text-gray-900 dark:text-white">16.4</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600 dark:text-gray-300">Aces promedio</span>
                        <span class="font-medium text-gray-900 dark:text-white">2.8</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600 dark:text-gray-300">Eficiencia promedio</span>
                        <span class="font-medium text-green-600">78.2%</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600 dark:text-gray-300">Partidos ganados</span>
                        <span class="font-medium text-blue-600">3/5</span>
                    </div>
                </div>
            </div>
            <div>
                <h4 class="font-medium text-gray-900 dark:text-white mb-3">Comparación Mensual</h4>
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600 dark:text-gray-300">Febrero vs Enero</span>
                        <span class="font-medium text-green-600">+12.3%</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600 dark:text-gray-300">Aces mejorados</span>
                        <span class="font-medium text-green-600">+18.5%</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600 dark:text-gray-300">Bloqueos</span>
                        <span class="font-medium text-red-600">-5.2%</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600 dark:text-gray-300">Tiempo de juego</span>
                        <span class="font-medium text-blue-600">+8.7%</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
