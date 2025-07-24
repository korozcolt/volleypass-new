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

{{-- SINGLE ROOT ELEMENT WRAPPER --}}
<div class="league-categories-manager-wrapper" x-data="{}">
    <div class="league-categories-manager">
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
                            <div class="flex items-center space-x-4">
                                @if($validation && $validation['valid'])
                                    <div class="bg-green-500 bg-opacity-20 backdrop-blur-sm text-green-100 px-6 py-3 rounded-xl border border-green-300 border-opacity-30 shadow-lg">
                                        <div class="flex items-center">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            <span class="font-semibold">Configuración Válida</span>
                                        </div>
                                    </div>
                                @else
                                    <div class="bg-yellow-500 bg-opacity-20 backdrop-blur-sm text-yellow-100 px-6 py-3 rounded-xl border border-yellow-300 border-opacity-30 shadow-lg">
                                        <div class="flex items-center">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                            </svg>
                                            <span class="font-semibold">Necesita Validación</span>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Main Content --}}
            @if($hasCategories)
                {{-- Statistics Cards --}}
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                    <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-600 text-sm font-medium">Total Categorías</p>
                                <p class="text-3xl font-bold text-gray-900">{{ $categories->count() }}</p>
                            </div>
                            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-600 text-sm font-medium">Categorías Activas</p>
                                <p class="text-3xl font-bold text-green-600">{{ $categories->where('is_active', true)->count() }}</p>
                            </div>
                            <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-600 text-sm font-medium">Rango de Edad</p>
                                <p class="text-3xl font-bold text-purple-600">{{ $categories->min('min_age') }}-{{ $categories->max('max_age') }}</p>
                            </div>
                            <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-600 text-sm font-medium">Estado</p>
                                <p class="text-3xl font-bold {{ $validation && $validation['valid'] ? 'text-green-600' : 'text-yellow-600' }}">
                                    {{ $validation && $validation['valid'] ? 'Válido' : 'Revisar' }}
                                </p>
                            </div>
                            <div class="w-12 h-12 {{ $validation && $validation['valid'] ? 'bg-green-100' : 'bg-yellow-100' }} rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6 {{ $validation && $validation['valid'] ? 'text-green-600' : 'text-yellow-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    @if($validation && $validation['valid'])
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    @else
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                    @endif
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Quick Actions --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
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
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold mb-2">Validar Configuración</h3>
                            <p class="text-green-100 text-sm">Verificar integridad de las categorías</p>
                        </button>
                    </div>

                    <div class="group">
                        <button
                            type="button"
                            onclick="exportConfiguration({{ $league->id }})"
                            class="w-full bg-gradient-to-br from-purple-500 to-pink-700 hover:from-purple-600 hover:to-pink-800 text-white rounded-2xl p-8 shadow-lg hover:shadow-2xl transform hover:scale-105 transition-all duration-300 group-hover:shadow-purple-500/25"
                        >
                            <div class="w-16 h-16 bg-white bg-opacity-20 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold mb-2">Exportar</h3>
                            <p class="text-purple-100 text-sm">Descargar configuración actual</p>
                        </button>
                    </div>
                </div>

                {{-- Categories List --}}
                <div class="bg-white rounded-3xl shadow-2xl overflow-hidden border border-gray-100">
                    <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-8 py-6 border-b border-gray-200">
                        <h2 class="text-2xl font-bold text-gray-900">Categorías Configuradas</h2>
                        <p class="text-gray-600 mt-1">Gestiona las categorías de edad específicas de tu liga</p>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Categoría</th>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rango de Edad</th>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Género</th>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jugadoras</th>
                                    <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($categories as $category)
                                    <tr class="hover:bg-gray-50 transition-colors duration-200">
                                        <td class="px-6 py-6">
                                            <div class="flex items-center">
                                                <div class="w-3 h-3 rounded-full {{ $category->is_active ? 'bg-green-500' : 'bg-gray-400' }} mr-3"></div>
                                                <div>
                                                    <div class="text-lg font-semibold text-gray-900">{{ $category->name }}</div>
                                                    @if($category->description)
                                                        <div class="text-sm text-gray-500">{{ $category->description }}</div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-6">
                                            <div class="flex items-center">
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                                    {{ $category->min_age }} - {{ $category->max_age }} años
                                                </span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-6">
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                                                {{ $category->gender === 'mixed' ? 'bg-purple-100 text-purple-800' : 
                                                   ($category->gender === 'female' ? 'bg-pink-100 text-pink-800' : 'bg-blue-100 text-blue-800') }}">
                                                {{ ucfirst($category->gender) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-6">
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                                                {{ $category->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                                {{ $category->is_active ? 'Activa' : 'Inactiva' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-6">
                                            <div class="text-lg font-semibold text-gray-900">
                                                {{ $category->players_count ?? 0 }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-6 text-right">
                                            <div class="flex items-center justify-end space-x-2">
                                                <button
                                                    type="button"
                                                    onclick="editCategory({{ $category->id }})"
                                                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 focus:outline-none focus:border-blue-700 focus:ring focus:ring-blue-200 active:bg-blue-600 disabled:opacity-25 transition"
                                                >
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                    </svg>
                                                    Editar
                                                </button>
                                                <button
                                                    type="button"
                                                    onclick="deleteCategory({{ $category->id }})"
                                                    class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 focus:outline-none focus:border-red-700 focus:ring focus:ring-red-200 active:bg-red-600 disabled:opacity-25 transition"
                                                >
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                    Eliminar
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-12 text-center">
                                            <div class="flex flex-col items-center">
                                                <svg class="w-16 h-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                                </svg>
                                                <p class="text-xl font-semibold text-gray-900 mb-2">No hay categorías configuradas</p>
                                                <p class="text-gray-500">Comienza agregando tu primera categoría personalizada</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            @else
                {{-- Empty State --}}
                <div class="bg-white rounded-3xl shadow-2xl p-16 text-center border border-gray-100">
                    <div class="max-w-md mx-auto">
                        <div class="w-24 h-24 bg-gradient-to-br from-blue-500 to-purple-600 rounded-3xl flex items-center justify-center mx-auto mb-8 shadow-2xl">
                            <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                        </div>
                        <h2 class="text-4xl font-bold text-gray-900 mb-4">Configuración Inicial</h2>
                        <p class="text-xl text-gray-600 mb-8">Esta liga aún no tiene categorías personalizadas configuradas. Puedes usar las categorías por defecto o crear tu propia configuración.</p>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <button
                                type="button"
                                onclick="createDefaultCategories({{ $league ? $league->id : 'null' }})"
                                class="bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-bold py-4 px-8 rounded-2xl shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-300"
                            >
                                <svg class="w-6 h-6 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                                Usar Configuración Por Defecto
                            </button>
                            
                            <button
                                type="button"
                                onclick="openCategoryModal()"
                                class="bg-gradient-to-r from-green-600 to-teal-600 hover:from-green-700 hover:to-teal-700 text-white font-bold py-4 px-8 rounded-2xl shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-300"
                            >
                                <svg class="w-6 h-6 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Crear Configuración Personalizada
                            </button>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Category Modal --}}
            <div id="categoryModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
                <div class="bg-white rounded-3xl shadow-2xl max-w-2xl w-full max-h-screen overflow-y-auto">
                    <div class="p-8">
                        <div class="flex items-center justify-between mb-6">
                            <h2 id="modalTitle" class="text-3xl font-bold text-gray-900">Nueva Categoría</h2>
                            <button
                                type="button"
                                onclick="closeCategoryModal()"
                                class="text-gray-400 hover:text-gray-600 transition-colors"
                            >
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>

                        <form id="categoryForm" class="space-y-6">
                            <input type="hidden" id="categoryId" name="categoryId">
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nombre de la Categoría</label>
                                    <input
                                        type="text"
                                        id="name"
                                        name="name"
                                        required
                                        class="w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        placeholder="Ej: Juvenil"
                                    >
                                </div>

                                <div>
                                    <label for="code" class="block text-sm font-medium text-gray-700 mb-2">Código</label>
                                    <input
                                        type="text"
                                        id="code"
                                        name="code"
                                        required
                                        class="w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        placeholder="Ej: JUV"
                                    >
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div>
                                    <label for="min_age" class="block text-sm font-medium text-gray-700 mb-2">Edad Mínima</label>
                                    <input
                                        type="number"
                                        id="min_age"
                                        name="min_age"
                                        required
                                        min="5"
                                        max="100"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    >
                                </div>

                                <div>
                                    <label for="max_age" class="block text-sm font-medium text-gray-700 mb-2">Edad Máxima</label>
                                    <input
                                        type="number"
                                        id="max_age"
                                        name="max_age"
                                        required
                                        min="5"
                                        max="100"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    >
                                </div>

                                <div>
                                    <label for="gender" class="block text-sm font-medium text-gray-700 mb-2">Género</label>
                                    <select
                                        id="gender"
                                        name="gender"
                                        required
                                        class="w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    >
                                        <option value="">Seleccionar...</option>
                                        <option value="mixed">Mixto</option>
                                        <option value="female">Femenino</option>
                                        <option value="male">Masculino</option>
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Descripción (Opcional)</label>
                                <textarea
                                    id="description"
                                    name="description"
                                    rows="3"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    placeholder="Descripción de la categoría..."
                                ></textarea>
                            </div>

                            <div class="flex items-center">
                                <input
                                    type="checkbox"
                                    id="is_active"
                                    name="is_active"
                                    checked
                                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                >
                                <label for="is_active" class="ml-2 block text-sm text-gray-900">
                                    Categoría activa
                                </label>
                            </div>

                            <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                                <button
                                    type="button"
                                    onclick="closeCategoryModal()"
                                    class="px-6 py-3 border border-gray-300 rounded-xl text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors"
                                >
                                    Cancelar
                                </button>
                                <button
                                    type="submit"
                                    class="px-6 py-3 border border-transparent rounded-xl shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors"
                                >
                                    <span id="submitButtonText">Guardar Categoría</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    // Variables globales
    let isEditMode = false;
    let currentCategoryId = null;

    // Abrir modal para nueva categoría
    function openCategoryModal() {
        document.getElementById('categoryModal').classList.remove('hidden');
        document.getElementById('modalTitle').textContent = 'Nueva Categoría';
        document.getElementById('submitButtonText').textContent = 'Guardar Categoría';
        document.getElementById('categoryForm').reset();
        document.getElementById('categoryId').value = '';
        document.getElementById('is_active').checked = true;
        isEditMode = false;
        currentCategoryId = null;
    }

    // Cerrar modal
    function closeCategoryModal() {
        document.getElementById('categoryModal').classList.add('hidden');
        document.getElementById('categoryForm').reset();
        isEditMode = false;
        currentCategoryId = null;
    }

    // Editar categoría existente
    function editCategory(categoryId) {
        // Aquí cargarías los datos de la categoría desde el servidor
        // Por ahora, abrimos el modal en modo edición
        document.getElementById('categoryModal').classList.remove('hidden');
        document.getElementById('modalTitle').textContent = 'Editar Categoría';
        document.getElementById('submitButtonText').textContent = 'Actualizar Categoría';
        document.getElementById('categoryId').value = categoryId;
        isEditMode = true;
        currentCategoryId = categoryId;
        
        // Aquí harías una petición AJAX para cargar los datos de la categoría
        loadCategoryData(categoryId);
    }

    // Cargar datos de categoría (placeholder)
    function loadCategoryData(categoryId) {
        // Implementar llamada AJAX para cargar datos de la categoría
        console.log('Loading category data for ID:', categoryId);
    }

    // Eliminar categoría
    function deleteCategory(categoryId) {
        if (confirm('¿Estás seguro de que quieres eliminar esta categoría? Esta acción no se puede deshacer.')) {
            // Implementar llamada AJAX para eliminar
            console.log('Deleting category:', categoryId);
            alert('Función de eliminación pendiente de implementar');
        }
    }

    // Validar configuración
    function validateConfiguration(leagueId) {
        // Implementar validación
        console.log('Validating configuration for league:', leagueId);
        alert('Función de validación pendiente de implementar');
    }

    // Exportar configuración
    function exportConfiguration(leagueId) {
        // Implementar exportación
        console.log('Exporting configuration for league:', leagueId);
        alert('Función de exportación pendiente de implementar');
    }

    // Crear categorías por defecto
    function createDefaultCategories(leagueId) {
        if (leagueId && confirm('¿Crear categorías por defecto para esta liga?')) {
            // Implementar creación de categorías por defecto
            console.log('Creating default categories for league:', leagueId);
            alert('Función de creación por defecto pendiente de implementar');
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

                // Implementar envío de datos
                console.log('Form data:', Object.fromEntries(formData));
                alert('Función de guardado pendiente de implementar');
                closeCategoryModal();
            });
        }
    });

    // Cerrar modal al hacer clic fuera
    document.addEventListener('click', function(e) {
        const modal = document.getElementById('categoryModal');
        if (e.target === modal) {
            closeCategoryModal();
        }
    });

    // Cerrar modal con tecla ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeCategoryModal();
        }
    });
    </script>
</div>
{{-- END SINGLE ROOT ELEMENT WRAPPER --}}