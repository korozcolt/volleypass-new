@extends('layouts.setup')

@section('title', 'Paso 1: Información General')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 py-12">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="mx-auto h-16 w-16 bg-indigo-600 rounded-full flex items-center justify-center mb-4">
                <span class="text-2xl font-bold text-white">1</span>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Información General</h1>
            <p class="text-lg text-gray-600">Configure la información básica de su aplicación</p>
        </div>

        <!-- Progress Bar -->
        <div class="mb-8">
            <div class="bg-white rounded-lg shadow-sm p-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-gray-700">Progreso: Paso 1 de 7</span>
                    <span class="text-sm font-medium text-indigo-600">{{ $progress }}%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-indigo-600 h-2 rounded-full transition-all duration-300" style="width: {{ $progress }}%"></div>
                </div>
            </div>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-lg shadow-sm">
            <form action="{{ route('setup.wizard.process', 1) }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="px-6 py-8">
                    <div class="grid grid-cols-1 gap-6">
                        <!-- Nombre de la Aplicación -->
                        <div>
                            <label for="app_name" class="block text-sm font-medium text-gray-700 mb-2">
                                Nombre de la Aplicación *
                            </label>
                            <input type="text" 
                                   id="app_name" 
                                   name="app_name" 
                                   value="{{ old('app_name', $data['app_name'] ?? 'VolleyPass Soft') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                   placeholder="Ej: Federación de Voleibol Regional"
                                   required>
                            @error('app_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Descripción de la Aplicación -->
                        <div>
                            <label for="app_description" class="block text-sm font-medium text-gray-700 mb-2">
                                Descripción de la Aplicación
                            </label>
                            <textarea id="app_description" 
                                      name="app_description" 
                                      rows="3"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                      placeholder="Breve descripción de su organización o federación">{{ old('app_description', $data['app_description'] ?? '') }}</textarea>
                            @error('app_description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Colores de Marca -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Color Primario -->
                            <div>
                                <label for="primary_color" class="block text-sm font-medium text-gray-700 mb-2">
                                    Color Primario *
                                </label>
                                <div class="flex items-center space-x-3">
                                    <input type="color" 
                                           id="primary_color" 
                                           name="primary_color" 
                                           value="{{ old('primary_color', $data['primary_color'] ?? '#4F46E5') }}"
                                           class="h-10 w-16 border border-gray-300 rounded-md cursor-pointer">
                                    <input type="text" 
                                           value="{{ old('primary_color', $data['primary_color'] ?? '#4F46E5') }}"
                                           class="flex-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                           placeholder="#4F46E5"
                                           pattern="^#[0-9A-Fa-f]{6}$"
                                           onchange="document.getElementById('primary_color').value = this.value"
                                           required>
                                </div>
                                @error('primary_color')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Color Secundario -->
                            <div>
                                <label for="secondary_color" class="block text-sm font-medium text-gray-700 mb-2">
                                    Color Secundario *
                                </label>
                                <div class="flex items-center space-x-3">
                                    <input type="color" 
                                           id="secondary_color" 
                                           name="secondary_color" 
                                           value="{{ old('secondary_color', $data['secondary_color'] ?? '#10B981') }}"
                                           class="h-10 w-16 border border-gray-300 rounded-md cursor-pointer">
                                    <input type="text" 
                                           value="{{ old('secondary_color', $data['secondary_color'] ?? '#10B981') }}"
                                           class="flex-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                           placeholder="#10B981"
                                           pattern="^#[0-9A-Fa-f]{6}$"
                                           onchange="document.getElementById('secondary_color').value = this.value"
                                           required>
                                </div>
                                @error('secondary_color')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Logo -->
                        <div>
                            <label for="logo" class="block text-sm font-medium text-gray-700 mb-2">
                                Logo de la Aplicación
                            </label>
                            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:border-gray-400 transition-colors duration-200">
                                <div class="space-y-1 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <div class="flex text-sm text-gray-600">
                                        <label for="logo" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                            <span>Subir un archivo</span>
                                            <input id="logo" name="logo" type="file" class="sr-only" accept="image/*">
                                        </label>
                                        <p class="pl-1">o arrastrar y soltar</p>
                                    </div>
                                    <p class="text-xs text-gray-500">PNG, JPG, SVG hasta 2MB</p>
                                </div>
                            </div>
                            @error('logo')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-between items-center rounded-b-lg">
                    <a href="{{ route('setup.wizard') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition-colors duration-200">
                        <svg class="mr-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                        Volver al Resumen
                    </a>
                    
                    <button type="submit" class="inline-flex items-center px-6 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                        Continuar al Paso 2
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
    // Sincronizar color picker con input de texto
    document.addEventListener('DOMContentLoaded', function() {
        const primaryColorPicker = document.getElementById('primary_color');
        const primaryColorText = primaryColorPicker.nextElementSibling;
        
        const secondaryColorPicker = document.getElementById('secondary_color');
        const secondaryColorText = secondaryColorPicker.nextElementSibling;
        
        primaryColorPicker.addEventListener('change', function() {
            primaryColorText.value = this.value;
        });
        
        secondaryColorPicker.addEventListener('change', function() {
            secondaryColorText.value = this.value;
        });
        
        primaryColorText.addEventListener('input', function() {
            if (/^#[0-9A-Fa-f]{6}$/.test(this.value)) {
                primaryColorPicker.value = this.value;
            }
        });
        
        secondaryColorText.addEventListener('input', function() {
            if (/^#[0-9A-Fa-f]{6}$/.test(this.value)) {
                secondaryColorPicker.value = this.value;
            }
        });
    });
</script>
@endpush
@endsection