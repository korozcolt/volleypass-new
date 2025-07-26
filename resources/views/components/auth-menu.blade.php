<!-- Authenticated User Navigation Menu -->
@php
    $user = auth()->user();
    $role = $user->role ?? 'player';
    $mobile = $mobile ?? false;
    $linkClass = $mobile 
        ? 'block py-2 text-sm text-gray-700 dark:text-gray-300 hover:text-vp-primary-600 dark:hover:text-vp-primary-400 transition-colors duration-200'
        : 'text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-vp-primary-600 dark:hover:text-vp-primary-400 transition-colors duration-200';
@endphp

<!-- Role-based Navigation Links -->
@if($role === 'player')
    <!-- Player Navigation -->
    <a href="{{ route('player.dashboard') }}" class="{{ $linkClass }} {{ request()->routeIs('player.dashboard') ? 'text-vp-primary-600 dark:text-vp-primary-400' : '' }}">
        @if($mobile)
            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v6a2 2 0 01-2 2H10a2 2 0 01-2-2V5z"></path>
            </svg>
        @endif
        Dashboard
    </a>
    
    <a href="{{ route('matches.index') }}" class="{{ $linkClass }} {{ request()->routeIs('matches.*') ? 'text-vp-primary-600 dark:text-vp-primary-400' : '' }}">
        @if($mobile)
            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
            </svg>
        @endif
        Mis Partidos
    </a>
    
    <a href="{{ route('player.stats') }}" class="{{ $linkClass }} {{ request()->routeIs('player.stats') ? 'text-vp-primary-600 dark:text-vp-primary-400' : '' }}">
        @if($mobile)
            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
            </svg>
        @endif
        Estadísticas
    </a>
    
    <a href="{{ route('teams.index') }}" class="{{ $linkClass }} {{ request()->routeIs('teams.*') ? 'text-vp-primary-600 dark:text-vp-primary-400' : '' }}">
        @if($mobile)
            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
        @endif
        Mi Equipo
    </a>

@elseif($role === 'coach')
    <!-- Coach Navigation -->
    <a href="{{ route('coach.dashboard') }}" class="{{ $linkClass }} {{ request()->routeIs('coach.dashboard') ? 'text-vp-primary-600 dark:text-vp-primary-400' : '' }}">
        @if($mobile)
            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v6a2 2 0 01-2 2H10a2 2 0 01-2-2V5z"></path>
            </svg>
        @endif
        Dashboard
    </a>
    
    <a href="{{ route('coach.team') }}" class="{{ $linkClass }} {{ request()->routeIs('coach.team*') ? 'text-vp-primary-600 dark:text-vp-primary-400' : '' }}">
        @if($mobile)
            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
        @endif
        Gestión de Equipo
    </a>
    
    <a href="{{ route('matches.index') }}" class="{{ $linkClass }} {{ request()->routeIs('matches.*') ? 'text-vp-primary-600 dark:text-vp-primary-400' : '' }}">
        @if($mobile)
            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 0h6m-6 0l-2 9a2 2 0 002 2h8a2 2 0 002-2l-2-9m-6 0V7"></path>
            </svg>
        @endif
        Partidos
    </a>
    
    <a href="{{ route('coach.tournaments') }}" class="{{ $linkClass }} {{ request()->routeIs('coach.tournaments*') ? 'text-vp-primary-600 dark:text-vp-primary-400' : '' }}">
        @if($mobile)
            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        @endif
        Torneos
    </a>
    
    <a href="{{ route('coach.reports') }}" class="{{ $linkClass }} {{ request()->routeIs('coach.reports*') ? 'text-vp-primary-600 dark:text-vp-primary-400' : '' }}">
        @if($mobile)
            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
            </svg>
        @endif
        Reportes
    </a>

@elseif($role === 'referee')
    <!-- Referee Navigation -->
    <a href="{{ route('referee.dashboard') }}" class="{{ $linkClass }} {{ request()->routeIs('referee.dashboard') ? 'text-vp-primary-600 dark:text-vp-primary-400' : '' }}">
        @if($mobile)
            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v6a2 2 0 01-2 2H10a2 2 0 01-2-2V5z"></path>
            </svg>
        @endif
        Dashboard
    </a>
    
    <a href="{{ route('referee.matches') }}" class="{{ $linkClass }} {{ request()->routeIs('referee.matches*') ? 'text-vp-primary-600 dark:text-vp-primary-400' : '' }}">
        @if($mobile)
            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
            </svg>
        @endif
        Mis Partidos
    </a>
    
    <a href="{{ route('referee.tournaments') }}" class="{{ $linkClass }} {{ request()->routeIs('referee.tournaments*') ? 'text-vp-primary-600 dark:text-vp-primary-400' : '' }}">
        @if($mobile)
            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
        @endif
        Torneos
    </a>
    
    <a href="{{ route('schedule') }}" class="{{ $linkClass }} {{ request()->routeIs('schedule*') ? 'text-vp-primary-600 dark:text-vp-primary-400' : '' }}">
        @if($mobile)
            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 0h6m-6 0l-2 9a2 2 0 002 2h8a2 2 0 002-2l-2-9m-6 0V7"></path>
            </svg>
        @endif
        Calendario
    </a>
    
    <a href="{{ route('settings') }}" class="{{ $linkClass }} {{ request()->routeIs('settings*') ? 'text-vp-primary-600 dark:text-vp-primary-400' : '' }}">
        @if($mobile)
            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
            </svg>
        @endif
        Configuración
    </a>

