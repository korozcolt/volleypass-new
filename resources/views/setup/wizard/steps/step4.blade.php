@extends('layouts.setup')

@section('title', 'Paso 4: Reglas de Voleibol')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 py-12">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="mx-auto h-16 w-16 bg-indigo-600 rounded-full flex items-center justify-center mb-4">
                <span class="text-2xl font-bold text-white">4</span>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Reglas de Voleibol</h1>
            <p class="text-lg text-gray-600">Configure las reglas por defecto para los partidos</p>
        </div>

        <!-- Progress Bar -->
        <div class="mb-8">
            <div class="bg-white rounded-lg shadow-sm p-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-gray-700">Progreso: Paso 4 de 7</span>
                    <span class="text-sm font-medium text-indigo-600">{{ $progress }}%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-indigo-600 h-2 rounded-full transition-all duration-300" style="width: {{ $progress }}%"></div>
                </div>
            </div>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-lg shadow-sm">
            <form action="{{ route('setup.wizard.process', 4) }}" method="POST">
                @csrf
                
                <div class="px-6 py-8">
                    <div class="grid grid-cols-1 gap-6">
                        <!-- Configuración de Sets -->
                        <div class="border-b border-gray-200 pb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Configuración de Sets</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Duración del Set -->
                                <div>
                                    <label for="set_duration" class="block text-sm font-medium text-gray-700 mb-2">
                                        Duración Máxima del Set (minutos) *
                                    </label>
                                    <select id="set_duration" 
                                            name="set_duration" 
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                            required>
                                        <option value="">Seleccionar...</option>
                                        <option value="20" {{ old('set_duration', $data['set_duration'] ?? '') == '20' ? 'selected' : '' }}>20 minutos</option>
                                        <option value="25" {{ old('set_duration', $data['set_duration'] ?? '25') == '25' ? 'selected' : '' }}>25 minutos (Recomendado)</option>
                                        <option value="30" {{ old('set_duration', $data['set_duration'] ?? '') == '30' ? 'selected' : '' }}>30 minutos</option>
                                        <option value="35" {{ old('set_duration', $data['set_duration'] ?? '') == '35' ? 'selected' : '' }}>35 minutos</option>
                                        <option value="40" {{ old('set_duration', $data['set_duration'] ?? '') == '40' ? 'selected' : '' }}>40 minutos</option>
                                    </select>
                                    <p class="mt-1 text-xs text-gray-500">Tiempo máximo permitido por set</p>
                                    @error('set_duration')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Puntos para Ganar Set -->
                                <div>
                                    <label for="points_to_win_set" class="block text-sm font-medium text-gray-700 mb-2">
                                        Puntos para Ganar Set *
                                    </label>
                                    <select id="points_to_win_set" 
                                            name="points_to_win_set" 
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                            required>
                                        <option value="">Seleccionar...</option>
                                        <option value="21" {{ old('points_to_win_set', $data['points_to_win_set'] ?? '') == '21' ? 'selected' : '' }}>21 puntos</option>
                                        <option value="25" {{ old('points_to_win_set', $data['points_to_win_set'] ?? '25') == '25' ? 'selected' : '' }}>25 puntos (Estándar FIVB)</option>
                                        <option value="30" {{ old('points_to_win_set', $data['points_to_win_set'] ?? '') == '30' ? 'selected' : '' }}>30 puntos</option>
                                    </select>
                                    <p class="mt-1 text-xs text-gray-500">Puntos necesarios para ganar un set</p>
                                    @error('points_to_win_set')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="mt-6">
                                <!-- Diferencia de Puntos -->
                                <div>
                                    <label for="points_difference_to_win" class="block text-sm font-medium text-gray-700 mb-2">
                                        Diferencia Mínima para Ganar *
                                    </label>
                                    <select id="points_difference_to_win" 
                                            name="points_difference_to_win" 
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                            required>
                                        <option value="">Seleccionar...</option>
                                        <option value="2" {{ old('points_difference_to_win', $data['points_difference_to_win'] ?? '2') == '2' ? 'selected' : '' }}>2 puntos (Estándar FIVB)</option>
                                        <option value="3" {{ old('points_difference_to_win', $data['points_difference_to_win'] ?? '') == '3' ? 'selected' : '' }}>3 puntos</option>
                                        <option value="4" {{ old('points_difference_to_win', $data['points_difference_to_win'] ?? '') == '4' ? 'selected' : '' }}>4 puntos</option>
                                        <option value="5" {{ old('points_difference_to_win', $data['points_difference_to_win'] ?? '') == '5' ? 'selected' : '' }}>5 puntos</option>
                                    </select>
                                    <p class="mt-1 text-xs text-gray-500">Diferencia mínima de puntos para ganar el set</p>
                                    @error('points_difference_to_win')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Configuración de Tiempos -->
                        <div class="border-b border-gray-200 pb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Configuración de Tiempos</h3>
                            
                            <div>
                                <!-- Duración del Timeout -->
                                <div>
                                    <label for="timeout_duration" class="block text-sm font-medium text-gray-700 mb-2">
                                        Duración del Timeout (segundos) *
                                    </label>
                                    <select id="timeout_duration" 
                                            name="timeout_duration" 
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                            required>
                                        <option value="">Seleccionar...</option>
                                        <option value="30" {{ old('timeout_duration', $data['timeout_duration'] ?? '30') == '30' ? 'selected' : '' }}>30 segundos (Estándar FIVB)</option>
                                        <option value="45" {{ old('timeout_duration', $data['timeout_duration'] ?? '') == '45' ? 'selected' : '' }}>45 segundos</option>
                                        <option value="60" {{ old('timeout_duration', $data['timeout_duration'] ?? '') == '60' ? 'selected' : '' }}>60 segundos</option>
                                        <option value="90" {{ old('timeout_duration', $data['timeout_duration'] ?? '') == '90' ? 'selected' : '' }}>90 segundos</option>
                                        <option value="120" {{ old('timeout_duration', $data['timeout_duration'] ?? '') == '120' ? 'selected' : '' }}>120 segundos</option>
                                    </select>
                                    <p class="mt-1 text-xs text-gray-500">Duración de cada timeout solicitado</p>
                                    @error('timeout_duration')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Configuración de Sustituciones -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Configuración de Sustituciones</h3>
                            
                            <div>
                                <!-- Máximo de Sustituciones -->
                                <div>
                                    <label for="max_substitutions" class="block text-sm font-medium text-gray-700 mb-2">
                                        Máximo de Sustituciones por Set *
                                    </label>
                                    <select id="max_substitutions" 
                                            name="max_substitutions" 
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                            required>
                                        <option value="">Seleccionar...</option>
                                        <option value="6" {{ old('max_substitutions', $data['max_substitutions'] ?? '6') == '6' ? 'selected' : '' }}>6 sustituciones (Estándar FIVB)</option>
                                        <option value="8" {{ old('max_substitutions', $data['max_substitutions'] ?? '') == '8' ? 'selected' : '' }}>8 sustituciones</option>
                                        <option value="10" {{ old('max_substitutions', $data['max_substitutions'] ?? '') == '10' ? 'selected' : '' }}>10 sustituciones</option>
                                        <option value="12" {{ old('max_substitutions', $data['max_substitutions'] ?? '') == '12' ? 'selected' : '' }}>12 sustituciones (Ilimitadas)</option>
                                    </select>
                                    <p class="mt-1 text-xs text-gray-500">Número máximo de sustituciones permitidas por equipo por set</p>
                                    @error('max_substitutions')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Información Adicional -->
                        <div class="mt-6">
                            <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <h4 class="text-sm font-medium text-blue-800">Información sobre las reglas</h4>
                                        <div class="mt-2 text-sm text-blue-700">
                                            <ul class="list-disc list-inside space-y-1">
                                                <li>Estas reglas se aplicarán por defecto a todos los nuevos torneos</li>
                                                <li>Los organizadores podrán modificar estas reglas para torneos específicos</li>
                                                <li>Las configuraciones siguen los estándares de la FIVB (Federación Internacional de Voleibol)</li>
                                                <li>Puede cambiar estas configuraciones posteriormente desde el panel de administración</li>
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
                    <a href="{{ route('setup.wizard.step', 3) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition-colors duration-200">
                        <svg class="mr-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                        Paso Anterior
                    </a>
                    
                    <button type="submit" class="inline-flex items-center px-6 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                        Continuar al Paso 5
                        <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection