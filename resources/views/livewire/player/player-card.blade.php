<div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
    <div class="flex items-center justify-between mb-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Carnet Digital</h3>
        <div class="flex items-center space-x-2">
            @if($cardStatus['status'] === 'active')
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    Activo
                </span>
            @elseif($cardStatus['status'] === 'expired')
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                    </svg>
                    Vencido
                </span>
            @else
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                    Restricción
                </span>
            @endif
        </div>
    </div>

    <div class="grid md:grid-cols-2 gap-6">
        <!-- Card Visual -->
        <div class="bg-gradient-to-br from-blue-600 to-purple-600 rounded-xl p-6 text-white relative overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-white bg-opacity-10 rounded-full -mr-16 -mt-16"></div>
            <div class="absolute bottom-0 left-0 w-24 h-24 bg-white bg-opacity-10 rounded-full -ml-12 -mb-12"></div>
            
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div class="text-sm font-medium opacity-90">VolleyPass Sucre</div>
                    <div class="w-8 h-8 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                        </svg>
                    </div>
                </div>
                
                <div class="mb-4">
                    <h4 class="text-lg font-bold">{{ Auth::user()->name ?? 'Jugadora' }}</h4>
                    <p class="text-sm opacity-90">Jugadora Federada</p>
                </div>
                
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="opacity-75">N° Federación</p>
                        <p class="font-mono font-bold">{{ $cardStatus['federation_number'] }}</p>
                    </div>
                    <div>
                        <p class="opacity-75">Válido hasta</p>
                        <p class="font-bold">{{ date('d/m/Y', strtotime($cardStatus['expiry_date'])) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- QR Code and Actions -->
        <div class="space-y-4">
            <div class="bg-gray-50 dark:bg-gray-700 rounded-xl p-6 text-center">
                <img src="{{ $qrCode }}" alt="QR Code" class="w-32 h-32 mx-auto mb-4 rounded-lg">
                <p class="text-sm text-gray-600 dark:text-gray-300">Código QR para verificación</p>
            </div>
            
            <div class="space-y-2">
                <button wire:click="downloadCard" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition-colors">
                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Descargar Carnet
                </button>
                
                <button class="w-full bg-gray-100 dark:bg-gray-600 hover:bg-gray-200 dark:hover:bg-gray-500 text-gray-700 dark:text-gray-200 font-medium py-2 px-4 rounded-lg transition-colors">
                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                    </svg>
                    Imprimir
                </button>
            </div>
        </div>
    </div>

    <!-- Medical Info Summary -->
    <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-600">
        <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-3">Información Médica Resumida</h4>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
            <div>
                <p class="text-gray-500 dark:text-gray-400">Tipo de Sangre</p>
                <p class="font-medium text-gray-900 dark:text-white">{{ $medicalInfo['blood_type'] }}</p>
            </div>
            <div>
                <p class="text-gray-500 dark:text-gray-400">Alergias</p>
                <p class="font-medium text-gray-900 dark:text-white">{{ $medicalInfo['allergies'] }}</p>
            </div>
            <div>
                <p class="text-gray-500 dark:text-gray-400">Contacto Emergencia</p>
                <p class="font-medium text-gray-900 dark:text-white">{{ $medicalInfo['emergency_contact'] }}</p>
            </div>
            <div>
                <p class="text-gray-500 dark:text-gray-400">Último Chequeo</p>
                <p class="font-medium text-gray-900 dark:text-white">{{ date('d/m/Y', strtotime($medicalInfo['last_medical_check'])) }}</p>
            </div>
        </div>
    </div>
</div>
