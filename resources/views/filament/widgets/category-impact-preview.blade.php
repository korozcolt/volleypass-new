<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            <div class="flex items-center gap-2">
                <x-heroicon-o-eye class="h-5 w-5 text-primary-500" />
                Preview del Impacto en Jugadoras
            </div>
        </x-slot>

        @if(!$hasCustomCategories)
            <div class="text-center py-8">
                <x-heroicon-o-information-circle class="mx-auto h-12 w-12 text-gray-400" />
                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">
                    Sistema Tradicional Activo
                </h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Configure categorías personalizadas para ver el impacto en las jugadoras.
                </p>
            </div>
        @else
            <div class="space-y-6">
                <!-- Resumen del Impacto -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-4">
                        <div class="flex items-center">
                            <x-heroicon-o-check-circle class="h-5 w-5 text-green-500" />
                            <span class="ml-2 text-sm font-medium text-green-800 dark:text-green-200">
                                Sin cambios
                            </span>
                        </div>
                        <p class="mt-1 text-2xl font-semibold text-green-900 dark:text-green-100">
                            {{ $impact['summary']['no_change'] }}
                        </p>
                    </div>

                    <div class="bg-yellow-50 dark:bg-yellow-900/20 rounded-lg p-4">
                        <div class="flex items-center">
                            <x-heroicon-o-arrow-path class="h-5 w-5 text-yellow-500" />
                            <span class="ml-2 text-sm font-medium text-yellow-800 dark:text-yellow-200">
                                Cambio categoría
                            </span>
                        </div>
                        <p class="mt-1 text-2xl font-semibold text-yellow-900 dark:text-yellow-100">
                            {{ $impact['summary']['category_change'] }}
                        </p>
                    </div>

                    <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4">
                        <div class="flex items-center">
                            <x-heroicon-o-plus-circle class="h-5 w-5 text-blue-500" />
                            <span class="ml-2 text-sm font-medium text-blue-800 dark:text-blue-200">
                                Nueva categoría
                            </span>
                        </div>
                        <p class="mt-1 text-2xl font-semibold text-blue-900 dark:text-blue-100">
                            {{ $impact['summary']['new_category'] }}
                        </p>
                    </div>

                    <div class="bg-red-50 dark:bg-red-900/20 rounded-lg p-4">
                        <div class="flex items-center">
                            <x-heroicon-o-exclamation-triangle class="h-5 w-5 text-red-500" />
                            <span class="ml-2 text-sm font-medium text-red-800 dark:text-red-200">
                                Sin categoría
                            </span>
                        </div>
                        <p class="mt-1 text-2xl font-semibold text-red-900 dark:text-red-100">
                            {{ $impact['summary']['no_category'] }}
                        </p>
                    </div>
                </div>

                <!-- Distribución por Categoría -->
                @if(count($impact['category_changes']) > 0)
                    <div>
                        <h4 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                            Distribución por Categoría Personalizada
                        </h4>
                        <div class="grid gap-4">
                            @foreach($impact['category_changes'] as $categoryName => $categoryData)
                                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                                    <div class="flex justify-between items-start mb-3">
                                        <div>
                                            <h5 class="font-medium text-gray-900 dark:text-gray-100">
                                                {{ $categoryName }}
                                            </h5>
                                            @if($categoryData['category'])
                                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                                    Rango: {{ $categoryData['category']->min_age }}-{{ $categoryData['category']->max_age }} años
                                                </p>
                                            @endif
                                        </div>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary-100 text-primary-800 dark:bg-primary-900 dark:text-primary-200">
                                            {{ $categoryData['players_count'] }} jugadoras
                                        </span>
                                    </div>
                                    
                                    @if(count($categoryData['from_traditional']) > 0)
                                        <div class="space-y-1">
                                            <p class="text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                Provienen de:
                                            </p>
                                            @foreach($categoryData['from_traditional'] as $traditionalCategory => $count)
                                                <div class="flex justify-between text-sm">
                                                    <span class="text-gray-600 dark:text-gray-400">{{ $traditionalCategory }}</span>
                                                    <span class="font-medium text-gray-900 dark:text-gray-100">{{ $count }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Jugadoras Afectadas (solo las primeras 10) -->
                @if(count($impact['affected_players']) > 0)
                    <div>
                        <h4 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                            Jugadoras Afectadas
                            @if(count($impact['affected_players']) > 10)
                                <span class="text-sm font-normal text-gray-500 dark:text-gray-400">
                                    (Mostrando las primeras 10 de {{ count($impact['affected_players']) }})
                                </span>
                            @endif
                        </h4>
                        <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
                            <table class="min-w-full divide-y divide-gray-300 dark:divide-gray-600">
                                <thead class="bg-gray-50 dark:bg-gray-800">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                            Jugadora
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                            Edad
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                            Club
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                            Cambio
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach(array_slice($impact['affected_players'], 0, 10) as $affectedPlayer)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                                {{ $affectedPlayer['player']->user->name ?? 'N/A' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                {{ $affectedPlayer['current_age'] }} años
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                {{ $affectedPlayer['player']->currentClub->name ?? 'N/A' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                    @if($affectedPlayer['change_type'] === 'category_change') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                                    @elseif($affectedPlayer['change_type'] === 'no_category') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                                    @else bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                                    @endif">
                                                    {{ $affectedPlayer['change_description'] }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>