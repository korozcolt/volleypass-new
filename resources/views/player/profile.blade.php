<div class="space-y-6">
    <!-- Profile Header -->
    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Mi Perfil</h2>
            <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                Editar Perfil
            </button>
        </div>

        <div class="grid md:grid-cols-3 gap-6">
            <!-- Profile Photo -->
            <div class="text-center">
                <div class="w-32 h-32 bg-gradient-to-br from-pink-500 to-purple-600 rounded-full flex items-center justify-center mx-auto mb-4">
                    <span class="text-white font-bold text-4xl">{{ substr(Auth::user()->name ?? 'J', 0, 1) }}</span>
                </div>
                <button class="text-sm text-blue-600 dark:text-blue-400 hover:underline">
                    Cambiar foto
                </button>
            </div>

            <!-- Basic Info -->
            <div class="md:col-span-2 space-y-4">
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nombre Completo</label>
                        <input type="text" value="{{ Auth::user()->name ?? 'María Fernanda González' }}" 
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email</label>
                        <input type="email" value="{{ Auth::user()->email ?? 'maria.gonzalez@email.com' }}" 
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Teléfono</label>
                        <input type="tel" value="+57 300 123 4567" 
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Fecha de Nacimiento</label>
                        <input type="date" value="1998-03-15" 
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sports Information -->
    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Información Deportiva</h3>
        <div class="grid md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Posición Principal</label>
                <select class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                    <option>Líbero</option>
                    <option>Opuesto</option>
                    <option>Central</option>
                    <option>Punta</option>
                    <option>Armadora</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Posición Secundaria</label>
                <select class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                    <option>Punta</option>
                    <option>Central</option>
                    <option>Ninguna</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Equipo Actual</label>
                <input type="text" value="Halcones FC" 
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-100 dark:bg-gray-600 text-gray-900 dark:text-white" readonly>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Altura (cm)</label>
                <input type="number" value="168" 
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Peso (kg)</label>
                <input type="number" value="62" 
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Años de Experiencia</label>
                <input type="number" value="8" 
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
            </div>
        </div>
    </div>

    <!-- Medical Information -->
    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Información Médica</h3>
        <div class="grid md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tipo de Sangre</label>
                <select class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                    <option>O+</option>
                    <option>O-</option>
                    <option>A+</option>
                    <option>A-</option>
                    <option>B+</option>
                    <option>B-</option>
                    <option>AB+</option>
                    <option>AB-</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Contacto de Emergencia</label>
                <input type="tel" value="+57 300 987 6543" 
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Alergias</label>
                <textarea rows="3" placeholder="Describe cualquier alergia conocida..." 
                          class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">Ninguna alergia conocida</textarea>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Condiciones Médicas</label>
                <textarea rows="3" placeholder="Describe cualquier condición médica relevante..." 
                          class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">Ninguna condición médica relevante</textarea>
            </div>
        </div>
    </div>

    <!-- Documents -->
    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Documentos</h3>
        <div class="space-y-4">
            <div class="flex items-center justify-between p-4 border border-gray-200 dark:border-gray-600 rounded-lg">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900 dark:text-white">Cédula de Ciudadanía</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Válido hasta: 2030-03-15</p>
                    </div>
                </div>
                <button class="text-blue-600 dark:text-blue-400 hover:underline text-sm">Ver documento</button>
            </div>

            <div class="flex items-center justify-between p-4 border border-gray-200 dark:border-gray-600 rounded-lg">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900 dark:text-white">Certificado Médico</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Válido hasta: 2024-08-15</p>
                    </div>
                </div>
                <button class="text-blue-600 dark:text-blue-400 hover:underline text-sm">Ver documento</button>
            </div>

            <div class="flex items-center justify-between p-4 border border-yellow-200 dark:border-yellow-600 rounded-lg bg-yellow-50 dark:bg-yellow-900/20">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-yellow-100 dark:bg-yellow-900 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900 dark:text-white">Seguro Deportivo</p>
                        <p class="text-sm text-yellow-600 dark:text-yellow-400">Vence en 15 días: 2024-03-01</p>
                    </div>
                </div>
                <button class="text-blue-600 dark:text-blue-400 hover:underline text-sm">Renovar</button>
            </div>
        </div>
    </div>

    <!-- Save Button -->
    <div class="flex justify-end">
        <button class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">
            Guardar Cambios
        </button>
    </div>
</div>
