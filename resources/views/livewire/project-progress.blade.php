<div class="max-w-4xl mx-auto space-y-8">
    @foreach($modules as $module)
    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 animate-on-scroll">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-{{ $module['color'] }}-100 dark:bg-{{ $module['color'] }}-900 rounded-lg flex items-center justify-center">
                    @switch($module['icon'])
                        @case('users')
                            <svg class="w-5 h-5 text-{{ $module['color'] }}-600 dark:text-{{ $module['color'] }}-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                            </svg>
                            @break
                        @case('shield')
                            <svg class="w-5 h-5 text-{{ $module['color'] }}-600 dark:text-{{ $module['color'] }}-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg>
                            @break
                        @case('qr-code')
                            <svg class="w-5 h-5 text-{{ $module['color'] }}-600 dark:text-{{ $module['color'] }}-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h4"></path>
                            </svg>
                            @break
                        @case('trophy')
                            <svg class="w-5 h-5 text-{{ $module['color'] }}-600 dark:text-{{ $module['color'] }}-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                            </svg>
                            @break
                        @case('bar-chart-3')
                            <svg class="w-5 h-5 text-{{ $module['color'] }}-600 dark:text-{{ $module['color'] }}-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                            @break
                    @endswitch
                </div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $module['name'] }}</h3>
            </div>
            <div class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                {{ $module['progress'] }}%
            </div>
        </div>

        <div class="mb-2">
            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                <div
                    class="bg-{{ $module['color'] }}-600 h-2 rounded-full transition-all duration-1000 ease-out"
                    style="width: {{ $module['progress'] }}%"
                    x-data="{ width: 0 }"
                    x-init="setTimeout(() => width = {{ $module['progress'] }}, 500)"
                    :style="`width: ${width}%`">
                </div>
            </div>
        </div>

        <p class="text-sm text-gray-600 dark:text-gray-400">{{ $module['description'] }}</p>
    </div>
    @endforeach

    <div class="text-center mt-12">
        <div class="inline-flex items-center space-x-2 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 px-4 py-2 rounded-full">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span class="text-sm font-medium">Lanzamiento estimado: Q2 2024</span>
        </div>
    </div>
</div>
