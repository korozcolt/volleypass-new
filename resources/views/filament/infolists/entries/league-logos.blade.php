@php
    $record = $getRecord();
    $lightLogo = $record->getLogoUrl('light');
    $darkLogo = $record->getLogoUrl('dark');
@endphp

<div class="space-y-4">
    {{-- Logo adaptativo principal --}}
    <div class="flex items-center justify-center p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
        @if($lightLogo && $darkLogo)
            <img
                src="{{ $lightLogo }}"
                alt="{{ $record->name }} - Modo Claro"
                class="w-20 h-20 object-contain block dark:hidden"
            />
            <img
                src="{{ $darkLogo }}"
                alt="{{ $record->name }} - Modo Oscuro"
                class="w-20 h-20 object-contain hidden dark:block"
            />
        @elseif($lightLogo)
            <img
                src="{{ $lightLogo }}"
                alt="{{ $record->name }}"
                class="w-20 h-20 object-contain"
            />
        @elseif($darkLogo)
            <img
                src="{{ $darkLogo }}"
                alt="{{ $record->name }}"
                class="w-20 h-20 object-contain"
            />
        @else
            <div class="w-20 h-20 rounded-lg bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                <x-heroicon-o-building-office class="w-10 h-10 text-gray-400" />
            </div>
        @endif
    </div>

    {{-- Mostrar ambos logos por separado si existen --}}
    @if($lightLogo && $darkLogo)
        <div class="grid grid-cols-2 gap-4">
            <div class="text-center">
                <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Modo Claro</p>
                <div class="flex items-center justify-center p-3 bg-white border rounded-lg">
                    <img
                        src="{{ $lightLogo }}"
                        alt="{{ $record->name }} - Claro"
                        class="w-12 h-12 object-contain"
                    />
                </div>
            </div>
            <div class="text-center">
                <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Modo Oscuro</p>
                <div class="flex items-center justify-center p-3 bg-gray-900 border rounded-lg">
                    <img
                        src="{{ $darkLogo }}"
                        alt="{{ $record->name }} - Oscuro"
                        class="w-12 h-12 object-contain"
                    />
                </div>
            </div>
        </div>
    @endif

    {{-- Información adicional --}}
    <div class="text-xs text-gray-500 dark:text-gray-400 text-center">
        @if($lightLogo && $darkLogo)
            ✅ Logos optimizados para ambos modos
        @elseif($lightLogo || $darkLogo)
            ⚠️ Solo un logo disponible
        @else
            ❌ Sin logos configurados
        @endif
    </div>
</div>
