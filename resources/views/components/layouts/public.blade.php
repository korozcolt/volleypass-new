@props(['title' => 'VolleyPass Sucre - Torneos en Vivo'])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ darkMode: false }" :class="{ 'dark': darkMode }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <!-- Meta Tags for SEO -->
    <meta name="description" content="Sigue los torneos de voleibol de Sucre en tiempo real. Resultados, estadísticas y clasificaciones actualizadas al instante.">
    <meta name="keywords" content="voleibol, Sucre, torneos, resultados, estadísticas, liga">
    <meta property="og:title" content="VolleyPass Sucre - Torneos en Vivo">
    <meta property="og:description" content="Plataforma oficial para seguir los torneos de voleibol de Sucre">
    <meta property="og:type" content="website">

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
</head>
<body class="font-sans antialiased bg-gray-50 dark:bg-gray-900 transition-colors duration-300">
    <!-- Modern Sports Header - ESPN Style -->
    <header class="sticky top-0 z-50 bg-white/95 dark:bg-gray-900/95 backdrop-blur-sm border-b border-gray-200 dark:border-gray-700 shadow-lg">
        <!-- Top Bar with Live Scores Ticker -->
        <div class="bg-gradient-to-r from-red-600 to-red-700 text-white py-1 overflow-hidden">
            <div class="animate-marquee whitespace-nowrap">
                <span class="inline-flex items-center mx-8">
                    <span class="live-indicator w-2 h-2 bg-white rounded-full mr-2"></span>
                    <strong>EN VIVO:</strong> Torneo Regional - Semifinal: Equipo A vs Equipo B (2-1)
                </span>
                <span class="inline-flex items-center mx-8">
                    <span class="live-indicator w-2 h-2 bg-white rounded-full mr-2"></span>
                    <strong>PRÓXIMO:</strong> Final Nacional - 15:30 hrs
                </span>
                <span class="inline-flex items-center mx-8">
                    <span class="live-indicator w-2 h-2 bg-white rounded-full mr-2"></span>
                    <strong>RESULTADO:</strong> Copa Metropolitana - Equipo C 3-0 Equipo D
                </span>
            </div>
        </div>
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo with Sports Branding -->
                <div class="flex-shrink-0">
                    <a href="{{ route('home') }}" class="flex items-center group">
                        <div class="relative">
                            <div class="w-10 h-10 bg-gradient-to-br from-blue-600 to-purple-600 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                                </svg>
                            </div>
                            <div class="absolute -top-1 -right-1 w-4 h-4 bg-red-500 rounded-full flex items-center justify-center">
                                <span class="text-xs font-bold text-white">!</span>
                            </div>
                        </div>
                        <div class="ml-3">
                            <span class="text-xl font-black text-gray-900 dark:text-white tracking-tight">VolleyPass</span>
                            <div class="text-xs text-gray-500 dark:text-gray-400 font-medium">SUCRE</div>
                        </div>
                    </a>
                </div>

                <!-- Sports Navigation with Icons -->
                <nav class="hidden md:flex space-x-1">
                    <a href="{{ route('home') }}" class="sports-nav-item group relative px-4 py-2 rounded-lg text-sm font-semibold text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-all duration-300">
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-2 group-hover:scale-110 transition-transform" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2l3.09 6.26L22 9l-5 4.87 1.18 6.88L12 17.77l-6.18 2.98L7 14.87 2 9l6.91-1.74L12 2z"/>
                            </svg>
                            Torneos
                        </span>
                    </a>
                    <a href="{{ route('public.teams') }}" class="sports-nav-item group relative px-4 py-2 rounded-lg text-sm font-semibold text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-all duration-300">
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-2 group-hover:scale-110 transition-transform" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M16 4c0-1.11.89-2 2-2s2 .89 2 2-.89 2-2 2-2-.89-2-2zm4 18v-6h2.5l-2.54-7.63A1.5 1.5 0 0 0 18.5 7H17c-.8 0-1.5.7-1.5 1.5v6c0 .8.7 1.5 1.5 1.5h1v6h2zM12.5 11.5c.83 0 1.5-.67 1.5-1.5s-.67-1.5-1.5-1.5S11 9.17 11 10s.67 1.5 1.5 1.5zM5.5 6c1.11 0 2-.89 2-2s-.89-2-2-2-2 .89-2 2 .89 2 2 2zm2 16v-6H9V9.5C9 8.67 8.33 8 7.5 8S6 8.67 6 9.5V16H7.5v6h2zm7.5-6v6h2v-6h-2z"/>
                            </svg>
                            Equipos
                        </span>
                    </a>
                    <a href="{{ route('public.standings') }}" class="sports-nav-item group relative px-4 py-2 rounded-lg text-sm font-semibold text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-all duration-300">
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-2 group-hover:scale-110 transition-transform" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM9 17H7v-7h2v7zm4 0h-2V7h2v10zm4 0h-2v-4h2v4z"/>
                            </svg>
                            Posiciones
                        </span>
                    </a>
                    <a href="{{ route('public.matches') }}" class="sports-nav-item group relative px-4 py-2 rounded-lg text-sm font-semibold text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-all duration-300">
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-2 group-hover:scale-110 transition-transform" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM7 10h5v5H7z"/>
                            </svg>
                            Partidos
                        </span>
                    </a>
                    <a href="{{ route('about') }}" class="sports-nav-item group relative px-4 py-2 rounded-lg text-sm font-semibold text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-all duration-300">
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-2 group-hover:scale-110 transition-transform" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/>
                            </svg>
                            Acerca de
                        </span>
                    </a>
                </nav>

                <!-- Right side with Quick Actions -->
                <div class="flex items-center space-x-3">
                    <!-- Live Score Quick View -->
                    <div class="hidden lg:flex items-center space-x-2 px-3 py-1 bg-red-50 dark:bg-red-900/20 rounded-lg border border-red-200 dark:border-red-800">
                        <div class="live-indicator w-2 h-2 bg-red-500 rounded-full"></div>
                        <span class="text-xs font-semibold text-red-700 dark:text-red-400">2 EN VIVO</span>
                    </div>
                    
                    <!-- Dark mode toggle -->
                    <button
                        @click="darkMode = !darkMode"
                        class="p-2 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-all duration-300"
                        :aria-label="darkMode ? 'Activar modo claro' : 'Activar modo oscuro'"
                    >
                        <svg x-show="!darkMode" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M17.293 13.293A8 8 0 716.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                        </svg>
                        <svg x-show="darkMode" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 2L13.09 8.26L20 9L14 14.74L15.18 21.02L10 17.77L4.82 21.02L6 14.74L0 9L6.91 8.26L10 2Z"></path>
                        </svg>
                    </button>

                    <!-- Login button with sports styling -->
                    <a href="/login" class="bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white px-6 py-2 rounded-xl text-sm font-bold transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl">
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                            </svg>
                            Iniciar Sesión
                        </span>
                    </a>

                    <!-- Mobile menu button -->
                    <button class="md:hidden p-2 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-all duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="pt-16 min-h-screen">
        {{ $slot }}
    </main>

    <!-- Modern Sports Footer -->
    <footer class="bg-gradient-to-br from-gray-900 via-gray-800 to-black text-white relative overflow-hidden">
        <!-- Background Pattern -->
        <div class="absolute inset-0 opacity-5">
            <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,<svg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"><g fill="none" fill-rule="evenodd"><g fill="%23ffffff" fill-opacity="0.1"><circle cx="30" cy="30" r="4"/></g></g></svg>'); background-size: 60px 60px;"></div>
        </div>
        
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <!-- Main Footer Content -->
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-12">
                <!-- Brand Section -->
                <div class="lg:col-span-2">
                    <div class="flex items-center mb-6">
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-600 to-purple-600 rounded-xl flex items-center justify-center mr-4">
                            <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-2xl font-black tracking-tight">VolleyPass</h2>
                            <p class="text-blue-400 font-semibold text-sm">SUCRE • BOLIVIA</p>
                        </div>
                    </div>
                    
                    <p class="text-gray-300 text-lg leading-relaxed mb-8 max-w-md">
                        La plataforma deportiva más avanzada de Bolivia. Conectamos equipos, jugadores y aficionados en una experiencia única de voleibol profesional.
                    </p>
                    
                    <!-- Social Media with Sports Style -->
                    <div class="flex space-x-4">
                        <a href="#" class="group w-12 h-12 bg-white/10 backdrop-blur-sm rounded-xl flex items-center justify-center hover:bg-blue-600 transition-all duration-300 transform hover:scale-110">
                            <svg class="w-5 h-5 text-white group-hover:scale-110 transition-transform" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/>
                            </svg>
                        </a>
                        <a href="#" class="group w-12 h-12 bg-white/10 backdrop-blur-sm rounded-xl flex items-center justify-center hover:bg-purple-600 transition-all duration-300 transform hover:scale-110">
                            <svg class="w-5 h-5 text-white group-hover:scale-110 transition-transform" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12.017 0C5.396 0 .029 5.367.029 11.987c0 5.079 3.158 9.417 7.618 11.174-.105-.949-.199-2.403.041-3.439.219-.937 1.406-5.957 1.406-5.957s-.359-.72-.359-1.781c0-1.663.967-2.911 2.168-2.911 1.024 0 1.518.769 1.518 1.688 0 1.029-.653 2.567-.992 3.992-.285 1.193.6 2.165 1.775 2.165 2.128 0 3.768-2.245 3.768-5.487 0-2.861-2.063-4.869-5.008-4.869-3.41 0-5.409 2.562-5.409 5.199 0 1.033.394 2.143.889 2.741.099.12.112.225.085.345-.09.375-.293 1.199-.334 1.363-.053.225-.172.271-.402.165-1.495-.69-2.433-2.878-2.433-4.646 0-3.776 2.748-7.252 7.92-7.252 4.158 0 7.392 2.967 7.392 6.923 0 4.135-2.607 7.462-6.233 7.462-1.214 0-2.357-.629-2.75-1.378l-.748 2.853c-.271 1.043-1.002 2.35-1.492 3.146C9.57 23.812 10.763 24.009 12.017 24.009c6.624 0 11.99-5.367 11.99-11.988C24.007 5.367 18.641.001.012.001z"/>
                            </svg>
                        </a>
                        <a href="#" class="group w-12 h-12 bg-white/10 backdrop-blur-sm rounded-xl flex items-center justify-center hover:bg-red-600 transition-all duration-300 transform hover:scale-110">
                            <svg class="w-5 h-5 text-white group-hover:scale-110 transition-transform" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                            </svg>
                        </a>
                        <a href="#" class="group w-12 h-12 bg-white/10 backdrop-blur-sm rounded-xl flex items-center justify-center hover:bg-green-600 transition-all duration-300 transform hover:scale-110">
                            <svg class="w-5 h-5 text-white group-hover:scale-110 transition-transform" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488"/>
                            </svg>
                        </a>
                    </div>
                </div>

                <!-- Quick Navigation -->
                <div>
                    <h3 class="text-xl font-bold mb-6 flex items-center">
                        <div class="sports-divider mr-3"></div>
                        Navegación
                    </h3>
                    <ul class="space-y-4">
                        <li><a href="{{ route('home') }}" class="text-gray-300 hover:text-blue-400 transition-colors duration-300 flex items-center group">
                            <svg class="w-4 h-4 mr-3 group-hover:scale-110 transition-transform" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2l3.09 6.26L22 9l-5 4.87 1.18 6.88L12 17.77l-6.18 2.98L7 14.87 2 9l6.91-1.74L12 2z"/>
                            </svg>
                            Torneos en Vivo
                        </a></li>
                        <li><a href="{{ route('about') }}" class="text-gray-300 hover:text-blue-400 transition-colors duration-300 flex items-center group">
                            <svg class="w-4 h-4 mr-3 group-hover:scale-110 transition-transform" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/>
                            </svg>
                            Acerca de Nosotros
                        </a></li>
                        <li><a href="{{ route('public.teams') }}" class="text-gray-300 hover:text-blue-400 transition-colors duration-300 flex items-center group">
                            <svg class="w-4 h-4 mr-3 group-hover:scale-110 transition-transform" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M16 4c0-1.11.89-2 2-2s2 .89 2 2-.89 2-2 2-2-.89-2-2zm4 18v-6h2.5l-2.54-7.63A1.5 1.5 0 0 0 18.5 7H17c-.8 0-1.5.7-1.5 1.5v6c0 .8.7 1.5 1.5 1.5h1v6h2z"/>
                            </svg>
                            Equipos Registrados
                        </a></li>
                        <li><a href="{{ route('public.stats') }}" class="text-gray-300 hover:text-blue-400 transition-colors duration-300 flex items-center group">
                            <svg class="w-4 h-4 mr-3 group-hover:scale-110 transition-transform" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM9 17H7v-7h2v7zm4 0h-2V7h2v10zm4 0h-2v-4h2v4z"/>
                            </svg>
                            Estadísticas
                        </a></li>
                    </ul>
                </div>

                <!-- Contact Information -->
                <div>
                    <h3 class="text-xl font-bold mb-6 flex items-center">
                        <div class="sports-divider mr-3"></div>
                        Contacto
                    </h3>
                    <div class="space-y-4">
                        <div class="flex items-start group">
                            <div class="w-10 h-10 bg-blue-600/20 rounded-lg flex items-center justify-center mr-4 group-hover:bg-blue-600/30 transition-colors">
                                <svg class="w-5 h-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path>
                                    <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-white font-semibold">Email</p>
                                <p class="text-gray-300">info@volleypass.com</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start group">
                            <div class="w-10 h-10 bg-purple-600/20 rounded-lg flex items-center justify-center mr-4 group-hover:bg-purple-600/30 transition-colors">
                                <svg class="w-5 h-5 text-purple-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-white font-semibold">Ubicación</p>
                                <p class="text-gray-300">Sucre, Bolivia</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start group">
                            <div class="w-10 h-10 bg-green-600/20 rounded-lg flex items-center justify-center mr-4 group-hover:bg-green-600/30 transition-colors">
                                <svg class="w-5 h-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-white font-semibold">Teléfono</p>
                                <p class="text-gray-300">+591 123 456 789</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bottom Bar -->
            <div class="border-t border-gray-700 mt-12 pt-8">
                <div class="flex flex-col lg:flex-row justify-between items-center">
                    <div class="flex items-center space-x-6 mb-4 lg:mb-0">
                        <p class="text-gray-400">&copy; {{ date('Y') }} VolleyPass Sucre. Todos los derechos reservados.</p>
                    </div>
                    
                    <div class="flex items-center space-x-6 text-sm">
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">Términos de Servicio</a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">Política de Privacidad</a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">Soporte</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    @livewireScripts

    <!-- Auto-refresh script -->
    <script>
        // Auto-refresh every 30 seconds for live updates
        setInterval(() => {
            if (typeof Livewire !== 'undefined') {
                window.dispatchEvent(new CustomEvent('refreshData'));
            }
        }, 30000);

        // Smooth scroll for anchor links
        document.addEventListener('DOMContentLoaded', function() {
            const links = document.querySelectorAll('a[href^="#"]');
            links.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({ behavior: 'smooth' });
                    }
                });
            });
        });
    </script>
</body>
</html>