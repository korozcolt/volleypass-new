<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ darkMode: false }" :class="{ 'dark': darkMode }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'VolleyPass Sucre - Torneos en Vivo' }}</title>

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
    <!-- Header -->
    <header class="fixed top-0 w-full bg-white/90 dark:bg-gray-900/90 backdrop-blur-md z-50 border-b border-gray-200 dark:border-gray-700 transition-all duration-300">
        <div class="container mx-auto px-4 lg:px-6 h-16 flex items-center justify-between">
            <!-- Logo -->
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-gradient-to-br from-blue-600 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
                <div>
                    <h1 class="text-xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                        VolleyPass Sucre
                    </h1>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Torneos en Vivo</p>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="hidden md:flex items-center space-x-6">
                <a href="{{ route('home') }}" class="text-sm font-medium text-blue-600 hover:text-blue-700 transition-colors">
                    Torneos
                </a>
                <a href="{{ route('about') }}" class="text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-blue-600 transition-colors">
                    Acerca de
                </a>
                <a href="#" class="text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-blue-600 transition-colors">
                    Equipos
                </a>
                <a href="#" class="text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-blue-600 transition-colors">
                    Estadísticas
                </a>
            </nav>

            <!-- Actions -->
            <div class="flex items-center space-x-3">
                <!-- Dark Mode Toggle -->
                <button
                    @click="darkMode = !darkMode"
                    class="w-9 h-9 rounded-lg bg-gray-100 dark:bg-gray-800 flex items-center justify-center hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors"
                    :aria-label="darkMode ? 'Activar modo claro' : 'Activar modo oscuro'"
                >
                    <svg x-show="!darkMode" class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                    </svg>
                    <svg x-show="darkMode" class="w-4 h-4 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </button>

                <!-- Login Button -->
                <a href="/login" class="hidden sm:inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-600 to-purple-600 text-white text-sm font-medium rounded-lg hover:from-blue-700 hover:to-purple-700 transition-all duration-200 shadow-lg hover:shadow-xl">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                    </svg>
                    Ingresar
                </a>

                <!-- Mobile Menu Button -->
                <button class="md:hidden w-9 h-9 rounded-lg bg-gray-100 dark:bg-gray-800 flex items-center justify-center hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors">
                    <svg class="w-5 h-5 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="pt-16 min-h-screen">
        {{ $slot }}
    </main>

    <!-- Footer -->
    <footer class="bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700 py-12">
        <div class="container mx-auto px-4 lg:px-6">
            <div class="grid md:grid-cols-4 gap-8">
                <div class="space-y-4">
                    <div class="flex items-center space-x-2">
                        <div class="w-8 h-8 bg-gradient-to-br from-blue-600 to-purple-600 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        </div>
                        <span class="text-lg font-bold text-gray-900 dark:text-white">VolleyPass Sucre</span>
                    </div>
                    <p class="text-gray-600 dark:text-gray-400 text-sm">
                        Plataforma oficial de la Liga de Voleibol de Sucre. Transparencia y modernidad en el deporte.
                    </p>
                </div>

                <div>
                    <h3 class="font-semibold text-gray-900 dark:text-white mb-4">Torneos</h3>
                    <ul class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                        <li><a href="#" class="hover:text-blue-600 transition-colors">Liga Federada</a></li>
                        <li><a href="#" class="hover:text-blue-600 transition-colors">Liga Alterna</a></li>
                        <li><a href="#" class="hover:text-blue-600 transition-colors">Copa Sucre</a></li>
                        <li><a href="#" class="hover:text-blue-600 transition-colors">Juveniles</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="font-semibold text-gray-900 dark:text-white mb-4">Información</h3>
                    <ul class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                        <li><a href="#" class="hover:text-blue-600 transition-colors">Reglamentos</a></li>
                        <li><a href="#" class="hover:text-blue-600 transition-colors">Calendario</a></li>
                        <li><a href="#" class="hover:text-blue-600 transition-colors">Estadísticas</a></li>
                        <li><a href="#" class="hover:text-blue-600 transition-colors">Noticias</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="font-semibold text-gray-900 dark:text-white mb-4">Contacto</h3>
                    <ul class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                        <li>Liga de Voleibol Sucre</li>
                        <li>Sucre, Colombia</li>
                        <li>info@volleypasssucre.com</li>
                        <li>+57 300 123 4567</li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-gray-200 dark:border-gray-700 mt-8 pt-8 text-center">
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    © {{ date('Y') }} VolleyPass Sucre. Todos los derechos reservados.
                </p>
            </div>
        </div>
    </footer>

    @livewireScripts

    <!-- Auto-refresh script -->
    <script>
        // Auto-refresh every 30 seconds for live updates
        setInterval(() => {
            if (typeof Livewire !== 'undefined') {
                Livewire.emit('refreshData');
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
