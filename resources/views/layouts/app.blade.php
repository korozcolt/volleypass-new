<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ darkMode: false }" :class="{ 'dark': darkMode }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="VolleyPass - Liga de Voleibol de Sucre, Colombia. Plataforma digital para la gestión integral de equipos, jugadoras y competencias.">
    <meta name="keywords" content="voleibol, sucre, colombia, liga, deportes, equipos, jugadoras">
    <meta name="author" content="VolleyPass Software">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="{{ $title ?? 'VolleyPass - Liga de Voleibol de Sucre' }}">
    <meta property="og:description" content="Plataforma digital oficial para la Liga de Voleibol de Sucre, Colombia.">
    <meta property="og:image" content="{{ asset('images/og-image.jpg') }}">
    
    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ url()->current() }}">
    <meta property="twitter:title" content="{{ $title ?? 'VolleyPass - Liga de Voleibol de Sucre' }}">
    <meta property="twitter:description" content="Plataforma digital oficial para la Liga de Voleibol de Sucre, Colombia.">
    <meta property="twitter:image" content="{{ asset('images/og-image.jpg') }}">
    
    <title>{{ $title ?? \App\Models\SystemConfiguration::getValue('app.name', 'VolleyPass') }} - Liga de Voleibol de Sucre</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ \App\Models\SystemConfiguration::getValue('branding.favicon', asset('favicon.ico')) }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest.php') }}">
    
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.tsx'])
    
    <!-- Livewire Styles -->
    @livewireStyles
    
    <!-- Alpine.js Global Stores -->
    <script>
        document.addEventListener('alpine:init', () => {
            // Global App Store
            Alpine.store('app', {
                darkMode: false,
                sidebarOpen: false,
                loading: false,
                
                toggleDarkMode() {
                    this.darkMode = !this.darkMode;
                },
                
                toggleSidebar() {
                    this.sidebarOpen = !this.sidebarOpen;
                },
                
                setLoading(state) {
                    this.loading = state;
                }
            });
            
            // Notifications Store
            Alpine.store('notifications', {
                items: [],
                
                add(notification) {
                    const id = Date.now();
                    this.items.push({ id, ...notification });
                    
                    // Auto remove after 5 seconds
                    setTimeout(() => {
                        this.remove(id);
                    }, 5000);
                },
                
                remove(id) {
                    this.items = this.items.filter(item => item.id !== id);
                },
                
                clear() {
                    this.items = [];
                }
            });
            
            // Live Matches Store
            Alpine.store('liveMatches', {
                matches: [],
                connected: false,
                
                init() {
                    // Initialize WebSocket connection for live updates
                    this.connectWebSocket();
                },
                
                connectWebSocket() {
                    // WebSocket implementation will be added later
                    this.connected = true;
                },
                
                updateMatch(matchData) {
                    const index = this.matches.findIndex(m => m.id === matchData.id);
                    if (index !== -1) {
                        this.matches[index] = matchData;
                    } else {
                        this.matches.push(matchData);
                    }
                }
            });
        });
    </script>
</head>

<body class="font-body antialiased bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 transition-colors duration-200"
      x-data="{ 
          showNotifications: false,
          init() {
              // Initialize global app functionality
              this.$store.liveMatches.init();
          }
      }"
      x-init="init()">
    
    <!-- Loading Overlay -->
    <div x-show="$store.app.loading" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm">
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-xl">
            <div class="flex items-center space-x-3">
                <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-vp-primary-500"></div>
                <span class="text-gray-900 dark:text-gray-100 font-medium">Cargando...</span>
            </div>
        </div>
    </div>
    
    <!-- Notification System -->
    <div class="fixed top-4 right-4 z-40 space-y-2" x-data="{ notifications: $store.notifications.items }">
        <template x-for="notification in notifications" :key="notification.id">
            <div x-show="true"
                 x-transition:enter="transition ease-out duration-300 transform"
                 x-transition:enter-start="translate-x-full opacity-0"
                 x-transition:enter-end="translate-x-0 opacity-100"
                 x-transition:leave="transition ease-in duration-200 transform"
                 x-transition:leave-start="translate-x-0 opacity-100"
                 x-transition:leave-end="translate-x-full opacity-0"
                 class="max-w-sm w-full bg-white dark:bg-gray-800 shadow-lg rounded-lg pointer-events-auto ring-1 ring-black ring-opacity-5 overflow-hidden">
                <div class="p-4">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <!-- Success Icon -->
                            <svg x-show="notification.type === 'success'" class="h-6 w-6 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <!-- Error Icon -->
                            <svg x-show="notification.type === 'error'" class="h-6 w-6 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
                            </svg>
                            <!-- Warning Icon -->
                            <svg x-show="notification.type === 'warning'" class="h-6 w-6 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
                            </svg>
                            <!-- Info Icon -->
                            <svg x-show="notification.type === 'info'" class="h-6 w-6 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-3 w-0 flex-1 pt-0.5">
                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100" x-text="notification.title"></p>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400" x-text="notification.message"></p>
                        </div>
                        <div class="ml-4 flex-shrink-0 flex">
                            <button @click="$store.notifications.remove(notification.id)"
                                    class="bg-white dark:bg-gray-800 rounded-md inline-flex text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-vp-primary-500">
                                <span class="sr-only">Cerrar</span>
                                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>
    
    <!-- Main Application Layout -->
    <div class="min-h-screen flex flex-col">
        <!-- Header -->
        @include('components.navigation.header')
        
        <!-- Main Content -->
        <main class="flex-1">
            {{ $slot }}
        </main>
        
        <!-- Footer -->
        @include('components.navigation.footer')
    </div>
    
    <!-- Livewire Scripts -->
    @livewireScripts
    
    <!-- Alpine.js is included with Livewire -->
    
    <!-- Custom Scripts -->
    <script>
        // Global utility functions
        window.VolleyPass = {
            // Show notification
            notify(type, title, message) {
                Alpine.store('notifications').add({ type, title, message });
            },
            
            // Show loading
            showLoading() {
                Alpine.store('app').setLoading(true);
            },
            
            // Hide loading
            hideLoading() {
                Alpine.store('app').setLoading(false);
            },
            
            // Format time for live matches
            formatMatchTime(seconds) {
                const minutes = Math.floor(seconds / 60);
                const remainingSeconds = seconds % 60;
                return `${minutes}:${remainingSeconds.toString().padStart(2, '0')}`;
            },
            
            // Format date
            formatDate(date) {
                return new Intl.DateTimeFormat('es-CO', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                }).format(new Date(date));
            },
            
            // Format time
            formatTime(time) {
                return new Intl.DateTimeFormat('es-CO', {
                    hour: '2-digit',
                    minute: '2-digit'
                }).format(new Date(time));
            }
        };
        
        // Livewire event listeners
        document.addEventListener('livewire:init', () => {
            Livewire.on('notify', (event) => {
                VolleyPass.notify(event.type, event.title, event.message);
            });
            
            Livewire.on('loading', (event) => {
                if (event.show) {
                    VolleyPass.showLoading();
                } else {
                    VolleyPass.hideLoading();
                }
            });
        });
        
        // Service Worker registration for PWA
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then((registration) => {
                        console.log('SW registered: ', registration);
                    })
                    .catch((registrationError) => {
                        console.log('SW registration failed: ', registrationError);
                    });
            });
        }
    </script>
    
    <!-- Additional Scripts -->
    @stack('scripts')
</body>
</html>