<!DOCTYPE html>
<html lang="es" x-data="{ darkMode: false }" :class="{ 'dark': darkMode }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Dashboard Jugadora' }} - VolleyPass Sucre</title>
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
                    }
                }
            }
        }
    </script>
    @livewireStyles
</head>
<body class="bg-gray-50 dark:bg-gray-900 transition-colors duration-300">
    <!-- Mobile Navigation -->
    <div x-data="{ mobileMenuOpen: false }" class="lg:hidden">
        <!-- Mobile menu button -->
        <div class="fixed top-0 left-0 right-0 z-50 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-4 py-3">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <div class="w-8 h-8 bg-gradient-to-br from-blue-600 to-purple-600 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                        </svg>
                    </div>
                    <span class="text-lg font-bold text-gray-900 dark:text-white">VolleyPass</span>
                </div>
                <button @click="mobileMenuOpen = !mobileMenuOpen" class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Mobile menu -->
        <div x-show="mobileMenuOpen" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 transform -translate-x-full"
             x-transition:enter-end="opacity-100 transform translate-x-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 transform translate-x-0"
             x-transition:leave-end="opacity-0 transform -translate-x-full"
             class="fixed inset-0 z-40 lg:hidden">
            <div class="fixed inset-0 bg-gray-600 bg-opacity-75" @click="mobileMenuOpen = false"></div>
            <div class="fixed top-0 left-0 w-64 h-full bg-white dark:bg-gray-800 shadow-xl">
                <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-pink-500 to-purple-600 rounded-full flex items-center justify-center">
                            <span class="text-white font-bold">{{ substr(Auth::user()->name ?? 'J', 0, 1) }}</span>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900 dark:text-white">{{ Auth::user()->name ?? 'Jugadora' }}</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Jugadora</p>
                        </div>
                    </div>
                </div>
                @livewire('shared.player-navigation')
            </div>
        </div>
    </div>

    <!-- Desktop Layout -->
    <div class="lg:flex">
        <!-- Desktop Sidebar -->
        <div class="hidden lg:flex lg:flex-shrink-0">
            <div class="flex flex-col w-64 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700">
                <!-- Logo -->
                <div class="flex items-center h-16 px-6 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center space-x-2">
                        <div class="w-8 h-8 bg-gradient-to-br from-blue-600 to-purple-600 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                            </svg>
                        </div>
                        <span class="text-lg font-bold text-gray-900 dark:text-white">VolleyPass</span>
                    </div>
                </div>

                <!-- User Profile -->
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center space-x-3">
                        <div class="w-12 h-12 bg-gradient-to-br from-pink-500 to-purple-600 rounded-full flex items-center justify-center">
                            <span class="text-white font-bold text-lg">{{ substr(Auth::user()->name ?? 'J', 0, 1) }}</span>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900 dark:text-white">{{ Auth::user()->name ?? 'Jugadora' }}</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Jugadora Activa</p>
                        </div>
                    </div>
                </div>

                <!-- Navigation -->
                @livewire('shared.player-navigation')
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 lg:ml-0">
            <!-- Top Bar -->
            <div class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-6 py-4 mt-16 lg:mt-0">
                <div class="flex items-center justify-between">
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{ $title ?? 'Dashboard' }}
                    </h1>
                    <div class="flex items-center space-x-4">
                        @livewire('shared.notification-widget')
                        <button @click="darkMode = !darkMode" class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                            <svg x-show="!darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                            </svg>
                            <svg x-show="darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Page Content -->
            <main class="p-6">
                {{ $slot }}
            </main>
        </div>
    </div>

    @livewireScripts
    
    <!-- Notification System -->
    <div x-data="{ notifications: [] }" 
         @show-notification.window="notifications.push($event.detail); setTimeout(() => notifications.shift(), 5000)"
         class="fixed top-4 right-4 z-50 space-y-2">
        <template x-for="notification in notifications" :key="notification.id">
            <div x-show="true" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform translate-x-full"
                 x-transition:enter-end="opacity-100 transform translate-x-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 transform translate-x-0"
                 x-transition:leave-end="opacity-0 transform translate-x-full"
                 class="max-w-sm w-full bg-white dark:bg-gray-800 shadow-lg rounded-lg pointer-events-auto ring-1 ring-black ring-opacity-5">
                <div class="p-4">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg x-show="notification.type === 'success'" class="h-6 w-6 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-3 w-0 flex-1 pt-0.5">
                            <p class="text-sm font-medium text-gray-900 dark:text-white" x-text="notification.message"></p>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>
</body>
</html>
