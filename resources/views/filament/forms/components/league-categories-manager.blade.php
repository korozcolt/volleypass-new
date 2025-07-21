@php
    use App\Services\LeagueConfigurationService;
    use App\Services\CategoryValidationService;

    $league = $this->data['league'] ?? null;
    $configService = app(LeagueConfigurationService::class);

    $hasCategories = $league && $league->hasCustomCategories();
    $categories = $hasCategories ? $league->getActiveCategories() : collect();
    $validation = $hasCategories ? $configService->validateCategoryConfiguration($league) : null;
@endphp

<div class="space-y-6">
    {{-- Header con estadísticas --}}
    <div class="bg-white rounded-lg border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-lg font-medium text-gray-900">Gestión de Categorías</h3>
                <p class="text-sm text-gray-500">Configura las categorías de edad específicas para esta liga</p>
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

        {{-- Estadísticas rápidas --}}
        @if($hasCategories)
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="text-2xl font-bold text-gray-900">{{ $categories->count() }}</div>
                    <div class="text-sm text-gray-500">Total Categorías</div>
                </div>
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="text-2xl font-bold text-gray-900">
                        {{ $categories->min('min_age') }}-{{ $categories->max('max_age') }}
                    </div>
                    <div class="text-sm text-gray-500">Rango de Edad</div>
                </div>
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="text-2xl font-bold text-gray-900">
                        {{ $categories->where('gender', 'mixed')->count() }}
                    </div>
                    <div class="text-sm text-gray-500">Categorías Mixtas</div>
                </div>
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="text-2xl font-bold text-gray-900">
                        {{ array_sum($league->getCategoryStats()) }}
                    </div>
                    <div class="text-sm text-gray-500">Jugadoras Asignadas</div>
                </div>
            </div>
        @endif
    </div>

    {{-- Acciones principales --}}
    <div class="bg-white rounded-lg border border-gray-200 p-6">
        <div class="flex flex-wrap gap-3">
            @if(!$hasCategories)
                <button
                    type="button"
                    onclick="createDefaultCategories({{ $league->id }})"
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700"
                >
                    ➕ Crear Categorías por Defecto
                </button>
            @else
                <button
                    type="button"
                    onclick="openCategoryModal()"
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700"
                >
                    ➕ Nueva Categoría
                </button>

                <button
                    type="button"
                    onclick="validateConfiguration({{ $league->id }})"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                >
                    ✓ Validar Configuración
                </button>

                <button
                    type="button"
                    onclick="exportConfiguration({{ $league->id }})"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                >
                    📥 Exportar
                </button>
            @endif
        </div>
    </div>
    {{-- Lista de categorías --}}
    @if($hasCategories)
        <div class="bg-white rounded-lg border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h4 class="text-lg font-medium text-gray-900">Categorías Configuradas</h4>
            </div>

            <div class="overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Categoría</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rango de Edad</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Género</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jugadoras</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($categories as $category)
                            @php
                                $stats = $category->getPlayerStats();
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-3 h-3 rounded-full mr-3" style="background-color: {{ $category->color }}"></div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $category->name }}</div>
                                            @if($category->code)
                                                <div class="text-sm text-gray-500">{{ $category->code }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $category->min_age }}-{{ $category->max_age }} años
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ $category->gender === 'mixed' ? 'bg-purple-100 text-purple-800' :
                                           ($category->gender === 'male' ? 'bg-blue-100 text-blue-800' : 'bg-pink-100 text-pink-800') }}">
                                        {{ $category->gender_label }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <div class="flex items-center">
                                        <span class="font-medium">{{ $stats['total'] }}</span>
                                        @if($stats['total'] > 0)
                                            <span class="ml-2 text-xs text-gray-500">
                                                (♂{{ $stats['male'] }} ♀{{ $stats['female'] }})
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($category->is_active)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Activa
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            Inactiva
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end space-x-2">
                                        <button
                                            type="button"
                                            onclick="editCategory({{ $category->id }})"
                                            class="text-blue-600 hover:text-blue-900"
                                            title="Editar"
                                        >
                                            ✏️
                                        </button>

                                        <button
                                            type="button"
                                            onclick="toggleCategoryStatus({{ $category->id }})"
                                            class="{{ $category->is_active ? 'text-red-600 hover:text-red-900' : 'text-green-600 hover:text-green-900' }}"
                                            title="{{ $category->is_active ? 'Desactivar' : 'Activar' }}"
                                        >
                                            {{ $category->is_active ? '❌' : '✅' }}
                                        </button>

                                        <button
                                            type="button"
                                            onclick="deleteCategory({{ $category->id }})"
                                            class="text-red-600 hover:text-red-900"
                                            title="Eliminar"
                                        >
                                            🗑️
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
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
</div>
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
