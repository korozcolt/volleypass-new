<nav class="flex space-x-8">
    <!-- Enlaces según el rol del usuario -->
    @if(auth()->user()->hasRole('player'))
        <a href="{{ route('player.dashboard') }}" 
           class="text-gray-700 dark:text-gray-300 hover:text-vp-primary-600 dark:hover:text-vp-primary-400 px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('player.*') ? 'text-vp-primary-600 dark:text-vp-primary-400' : '' }}">
            Mi Dashboard
        </a>
        <a href="{{ route('player.matches') }}" 
           class="text-gray-700 dark:text-gray-300 hover:text-vp-primary-600 dark:hover:text-vp-primary-400 px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('player.matches') ? 'text-vp-primary-600 dark:text-vp-primary-400' : '' }}">
            Mis Partidos
        </a>
        <a href="{{ route('player.team') }}" 
           class="text-gray-700 dark:text-gray-300 hover:text-vp-primary-600 dark:hover:text-vp-primary-400 px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('player.team') ? 'text-vp-primary-600 dark:text-vp-primary-400' : '' }}">
            Mi Equipo
        </a>
        <a href="{{ route('player.stats') }}" 
           class="text-gray-700 dark:text-gray-300 hover:text-vp-primary-600 dark:hover:text-vp-primary-400 px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('player.stats') ? 'text-vp-primary-600 dark:text-vp-primary-400' : '' }}">
            Estadísticas
        </a>
    @endif

    @if(auth()->user()->hasRole('coach'))
        <a href="{{ route('coach.dashboard') }}" 
           class="text-gray-700 dark:text-gray-300 hover:text-vp-primary-600 dark:hover:text-vp-primary-400 px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('coach.*') ? 'text-vp-primary-600 dark:text-vp-primary-400' : '' }}">
            Panel de Control
        </a>
        <a href="{{ route('coach.team') }}" 
           class="text-gray-700 dark:text-gray-300 hover:text-vp-primary-600 dark:hover:text-vp-primary-400 px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('coach.team') ? 'text-vp-primary-600 dark:text-vp-primary-400' : '' }}">
            Mi Equipo
        </a>
        <a href="{{ route('coach.matches') }}" 
           class="text-gray-700 dark:text-gray-300 hover:text-vp-primary-600 dark:hover:text-vp-primary-400 px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('coach.matches') ? 'text-vp-primary-600 dark:text-vp-primary-400' : '' }}">
            Calendario
        </a>
        <a href="{{ route('coach.training') }}" 
           class="text-gray-700 dark:text-gray-300 hover:text-vp-primary-600 dark:hover:text-vp-primary-400 px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('coach.training') ? 'text-vp-primary-600 dark:text-vp-primary-400' : '' }}">
            Entrenamientos
        </a>
    @endif

    @if(auth()->user()->hasRole('referee'))
        <a href="{{ route('referee.dashboard') }}" 
           class="text-gray-700 dark:text-gray-300 hover:text-vp-primary-600 dark:hover:text-vp-primary-400 px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('referee.*') ? 'text-vp-primary-600 dark:text-vp-primary-400' : '' }}">
            Panel de Árbitro
        </a>
        <a href="{{ route('referee.assignments') }}" 
           class="text-gray-700 dark:text-gray-300 hover:text-vp-primary-600 dark:hover:text-vp-primary-400 px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('referee.assignments') ? 'text-vp-primary-600 dark:text-vp-primary-400' : '' }}">
            Asignaciones
        </a>
        <a href="{{ route('referee.reports') }}" 
           class="text-gray-700 dark:text-gray-300 hover:text-vp-primary-600 dark:hover:text-vp-primary-400 px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('referee.reports') ? 'text-vp-primary-600 dark:text-vp-primary-400' : '' }}">
            Reportes
        </a>
    @endif

    @if(auth()->user()->hasRole('medical'))
        <a href="{{ route('medical.dashboard') }}" 
           class="text-gray-700 dark:text-gray-300 hover:text-vp-primary-600 dark:hover:text-vp-primary-400 px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('medical.*') ? 'text-vp-primary-600 dark:text-vp-primary-400' : '' }}">
            Panel Médico
        </a>
        <a href="{{ route('medical.players') }}" 
           class="text-gray-700 dark:text-gray-300 hover:text-vp-primary-600 dark:hover:text-vp-primary-400 px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('medical.players') ? 'text-vp-primary-600 dark:text-vp-primary-400' : '' }}">
            Jugadoras
        </a>
        <a href="{{ route('medical.reports') }}" 
           class="text-gray-700 dark:text-gray-300 hover:text-vp-primary-600 dark:hover:text-vp-primary-400 px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('medical.reports') ? 'text-vp-primary-600 dark:text-vp-primary-400' : '' }}">
            Reportes Médicos
        </a>
    @endif

    <!-- Enlaces comunes para todos los usuarios autenticados -->
    <a href="{{ route('public.matches') }}" 
       class="text-gray-700 dark:text-gray-300 hover:text-vp-primary-600 dark:hover:text-vp-primary-400 px-3 py-2 text-sm font-medium transition-colors">
        Partidos
    </a>
    <a href="{{ route('public.teams') }}" 
       class="text-gray-700 dark:text-gray-300 hover:text-vp-primary-600 dark:hover:text-vp-primary-400 px-3 py-2 text-sm font-medium transition-colors">
        Equipos
    </a>
</nav>