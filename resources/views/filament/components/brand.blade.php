<div class="flex items-center space-x-3">
    {{-- Logo --}}
    <div class="flex-shrink-0">
        <img
            src="{{ asset('images/logo-volley_pass_black_back.png') }}"
            alt="VolleyPass Logo"
            class="h-10 w-auto"
        >
    </div>

    {{-- Nombre de la aplicación --}}
    <div class="flex flex-col">
        <span class="text-lg font-semibold text-gray-900 dark:text-white leading-tight">
            {{ app_name() }}
        </span>
        <span class="text-xs text-gray-500 dark:text-gray-400 leading-none">
            {{ app_description() }}
        </span>
    </div>
</div>
