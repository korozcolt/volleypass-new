@extends('layouts.setup')

@section('title', 'Paso 5: Categorías por Defecto')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="mx-auto h-16 w-16 bg-indigo-600 rounded-full flex items-center justify-center mb-4">
                <span class="text-2xl font-bold text-white">5</span>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Categorías por Defecto</h1>
            <p class="text-lg text-gray-600">Configure las categorías estándar de voleibol para su federación</p>
        </div>

        <!-- Progress Bar -->
        <div class="mb-8">
            <div class="bg-white rounded-lg shadow-sm p-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-gray-700">Progreso: Paso 5 de 7</span>
                    <span class="text-sm font-medium text-indigo-600">{{ $progress }}%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-indigo-600 h-2 rounded-full transition-all duration-300" style="width: {{ $progress }}%"></div>
                </div>
            </div>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-lg shadow-sm">
            <form action="{{ route('setup.wizard.process', 5) }}" method="POST">
                @csrf
                
                <div class="px-6 py-8">
                    <div class="space-y-8">
                        <!-- Categorías Masculinas -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                                </svg>
                                Categorías Masculinas
                            </h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                @php
                                    $maleCategories = [
                                        'sub_12_masculino' => ['name' => 'Sub-12 Masculino', 'description' => 'Hasta 12 años'],
                                        'sub_14_masculino' => ['name' => 'Sub-14 Masculino', 'description' => 'Hasta 14 años'],
                                        'sub_16_masculino' => ['name' => 'Sub-16 Masculino', 'description' => 'Hasta 16 años'],
                                        'sub_18_masculino' => ['name' => 'Sub-18 Masculino', 'description' => 'Hasta 18 años'],
                                        'sub_21_masculino' => ['name' => 'Sub-21 Masculino', 'description' => 'Hasta 21 años'],
                                        'adulto_masculino' => ['name' => 'Adulto Masculino', 'description' => 'Sin límite de edad'],
                                        'veterano_masculino' => ['name' => 'Veterano Masculino', 'description' => 'Más de 35 años']
                                    ];
                                @endphp
                                
                                @foreach($maleCategories as $key => $category)
                                    <div class="relative">
                                        <label class="flex items-start p-4 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer transition-colors duration-200">
                                            <input type="checkbox" 
                                                   name="categories[]" 
                                                   value="{{ $key }}"
                                                   class="mt-1 h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                                                   {{ in_array($key, old('categories', $data['categories'] ?? array_keys($maleCategories))) ? 'checked' : '' }}>
                                            <div class="ml-3">
                                                <div class="text-sm font-medium text-gray-900">{{ $category['name'] }}</div>
                                                <div class="text-xs text-gray-500">{{ $category['description'] }}</div>
                                            </div>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Categorías Femeninas -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-pink-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                                </svg>
                                Categorías Femeninas
                            </h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                @php
                                    $femaleCategories = [
                                        'sub_12_femenino' => ['name' => 'Sub-12 Femenino', 'description' => 'Hasta 12 años'],
                                        'sub_14_femenino' => ['name' => 'Sub-14 Femenino', 'description' => 'Hasta 14 años'],
                                        'sub_16_femenino' => ['name' => 'Sub-16 Femenino', 'description' => 'Hasta 16 años'],
                                        'sub_18_femenino' => ['name' => 'Sub-18 Femenino', 'description' => 'Hasta 18 años'],
                                        'sub_21_femenino' => ['name' => 'Sub-21 Femenino', 'description' => 'Hasta 21 años'],
                                        'adulto_femenino' => ['name' => 'Adulto Femenino', 'description' => 'Sin límite de edad'],
                                        'veterano_femenino' => ['name' => 'Veterano Femenino', 'description' => 'Más de 35 años']
                                    ];
                                @endphp
                                
                                @foreach($femaleCategories as $key => $category)
                                    <div class="relative">
                                        <label class="flex items-start p-4 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer transition-colors duration-200">
                                            <input type="checkbox" 
                                                   name="categories[]" 
                                                   value="{{ $key }}"
                                                   class="mt-1 h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                                                   {{ in_array($key, old('categories', $data['categories'] ?? array_keys($femaleCategories))) ? 'checked' : '' }}>
                                            <div class="ml-3">
                                                <div class="text-sm font-medium text-gray-900">{{ $category['name'] }}</div>
                                                <div class="text-xs text-gray-500">{{ $category['description'] }}</div>
                                            </div>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Categorías Mixtas -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-6-3a2 2 0 11-4 0 2 2 0 014 0zm-2 4a5 5 0 00-4.546 2.916A5.986 5.986 0 0010 16a5.986 5.986 0 004.546-2.084A5 5 0 0010 11z" clip-rule="evenodd"></path>
                                </svg>
                                Categorías Mixtas
                            </h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                @php
                                    $mixedCategories = [
                                        'sub_12_mixto' => ['name' => 'Sub-12 Mixto', 'description' => 'Hasta 12 años'],
                                        'sub_14_mixto' => ['name' => 'Sub-14 Mixto', 'description' => 'Hasta 14 años'],
                                        'sub_16_mixto' => ['name' => 'Sub-16 Mixto', 'description' => 'Hasta 16 años'],
                                        'sub_18_mixto' => ['name' => 'Sub-18 Mixto', 'description' => 'Hasta 18 años'],
                                        'adulto_mixto' => ['name' => 'Adulto Mixto', 'description' => 'Sin límite de edad'],
                                        'veterano_mixto' => ['name' => 'Veterano Mixto', 'description' => 'Más de 35 años']
                                    ];
                                @endphp
                                
                                @foreach($mixedCategories as $key => $category)
                                    <div class="relative">
                                        <label class="flex items-start p-4 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer transition-colors duration-200">
                                            <input type="checkbox" 
                                                   name="categories[]" 
                                                   value="{{ $key }}"
                                                   class="mt-1 h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                                                   {{ in_array($key, old('categories', $data['categories'] ?? [])) ? 'checked' : '' }}>
                                            <div class="ml-3">
                                                <div class="text-sm font-medium text-gray-900">{{ $category['name'] }}</div>
                                                <div class="text-xs text-gray-500">{{ $category['description'] }}</div>
                                            </div>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Acciones de Selección -->
                        <div class="border-t border-gray-200 pt-6">
                            <div class="flex flex-wrap gap-3">
                                <button type="button" 
                                        onclick="selectAllCategories()"
                                        class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition-colors duration-200">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Seleccionar Todas
                                </button>
                                
                                <button type="button" 
                                        onclick="deselectAllCategories()"
                                        class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition-colors duration-200">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                    Deseleccionar Todas
                                </button>
                                
                                <button type="button" 
                                        onclick="selectStandardCategories()"
                                        class="inline-flex items-center px-3 py-2 border border-indigo-300 text-sm font-medium rounded-md text-indigo-700 bg-indigo-50 hover:bg-indigo-100 transition-colors duration-200">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4"></path>
                                    </svg>
                                    Seleccionar Estándar
                                </button>
                            </div>
                        </div>

                        @error('categories')
                            <div class="text-sm text-red-600">{{ $message }}</div>
                        @enderror

                        <!-- Información Adicional -->
                        <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h4 class="text-sm font-medium text-blue-800">Información sobre las categorías</h4>
                                    <div class="mt-2 text-sm text-blue-700">
                                        <ul class="list-disc list-inside space-y-1">
                                            <li>Seleccione las categorías que desea crear por defecto en su sistema</li>
                                            <li>Puede agregar, modificar o eliminar categorías posteriormente</li>
                                            <li>Las categorías estándar incluyen Sub-14, Sub-16, Sub-18 y Adulto para ambos géneros</li>
                                            <li>Las categorías mixtas permiten equipos con jugadores de ambos géneros</li>
                                            <li>Cada categoría puede tener reglas específicas que se configuran por torneo</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-between items-center rounded-b-lg">
                    <a href="{{ route('setup.wizard.step', 4) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition-colors duration-200">
                        <svg class="mr-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                        Paso Anterior
                    </a>
                    
                    <button type="submit" class="inline-flex items-center px-6 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                        Continuar al Paso 6
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
function selectAllCategories() {
    const checkboxes = document.querySelectorAll('input[name="categories[]"]');
    checkboxes.forEach(checkbox => {
        checkbox.checked = true;
    });
}

function deselectAllCategories() {
    const checkboxes = document.querySelectorAll('input[name="categories[]"]');
    checkboxes.forEach(checkbox => {
        checkbox.checked = false;
    });
}

function selectStandardCategories() {
    // Deseleccionar todas primero
    deselectAllCategories();
    
    // Seleccionar categorías estándar
    const standardCategories = [
        'sub_14_masculino', 'sub_16_masculino', 'sub_18_masculino', 'adulto_masculino',
        'sub_14_femenino', 'sub_16_femenino', 'sub_18_femenino', 'adulto_femenino'
    ];
    
    standardCategories.forEach(category => {
        const checkbox = document.querySelector(`input[value="${category}"]`);
        if (checkbox) {
            checkbox.checked = true;
        }
    });
}
</script>
@endsection