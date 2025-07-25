<div class="space-y-6">
    <!-- Stats Overview -->
    @livewire('player.player-stats')

    <!-- Detailed Performance Charts -->
    <div class="grid lg:grid-cols-2 gap-6">
        <!-- Performance Evolution -->
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Evolución de Rendimiento</h3>
            <div class="h-64 flex items-center justify-center bg-gray-50 dark:bg-gray-700 rounded-lg">
                <div class="text-center">
                    <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    <p class="text-gray-500 dark:text-gray-400">Gráfico de evolución mensual</p>
                </div>
            </div>
        </div>

        <!-- Position Performance -->
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Rendimiento por Posición</h3>
            <div class="space-y-4">
                <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <div>
                        <p class="font-medium text-gray-900 dark:text-white">Líbero</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">8 partidos</p>
                    </div>
                    <div class="text-right">
                        <p class="font-bold text-green-600">85.2%</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Eficiencia</p>
                    </div>
                </div>
                
                <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <div>
                        <p class="font-medium text-gray-900 dark:text-white">Punta</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">12 partidos</p>
                    </div>
                    <div class="text-right">
                        <p class="font-bold text-blue-600">78.9%</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Eficiencia</p>
                    </div>
                </div>
                
                <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <div>
                        <p class="font-medium text-gray-900 dark:text-white">Armadora</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">3 partidos</p>
                    </div>
                    <div class="text-right">
                        <p class="font-bold text-purple-600">72.1%</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Eficiencia</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Match by Match Stats -->
    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Estadísticas por Partido</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-gray-600">
                        <th class="text-left py-3 px-4 font-medium text-gray-900 dark:text-white">Fecha</th>
                        <th class="text-left py-3 px-4 font-medium text-gray-900 dark:text-white">Oponente</th>
                        <th class="text-center py-3 px-4 font-medium text-gray-900 dark:text-white">Resultado</th>
                        <th class="text-center py-3 px-4 font-medium text-gray-900 dark:text-white">Puntos</th>
                        <th class="text-center py-3 px-4 font-medium text-gray-900 dark:text-white">Aces</th>
                        <th class="text-center py-3 px-4 font-medium text-gray-900 dark:text-white">Bloqueos</th>
                        <th class="text-center py-3 px-4 font-medium text-gray-900 dark:text-white">Eficiencia</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
                    <tr>
                        <td class="py-3 px-4 text-gray-900 dark:text-white">08 Feb</td>
                        <td class="py-3 px-4 text-gray-900 dark:text-white">Leones FC</td>
                        <td class="py-3 px-4 text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                W 3-1
                            </span>
                        </td>
                        <td class="py-3 px-4 text-center font-medium text-gray-900 dark:text-white">18</td>
                        <td class="py-3 px-4 text-center font-medium text-gray-900 dark:text-white">3</td>
                        <td class="py-3 px-4 text-center font-medium text-gray-900 dark:text-white">2</td>
                        <td class="py-3 px-4 text-center font-medium text-green-600">82.5%</td>
                    </tr>
                    <tr>
                        <td class="py-3 px-4 text-gray-900 dark:text-white">01 Feb</td>
                        <td class="py-3 px-4 text-gray-900 dark:text-white">Cóndores</td>
                        <td class="py-3 px-4 text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                L 1-3
                            </span>
                        </td>
                        <td class="py-3 px-4 text-center font-medium text-gray-900 dark:text-white">12</td>
                        <td class="py-3 px-4 text-center font-medium text-gray-900 dark:text-white">1</td>
                        <td class="py-3 px-4 text-center font-medium text-gray-900 dark:text-white">4</td>
                        <td class="py-3 px-4 text-center font-medium text-blue-600">75.3%</td>
                    </tr>
                    <tr>
                        <td class="py-3 px-4 text-gray-900 dark:text-white">25 Ene</td>
                        <td class="py-3 px-4 text-gray-900 dark:text-white">Águilas Doradas</td>
                        <td class="py-3 px-4 text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                W 3-0
                            </span>
                        </td>
                        <td class="py-3 px-4 text-center font-medium text-gray-900 dark:text-white">22</td>
                        <td class="py-3 px-4 text-center font-medium text-gray-900 dark:text-white">5</td>
                        <td class="py-3 px-4 text-center font-medium text-gray-900 dark:text-white">1</td>
                        <td class="py-3 px-4 text-center font-medium text-green-600">89.1%</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Awards and Achievements -->
    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Logros y Reconocimientos</h3>
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div class="flex items-center space-x-3 p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg border border-yellow-200 dark:border-yellow-800">
                <div class="w-12 h-12 bg-yellow-100 dark:bg-yellow-900 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                    </svg>
                </div>
                <div>
                    <p class="font-medium text-gray-900 dark:text-white">MVP del Mes</p>
                    <p class="text-sm text-gray-600 dark:text-gray-300">Enero 2024</p>
                </div>
            </div>

            <div class="flex items-center space-x-3 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
                <div>
                    <p class="font-medium text-gray-900 dark:text-white">Mejor Sacadora</p>
                    <p class="text-sm text-gray-600 dark:text-gray-300">Liga 2023</p>
                </div>
            </div>

            <div class="flex items-center space-x-3 p-4 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800">
                <div class="w-12 h-12 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <div>
                    <p class="font-medium text-gray-900 dark:text-white">Capitana del Equipo</p>
                    <p class="text-sm text-gray-600 dark:text-gray-300">2024</p>
                </div>
            </div>
        </div>
    </div>
</div>
