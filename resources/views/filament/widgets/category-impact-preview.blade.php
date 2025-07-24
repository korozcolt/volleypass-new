{{-- resources/views/filament/widgets/category-impact-preview.blade.php --}}

<x-filament-widgets::widget>
    <x-filament::section>
        {{-- Header simple y limpio --}}
        <x-slot name="heading">
            <div class="flex items-center gap-x-3">
                <x-filament::icon
                    icon="heroicon-m-eye"
                    class="h-5 w-5"
                />
                Preview del Impacto en Jugadoras
            </div>
        </x-slot>

        <x-slot name="description">
            @if(!$hasData)
                Guarda la liga primero para ver el análisis de impacto
            @elseif(!$hasCustomCategories)
                Configure categorías personalizadas para ver el impacto detallado
            @else
                Análisis del impacto de las categorías personalizadas en las jugadoras existentes
            @endif
        </x-slot>

        {{-- Contenido principal limpio --}}
        <div class="space-y-6">

            {{-- Estado: Liga no guardada --}}
            @if(!$hasData)
                <div class="flex flex-col items-center justify-center py-12 text-center">
                    <x-filament::icon
                        icon="heroicon-o-document-plus"
                        class="h-12 w-12 text-gray-400 dark:text-gray-500"
                    />
                    <h3 class="mt-4 text-sm font-medium text-gray-900 dark:text-gray-100">
                        Liga no guardada
                    </h3>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                        Guarda la liga para ver el análisis de impacto
                    </p>
                </div>
            @endif

            {{-- Estado: Sin categorías personalizadas --}}
            @if($hasData && !$hasCustomCategories)
                <x-filament::section>
                    <div class="flex items-start gap-x-3">
                        <x-filament::icon
                            icon="heroicon-m-information-circle"
                            class="mt-0.5 h-5 w-5 text-blue-500"
                        />
                        <div>
                            <h3 class="text-base font-medium text-gray-900 dark:text-gray-100">
                                Sistema Tradicional Activo
                            </h3>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                Configure categorías personalizadas en la sección superior para ver el impacto detallado en las jugadoras existentes.
                            </p>
                        </div>
                    </div>
                </x-filament::section>
            @endif

            {{-- Contenido: Con categorías personalizadas --}}
            @if($hasData && $hasCustomCategories)

                {{-- Alerta crítica --}}
                @if($needsAttention)
                    <x-filament::section
                        :compact="true"
                        class="border-l-4 border-l-danger-600 bg-danger-50 dark:bg-danger-950/50"
                    >
                        <div class="flex items-start gap-x-3">
                            <x-filament::icon
                                icon="heroicon-m-exclamation-triangle"
                                class="mt-0.5 h-5 w-5 text-danger-600"
                            />
                            <div>
                                <h3 class="text-sm font-medium text-danger-800 dark:text-danger-200">
                                    Atención Requerida
                                </h3>
                                <p class="mt-1 text-sm text-danger-700 dark:text-danger-300">
                                    {{ $criticalCount }} jugadoras no tienen categoría asignada y requieren configuración manual.
                                </p>
                            </div>
                        </div>
                    </x-filament::section>
                @endif

                {{-- Métricas en grid limpio --}}
                <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
                    {{-- Sin cambios --}}
                    <x-filament::section :compact="true">
                        <div class="flex items-center gap-x-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-success-100 dark:bg-success-950">
                                <x-filament::icon
                                    icon="heroicon-m-check-circle"
                                    class="h-5 w-5 text-success-600 dark:text-success-400"
                                />
                            </div>
                            <div>
                                <p class="text-2xl font-bold tracking-tight text-gray-900 dark:text-gray-100">
                                    {{ $impact['summary']['no_change'] }}
                                </p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    Sin cambios
                                </p>
                            </div>
                        </div>
                    </x-filament::section>

                    {{-- Cambio categoría --}}
                    <x-filament::section :compact="true">
                        <div class="flex items-center gap-x-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-warning-100 dark:bg-warning-950">
                                <x-filament::icon
                                    icon="heroicon-m-arrow-path"
                                    class="h-5 w-5 text-warning-600 dark:text-warning-400"
                                />
                            </div>
                            <div>
                                <p class="text-2xl font-bold tracking-tight text-gray-900 dark:text-gray-100">
                                    {{ $impact['summary']['category_change'] }}
                                </p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    Cambio categoría
                                </p>
                            </div>
                        </div>
                    </x-filament::section>

                    {{-- Nueva categoría --}}
                    <x-filament::section :compact="true">
                        <div class="flex items-center gap-x-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-info-100 dark:bg-info-950">
                                <x-filament::icon
                                    icon="heroicon-m-plus-circle"
                                    class="h-5 w-5 text-info-600 dark:text-info-400"
                                />
                            </div>
                            <div>
                                <p class="text-2xl font-bold tracking-tight text-gray-900 dark:text-gray-100">
                                    {{ $impact['summary']['new_category'] }}
                                </p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    Nueva categoría
                                </p>
                            </div>
                        </div>
                    </x-filament::section>

                    {{-- Sin categoría --}}
                    <x-filament::section
                        :compact="true"
                        @class([
                            'ring-2 ring-danger-200 dark:ring-danger-800' => $impact['summary']['no_category'] > 0
                        ])
                    >
                        <div class="flex items-center gap-x-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-danger-100 dark:bg-danger-950">
                                <x-filament::icon
                                    icon="heroicon-m-exclamation-triangle"
                                    class="h-5 w-5 text-danger-600 dark:text-danger-400"
                                />
                            </div>
                            <div>
                                <p class="text-2xl font-bold tracking-tight text-gray-900 dark:text-gray-100">
                                    {{ $impact['summary']['no_category'] }}
                                </p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    Sin categoría
                                </p>
                            </div>
                        </div>
                    </x-filament::section>
                </div>

                {{-- Lista de jugadoras afectadas --}}
                @if($totalAffected > 0)
                    <x-filament::section>
                        <x-slot name="heading">
                            Jugadoras Afectadas
                            <span class="ml-2 text-sm font-normal text-gray-500">({{ $totalAffected }} total)</span>
                        </x-slot>

                        <x-slot name="description">
                            Jugadoras que requieren atención o cambiarán de categoría
                        </x-slot>

                        <div class="space-y-3">
                            @foreach(array_slice($impact['affected_players'], 0, 5) as $affected)
                                <div class="flex items-center justify-between rounded-lg border border-gray-200 p-4 dark:border-gray-700">
                                    <div class="flex items-center gap-x-3">
                                        {{-- Avatar --}}
                                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-800">
                                            <x-filament::icon
                                                icon="heroicon-m-user"
                                                class="h-5 w-5 text-gray-500"
                                            />
                                        </div>

                                        {{-- Info jugadora --}}
                                        <div>
                                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                {{ $affected['player']->user->name ?? 'Sin nombre' }}
                                            </p>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ $affected['current_age'] }} años •
                                                {{ $affected['player']->currentClub->name ?? 'Sin club' }}
                                            </p>
                                        </div>
                                    </div>

                                    {{-- Badge estado --}}
                                    @php
                                        $badgeConfig = match($affected['change_type']) {
                                            'no_category' => ['danger', 'Sin categoría'],
                                            'category_change' => ['warning', 'Cambio'],
                                            'new_category' => ['info', 'Nueva'],
                                            default => ['gray', 'Normal']
                                        };
                                    @endphp

                                    <x-filament::badge
                                        :color="$badgeConfig[0]"
                                        size="sm"
                                    >
                                        {{ $badgeConfig[1] }}
                                    </x-filament::badge>
                                </div>
                            @endforeach

                            {{-- Ver más --}}
                            @if($totalAffected > 5)
                                <div class="rounded-lg bg-gray-50 p-4 text-center dark:bg-gray-800">
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        Mostrando 5 de {{ $totalAffected }} jugadoras afectadas
                                    </p>
                                    <x-filament::button
                                        size="sm"
                                        color="gray"
                                        class="mt-2"
                                    >
                                        Ver todas
                                    </x-filament::button>
                                </div>
                            @endif
                        </div>
                    </x-filament::section>
                @else
                    {{-- Estado exitoso --}}
                    <x-filament::section>
                        <div class="flex flex-col items-center justify-center py-8 text-center">
                            <x-filament::icon
                                icon="heroicon-o-check-circle"
                                class="h-12 w-12 text-success-500"
                            />
                            <h3 class="mt-4 text-sm font-medium text-gray-900 dark:text-gray-100">
                                ¡Excelente configuración!
                            </h3>
                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                Todas las jugadoras mantienen sus categorías con la nueva configuración
                            </p>
                        </div>
                    </x-filament::section>
                @endif

                {{-- Distribución por categoría --}}
                @if(!empty($impact['category_changes']))
                    <x-filament::section>
                        <x-slot name="heading">
                            Distribución por Categoría
                        </x-slot>

                        <x-slot name="description">
                            Cómo se distribuirán las jugadoras con las nuevas categorías
                        </x-slot>

                        <div class="space-y-3">
                            @foreach($impact['category_changes'] as $categoryName => $data)
                                <div class="flex items-center justify-between rounded-lg bg-gray-50 p-3 dark:bg-gray-800">
                                    <div class="flex items-center gap-x-3">
                                        <div @class([
                                            'h-3 w-3 rounded-full',
                                            'bg-danger-500' => $categoryName === 'Sin categoría',
                                            'bg-primary-500' => $categoryName !== 'Sin categoría'
                                        ])></div>
                                        <span class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                            {{ $categoryName }}
                                        </span>
                                        @if($data['category'])
                                            <span class="text-xs text-gray-500">
                                                ({{ $data['category']->min_age }}-{{ $data['category']->max_age }} años)
                                            </span>
                                        @endif
                                    </div>
                                    <div class="flex items-center gap-x-2">
                                        <span class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                            {{ $data['players_count'] }}
                                        </span>
                                        @if($categoryName === 'Sin categoría')
                                            <x-filament::icon
                                                icon="heroicon-m-exclamation-triangle"
                                                class="h-4 w-4 text-danger-500"
                                            />
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </x-filament::section>
                @endif

                {{-- Acciones recomendadas --}}
                @if($needsAttention)
                    <x-filament::section>
                        <x-slot name="heading">
                            <div class="flex items-center gap-x-2">
                                <x-filament::icon
                                    icon="heroicon-m-light-bulb"
                                    class="h-5 w-5 text-info-500"
                                />
                                Acciones Recomendadas
                            </div>
                        </x-slot>

                        <div class="space-y-4">
                            <div class="flex gap-x-3">
                                <div class="flex h-6 w-6 items-center justify-center rounded-full bg-primary-100 text-xs font-medium text-primary-700 dark:bg-primary-950 dark:text-primary-300">
                                    1
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                        Ajustar rangos de edad
                                    </p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        Modifica los rangos para cubrir las {{ $criticalCount }} jugadoras sin asignar
                                    </p>
                                </div>
                            </div>

                            <div class="flex gap-x-3">
                                <div class="flex h-6 w-6 items-center justify-center rounded-full bg-primary-100 text-xs font-medium text-primary-700 dark:bg-primary-950 dark:text-primary-300">
                                    2
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                        Crear categoría adicional
                                    </p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        Agrega una nueva categoría que cubra las edades faltantes
                                    </p>
                                </div>
                            </div>

                            <div class="mt-4 flex gap-x-3">
                                <x-filament::button
                                    color="primary"
                                    size="sm"
                                    icon="heroicon-m-cog-6-tooth"
                                >
                                    Configuración automática
                                </x-filament::button>

                                <x-filament::button
                                    color="gray"
                                    size="sm"
                                    icon="heroicon-m-eye"
                                >
                                    Ver todas las jugadoras
                                </x-filament::button>
                            </div>
                        </div>
                    </x-filament::section>
                @endif

            @endif

        </div>

        {{-- Footer informativo --}}
        @if($hasData && $hasCustomCategories)
            <x-slot name="footerActions">
                <div class="flex w-full items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                    <span>
                        Última actualización: {{ now()->format('d/m/Y H:i') }}
                    </span>
                    <span>
                        {{ $impact['total_players'] }} jugadoras analizadas
                    </span>
                </div>
            </x-slot>
        @endif

    </x-filament::section>
</x-filament-widgets::widget>
