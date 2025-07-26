<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Configuración') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-8">
            <!-- Configuración de Cuenta -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Configuración de Cuenta</h3>
                    
                    <form wire:submit.prevent="updateAccountSettings">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Idioma -->
                            <div>
                                <label for="language" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Idioma
                                </label>
                                <select id="language" wire:model="settings.language" 
                                        class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                    <option value="es">Español</option>
                                    <option value="en">English</option>
                                    <option value="pt">Português</option>
                                </select>
                                @error('settings.language') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>

                            <!-- Zona Horaria -->
                            <div>
                                <label for="timezone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Zona Horaria
                                </label>
                                <select id="timezone" wire:model="settings.timezone" 
                                        class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                    <option value="America/Bogota">Bogotá (GMT-5)</option>
                                    <option value="America/Lima">Lima (GMT-5)</option>
                                    <option value="America/Mexico_City">Ciudad de México (GMT-6)</option>
                                    <option value="America/Argentina/Buenos_Aires">Buenos Aires (GMT-3)</option>
                                    <option value="America/Sao_Paulo">São Paulo (GMT-3)</option>
                                    <option value="America/Santiago">Santiago (GMT-3)</option>
                                </select>
                                @error('settings.timezone') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>

                            <!-- Tema -->
                            <div>
                                <label for="theme" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Tema
                                </label>
                                <select id="theme" wire:model="settings.theme" 
                                        class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                    <option value="light">Claro</option>
                                    <option value="dark">Oscuro</option>
                                    <option value="auto">Automático</option>
                                </select>
                                @error('settings.theme') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>

                            <!-- Formato de Fecha -->
                            <div>
                                <label for="date_format" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Formato de Fecha
                                </label>
                                <select id="date_format" wire:model="settings.date_format" 
                                        class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                    <option value="d/m/Y">DD/MM/AAAA</option>
                                    <option value="m/d/Y">MM/DD/AAAA</option>
                                    <option value="Y-m-d">AAAA-MM-DD</option>
                                    <option value="d-m-Y">DD-MM-AAAA</option>
                                </select>
                                @error('settings.date_format') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end">
                            <button type="submit" 
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                                Guardar Configuración
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Configuración de Privacidad -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Configuración de Privacidad</h3>
                    
                    <form wire:submit.prevent="updatePrivacySettings">
                        <div class="space-y-6">
                            <!-- Perfil Público -->
                            <div class="flex items-center justify-between">
                                <div>
                                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Perfil Público
                                    </label>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        Permitir que otros usuarios vean tu perfil público
                                    </p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" wire:model="privacy.public_profile" class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                                </label>
                            </div>

                            <!-- Mostrar Estadísticas -->
                            <div class="flex items-center justify-between">
                                <div>
                                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Mostrar Estadísticas
                                    </label>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        Permitir que otros vean tus estadísticas de juego
                                    </p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" wire:model="privacy.show_stats" class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                                </label>
                            </div>

                            <!-- Mostrar Contacto -->
                            <div class="flex items-center justify-between">
                                <div>
                                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Mostrar Información de Contacto
                                    </label>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        Permitir que otros vean tu información de contacto
                                    </p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" wire:model="privacy.show_contact" class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                                </label>
                            </div>

                            <!-- Permitir Mensajes -->
                            <div class="flex items-center justify-between">
                                <div>
                                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Permitir Mensajes
                                    </label>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        Permitir que otros usuarios te envíen mensajes
                                    </p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" wire:model="privacy.allow_messages" class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                                </label>
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end">
                            <button type="submit" 
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                                Guardar Privacidad
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Configuración de Notificaciones -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Configuración de Notificaciones</h3>
                    
                    <form wire:submit.prevent="updateNotificationSettings">
                        <div class="space-y-6">
                            <!-- Notificaciones Push -->
                            <div class="flex items-center justify-between">
                                <div>
                                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Notificaciones Push
                                    </label>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        Recibir notificaciones push en tu dispositivo
                                    </p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" wire:model="notifications.push_enabled" class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                                </label>
                            </div>

                            <!-- Notificaciones por Email -->
                            <div class="flex items-center justify-between">
                                <div>
                                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Notificaciones por Email
                                    </label>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        Recibir notificaciones en tu correo electrónico
                                    </p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" wire:model="notifications.email_enabled" class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                                </label>
                            </div>

                            <!-- Recordatorios de Partidos -->
                            <div class="flex items-center justify-between">
                                <div>
                                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Recordatorios de Partidos
                                    </label>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        Recibir recordatorios antes de tus partidos
                                    </p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" wire:model="notifications.match_reminders" class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                                </label>
                            </div>

                            <!-- Tiempo de Recordatorio -->
                            @if($notifications['match_reminders'] ?? false)
                                <div class="ml-6">
                                    <label for="reminder_time" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        Tiempo de Recordatorio
                                    </label>
                                    <select id="reminder_time" wire:model="notifications.reminder_time" 
                                            class="mt-1 block w-full max-w-xs border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                        <option value="15">15 minutos antes</option>
                                        <option value="30">30 minutos antes</option>
                                        <option value="60">1 hora antes</option>
                                        <option value="120">2 horas antes</option>
                                        <option value="1440">1 día antes</option>
                                    </select>
                                </div>
                            @endif

                            <!-- Actualizaciones de Torneos -->
                            <div class="flex items-center justify-between">
                                <div>
                                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Actualizaciones de Torneos
                                    </label>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        Recibir actualizaciones sobre tus torneos
                                    </p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" wire:model="notifications.tournament_updates" class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                                </label>
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end">
                            <button type="submit" 
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                                Guardar Notificaciones
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Configuración de Seguridad -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Configuración de Seguridad</h3>
                    
                    <div class="space-y-6">
                        <!-- Autenticación de Dos Factores -->
                        <div class="flex items-center justify-between">
                            <div>
                                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Autenticación de Dos Factores
                                </label>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    Agregar una capa extra de seguridad a tu cuenta
                                </p>
                            </div>
                            <div class="flex items-center space-x-2">
                                @if($user->two_factor_secret)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                        Activado
                                    </span>
                                    <button wire:click="disableTwoFactor" 
                                            class="inline-flex items-center px-3 py-2 border border-red-300 text-sm leading-4 font-medium rounded-md text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                        Desactivar
                                    </button>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                                        Desactivado
                                    </span>
                                    <button wire:click="enableTwoFactor" 
                                            class="inline-flex items-center px-3 py-2 border border-green-300 text-sm leading-4 font-medium rounded-md text-green-700 bg-white hover:bg-green-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                        Activar
                                    </button>
                                @endif
                            </div>
                        </div>

                        <!-- Sesiones Activas -->
                        <div>
                            <div class="flex items-center justify-between mb-4">
                                <div>
                                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Sesiones Activas
                                    </label>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        Administra tus sesiones activas en diferentes dispositivos
                                    </p>
                                </div>
                                <button wire:click="logoutOtherSessions" 
                                        class="inline-flex items-center px-3 py-2 border border-red-300 text-sm leading-4 font-medium rounded-md text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                    Cerrar Otras Sesiones
                                </button>
                            </div>
                            
                            <div class="space-y-3">
                                @foreach($activeSessions as $session)
                                    <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                        <div class="flex items-center space-x-3">
                                            <div class="flex-shrink-0">
                                                @if($session['is_current'])
                                                    <div class="w-3 h-3 bg-green-400 rounded-full"></div>
                                                @else
                                                    <div class="w-3 h-3 bg-gray-400 rounded-full"></div>
                                                @endif
                                            </div>
                                            <div>
                                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                    {{ $session['device'] }}
                                                    @if($session['is_current'])
                                                        <span class="text-green-600 dark:text-green-400">(Actual)</span>
                                                    @endif
                                                </p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                                    {{ $session['ip_address'] }} • {{ $session['last_active'] }}
                                                </p>
                                            </div>
                                        </div>
                                        @if(!$session['is_current'])
                                            <button wire:click="logoutSession('{{ $session['id'] }}')" 
                                                    class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                            </button>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Zona de Peligro -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg border-l-4 border-red-500">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-red-600 dark:text-red-400 mb-6">Zona de Peligro</h3>
                    
                    <div class="space-y-6">
                        <!-- Descargar Datos -->
                        <div class="flex items-center justify-between">
                            <div>
                                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Descargar Mis Datos
                                </label>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    Descarga una copia de todos tus datos personales
                                </p>
                            </div>
                            <button wire:click="downloadData" 
                                    class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm leading-4 font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Descargar
                            </button>
                        </div>

                        <!-- Eliminar Cuenta -->
                        <div class="flex items-center justify-between">
                            <div>
                                <label class="text-sm font-medium text-red-600 dark:text-red-400">
                                    Eliminar Cuenta
                                </label>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    Elimina permanentemente tu cuenta y todos los datos asociados
                                </p>
                            </div>
                            <button wire:click="confirmAccountDeletion" 
                                    class="inline-flex items-center px-4 py-2 border border-red-300 text-sm leading-4 font-medium rounded-md text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                Eliminar Cuenta
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Confirmación de Eliminación -->
    @if($showDeleteConfirmation)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 dark:bg-red-900 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white" id="modal-title">
                                    Confirmar Eliminación de Cuenta
                                </h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        ¿Estás segura de que quieres eliminar tu cuenta? Esta acción no se puede deshacer y perderás todos tus datos permanentemente.
                                    </p>
                                    <div class="mt-4">
                                        <input type="password" wire:model="deletePassword" placeholder="Confirma tu contraseña" 
                                               class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-red-500 dark:focus:border-red-600 focus:ring-red-500 dark:focus:ring-red-600 rounded-md shadow-sm">
                                        @error('deletePassword') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button wire:click="deleteAccount" type="button" 
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Eliminar Cuenta
                        </button>
                        <button wire:click="$set('showDeleteConfirmation', false)" type="button" 
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-700 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancelar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>