@elseif($role === 'medical')
    <!-- Medical Staff Navigation -->
    <a href="{{ route('medical.dashboard') }}" class="{{ $linkClass }} {{ request()->routeIs('medical.dashboard') ? 'text-vp-primary-600 dark:text-vp-primary-400' : '' }}">
        @if($mobile)
            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v6a2 2 0 01-2 2H10a2 2 0 01-2-2V5z"></path>
            </svg>
        @endif
        Dashboard
    </a>
    
    <a href="{{ route('medical.players') }}" class="{{ $linkClass }} {{ request()->routeIs('medical.players*') ? 'text-vp-primary-600 dark:text-vp-primary-400' : '' }}">
        @if($mobile)
            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>
        @endif
        Jugadoras
    </a>
    
    <a href="{{ route('medical.reports') }}" class="{{ $linkClass }} {{ request()->routeIs('medical.reports*') ? 'text-vp-primary-600 dark:text-vp-primary-400' : '' }}">
        @if($mobile)
            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        @endif
        Reportes
    </a>
    
    <a href="{{ route('settings') }}" class="{{ $linkClass }} {{ request()->routeIs('settings*') ? 'text-vp-primary-600 dark:text-vp-primary-400' : '' }}">
        @if($mobile)
            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
        @endif
        Configuración
    </a>
    


@elseif($role === 'admin')
    <!-- Admin Navigation -->
    <a href="{{ route('admin.dashboard') }}" class="{{ $linkClass }} {{ request()->routeIs('admin.dashboard') ? 'text-vp-primary-600 dark:text-vp-primary-400' : '' }}">
        @if($mobile)
            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v6a2 2 0 01-2 2H10a2 2 0 01-2-2V5z"></path>
            </svg>
        @endif
        Dashboard
    </a>
    
    <a href="{{ route('admin.users') }}" class="{{ $linkClass }} {{ request()->routeIs('admin.users*') ? 'text-vp-primary-600 dark:text-vp-primary-400' : '' }}">
        @if($mobile)
            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
            </svg>
        @endif
        Usuarios
    </a>
    
    <a href="{{ route('admin.teams') }}" class="{{ $linkClass }} {{ request()->routeIs('admin.teams*') ? 'text-vp-primary-600 dark:text-vp-primary-400' : '' }}">
        @if($mobile)
            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
        @endif
        Equipos
    </a>
    
    <a href="{{ route('admin.matches') }}" class="{{ $linkClass }} {{ request()->routeIs('admin.matches*') ? 'text-vp-primary-600 dark:text-vp-primary-400' : '' }}">
        @if($mobile)
            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 0h6m-6 0l-2 9a2 2 0 002 2h8a2 2 0 002-2l-2-9m-6 0V7"></path>
            </svg>
        @endif
        Partidos
    </a>
    
    <a href="{{ route('admin.league') }}" class="{{ $linkClass }} {{ request()->routeIs('admin.league*') ? 'text-vp-primary-600 dark:text-vp-primary-400' : '' }}">
        @if($mobile)
            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
            </svg>
        @endif
        Liga
    </a>
    
    <a href="{{ route('admin.settings') }}" class="{{ $linkClass }} {{ request()->routeIs('admin.settings*') ? 'text-vp-primary-600 dark:text-vp-primary-400' : '' }}">
        @if($mobile)
            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
            </svg>
        @endif
        Configuración
    </a>
@endif

<!-- Common Authenticated Links -->
@if(!$mobile)
    <div class="h-4 border-l border-gray-300 dark:border-gray-600"></div>
@else
    <div class="border-t border-gray-200 dark:border-gray-700 my-2"></div>
@endif

<a href="{{ route('welcome') }}" class="{{ $linkClass }}">
    @if($mobile)
        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
        </svg>
    @endif
    Inicio Público
</a>

<a href="#" class="{{ $linkClass }}">
    @if($mobile)
        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM4 19h10a2 2 0 002-2V7a2 2 0 00-2-2H4a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
        </svg>
    @endif
    Notificaciones
    @if($mobile && isset($user) && $user->unread_notifications_count > 0)
        <span class="inline-flex items-center justify-center w-4 h-4 ml-2 text-xs font-bold text-white bg-red-500 rounded-full">
            {{ $user->unread_notifications_count > 9 ? '9+' : $user->unread_notifications_count }}
        </span>
    @endif
</a>