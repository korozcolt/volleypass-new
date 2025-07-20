@php
    $record = $getRecord();
    $lightLogo = $record->getLogoUrl('light');
    $darkLogo = $record->getLogoUrl('dark');
@endphp

<div class="flex items-center justify-center">
    @if($lightLogo && $darkLogo)
        {{-- Ambos logos disponibles - mostrar según el modo --}}
        <img
            src="{{ $lightLogo }}"
            alt="{{ $record->name }}"
            class="w-10 h-10 rounded-full object-cover block dark:hidden"
        />
        <img
            src="{{ $darkLogo }}"
            alt="{{ $record->name }}"
            class="w-10 h-10 rounded-full object-cover hidden dark:block"
        />
    @elseif($lightLogo)
        {{-- Solo logo claro disponible --}}
        <img
            src="{{ $lightLogo }}"
            alt="{{ $record->name }}"
            class="w-10 h-10 rounded-full object-cover"
        />
    @elseif($darkLogo)
        {{-- Solo logo oscuro disponible --}}
        <img
            src="{{ $darkLogo }}"
            alt="{{ $record->name }}"
            class="w-10 h-10 rounded-full object-cover"
        />
    @else
        {{-- Sin logo - mostrar placeholder --}}
        <div class="w-10 h-10 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
            <x-heroicon-o-building-office class="w-5 h-5 text-gray-400" />
        </div>
    @endif
</div>
