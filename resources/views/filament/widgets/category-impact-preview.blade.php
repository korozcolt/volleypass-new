<x-filament-widgets::widget>
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center gap-2">
                <x-heroicon-o-eye class="h-5 w-5 text-primary-500" />
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Preview del Impacto en Jugadoras</h3>
            </div>
        </div>
        <div class="p-6">

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
                    <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-4 border border-green-200 dark:border-green-700">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="flex items-center gap-2 mb-2">
                                    <x-heroicon-o-check-circle class="h-5 w-5 text-green-500" />
                                    <span class="text-sm font-medium text-green-800 dark:text-green-200">
                                        Sin cambios
                                    </span>
                                </div>
                                <p class="text-2xl font-bold text-green-900 dark:text-green-100">
                                    {{ $impact['summary']['no_change'] }}
                                </p>
                                <p class="text-xs text-green-600 dark:text-green-400 mt-1">
                                    Jugadoras que mantienen su categoría
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-yellow-50 dark:bg-yellow-900/20 rounded-lg p-4 border border-yellow-200 dark:border-yellow-700">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="flex items-center gap-2 mb-2">
                                    <x-heroicon-o-arrow-path class="h-5 w-5 text-yellow-500" />
                                    <span class="text-sm font-medium text-yellow-800 dark:text-yellow-200">
                                        Cambio categoría
                                    </span>
                                </div>
                                <p class="text-2xl font-bold text-yellow-900 dark:text-yellow-100">
                                    {{ $impact['summary']['category_change'] }}
                                </p>
                                <p class="text-xs text-yellow-600 dark:text-yellow-400 mt-1">
                                    Jugadoras que cambian de categoría
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4 border border-blue-200 dark:border-blue-700">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="flex items-center gap-2 mb-2">
                                    <x-heroicon-o-plus-circle class="h-5 w-5 text-blue-500" />
                                    <span class="text-sm font-medium text-blue-800 dark:text-blue-200">
                                        Nueva categoría
                                    </span>
                                </div>
                                <p class="text-2xl font-bold text-blue-900 dark:text-blue-100">
                                    {{ $impact['summary']['new_category'] }}
                                </p>
                                <p class="text-xs text-blue-600 dark:text-blue-400 mt-1">
                                    Jugadoras asignadas a nueva categoría
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-red-50 dark:bg-red-900/20 rounded-lg p-4 border border-red-200 dark:border-red-700">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="flex items-center gap-2 mb-2">
                                    <x-heroicon-o-exclamation-triangle class="h-5 w-5 text-red-500" />
                                    <span class="text-sm font-medium text-red-800 dark:text-red-200">
                                        Sin categoría
                                    </span>
                                </div>
                                <p class="text-2xl font-bold text-red-900 dark:text-red-100">
                                    {{ $impact['summary']['no_category'] }}
                                </p>
                                <p class="text-xs text-red-600 dark:text-red-400 mt-1">
                                    Jugadoras sin categoría asignada
                                </p>
                            </div>
                        </div>
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
                                <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                                    <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                                        <div class="flex items-center justify-between w-full">
                                            <div class="flex items-center gap-2">
                                                <x-heroicon-o-user-group class="h-5 w-5 text-primary-500" />
                                                <span class="font-medium text-gray-900 dark:text-white">{{ $categoryName }}</span>
                                                @if($categoryData['category'])
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                                                        {{ $categoryData['category']->min_age }}-{{ $categoryData['category']->max_age }} años
                                                    </span>
                                                @endif
                                            </div>
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-primary-100 text-primary-800 dark:bg-primary-900 dark:text-primary-200">
                                                {{ $categoryData['players_count'] }} jugadoras
                                            </span>
                                        </div>
                                    </div>
                                    <div class="p-4">
                                    
                                    @if(count($categoryData['from_traditional']) > 0)
                                        <div class="space-y-3">
                                            <h6 class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                                <x-heroicon-o-arrow-right class="inline h-4 w-4 mr-1" />
                                                Provienen de categorías tradicionales:
                                            </h6>
                                            <div class="grid gap-2">
                                                @foreach($categoryData['from_traditional'] as $traditionalCategory => $count)
                                                    <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                                                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ $traditionalCategory }}</span>
                                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                                            {{ $count }} jugadoras
                                                        </span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                    </div>
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
                        <div class="overflow-x-auto">
                            <table class="w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-800">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Jugadora</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Edad</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Club</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Cambio</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            
                            @foreach(array_slice($impact['affected_players'], 0, 10) as $affectedPlayer)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-3">
                                            @if($affectedPlayer['player']->user->avatar_url ?? null)
                                                <img class="h-8 w-8 rounded-full object-cover" 
                                                     src="{{ $affectedPlayer['player']->user->avatar_url }}" 
                                                     alt="{{ $affectedPlayer['player']->user->name ?? 'N/A' }}">
                                            @else
                                                <div class="h-8 w-8 rounded-full bg-gray-300 dark:bg-gray-600 flex items-center justify-center">
                                                    <x-heroicon-o-user class="h-4 w-4 text-gray-500 dark:text-gray-400" />
                                                </div>
                                            @endif
                                            <div>
                                                <div class="font-medium text-gray-900 dark:text-gray-100">
                                                    {{ $affectedPlayer['player']->user->name ?? 'N/A' }}
                                                </div>
                                                @if($affectedPlayer['player']->user->email)
                                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                                        {{ $affectedPlayer['player']->user->email }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                                            {{ $affectedPlayer['current_age'] }} años
                                        </span>
                                    </td>
                                    
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 dark:text-gray-100">
                                            {{ $affectedPlayer['player']->currentClub->name ?? 'Sin club' }}
                                        </div>
                                    </td>
                                    
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $badgeColor = match($affectedPlayer['change_type']) {
                                                'category_change' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                                                'no_category' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
                                                default => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200'
                                            };
                                        @endphp
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $badgeColor }}">
                                            {{ $affectedPlayer['change_description'] }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                         </tbody>
                     </table>
                    </div>
                @endif
            </div>
        @endif
        </div>
    </div>
</x-filament-widgets::widget>