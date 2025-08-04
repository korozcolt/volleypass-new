@extends('layouts.setup')

@section('title', 'Paso 2: Información de Contacto')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 py-12">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="mx-auto h-16 w-16 bg-indigo-600 rounded-full flex items-center justify-center mb-4">
                <span class="text-2xl font-bold text-white">2</span>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Información de Contacto</h1>
            <p class="text-lg text-gray-600">Configure los datos de contacto de su organización</p>
        </div>

        <!-- Progress Bar -->
        <div class="mb-8">
            <div class="bg-white rounded-lg shadow-sm p-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-gray-700">Progreso: Paso 2 de 7</span>
                    <span class="text-sm font-medium text-indigo-600">{{ $progress }}%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-indigo-600 h-2 rounded-full transition-all duration-300" style="width: {{ $progress }}%"></div>
                </div>
            </div>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-lg shadow-sm">
            <form action="{{ route('setup.wizard.process', 2) }}" method="POST">
                @csrf
                
                <div class="px-6 py-8">
                    <div class="grid grid-cols-1 gap-6">
                        <!-- Información de Contacto Principal -->
                        <div class="border-b border-gray-200 pb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Contacto Principal</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Email de Contacto -->
                                <div>
                                    <label for="contact_email" class="block text-sm font-medium text-gray-700 mb-2">
                                        Email de Contacto *
                                    </label>
                                    <input type="email" 
                                           id="contact_email" 
                                           name="contact_email" 
                                           value="{{ old('contact_email', $data['contact_email'] ?? '') }}"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                           placeholder="contacto@federacion.com"
                                           required>
                                    @error('contact_email')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Teléfono de Contacto -->
                                <div>
                                    <label for="contact_phone" class="block text-sm font-medium text-gray-700 mb-2">
                                        Teléfono de Contacto *
                                    </label>
                                    <input type="tel" 
                                           id="contact_phone" 
                                           name="contact_phone" 
                                           value="{{ old('contact_phone', $data['contact_phone'] ?? '') }}"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                           placeholder="+57 300 123 4567"
                                           required>
                                    @error('contact_phone')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Dirección -->
                            <div class="mt-6">
                                <label for="contact_address" class="block text-sm font-medium text-gray-700 mb-2">
                                    Dirección *
                                </label>
                                <textarea id="contact_address" 
                                          name="contact_address" 
                                          rows="3"
                                          class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                          placeholder="Dirección completa de la sede principal"
                                          required>{{ old('contact_address', $data['contact_address'] ?? '') }}</textarea>
                                @error('contact_address')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Información Web -->
                        <div class="border-b border-gray-200 pb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Presencia Web</h3>
                            
                            <!-- Sitio Web -->
                            <div class="mb-6">
                                <label for="website_url" class="block text-sm font-medium text-gray-700 mb-2">
                                    Sitio Web
                                </label>
                                <input type="url" 
                                       id="website_url" 
                                       name="website_url" 
                                       value="{{ old('website_url', $data['website_url'] ?? '') }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                       placeholder="https://www.federacion.com">
                                @error('website_url')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Redes Sociales -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Redes Sociales</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <!-- Facebook -->
                                <div>
                                    <label for="social_facebook" class="block text-sm font-medium text-gray-700 mb-2">
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-2 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                            </svg>
                                            Facebook
                                        </div>
                                    </label>
                                    <input type="url" 
                                           id="social_facebook" 
                                           name="social_facebook" 
                                           value="{{ old('social_facebook', $data['social_facebook'] ?? '') }}"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                           placeholder="https://facebook.com/federacion">
                                    @error('social_facebook')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Instagram -->
                                <div>
                                    <label for="social_instagram" class="block text-sm font-medium text-gray-700 mb-2">
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-2 text-pink-600" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M12.017 0C5.396 0 .029 5.367.029 11.987c0 6.62 5.367 11.987 11.988 11.987 6.62 0 11.987-5.367 11.987-11.987C24.014 5.367 18.637.001 12.017.001zM8.449 16.988c-1.297 0-2.448-.49-3.323-1.297C4.198 14.895 3.708 13.744 3.708 12.447s.49-2.448 1.418-3.323C6.001 8.198 7.152 7.708 8.449 7.708s2.448.49 3.323 1.416c.875.875 1.365 2.026 1.365 3.323s-.49 2.448-1.365 3.323c-.875.807-2.026 1.218-3.323 1.218zm7.718-1.297c-.875.875-2.026 1.365-3.323 1.365s-2.448-.49-3.323-1.365c-.875-.875-1.365-2.026-1.365-3.323s.49-2.448 1.365-3.323c.875-.875 2.026-1.365 3.323-1.365s2.448.49 3.323 1.365c.875.875 1.365 2.026 1.365 3.323s-.49 2.448-1.365 3.323z"/>
                                            </svg>
                                            Instagram
                                        </div>
                                    </label>
                                    <input type="url" 
                                           id="social_instagram" 
                                           name="social_instagram" 
                                           value="{{ old('social_instagram', $data['social_instagram'] ?? '') }}"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                           placeholder="https://instagram.com/federacion">
                                    @error('social_instagram')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Twitter -->
                                <div>
                                    <label for="social_twitter" class="block text-sm font-medium text-gray-700 mb-2">
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-2 text-blue-400" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                                            </svg>
                                            Twitter
                                        </div>
                                    </label>
                                    <input type="url" 
                                           id="social_twitter" 
                                           name="social_twitter" 
                                           value="{{ old('social_twitter', $data['social_twitter'] ?? '') }}"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                           placeholder="https://twitter.com/federacion">
                                    @error('social_twitter')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-between items-center rounded-b-lg">
                    <a href="{{ route('setup.wizard.step', 1) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition-colors duration-200">
                        <svg class="mr-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                        Paso Anterior
                    </a>
                    
                    <button type="submit" class="inline-flex items-center px-6 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                        Continuar al Paso 3
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