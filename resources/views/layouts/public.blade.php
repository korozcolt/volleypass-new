<!DOCTYPE html>
<html lang="es" x-data="{ darkMode: false }" :class="{ 'dark': darkMode }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Torneos Públicos') - VolleyPass Sucre</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#eff6ff',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8'
                        }
                    },
                    animation: {
                        'pulse-slow': 'pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                        'bounce-slow': 'bounce 2s infinite',
                    }
                }
            }
        }
    </script>
    @livewireStyles
</head>
<body class="bg-gray-50 dark:bg-gray-900 transition-colors duration-300">
    <!-- Public Header -->
    <header class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Logo -->
                <div class="flex items-center">
                    <a href="/" class="flex items-center space-x-2">
                        <div class="w-8 h-8 bg-gradient-to-br from-blue-600 to-purple-600 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                            </svg>
                        </div>
                        <span class="text-xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                            VolleyPass Sucre
                        </span>
                    </a>
                </div>

                <!-- Navigation -->
                <nav class="hidden md:flex items-center space-x-8">
                    <a href="/" class="text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 font-medium transition-colors">
                        Inicio
                    </a>
                    <a href="/tournaments/public" class="text-blue-600 dark:text-blue-400 font-medium">
                        Torneos
                    </a>
                    <a href="#" class="text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 font-medium transition-colors">
                        Equipos
                    </a>
                    <a href="#" class="text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 font-medium transition-colors">
                        Estadísticas
                    </a>
                </nav>

                <!-- Actions -->
                <div class="flex items-center space-x-4">
                    <button @click="darkMode = !darkMode" class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        <svg x-show="!darkMode" class="w-5 h-5 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                        </svg>
                        <svg x-show="darkMode" class="w-5 h-5 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                    </button>

                    @guest
                    <a href="/login" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                        Iniciar Sesión
                    </a>
                    @endguest

                    @auth
                    <a href="/dashboard/{{ auth()->user()->role ?? 'player' }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                        Mi Dashboard
                    </a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid md:grid-cols-4 gap-8">
                <div class="space-y-4">
                    <div class="flex items-center space-x-2">
                        <div class="w-8 h-8 bg-gradient-to-br from-blue-600 to-purple-600 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                            </svg>
                        </div>
                        <span class="text-xl font-bold text-gray-900 dark:text-white">VolleyPass Sucre</span>
                    </div>
                    <p class="text-gray-600 dark:text-gray-300">
                        Plataforma oficial de la Liga de Voleibol de Sucre, Colombia.
                    </p>
                </div>

                <div>
                    <h3 class="font-semibold text-gray-900 dark:text-white mb-4">Torneos</h3>
                    <ul class="space-y-2 text-gray-600 dark:text-gray-300">
                        <li><a href="#" class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Liga Profesional</a></li>
                        <li><a href="#" class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Copa Departamental</a></li>
                        <li><a href="#" class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Liga Juvenil</a></li>
                        <li><a href="#" class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Torneo Femenino</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="font-semibold text-gray-900 dark:text-white mb-4">Información</h3>
                    <ul class="space-y-2 text-gray-600 dark:text-gray-300">
                        <li><a href="#" class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Reglamentos</a></li>
                        <li><a href="#" class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Calendario</a></li>
                        <li><a href="#" class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Resultados</a></li>
                        <li><a href="#" class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Estadísticas</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="font-semibold text-gray-900 dark:text-white mb-4">Contacto</h3>
                    <ul class="space-y-2 text-gray-600 dark:text-gray-300">
                        <li>📧 info@volleypasssucre.com</li>
                        <li>📱 +57 300 123 4567</li>
                        <li>📍 Sucre, Colombia</li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-gray-200 dark:border-gray-700 mt-8 pt-8 text-center text-gray-600 dark:text-gray-300">
                <p>&copy; 2024 VolleyPass Sucre. Todos los derechos reservados.</p>
            </div>
        </div>
    </footer>

    @livewireScripts
</body>
</html>
