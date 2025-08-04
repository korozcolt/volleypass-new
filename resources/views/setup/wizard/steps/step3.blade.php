@extends('layouts.setup')

@section('title', 'Paso 3: Configuración de Federación')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 py-12">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="mx-auto h-16 w-16 bg-indigo-600 rounded-full flex items-center justify-center mb-4">
                <span class="text-2xl font-bold text-white">3</span>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Configuración de Federación</h1>
            <p class="text-lg text-gray-600">Configure los parámetros específicos de su federación</p>
        </div>

        <!-- Progress Bar -->
        <div class="mb-8">
            <div class="bg-white rounded-lg shadow-sm p-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-gray-700">Progreso: Paso 3 de 7</span>
                    <span class="text-sm font-medium text-indigo-600">{{ $progress }}%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-indigo-600 h-2 rounded-full transition-all duration-300" style="width: {{ $progress }}%"></div>
                </div>
            </div>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-lg shadow-sm">
            <form action="{{ route('setup.wizard.process', 3) }}" method="POST">
                @csrf
                
                <div class="px-6 py-8">
                    <div class="grid grid-cols-1 gap-6">
                        <!-- Información de la Federación -->
                        <div class="border-b border-gray-200 pb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Información de la Federación</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Nombre de la Federación -->
                                <div>
                                    <label for="federation_name" class="block text-sm font-medium text-gray-700 mb-2">
                                        Nombre de la Federación *
                                    </label>
                                    <input type="text" 
                                           id="federation_name" 
                                           name="federation_name" 
                                           value="{{ old('federation_name', $data['federation_name'] ?? '') }}"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                           placeholder="Ej: Federación Regional de Voleibol"
                                           required>
                                    @error('federation_name')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Código de la Federación -->
                                <div>
                                    <label for="federation_code" class="block text-sm font-medium text-gray-700 mb-2">
                                        Código de la Federación *
                                    </label>
                                    <input type="text" 
                                           id="federation_code" 
                                           name="federation_code" 
                                           value="{{ old('federation_code', $data['federation_code'] ?? '') }}"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                           placeholder="Ej: FRV2024"
                                           maxlength="10"
                                           style="text-transform: uppercase;"
                                           required>
                                    <p class="mt-1 text-xs text-gray-500">Código único de identificación (máximo 10 caracteres)</p>
                                    @error('federation_code')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Configuración Financiera -->
                        <div class="border-b border-gray-200 pb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Configuración Financiera</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Cuota Anual -->
                                <div>
                                    <label for="annual_fee" class="block text-sm font-medium text-gray-700 mb-2">
                                        Cuota Anual de Federación *
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500 sm:text-sm">$</span>
                                        </div>
                                        <input type="number" 
                                               id="annual_fee" 
                                               name="annual_fee" 
                                               value="{{ old('annual_fee', $data['annual_fee'] ?? '50000') }}"
                                               class="w-full pl-7 pr-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                               placeholder="50000"
                                               min="0"
                                               step="1000"
                                               required>
                                    </div>
                                    <p class="mt-1 text-xs text-gray-500">Monto en pesos colombianos</p>
                                    @error('annual_fee')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Validez del Carnet -->
                                <div>
                                    <label for="card_validity_months" class="block text-sm font-medium text-gray-700 mb-2">
                                        Validez del Carnet (meses) *
                                    </label>
                                    <select id="card_validity_months" 
                                            name="card_validity_months" 
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                            required>
                                        <option value="">Seleccionar...</option>
                                        <option value="6" {{ old('card_validity_months', $data['card_validity_months'] ?? '') == '6' ? 'selected' : '' }}>6 meses</option>
                                        <option value="12" {{ old('card_validity_months', $data['card_validity_months'] ?? '12') == '12' ? 'selected' : '' }}>12 meses (Recomendado)</option>
                                        <option value="18" {{ old('card_validity_months', $data['card_validity_months'] ?? '') == '18' ? 'selected' : '' }}>18 meses</option>
                                        <option value="24" {{ old('card_validity_months', $data['card_validity_months'] ?? '') == '24' ? 'selected' : '' }}>24 meses</option>
                                        <option value="36" {{ old('card_validity_months', $data['card_validity_months'] ?? '') == '36' ? 'selected' : '' }}>36 meses</option>
                                    </select>
                                    <p class="mt-1 text-xs text-gray-500">Tiempo de validez del carnet de jugadora</p>
                                    @error('card_validity_months')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Información Adicional -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Información Adicional</h3>
                            
                            <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <h4 class="text-sm font-medium text-blue-800">Información importante</h4>
                                        <div class="mt-2 text-sm text-blue-700">
                                            <ul class="list-disc list-inside space-y-1">
                                                <li>El código de federación debe ser único y no se puede cambiar después</li>
                                                <li>La cuota anual se aplicará a todas las nuevas federaciones</li>
                                                <li>La validez del carnet afecta la renovación automática</li>
                                                <li>Estos valores se pueden modificar posteriormente desde la configuración</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-between items-center rounded-b-lg">
                    <a href="{{ route('setup.wizard.step', 2) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition-colors duration-200">
                        <svg class="mr-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                        Paso Anterior
                    </a>
                    
                    <button type="submit" class="inline-flex items-center px-6 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                        Continuar al Paso 4
                        <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Convertir código de federación a mayúsculas
    document.addEventListener('DOMContentLoaded', function() {
        const federationCodeInput = document.getElementById('federation_code');
        
        federationCodeInput.addEventListener('input', function() {
            this.value = this.value.toUpperCase();
        });
    });
</script>
@endpush
@endsection