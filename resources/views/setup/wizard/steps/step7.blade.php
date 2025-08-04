@extends('layouts.setup')

@section('title', 'Paso 7: Revisión Final')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="mx-auto h-16 w-16 bg-green-600 rounded-full flex items-center justify-center mb-4">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Revisión Final</h1>
            <p class="text-lg text-gray-600">Revise toda la configuración antes de finalizar la instalación</p>
        </div>

        <!-- Progress Bar -->
        <div class="mb-8">
            <div class="bg-white rounded-lg shadow-sm p-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-gray-700">Progreso: Paso 7 de 7</span>
                    <span class="text-sm font-medium text-green-600">{{ $progress }}%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-green-600 h-2 rounded-full transition-all duration-300" style="width: {{ $progress }}%"></div>
                </div>
            </div>
        </div>

        <!-- Review Content -->
        <div class="space-y-6">
            <!-- Paso 1: Información General -->
            <div class="bg-white rounded-lg shadow-sm">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-medium text-gray-900 flex items-center">
                            <span class="inline-flex items-center justify-center w-6 h-6 bg-green-100 text-green-600 rounded-full text-sm font-medium mr-3">1</span>
                            Información General
                        </h3>
                        <a href="{{ route('setup.wizard.step', 1) }}" class="text-indigo-600 hover:text-indigo-500 text-sm font-medium">
                            Editar
                        </a>
                    </div>
                </div>
                <div class="px-6 py-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Nombre de la Aplicación</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $allData['step1']['app_name'] ?? 'No configurado' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Color Primario</dt>
                            <dd class="mt-1 text-sm text-gray-900 flex items-center">
                                <span class="inline-block w-4 h-4 rounded mr-2" style="background-color: {{ $allData['step1']['primary_color'] ?? '#6366f1' }}"></span>
                                {{ $allData['step1']['primary_color'] ?? '#6366f1' }}
                            </dd>
                        </div>
                        <div class="md:col-span-2">
                            <dt class="text-sm font-medium text-gray-500">Descripción</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $allData['step1']['app_description'] ?? 'No configurado' }}</dd>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Paso 2: Información de Contacto -->
            <div class="bg-white rounded-lg shadow-sm">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-medium text-gray-900 flex items-center">
                            <span class="inline-flex items-center justify-center w-6 h-6 bg-green-100 text-green-600 rounded-full text-sm font-medium mr-3">2</span>
                            Información de Contacto
                        </h3>
                        <a href="{{ route('setup.wizard.step', 2) }}" class="text-indigo-600 hover:text-indigo-500 text-sm font-medium">
                            Editar
                        </a>
                    </div>
                </div>
                <div class="px-6 py-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Email de Contacto</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $allData['step2']['contact_email'] ?? 'No configurado' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Teléfono</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $allData['step2']['contact_phone'] ?? 'No configurado' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Sitio Web</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $allData['step2']['website_url'] ?? 'No configurado' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Dirección</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $allData['step2']['contact_address'] ?? 'No configurado' }}</dd>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Paso 3: Configuración de Federación -->
            <div class="bg-white rounded-lg shadow-sm">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-medium text-gray-900 flex items-center">
                            <span class="inline-flex items-center justify-center w-6 h-6 bg-green-100 text-green-600 rounded-full text-sm font-medium mr-3">3</span>
                            Configuración de Federación
                        </h3>
                        <a href="{{ route('setup.wizard.step', 3) }}" class="text-indigo-600 hover:text-indigo-500 text-sm font-medium">
                            Editar
                        </a>
                    </div>
                </div>
                <div class="px-6 py-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Nombre de la Federación</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $allData['step3']['federation_name'] ?? 'No configurado' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Código de Federación</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $allData['step3']['federation_code'] ?? 'No configurado' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Cuota Anual</dt>
                            <dd class="mt-1 text-sm text-gray-900">${{ number_format($allData['step3']['annual_fee'] ?? 0, 2) }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Validez del Carnet (meses)</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $allData['step3']['card_validity_months'] ?? 'No configurado' }}</dd>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Paso 4: Reglas de Voleibol -->
            <div class="bg-white rounded-lg shadow-sm">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-medium text-gray-900 flex items-center">
                            <span class="inline-flex items-center justify-center w-6 h-6 bg-green-100 text-green-600 rounded-full text-sm font-medium mr-3">4</span>
                            Reglas de Voleibol
                        </h3>
                        <a href="{{ route('setup.wizard.step', 4) }}" class="text-indigo-600 hover:text-indigo-500 text-sm font-medium">
                            Editar
                        </a>
                    </div>
                </div>
                <div class="px-6 py-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Duración del Set</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $allData['step4']['set_duration'] ?? 'No configurado' }} minutos</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Puntos para Ganar Set</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $allData['step4']['points_to_win_set'] ?? 'No configurado' }} puntos</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Diferencia Mínima</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $allData['step4']['points_difference_to_win'] ?? 'No configurado' }} puntos</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Duración del Timeout</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $allData['step4']['timeout_duration'] ?? 'No configurado' }} segundos</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Máximo de Sustituciones</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $allData['step4']['max_substitutions'] ?? 'No configurado' }} por set</dd>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Paso 5: Categorías por Defecto -->
            <div class="bg-white rounded-lg shadow-sm">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-medium text-gray-900 flex items-center">
                            <span class="inline-flex items-center justify-center w-6 h-6 bg-green-100 text-green-600 rounded-full text-sm font-medium mr-3">5</span>
                            Categorías por Defecto
                        </h3>
                        <a href="{{ route('setup.wizard.step', 5) }}" class="text-indigo-600 hover:text-indigo-500 text-sm font-medium">
                            Editar
                        </a>
                    </div>
                </div>
                <div class="px-6 py-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500 mb-2">Categorías Seleccionadas</dt>
                        <dd class="mt-1">
                            @if(isset($allData['step5']['categories']) && count($allData['step5']['categories']) > 0)
                                <div class="flex flex-wrap gap-2">
                                    @foreach($allData['step5']['categories'] as $category)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                            {{ ucwords(str_replace('_', ' ', $category)) }}
                                        </span>
                                    @endforeach
                                </div>
                                <p class="text-sm text-gray-500 mt-2">Total: {{ count($allData['step5']['categories']) }} categorías</p>
                            @else
                                <span class="text-sm text-gray-500">No se han seleccionado categorías</span>
                            @endif
                        </dd>
                    </div>
                </div>
            </div>

            <!-- Paso 6: Usuario Administrador -->
            <div class="bg-white rounded-lg shadow-sm">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-medium text-gray-900 flex items-center">
                            <span class="inline-flex items-center justify-center w-6 h-6 bg-green-100 text-green-600 rounded-full text-sm font-medium mr-3">6</span>
                            Usuario Administrador
                        </h3>
                        <a href="{{ route('setup.wizard.step', 6) }}" class="text-indigo-600 hover:text-indigo-500 text-sm font-medium">
                            Editar
                        </a>
                    </div>
                </div>
                <div class="px-6 py-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Nombre Completo</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ ($allData['step6']['first_name'] ?? '') . ' ' . ($allData['step6']['last_name'] ?? '') }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Email</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $allData['step6']['email'] ?? 'No configurado' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Teléfono</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $allData['step6']['phone'] ?? 'No configurado' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Documento</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $allData['step6']['document_number'] ?? 'No configurado' }}</dd>
                        </div>
                        <div class="md:col-span-2">
                            <dt class="text-sm font-medium text-gray-500">Rol</dt>
                            <dd class="mt-1">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    Superadministrador
                                </span>
                            </dd>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Final Actions -->
        <div class="mt-8">
            <div class="bg-white rounded-lg shadow-sm">
                <form action="{{ route('setup.wizard.process', 7) }}" method="POST">
                    @csrf
                    
                    <div class="px-6 py-8">
                        <!-- Confirmación -->
                        <div class="mb-6">
                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input id="confirm_setup" 
                                           name="confirm_setup" 
                                           type="checkbox" 
                                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                                           required>
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="confirm_setup" class="font-medium text-gray-700">
                                        Confirmo que he revisado toda la configuración y deseo finalizar la instalación
                                    </label>
                                    <p class="text-gray-500">Al confirmar, se aplicarán todas las configuraciones y se completará la instalación del sistema.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Información Final -->
                        <div class="bg-green-50 border border-green-200 rounded-md p-4 mb-6">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h4 class="text-sm font-medium text-green-800">¡Casi terminamos!</h4>
                                    <div class="mt-2 text-sm text-green-700">
                                        <p>Al finalizar la configuración:</p>
                                        <ul class="list-disc list-inside mt-1 space-y-1">
                                            <li>Se aplicarán todas las configuraciones del sistema</li>
                                            <li>Se creará el usuario administrador</li>
                                            <li>Se instalarán las categorías por defecto</li>
                                            <li>El sistema estará listo para usar</li>
                                            <li>Será redirigido al panel de administración</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-between items-center rounded-b-lg">
                        <a href="{{ route('setup.wizard.step', 6) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition-colors duration-200">
                            <svg class="mr-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                            Paso Anterior
                        </a>
                        
                        <button type="submit" class="inline-flex items-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-200">
                            <svg class="mr-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Finalizar Configuración
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection