<div class="px-4 pt-2 pb-3 space-y-1">
    @auth
        <!-- User Info -->
        <div class="flex items-center px-3 py-2 mb-4">
            <img class="h-10 w-10 rounded-full" 
                 src="{{ auth()->user()->avatar_url ?? '/placeholder.svg?height=40&width=40' }}" 
                 alt="{{ auth()->user()->name }}">
            <div class="ml-3">
                <div class="text-base font-medium text-gray-800 dark:text-white">{{ auth()->user()->name }}</div>
                <div class="text-sm font-medium text-gray-500 dark:text-gray-400 capitalize">{{ auth()->user()->role }}</div>
            </div>
        </div>
        
        <!-- Role-specific navigation -->
        @if(auth()->user()->role === 'player')
            <a href="{{ route('player.dashboard') }}" 
               class="block px-3 py-2 text-base font-medium text-gray-700 dark:text-gray-300 hover:text-vp-primary-600 dark:hover:text-vp-primary-400 hover:bg-gray-50 dark:hover:bg-gray-800 rounded-md transition-colors {{ request()->routeIs('player.dashboard') ? 'text-vp-primary-600 dark:text-vp-primary-400 bg-vp-primary-50 dark:bg-vp-primary-900' : '' }}">
                Dashboard
            </a>
            <a href="{{ route('player.tournaments') }}" 
               class="block px-3 py-2 text-base font-medium text-gray-700 dark:text-gray-300 hover:text-vp-primary-600 dark:hover:text-vp-primary-400 hover:bg-gray-50 dark:hover:bg-gray-800 rounded-md transition-colors {{ request()->routeIs('player.tournaments') ? 'text-vp-primary-600 dark:text-vp-primary-400 bg-vp-primary-50 dark:bg-vp-primary-900' : '' }}">
                Mis Torneos
            </a>
            <a href="{{ route('player.stats') }}" 
               class="block px-3 py-2 text-base font-medium text-gray-700 dark:text-gray-300 hover:text-vp-primary-600 dark:hover:text-vp-primary-400 hover:bg-gray-50 dark:hover:bg-gray-800 rounded-md transition-colors {{ request()->routeIs('player.stats') ? 'text-vp-primary-600 dark:text-vp-primary-400 bg-vp-primary-50 dark:bg-vp-primary-900' : '' }}">
                Estadísticas
            </a>
            <a href="{{ route('player.card') }}" 
               class="block px-3 py-2 text-base font-medium text-gray-700 dark:text-gray-300 hover:text-vp-primary-600 dark:hover:text-vp-primary-400 hover:bg-gray-50 dark:hover:bg-gray-800 rounded-md transition-colors {{ request()->routeIs('player.card') ? 'text-vp-primary-600 dark:text-vp-primary-400 bg-vp-primary-50 dark:bg-vp-primary-900' : '' }}">
                Carnet Digital
            </a>
        @elseif(auth()->user()->role === 'coach')
            <a href="{{ route('coach.dashboard') }}" 
               class="block px-3 py-2 text-base font-medium text-gray-700 dark:text-gray-300 hover:text-vp-primary-600 dark:hover:text-vp-primary-400 hover:bg-gray-50 dark:hover:bg-gray-800 rounded-md transition-colors {{ request()->routeIs('coach.dashboard') ? 'text-vp-primary-600 dark:text-vp-primary-400 bg-vp-primary-50 dark:bg-vp-primary-900' : '' }}">
                Dashboard
            </a>
            <a href="{{ route('coach.team') }}" 
               class="block px-3 py-2 text-base font-medium text-gray-700 dark:text-gray-300 hover:text-vp-primary-600 dark:hover:text-vp-primary-400 hover:bg-gray-50 dark:hover:bg-gray-800 rounded-md transition-colors {{ request()->routeIs('coach.team') ? 'text-vp-primary-600 dark:text-vp-primary-400 bg-vp-primary-50 dark:bg-vp-primary-900' : '' }}">
                Mi Equipo
            </a>
            <a href="{{ route('coach.tournaments') }}" 
               class="block px-3 py-2 text-base font-medium text-gray-700 dark:text-gray-300 hover:text-vp-primary-600 dark:hover:text-vp-primary-400 hover:bg-gray-50 dark:hover:bg-gray-800 rounded-md transition-colors {{ request()->routeIs('coach.tournaments') ? 'text-vp-primary-600 dark:text-vp-primary-400 bg-vp-primary-50 dark:bg-vp-primary-900' : '' }}">
                Torneos
            </a>
            <a href="{{ route('coach.reports') }}" 
               class="block px-3 py-2 text-base font-medium text-gray-700 dark:text-gray-300 hover:text-vp-primary-600 dark:hover:text-vp-primary-400 hover:bg-gray-50 dark:hover:bg-gray-800 rounded-md transition-colors {{ request()->routeIs('coach.reports') ? 'text-vp-primary-600 dark:text-vp-primary-400 bg-vp-primary-50 dark:bg-vp-primary-900' : '' }}">
                Reportes
            </a>
        @elseif(auth()->user()->role === 'referee')
            <a href="{{ route('referee.dashboard') }}" 
               class="block px-3 py-2 text-base font-medium text-gray-700 dark:text-gray-300 hover:text-vp-primary-600 dark:hover:text-vp-primary-400 hover:bg-gray-50 dark:hover:bg-gray-800 rounded-md transition-colors {{ request()->routeIs('referee.dashboard') ? 'text-vp-primary-600 dark:text-vp-primary-400 bg-vp-primary-50 dark:bg-vp-primary-900' : '' }}">
                Dashboard
            </a>
            <a href="{{ route('referee.matches') }}" 
               class="block px-3 py-2 text-base font-medium text-gray-700 dark:text-gray-300 hover:text-vp-primary-600 dark:hover:text-vp-primary-400 hover:bg-gray-50 dark:hover:bg-gray-800 rounded-md transition-colors {{ request()->routeIs('referee.matches') ? 'text-vp-primary-600 dark:text-vp-primary-400 bg-vp-primary-50 dark:bg-vp-primary-900' : '' }}">
                Mis Partidos
            </a>
            <a href="{{ route('referee.tournaments') }}" 
               class="block px-3 py-2 text-base font-medium text-gray-700 dark:text-gray-300 hover:text-vp-primary-600 dark:hover:text-vp-primary-400 hover:bg-gray-50 dark:hover:bg-gray-800 rounded-md transition-colors {{ request()->routeIs('referee.tournaments') ? 'text-vp-primary-600 dark:text-vp-primary-400 bg-vp-primary-50 dark:bg-vp-primary-900' : '' }}">
                Torneos
            </a>
        @elseif(auth()->user()->role === 'medical')
            <a href="{{ route('medical.dashboard') }}" 
               class="block px-3 py-2 text-base font-medium text-gray-700 dark:text-gray-300 hover:text-vp-primary-600 dark:hover:text-vp-primary-400 hover:bg-gray-50 dark:hover:bg-gray-800 rounded-md transition-colors {{ request()->routeIs('medical.dashboard') ? 'text-vp-primary-600 dark:text-vp-primary-400 bg-vp-primary-50 dark:bg-vp-primary-900' : '' }}">
                Dashboard
            </a>
            <a href="{{ route('medical.players') }}" 
               class="block px-3 py-2 text-base font-medium text-gray-700 dark:text-gray-300 hover:text-vp-primary-600 dark:hover:text-vp-primary-400 hover:bg-gray-50 dark:hover:bg-gray-800 rounded-md transition-colors {{ request()->routeIs('medical.players') ? 'text-vp-primary-600 dark:text-vp-primary-400 bg-vp-primary-50 dark:bg-vp-primary-900' : '' }}">
                Jugadoras
            </a>
            <a href="{{ route('medical.reports') }}" 
               class="block px-3 py-2 text-base font-medium text-gray-700 dark:text-gray-300 hover:text-vp-primary-600 dark:hover:text-vp-primary-400 hover:bg-gray-50 dark:hover:bg-gray-800 rounded-md transition-colors {{ request()->routeIs('medical.reports') ? 'text-vp-primary-600 dark:text-vp-primary-400 bg-vp-primary-50 dark:bg-vp-primary-900' : '' }}">
                Reportes Médicos
            </a>
        @endif
        
        <!-- Common links for all authenticated users -->
        <div class="border-t border-gray-200 dark:border-gray-700 pt-2 mt-2">
            <a href="{{ route('matches.index') }}" 
               class="block px-3 py-2 text-base font-medium text-gray-700 dark:text-gray-300 hover:text-vp-primary-600 dark:hover:text-vp-primary-400 hover:bg-gray-50 dark:hover:bg-gray-800 rounded-md transition-colors {{ request()->routeIs('matches.index') ? 'text-vp-primary-600 dark:text-vp-primary-400 bg-vp-primary-50 dark:bg-vp-primary-900' : '' }}">
                Partidos
            </a>
            <a href="{{ route('teams.index') }}" 
               class="block px-3 py-2 text-base font-medium text-gray-700 dark:text-gray-300 hover:text-vp-primary-600 dark:hover:text-vp-primary-400 hover:bg-gray-50 dark:hover:bg-gray-800 rounded-md transition-colors {{ request()->routeIs('teams.index') ? 'text-vp-primary-600 dark:text-vp-primary-400 bg-vp-primary-50 dark:bg-vp-primary-900' : '' }}">
                Equipos
            </a>
        </div>
        
        <!-- User actions -->
        <div class="border-t border-gray-200 dark:border-gray-700 pt-2 mt-2 space-y-1">
            <a href="{{ route('profile.edit') }}" 
               class="block px-3 py-2 text-base font-medium text-gray-700 dark:text-gray-300 hover:text-vp-primary-600 dark:hover:text-vp-primary-400 hover:bg-gray-50 dark:hover:bg-gray-800 rounded-md transition-colors">
                Perfil
            </a>
            <a href="{{ route('settings') }}" 
               class="block px-3 py-2 text-base font-medium text-gray-700 dark:text-gray-300 hover:text-vp-primary-600 dark:hover:text-vp-primary-400 hover:bg-gray-50 dark:hover:bg-gray-800 rounded-md transition-colors">
                Configuración
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" 
                        class="block w-full text-left px-3 py-2 text-base font-medium text-gray-700 dark:text-gray-300 hover:text-red-600 dark:hover:text-red-400 hover:bg-gray-50 dark:hover:bg-gray-800 rounded-md transition-colors">
                    Cerrar Sesión
                </button>
            </form>
        </div>
    @else
        <!-- Guest Navigation -->
        <a href="{{ route('home') }}" 
           class="block px-3 py-2 text-base font-medium text-gray-700 dark:text-gray-300 hover:text-vp-primary-600 dark:hover:text-vp-primary-400 hover:bg-gray-50 dark:hover:bg-gray-800 rounded-md transition-colors {{ request()->routeIs('home') ? 'text-vp-primary-600 dark:text-vp-primary-400 bg-vp-primary-50 dark:bg-vp-primary-900' : '' }}">
            Inicio
        </a>
        
        <a href="{{ route('public.matches') }}" 
           class="block px-3 py-2 text-base font-medium text-gray-700 dark:text-gray-300 hover:text-vp-primary-600 dark:hover:text-vp-primary-400 hover:bg-gray-50 dark:hover:bg-gray-800 rounded-md transition-colors {{ request()->routeIs('public.matches') ? 'text-vp-primary-600 dark:text-vp-primary-400 bg-vp-primary-50 dark:bg-vp-primary-900' : '' }}">
            Partidos en Vivo
        </a>
        
        <a href="{{ route('public.results') }}" 
           class="block px-3 py-2 text-base font-medium text-gray-700 dark:text-gray-300 hover:text-vp-primary-600 dark:hover:text-vp-primary-400 hover:bg-gray-50 dark:hover:bg-gray-800 rounded-md transition-colors {{ request()->routeIs('public.results') ? 'text-vp-primary-600 dark:text-vp-primary-400 bg-vp-primary-50 dark:bg-vp-primary-900' : '' }}">
            Resultados
        </a>
        
        <a href="{{ route('public.teams') }}" 
           class="block px-3 py-2 text-base font-medium text-gray-700 dark:text-gray-300 hover:text-vp-primary-600 dark:hover:text-vp-primary-400 hover:bg-gray-50 dark:hover:bg-gray-800 rounded-md transition-colors {{ request()->routeIs('public.teams') ? 'text-vp-primary-600 dark:text-vp-primary-400 bg-vp-primary-50 dark:bg-vp-primary-900' : '' }}">
            Equipos
        </a>
        
        <a href="{{ route('public.standings') }}" 
           class="block px-3 py-2 text-base font-medium text-gray-700 dark:text-gray-300 hover:text-vp-primary-600 dark:hover:text-vp-primary-400 hover:bg-gray-50 dark:hover:bg-gray-800 rounded-md transition-colors {{ request()->routeIs('public.standings') ? 'text-vp-primary-600 dark:text-vp-primary-400 bg-vp-primary-50 dark:bg-vp-primary-900' : '' }}">
            Tabla de Posiciones
        </a>
        
        <a href="{{ route('public.stats') }}" 
           class="block px-3 py-2 text-base font-medium text-gray-700 dark:text-gray-300 hover:text-vp-primary-600 dark:hover:text-vp-primary-400 hover:bg-gray-50 dark:hover:bg-gray-800 rounded-md transition-colors {{ request()->routeIs('public.stats') ? 'text-vp-primary-600 dark:text-vp-primary-400 bg-vp-primary-50 dark:bg-vp-primary-900' : '' }}">
            Estadísticas
        </a>
        
        <!-- Guest Actions -->
        <div class="border-t border-gray-200 dark:border-gray-700 pt-4 mt-4 space-y-1">
            <a href="{{ route('login') }}" 
               class="block px-3 py-2 text-base font-medium text-gray-700 dark:text-gray-300 hover:text-vp-primary-600 dark:hover:text-vp-primary-400 hover:bg-gray-50 dark:hover:bg-gray-800 rounded-md transition-colors">
                Iniciar Sesión
            </a>
            <a href="{{ route('register') }}" 
               class="block px-3 py-2 text-base font-medium bg-vp-primary-500 text-white hover:bg-vp-primary-600 rounded-md transition-colors text-center">
                Registrarse
            </a>
        </div>
    @endauth
</div>