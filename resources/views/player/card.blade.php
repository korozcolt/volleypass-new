<div class="space-y-6">
    <!-- Card Display -->
    @livewire('player.player-card')

    <!-- QR Code Usage Instructions -->
    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">¿Cómo usar tu Carnet Digital?</h3>
        <div class="grid md:grid-cols-3 gap-6">
            <div class="text-center">
                <div class="w-16 h-16 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <h4 class="font-semibold text-gray-900 dark:text-white mb-2">1. Presenta tu móvil</h4>
                <p class="text-sm text-gray-600 dark:text-gray-300">Muestra el código QR en tu teléfono al árbitro antes del partido</p>
            </div>
            <div class="text-center">
                <div class="w-16 h-16 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h4"></path>
                    </svg>
                </div>
                <h4 class="font-semibold text-gray-900 dark:text-white mb-2">2. Escaneo rápido</h4>
                <p class="text-sm text-gray-600 dark:text-gray-300">El árbitro escaneará tu código para verificar tu elegibilidad</p>
            </div>
            <div class="text-center">
                <div class="w-16 h-16 bg-purple-100 dark:bg-purple-900 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h4 class="font-semibold text-gray-900 dark:text-white mb-2">3. Verificación completa</h4>
                <p class="text-sm text-gray-600 dark:text-gray-300">Sistema confirma tu estado y autoriza tu participación</p>
            </div>
        </div>
    </div>

    <!-- Card History -->
    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Historial del Carnet</h3>
        <div class="space-y-4">
            <div class="flex items-center justify-between p-4 border border-gray-200 dark:border-gray-600 rounded-lg">
                <div class="flex items-center space-x-3">
                    <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                    <div>
                        <p class="font-medium text-gray-900 dark:text-white">Carnet renovado</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">15 de enero, 2024</p>
                    </div>
                </div>
                <span class="text-sm text-green-600 dark:text-green-400">Activo</span>
            </div>
            
            <div class="flex items-center justify-between p-4 border border-gray-200 dark:border-gray-600 rounded-lg">
                <div class="flex items-center space-x-3">
                    <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                    <div>
                        <p class="font-medium text-gray-900 dark:text-white">Verificación en partido</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">08 de febrero, 2024 - vs Águilas Doradas</p>
                    </div>
                </div>
                <span class="text-sm text-blue-600 dark:text-blue-400">Verificado</span>
            </div>
            
            <div class="flex items-center justify-between p-4 border border-gray-200 dark:border-gray-600 rounded-lg">
                <div class="flex items-center space-x-3">
                    <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                    <div>
                        <p class="font-medium text-gray-900 dark:text-white">Verificación en partido</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">01 de febrero, 2024 - vs Leones FC</p>
                    </div>
                </div>
                <span class="text-sm text-blue-600 dark:text-blue-400">Verificado</span>
            </div>
        </div>
    </div>

    <!-- Emergency Information -->
    <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-6">
        <div class="flex items-start space-x-3">
            <svg class="w-6 h-6 text-red-600 dark:text-red-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
            </svg>
            <div>
                <h4 class="font-semibold text-red-800 dark:text-red-200 mb-2">Información Importante</h4>
                <ul class="text-sm text-red-700 dark:text-red-300 space-y-1">
                    <li>• Mantén tu carnet digital siempre actualizado</li>
                    <li>• Verifica que tu teléfono tenga batería antes de los partidos</li>
                    <li>• En caso de problemas técnicos, contacta al administrador</li>
                    <li>• El carnet debe renovarse anualmente</li>
                </ul>
            </div>
        </div>
    </div>
</div>
