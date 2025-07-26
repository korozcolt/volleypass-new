<nav class="flex space-x-8">
    <a href="{{ route('home') }}" 
       class="text-gray-700 dark:text-gray-300 hover:text-vp-primary-600 dark:hover:text-vp-primary-400 px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('home') ? 'text-vp-primary-600 dark:text-vp-primary-400' : '' }}">
        Inicio
    </a>
    <a href="{{ route('public.matches') }}" 
       class="text-gray-700 dark:text-gray-300 hover:text-vp-primary-600 dark:hover:text-vp-primary-400 px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('public.matches') ? 'text-vp-primary-600 dark:text-vp-primary-400' : '' }}">
        Partidos en Vivo
    </a>
    <a href="{{ route('public.results') }}" 
       class="text-gray-700 dark:text-gray-300 hover:text-vp-primary-600 dark:hover:text-vp-primary-400 px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('public.results') ? 'text-vp-primary-600 dark:text-vp-primary-400' : '' }}">
        Resultados
    </a>
    <a href="{{ route('public.teams') }}" 
       class="text-gray-700 dark:text-gray-300 hover:text-vp-primary-600 dark:hover:text-vp-primary-400 px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('public.teams') ? 'text-vp-primary-600 dark:text-vp-primary-400' : '' }}">
        Equipos
    </a>
    <a href="{{ route('public.standings') }}" 
       class="text-gray-700 dark:text-gray-300 hover:text-vp-primary-600 dark:hover:text-vp-primary-400 px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('public.standings') ? 'text-vp-primary-600 dark:text-vp-primary-400' : '' }}">
        Tabla de Posiciones
    </a>
    <a href="{{ route('public.stats') }}" 
       class="text-gray-700 dark:text-gray-300 hover:text-vp-primary-600 dark:hover:text-vp-primary-400 px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('public.stats') ? 'text-vp-primary-600 dark:text-vp-primary-400' : '' }}">
        Estadísticas
    </a>
</nav>