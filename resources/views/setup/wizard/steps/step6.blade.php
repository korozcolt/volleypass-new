@extends('layouts.setup')

@section('title', 'Paso 6: Usuario Administrador')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 py-12">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="mx-auto h-16 w-16 bg-indigo-600 rounded-full flex items-center justify-center mb-4">
                <span class="text-2xl font-bold text-white">6</span>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Usuario Administrador</h1>
            <p class="text-lg text-gray-600">Configure el usuario administrador principal del sistema</p>
        </div>

        <!-- Progress Bar -->
        <div class="mb-8">
            <div class="bg-white rounded-lg shadow-sm p-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-gray-700">Progreso: Paso 6 de 7</span>
                    <span class="text-sm font-medium text-indigo-600">{{ $progress }}%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-indigo-600 h-2 rounded-full transition-all duration-300" style="width: {{ $progress }}%"></div>
                </div>
            </div>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-lg shadow-sm">
            <form action="{{ route('setup.wizard.process', 6) }}" method="POST">
                @csrf
                
                <div class="px-6 py-8">
                    <div class="space-y-6">
                        <!-- Información Personal -->
                        <div class="border-b border-gray-200 pb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Información Personal</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Nombre -->
                                <div>
                                    <label for="first_name" class="block text-sm font-medium text-gray-700 mb-2">
                                        Nombre *
                                    </label>
                                    <input type="text" 
                                           id="first_name" 
                                           name="first_name" 
                                           value="{{ old('first_name', $data['first_name'] ?? '') }}"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                           required>
                                    @error('first_name')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Apellido -->
                                <div>
                                    <label for="last_name" class="block text-sm font-medium text-gray-700 mb-2">
                                        Apellido *
                                    </label>
                                    <input type="text" 
                                           id="last_name" 
                                           name="last_name" 
                                           value="{{ old('last_name', $data['last_name'] ?? '') }}"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                           required>
                                    @error('last_name')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="mt-6">
                                <!-- Email -->
                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                        Correo Electrónico *
                                    </label>
                                    <input type="email" 
                                           id="email" 
                                           name="email" 
                                           value="{{ old('email', $data['email'] ?? '') }}"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                           required>
                                    <p class="mt-1 text-xs text-gray-500">Este será el email principal para acceder al sistema</p>
                                    @error('email')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Información de Contacto -->
                        <div class="border-b border-gray-200 pb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Información de Contacto</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Teléfono -->
                                <div>
                                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                                        Teléfono
                                    </label>
                                    <input type="tel" 
                                           id="phone" 
                                           name="phone" 
                                           value="{{ old('phone', $data['phone'] ?? '') }}"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                           placeholder="+1234567890">
                                    @error('phone')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Documento de Identidad -->
                                <div>
                                    <label for="document_number" class="block text-sm font-medium text-gray-700 mb-2">
                                        Documento de Identidad
                                    </label>
                                    <input type="text" 
                                           id="document_number" 
                                           name="document_number" 
                                           value="{{ old('document_number', $data['document_number'] ?? '') }}"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                           placeholder="12345678">
                                    @error('document_number')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Configuración de Acceso -->
                        <div class="border-b border-gray-200 pb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Configuración de Acceso</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Contraseña -->
                                <div>
                                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                                        Contraseña *
                                    </label>
                                    <input type="password" 
                                           id="password" 
                                           name="password" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                           required
                                           minlength="8">
                                    <p class="mt-1 text-xs text-gray-500">Mínimo 8 caracteres, incluya mayúsculas, minúsculas y números</p>
                                    @error('password')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Confirmar Contraseña -->
                                <div>
                                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                                        Confirmar Contraseña *
                                    </label>
                                    <input type="password" 
                                           id="password_confirmation" 
                                           name="password_confirmation" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                           required
                                           minlength="8">
                                    @error('password_confirmation')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Permisos y Roles -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Permisos y Roles</h3>
                            
                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <h4 class="text-sm font-medium text-gray-900">Superadministrador</h4>
                                        <div class="mt-2 text-sm text-gray-700">
                                            <p>Este usuario tendrá acceso completo al sistema con los siguientes permisos:</p>
                                            <ul class="mt-2 list-disc list-inside space-y-1">
                                                <li>Gestión completa de usuarios y roles</li>
                                                <li>Configuración del sistema</li>
                                                <li>Gestión de federaciones y clubes</li>
                                                <li>Administración de torneos y competencias</li>
                                                <li>Acceso a reportes y estadísticas</li>
                                                <li>Configuración de pagos y facturación</li>
                                                <li>Gestión de respaldos y mantenimiento</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Información Adicional -->
                        <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h4 class="text-sm font-medium text-yellow-800">Importante</h4>
                                    <div class="mt-2 text-sm text-yellow-700">
                                        <ul class="list-disc list-inside space-y-1">
                                            <li>Guarde esta información de acceso en un lugar seguro</li>
                                            <li>Este usuario tendrá privilegios completos sobre el sistema</li>
                                            <li>Puede crear usuarios adicionales después de completar la configuración</li>
                                            <li>Se recomienda habilitar la autenticación de dos factores después del primer acceso</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-between items-center rounded-b-lg">
                    <a href="{{ route('setup.wizard.step', 5) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition-colors duration-200">
                        <svg class="mr-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                        Paso Anterior
                    </a>
                    
                    <button type="submit" class="inline-flex items-center px-6 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                        Continuar al Paso 7
                        <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Validación de contraseña en tiempo real
document.addEventListener('DOMContentLoaded', function() {
    const password = document.getElementById('password');
    const passwordConfirmation = document.getElementById('password_confirmation');
    
    function validatePasswords() {
        if (password.value && passwordConfirmation.value) {
            if (password.value !== passwordConfirmation.value) {
                passwordConfirmation.setCustomValidity('Las contraseñas no coinciden');
            } else {
                passwordConfirmation.setCustomValidity('');
            }
        }
    }
    
    password.addEventListener('input', validatePasswords);
    passwordConfirmation.addEventListener('input', validatePasswords);
});
</script>
@endsection