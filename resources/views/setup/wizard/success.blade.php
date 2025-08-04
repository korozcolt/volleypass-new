@extends('layouts.setup')

@section('title', 'Configuración Completada')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-green-50 to-emerald-100 py-12">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Success Header -->
        <div class="text-center mb-8">
            <div class="mx-auto h-20 w-20 bg-green-600 rounded-full flex items-center justify-center mb-6">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <h1 class="text-4xl font-bold text-gray-900 mb-4">¡Configuración Completada!</h1>
            <p class="text-xl text-gray-600">Su sistema VolleyPass ha sido configurado exitosamente</p>
        </div>

        <!-- Success Content -->
        <div class="bg-white rounded-lg shadow-sm mb-8">
            <div class="px-6 py-8">
                <!-- Success Message -->
                <div class="text-center mb-8">
                    <div class="bg-green-50 border border-green-200 rounded-lg p-6">
                        <div class="flex items-center justify-center mb-4">
                            <svg class="h-8 w-8 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-green-800 mb-2">Sistema Configurado Correctamente</h3>
                        <p class="text-green-700">Todas las configuraciones han sido aplicadas y el sistema está listo para usar.</p>
                    </div>
                </div>

                <!-- Configuration Summary -->
                <div class="space-y-6">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Resumen de Configuración</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Sistema -->
                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="flex items-center mb-3">
                                    <svg class="w-5 h-5 text-indigo-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z" clip-rule="evenodd"></path>
                                    </svg>
                                    <h4 class="font-medium text-gray-900">Sistema</h4>
                                </div>
                                <ul class="text-sm text-gray-600 space-y-1">
                                    <li>✓ Aplicación configurada</li>
                                    <li>✓ Colores y branding aplicados</li>
                                    <li>✓ Información de contacto guardada</li>
                                </ul>
                            </div>

                            <!-- Federación -->
                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="flex items-center mb-3">
                                    <svg class="w-5 h-5 text-blue-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"></path>
                                    </svg>
                                    <h4 class="font-medium text-gray-900">Federación</h4>
                                </div>
                                <ul class="text-sm text-gray-600 space-y-1">
                                    <li>✓ Datos de federación configurados</li>
                                    <li>✓ Cuotas y validez establecidas</li>
                                    <li>✓ Código de federación asignado</li>
                                </ul>
                            </div>

                            <!-- Reglas -->
                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="flex items-center mb-3">
                                    <svg class="w-5 h-5 text-green-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    <h4 class="font-medium text-gray-900">Reglas de Voleibol</h4>
                                </div>
                                <ul class="text-sm text-gray-600 space-y-1">
                                    <li>✓ Reglas por defecto configuradas</li>
                                    <li>✓ Tiempos y puntuaciones establecidos</li>
                                    <li>✓ Configuración de sustituciones</li>
                                </ul>
                            </div>

                            <!-- Categorías -->
                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="flex items-center mb-3">
                                    <svg class="w-5 h-5 text-purple-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"></path>
                                    </svg>
                                    <h4 class="font-medium text-gray-900">Categorías</h4>
                                </div>
                                <ul class="text-sm text-gray-600 space-y-1">
                                    <li>✓ Categorías por defecto creadas</li>
                                    <li>✓ Divisiones por edad y género</li>
                                    <li>✓ Sistema listo para torneos</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Admin User Info -->
                    <div class="border-t border-gray-200 pt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Usuario Administrador Creado</h3>
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <div class="flex items-start">
                                <svg class="h-5 w-5 text-blue-400 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                </svg>
                                <div>
                                    <h4 class="text-sm font-medium text-blue-800">Credenciales de Acceso</h4>
                                    <div class="mt-2 text-sm text-blue-700">
                                        <p><strong>Email:</strong> {{ $adminUser['email'] ?? 'No disponible' }}</p>
                                        <p><strong>Rol:</strong> Superadministrador</p>
                                        <p class="mt-2">Use estas credenciales para acceder al panel de administración.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Next Steps -->
                    <div class="border-t border-gray-200 pt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Próximos Pasos</h3>
                        <div class="space-y-3">
                            <div class="flex items-start">
                                <span class="inline-flex items-center justify-center w-6 h-6 bg-indigo-100 text-indigo-600 rounded-full text-sm font-medium mr-3 mt-0.5">1</span>
                                <div>
                                    <h4 class="font-medium text-gray-900">Acceder al Panel de Administración</h4>
                                    <p class="text-sm text-gray-600">Use el botón de abajo para acceder directamente al panel de administración.</p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <span class="inline-flex items-center justify-center w-6 h-6 bg-indigo-100 text-indigo-600 rounded-full text-sm font-medium mr-3 mt-0.5">2</span>
                                <div>
                                    <h4 class="font-medium text-gray-900">Configurar Autenticación de Dos Factores</h4>
                                    <p class="text-sm text-gray-600">Recomendamos habilitar 2FA para mayor seguridad de su cuenta.</p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <span class="inline-flex items-center justify-center w-6 h-6 bg-indigo-100 text-indigo-600 rounded-full text-sm font-medium mr-3 mt-0.5">3</span>
                                <div>
                                    <h4 class="font-medium text-gray-900">Crear Usuarios Adicionales</h4>
                                    <p class="text-sm text-gray-600">Agregue otros administradores, organizadores y usuarios según sea necesario.</p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <span class="inline-flex items-center justify-center w-6 h-6 bg-indigo-100 text-indigo-600 rounded-full text-sm font-medium mr-3 mt-0.5">4</span>
                                <div>
                                    <h4 class="font-medium text-gray-900">Configurar su Primer Torneo</h4>
                                    <p class="text-sm text-gray-600">Comience creando su primer torneo para probar todas las funcionalidades.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="text-center space-y-4">
            <a href="{{ route('filament.admin.pages.dashboard') }}" 
               class="inline-flex items-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                <svg class="mr-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                Ir al Panel de Administración
            </a>
            
            <div>
                <a href="{{ route('home') }}" 
                   class="inline-flex items-center px-6 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition-colors duration-200">
                    Ver Sitio Público
                </a>
            </div>
        </div>

        <!-- Support Information -->
        <div class="mt-12 text-center">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">¿Necesita Ayuda?</h3>
                <p class="text-gray-600 mb-4">Si tiene alguna pregunta o necesita asistencia, no dude en contactarnos.</p>
                <div class="flex justify-center space-x-6 text-sm">
                    <a href="#" class="text-indigo-600 hover:text-indigo-500">
                        <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path>
                            <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path>
                        </svg>
                        Soporte por Email
                    </a>
                    <a href="#" class="text-indigo-600 hover:text-indigo-500">
                        <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-2 0c0 .993-.241 1.929-.668 2.754l-1.524-1.525a3.997 3.997 0 00.078-2.183l1.562-1.562C17.756 8.249 18 9.1 18 10z" clip-rule="evenodd"></path>
                        </svg>
                        Documentación
                    </a>
                    <a href="#" class="text-indigo-600 hover:text-indigo-500">
                        <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 13V5a2 2 0 00-2-2H4a2 2 0 00-2 2v8a2 2 0 002 2h3l3 3 3-3h3a2 2 0 002-2zM5 7a1 1 0 011-1h8a1 1 0 110 2H6a1 1 0 01-1-1zm1 3a1 1 0 100 2h3a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                        </svg>
                        Chat en Vivo
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection