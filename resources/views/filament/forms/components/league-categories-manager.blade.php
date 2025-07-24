@php
    use App\Services\LeagueConfigurationService;
    use App\Services\CategoryValidationService;

    // Try multiple ways to get the league record
    $league = $league ?? $this->data['league'] ?? $record ?? null;
    $configService = app(LeagueConfigurationService::class);

    $hasCategories = $league && $league->hasCustomCategories();
    $categories = $hasCategories ? $league->getActiveCategories() : collect();
    $validation = $hasCategories ? $configService->validateCategoryConfiguration($league) : null;
@endphp

<div class="league-categories-manager" x-data="{}">
    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100 p-6">
    {{-- Hero Header --}}
    <div class="relative overflow-hidden bg-gradient-to-r from-blue-600 via-purple-600 to-indigo-700 rounded-3xl shadow-2xl mb-8">
        <div class="absolute inset-0 bg-black opacity-10"></div>
        <div class="absolute inset-0">
            <svg class="absolute bottom-0 left-0 mb-8" viewBox="0 0 375 283" fill="none" style="transform: scale(1.5); opacity: 0.1;">
                <rect x="159.52" y="175" width="152" height="152" rx="8" transform="rotate(-45 159.52 175)" fill="white"/>
                <rect x="159.52" y="107" width="152" height="152" rx="8" transform="rotate(-45 159.52 107)" fill="white"/>
            </svg>
        </div>
        <div class="relative px-8 py-12">
            <div class="flex items-center justify-between">
                <div>
                    <div class="flex items-center mb-4">
                        <div class="w-16 h-16 bg-white bg-opacity-20 backdrop-blur-sm rounded-2xl flex items-center justify-center mr-4 shadow-lg">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-4xl font-bold text-white mb-2">Configuración de Categorías</h1>
                            <p class="text-xl text-blue-100 font-medium">Sistema avanzado de gestión de categorías deportivas</p>
                        </div>
                    </div>
                </div>

            @if($hasCategories)
                <div class="flex items-center space-x-2">
                    @if($validation && $validation['valid'])
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            ✓ Configuración Válida
                        </span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                            ✗ Con Errores
                        </span>
                    @endif

                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        {{ $categories->count() }} Categorías
                    </span>
                </div>
            @endif
        </div>

                @if($hasCategories)
                    <div class="flex items-center space-x-4 mt-6">
                        @if($validation && $validation['valid'])
                            <div class="flex items-center px-4 py-2 bg-green-500 bg-opacity-20 backdrop-blur-sm rounded-full">
                                <svg class="w-5 h-5 text-green-200 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4"></path>
                                </svg>
                                <span class="text-green-100 font-semibold">Configuración Válida</span>
                            </div>
                        @else
                            <div class="flex items-center px-4 py-2 bg-red-500 bg-opacity-20 backdrop-blur-sm rounded-full">
                                <svg class="w-5 h-5 text-red-200 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                <span class="text-red-100 font-semibold">Con Errores</span>
                            </div>
                        @endif
                        <div class="flex items-center px-4 py-2 bg-white bg-opacity-20 backdrop-blur-sm rounded-full">
                            <span class="text-white font-bold text-lg">{{ $categories->count() }}</span>
                            <span class="text-blue-100 ml-2">Categorías</span>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Estadísticas Dashboard --}}
    @if($hasCategories)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100 hover:shadow-xl transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 mb-1">Total Categorías</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $categories->count() }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100 hover:shadow-xl transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 mb-1">Rango de Edad</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $categories->min('min_age') }}-{{ $categories->max('max_age') }}</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100 hover:shadow-xl transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 mb-1">Categorías Mixtas</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $categories->where('gender', 'mixed')->count() }}</p>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100 hover:shadow-xl transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 mb-1">Jugadoras Asignadas</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $league ? array_sum($league->getCategoryStats()) : 0 }}</p>
                    </div>
                    <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Acciones principales --}}
    @if($league && !$hasCategories)
        <div class="relative overflow-hidden bg-gradient-to-br from-emerald-400 via-teal-500 to-blue-600 rounded-3xl shadow-2xl mb-8">
            <div class="absolute inset-0 bg-white opacity-10"></div>
            <div class="absolute inset-0">
                <svg class="absolute top-0 right-0 mt-8 mr-8" viewBox="0 0 200 200" fill="none" style="transform: scale(2); opacity: 0.1;">
                    <circle cx="100" cy="100" r="80" stroke="white" stroke-width="2" fill="none"/>
                    <circle cx="100" cy="100" r="40" stroke="white" stroke-width="2" fill="none"/>
                </svg>
            </div>
            <div class="relative px-12 py-16 text-center">
                <div class="max-w-2xl mx-auto">
                    <div class="w-24 h-24 bg-white bg-opacity-20 backdrop-blur-sm rounded-3xl flex items-center justify-center mx-auto mb-8 shadow-2xl">
                        <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                    </div>
                    <h2 class="text-4xl font-bold text-white mb-4">¡Comienza tu configuración!</h2>
                    <p class="text-xl text-white opacity-90 mb-8 leading-relaxed">Crea automáticamente las categorías estándar de voleibol con un solo clic, o configura categorías personalizadas según las necesidades específicas de tu liga</p>
                    <div class="space-y-4">
                        <button
                            type="button"
                            onclick="createDefaultCategories({{ $league->id }})"
                            class="inline-flex items-center px-12 py-6 border-2 border-white text-xl font-bold rounded-2xl shadow-2xl text-white bg-white bg-opacity-20 backdrop-blur-sm hover:bg-opacity-30 transform hover:scale-105 transition-all duration-300 hover:shadow-3xl"
                        >
                            <svg class="w-8 h-8 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                            Crear Categorías por Defecto
                        </button>
                        <p class="text-white opacity-75 text-sm">Se crearán automáticamente las categorías estándar de voleibol</p>
                    </div>
                </div>
            </div>
        </div>
    @elseif($league && $hasCategories)
        <div class="bg-white rounded-3xl shadow-xl border border-gray-100 p-8 mb-8">
            <div class="text-center mb-8">
                <h2 class="text-3xl font-bold text-gray-900 mb-3">Panel de Control de Categorías</h2>
                <p class="text-lg text-gray-600">Gestiona, valida y exporta la configuración de tus categorías</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="group">
                    <button
                        type="button"
                        onclick="openCategoryModal()"
                        class="w-full bg-gradient-to-br from-blue-500 to-blue-700 hover:from-blue-600 hover:to-blue-800 text-white rounded-2xl p-8 shadow-lg hover:shadow-2xl transform hover:scale-105 transition-all duration-300 group-hover:shadow-blue-500/25"
                    >
                        <div class="w-16 h-16 bg-white bg-opacity-20 rounded-2xl flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold mb-2">Nueva Categoría</h3>
                        <p class="text-blue-100 text-sm">Agregar una nueva categoría personalizada</p>
                    </button>
                </div>

                <div class="group">
                    <button
                        type="button"
                        onclick="validateConfiguration({{ $league->id }})"
                        class="w-full bg-gradient-to-br from-green-500 to-emerald-700 hover:from-green-600 hover:to-emerald-800 text-white rounded-2xl p-8 shadow-lg hover:shadow-2xl transform hover:scale-105 transition-all duration-300 group-hover:shadow-green-500/25"
                    >
                        <div class="w-16 h-16 bg-white bg-opacity-20 rounded-2xl flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold mb-2">Validar Configuración</h3>
                        <p class="text-green-100 text-sm">Verificar la integridad de las categorías</p>
                    </button>
                </div>

                <div class="group">
                    <button
                        type="button"
                        onclick="exportConfiguration({{ $league->id }})"
                        class="w-full bg-gradient-to-br from-purple-500 to-indigo-700 hover:from-purple-600 hover:to-indigo-800 text-white rounded-2xl p-8 shadow-lg hover:shadow-2xl transform hover:scale-105 transition-all duration-300 group-hover:shadow-purple-500/25"
                    >
                        <div class="w-16 h-16 bg-white bg-opacity-20 rounded-2xl flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold mb-2">Exportar</h3>
                        <p class="text-purple-100 text-sm">Descargar configuración completa</p>
                    </button>
                </div>
            </div>
        </div>
            @else
                <div class="text-sm text-gray-500">
                    No se pudo cargar la información de la liga.
                </div>
            @endif
    {{-- Lista de categorías --}}
    @if($hasCategories)
        <div class="bg-white rounded-3xl shadow-2xl border border-gray-100 overflow-hidden">
            <div class="px-8 py-8 bg-gradient-to-r from-slate-800 via-gray-900 to-slate-800">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-white bg-opacity-20 backdrop-blur-sm rounded-2xl flex items-center justify-center mr-4">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-2xl font-bold text-white">Categorías Configuradas</h4>
                            <p class="text-gray-300">Gestión completa de categorías activas</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-3xl font-bold text-white">{{ $categories->count() }}</div>
                        <div class="text-gray-300 text-sm">Total</div>
                    </div>
                </div>
            </div>

            <div class="p-8">
                <div class="grid gap-6">
                    @foreach($categories as $category)
                        @php
                            $stats = $category->getPlayerStats();
                        @endphp
                        <div class="bg-gradient-to-r from-gray-50 to-white rounded-2xl border border-gray-200 hover:border-gray-300 hover:shadow-lg transition-all duration-300 p-6">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-6">
                                    <div class="w-16 h-16 rounded-2xl flex items-center justify-center shadow-lg" style="background: linear-gradient(135deg, {{ $category->color }}, {{ $category->color }}dd);">
                                        <span class="text-white font-bold text-lg">{{ substr($category->name, 0, 2) }}</span>
                                    </div>
                                    <div class="flex-1">
                                        <h3 class="text-xl font-bold text-gray-900 mb-1">{{ $category->name }}</h3>
                                        @if($category->code)
                                            <p class="text-gray-600 mb-3">Código: {{ $category->code }}</p>
                                        @endif
                                        <div class="flex flex-wrap gap-3">
                                            <span class="inline-flex items-center px-4 py-2 rounded-xl text-sm font-semibold bg-blue-100 text-blue-800">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                                {{ $category->min_age }}-{{ $category->max_age }} años
                                            </span>
                                            <span class="inline-flex items-center px-4 py-2 rounded-xl text-sm font-semibold 
                                                {{ $category->gender === 'mixed' ? 'bg-purple-100 text-purple-800' :
                                                   ($category->gender === 'male' ? 'bg-blue-100 text-blue-800' : 'bg-pink-100 text-pink-800') }}">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                </svg>
                                                {{ $category->gender_label }}
                                            </span>
                                            <span class="inline-flex items-center px-4 py-2 rounded-xl text-sm font-semibold bg-gray-100 text-gray-800">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                                </svg>
                                                {{ $stats['total'] }} jugadoras
                                                @if($stats['total'] > 0)
                                                    <span class="ml-1 text-xs opacity-75">(♂{{ $stats['male'] }} ♀{{ $stats['female'] }})</span>
                                                @endif
                                            </span>
                                            <span class="inline-flex items-center px-4 py-2 rounded-xl text-sm font-semibold 
                                                {{ $category->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                <div class="w-2 h-2 rounded-full mr-2 {{ $category->is_active ? 'bg-green-500' : 'bg-red-500' }}"></div>
                                                {{ $category->is_active ? 'Activa' : 'Inactiva' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex space-x-3">
                                    <button
                                        type="button"
                                        onclick="editCategory({{ $category->id }})"
                                        class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200"
                                        title="Editar categoría"
                                    >
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                        Editar
                                    </button>
                                    <button
                                        type="button"
                                        onclick="toggleCategoryStatus({{ $category->id }})"
                                        class="inline-flex items-center px-6 py-3 font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200
                                            {{ $category->is_active ? 'bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white' : 'bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white' }}"
                                        title="{{ $category->is_active ? 'Desactivar' : 'Activar' }} categoría"
                                    >
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            @if($category->is_active)
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L5.636 5.636"></path>
                                            @else
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            @endif
                                        </svg>
                                        {{ $category->is_active ? 'Desactivar' : 'Activar' }}
                                    </button>
                                    <button
                                        type="button"
                                        onclick="deleteCategory({{ $category->id }})"
                                        class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-gray-500 to-gray-600 hover:from-gray-600 hover:to-gray-700 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200"
                                        title="Eliminar categoría"
                                    >
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                        Eliminar
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    {{-- Alertas de validación --}}
    @if($hasCategories && $validation && (!$validation['valid'] || !empty($validation['warnings'])))
        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <h4 class="text-lg font-medium text-gray-900 mb-4">Validación de Configuración</h4>

            @if(!empty($validation['errors']))
                <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-md">
                    <h5 class="text-sm font-medium text-red-800">❌ Errores que deben corregirse:</h5>
                    <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                        @foreach($validation['errors'] as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if(!empty($validation['warnings']))
                <div class="mb-4 p-4 bg-yellow-50 border border-yellow-200 rounded-md">
                    <h5 class="text-sm font-medium text-yellow-800">⚠️ Advertencias:</h5>
                    <ul class="mt-2 text-sm text-yellow-700 list-disc list-inside">
                        @foreach($validation['warnings'] as $warning)
                            <li>{{ $warning }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if(!empty($validation['suggestions']))
                <div class="p-4 bg-blue-50 border border-blue-200 rounded-md">
                    <h5 class="text-sm font-medium text-blue-800">💡 Sugerencias de mejora:</h5>
                    <ul class="mt-2 text-sm text-blue-700 list-disc list-inside">
                        @foreach($validation['suggestions'] as $suggestion)
                            <li>{{ $suggestion }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    @endif
    {{-- Modal para crear/editar categoría --}}
    <div id="categoryModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 id="modalTitle" class="text-lg font-medium text-gray-900">Nueva Categoría</h3>
                <button type="button" onclick="closeCategoryModal()" class="text-gray-400 hover:text-gray-600">
                    ✖️
                </button>
            </div>

            <form id="categoryForm">
                <input type="hidden" id="categoryId" name="categoryId">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nombre *</label>
                        <input type="text" id="name" name="name" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="Ej: Mini, Infantil, Juvenil">
                    </div>

                    <div>
                        <label for="code" class="block text-sm font-medium text-gray-700 mb-1">Código</label>
                        <input type="text" id="code" name="code"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="Ej: MINI, INF, JUV">
                    </div>

                    <div class="md:col-span-2">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                        <textarea id="description" name="description" rows="2"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                  placeholder="Descripción de la categoría"></textarea>
                    </div>

                    <div>
                        <label for="gender" class="block text-sm font-medium text-gray-700 mb-1">Género *</label>
                        <select id="gender" name="gender" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="mixed">Mixto</option>
                            <option value="male">Masculino</option>
                            <option value="female">Femenino</option>
                        </select>
                    </div>

                    <div>
                        <label for="color" class="block text-sm font-medium text-gray-700 mb-1">Color</label>
                        <input type="color" id="color" name="color" value="#3b82f6"
                               class="w-full h-10 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="min_age" class="block text-sm font-medium text-gray-700 mb-1">Edad Mínima *</label>
                        <input type="number" id="min_age" name="min_age" required min="5" max="100"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="max_age" class="block text-sm font-medium text-gray-700 mb-1">Edad Máxima *</label>
                        <input type="number" id="max_age" name="max_age" required min="5" max="100"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="sort_order" class="block text-sm font-medium text-gray-700 mb-1">Orden</label>
                        <input type="number" id="sort_order" name="sort_order" min="0"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="Orden de visualización">
                    </div>

                    <div>
                        <label for="icon" class="block text-sm font-medium text-gray-700 mb-1">Icono</label>
                        <input type="text" id="icon" name="icon"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="heroicon-o-star">
                    </div>

                    <div class="md:col-span-2">
                        <label class="flex items-center">
                            <input type="checkbox" id="is_active" name="is_active" checked
                                   class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            <span class="ml-2 text-sm text-gray-700">Categoría activa</span>
                        </label>
                    </div>
                </div>

                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" onclick="closeCategoryModal()"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200">
                        Cancelar
                    </button>
                    <button type="submit"
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700">
                        <span id="saveButtonText">Guardar</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    </div>
    {{-- JavaScript para funcionalidad --}}
    <script>
function createDefaultCategories(leagueId) {
    if (confirm('¿Estás seguro de que quieres crear las categorías por defecto? Esta acción no se puede deshacer.')) {
        fetch(`/admin/leagues/${leagueId}/categories/create-default`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('✅ ' + data.message);
                location.reload();
            } else {
                alert('❌ Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('❌ Error creando categorías por defecto');
        });
    }
}

function validateConfiguration(leagueId) {
    fetch(`/admin/leagues/${leagueId}/categories/validate`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.valid) {
            alert('✅ Configuración válida: ' + data.message);
        } else {
            alert('❌ Configuración con errores:\n' + data.errors.join('\n'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('❌ Error validando configuración');
    });
}

function exportConfiguration(leagueId) {
    window.open(`/admin/leagues/${leagueId}/categories/export`, '_blank');
}

function openCategoryModal(categoryId = null) {
    const modal = document.getElementById('categoryModal');
    const form = document.getElementById('categoryForm');
    const modalTitle = document.getElementById('modalTitle');
    const saveButtonText = document.getElementById('saveButtonText');

    // Resetear formulario
    form.reset();
    document.getElementById('categoryId').value = '';

    if (categoryId) {
        // Modo edición
        modalTitle.textContent = 'Editar Categoría';
        saveButtonText.textContent = 'Actualizar';
        loadCategoryData(categoryId);
    } else {
        // Modo creación
        modalTitle.textContent = 'Nueva Categoría';
        saveButtonText.textContent = 'Crear';
        // Establecer valores por defecto
        document.getElementById('gender').value = 'mixed';
        document.getElementById('is_active').checked = true;
        document.getElementById('color').value = '#3b82f6';
    }

    modal.classList.remove('hidden');
}

function closeCategoryModal() {
    const modal = document.getElementById('categoryModal');
    modal.classList.add('hidden');
}

function loadCategoryData(categoryId) {
    fetch(`/admin/categories/${categoryId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const category = data.category;
                document.getElementById('categoryId').value = category.id;
                document.getElementById('name').value = category.name || '';
                document.getElementById('code').value = category.code || '';
                document.getElementById('description').value = category.description || '';
                document.getElementById('gender').value = category.gender || 'mixed';
                document.getElementById('min_age').value = category.min_age || '';
                document.getElementById('max_age').value = category.max_age || '';
                document.getElementById('color').value = category.color || '#3b82f6';
                document.getElementById('icon').value = category.icon || '';
                document.getElementById('sort_order').value = category.sort_order || '';
                document.getElementById('is_active').checked = category.is_active || false;
            } else {
                alert('❌ Error cargando datos de la categoría');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('❌ Error cargando datos de la categoría');
        });
}

function editCategory(categoryId) {
    openCategoryModal(categoryId);
}

function toggleCategoryStatus(categoryId) {
    if (confirm('¿Estás seguro de que quieres cambiar el estado de esta categoría?')) {
        fetch(`/admin/categories/${categoryId}/toggle-status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('✅ ' + data.message);
                location.reload();
            } else {
                alert('❌ Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('❌ Error cambiando estado de categoría');
        });
    }
}

function deleteCategory(categoryId) {
    if (confirm('¿Estás seguro de que quieres eliminar esta categoría? Esta acción no se puede deshacer.')) {
        fetch(`/admin/categories/${categoryId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('✅ ' + data.message);
                location.reload();
            } else {
                alert('❌ Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('❌ Error eliminando categoría');
        });
    }
}

// Manejar envío del formulario
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('categoryForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(form);
            const categoryId = formData.get('categoryId');
            const leagueId = {{ $league ? $league->id : 'null' }};

            // Validaciones básicas
            const minAge = parseInt(formData.get('min_age'));
            const maxAge = parseInt(formData.get('max_age'));

            if (minAge > maxAge) {
                alert('❌ La edad mínima no puede ser mayor que la edad máxima');
                return;
            }

            // Convertir FormData a objeto
            const data = {};
            for (let [key, value] of formData.entries()) {
                if (key === 'is_active') {
                    data[key] = document.getElementById('is_active').checked;
                } else if (key !== 'categoryId' && value !== '') {
                    data[key] = value;
                }
            }

            const url = categoryId ?
                `/admin/categories/${categoryId}` :
                `/admin/leagues/${leagueId}/categories`;

            const method = categoryId ? 'PUT' : 'POST';

            fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('✅ ' + data.message);
                    closeCategoryModal();
                    location.reload();
                } else {
                    if (data.errors) {
                        let errorMessage = '❌ Errores de validación:\n';
                        for (let field in data.errors) {
                            errorMessage += `- ${data.errors[field].join(', ')}\n`;
                        }
                        alert(errorMessage);
                    } else {
                        alert('❌ Error: ' + data.message);
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('❌ Error guardando categoría');
            });
        });
    }
});
</script>
    </div>
</div>
