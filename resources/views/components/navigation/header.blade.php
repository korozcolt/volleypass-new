<header class="bg-white dark:bg-gray-900 shadow-sm sticky top-0 z-50 transition-colors duration-200"
        x-data="headerData()"
        x-init="init()">
    
    <!-- Desktop Navigation -->
    <nav class="hidden lg:block max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <!-- Logo -->
            <div class="flex items-center">
                <a href="{{ route('home') }}" class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-vp-primary-500 to-vp-secondary-500 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10" fill="none" stroke="currentColor" stroke-width="2"/>
                            <path d="M2 12h20M12 2v20M7 7l10 10M17 7L7 17" stroke="currentColor" stroke-width="1"/>
                        </svg>
                    </div>
                    <div>
                        <span class="font-display font-bold text-xl text-gray-900 dark:text-white">
                            VolleyPass
                        </span>
                        <div class="text-xs text-gray-500 dark:text-gray-400 -mt-1">
                            Liga de Sucre
                        </div>
                    </div>
                </a>
            </div>

            <!-- Main Navigation -->
            <div class="hidden lg:flex space-x-8">
                @auth
                    @include('components.navigation.auth-menu')
                @else
                    @include('components.navigation.guest-menu')
                @endauth
            </div>

            <!-- Right Side Actions -->
            <div class="flex items-center space-x-4">
                <!-- Dark Mode Toggle -->
                <button @click="$store.app.toggleDarkMode()" 
                        class="p-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors">
                    <svg x-show="!$store.app.darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                    </svg>
                    <svg x-show="$store.app.darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </button>

                @auth
                    <!-- Notifications -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" 
                                class="p-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors relative">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM10.07 2.82l-.03.03a1.51 1.51 0 000 2.13l1.06 1.06 8.49 8.48a1.51 1.51 0 002.12 0l.03-.03a1.51 1.51 0 000-2.12L12.25 2.88a1.51 1.51 0 00-2.13 0l-.05.05z" />
                            </svg>
                            <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                                3
                            </span>
                        </button>
                        
                        <!-- Notifications Dropdown -->
                        <div x-show="open" 
                             @click.away="open = false"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             class="absolute right-0 mt-2 w-80 bg-white dark:bg-gray-800 rounded-lg shadow-lg ring-1 ring-black ring-opacity-5">
                            @livewire('shared.notifications-dropdown')
                        </div>
                    </div>

                    <!-- User Menu -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" 
                                class="flex items-center space-x-3 p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                            <img class="h-8 w-8 rounded-full object-cover" 
                                 src="{{ auth()->user()->avatar_url ?? '/placeholder.svg?height=32&width=32' }}" 
                                 alt="{{ auth()->user()->name }}">
                            <div class="hidden sm:block text-left">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ auth()->user()->name }}
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ ucfirst(auth()->user()->role) }}
                                </div>
                            </div>
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        
                        <!-- User Dropdown -->
                        <div x-show="open" 
                             @click.away="open = false"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg ring-1 ring-black ring-opacity-5">
                            @include('components.navigation.user-dropdown')
                        </div>
                    </div>
                @else
                    <!-- Guest Actions -->
                    <div class="flex items-center space-x-3">
                        <a href="{{ route('login') }}" 
                           class="text-gray-700 dark:text-gray-300 hover:text-vp-primary-600 dark:hover:text-vp-primary-400 font-medium transition-colors">
                            Iniciar Sesión
                        </a>
                        <a href="{{ route('register') }}" 
                           class="bg-vp-primary-500 text-white px-4 py-2 rounded-lg hover:bg-vp-primary-600 transition-colors font-medium">
                            Registrarse
                        </a>
                    </div>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Mobile Navigation -->
    <div class="lg:hidden">
        <div class="flex items-center justify-between h-16 px-4">
            <!-- Mobile Logo -->
            <a href="{{ route('home') }}" class="flex items-center space-x-2">
                <div class="w-8 h-8 bg-gradient-to-br from-vp-primary-500 to-vp-secondary-500 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10" fill="none" stroke="currentColor" stroke-width="2"/>
                        <path d="M2 12h20M12 2v20M7 7l10 10M17 7L7 17" stroke="currentColor" stroke-width="1"/>
                    </svg>
                </div>
                <span class="font-display font-bold text-lg text-gray-900 dark:text-white">
                    VolleyPass
                </span>
            </a>

            <!-- Mobile Menu Button -->
            <button @click="mobileMenuOpen = !mobileMenuOpen" 
                    class="p-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                <svg x-show="!mobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
                <svg x-show="mobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Mobile Menu -->
        <div x-show="mobileMenuOpen" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-1"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-1"
             class="border-t border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900">
            @include('components.navigation.mobile-menu')
        </div>
    </div>

    <script>
        function headerData() {
            return {
                mobileMenuOpen: false,
                
                init() {
                    // Close mobile menu when clicking outside
                    document.addEventListener('click', (e) => {
                        if (!this.$el.contains(e.target)) {
                            this.mobileMenuOpen = false;
                        }
                    });
                }
            }
        }
    </script>
</header>