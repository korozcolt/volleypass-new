<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ darkMode: false }" :class="{ 'dark': darkMode }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="VolleyPass - Acceso al sistema de gestión de la Liga de Voleibol de Sucre, Colombia.">
    <meta name="keywords" content="voleibol, sucre, colombia, liga, login, acceso">
    <meta name="author" content="VolleyPass Software">
    
    <title>{{ $title ?? 'Acceso' }} - VolleyPass</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">
    
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Livewire Styles -->
    @livewireStyles
    
    <!-- Alpine.js Global Stores -->
    <script>
        document.addEventListener('alpine:init', () => {
            // Global App Store
            Alpine.store('app', {
                darkMode: false,
                mobileMenuOpen: false,
                loading: false,
                
                toggleDarkMode() {
                    this.darkMode = !this.darkMode;
                },
                
                toggleMobileMenu() {
                    this.mobileMenuOpen = !this.mobileMenuOpen;
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
        });
    </script>
    
    <!-- Custom Auth Styles -->
    <style>
        .volleyball-pattern {
            background-image: 
                radial-gradient(circle at 25% 25%, rgba(252, 211, 77, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 75% 75%, rgba(59, 130, 246, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 50% 50%, rgba(239, 68, 68, 0.1) 0%, transparent 50%);
            background-size: 200px 200px, 300px 300px, 250px 250px;
            background-position: 0 0, 100px 100px, 50px 150px;
            animation: float 20s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            33% { transform: translateY(-10px) rotate(1deg); }
            66% { transform: translateY(5px) rotate(-1deg); }
        }
        
        .auth-card {
            backdrop-filter: blur(20px);
            background: rgba(255, 255, 255, 0.95);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .dark .auth-card {
            background: rgba(17, 24, 39, 0.95);
            border: 1px solid rgba(75, 85, 99, 0.2);
        }
        
        .volleyball-icon {
            background: linear-gradient(135deg, #fcd34d 0%, #f59e0b 50%, #d97706 100%);
            box-shadow: 0 10px 25px rgba(245, 158, 11, 0.3);
        }
    </style>
</head>

<body class="font-body antialiased bg-gradient-to-br from-vp-primary-50 via-vp-secondary-50 to-vp-accent-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 min-h-screen transition-colors duration-200"
      x-data="{ 
          init() {
              // Initialize auth page functionality
              console.log('VolleyPass Auth System Initialized');
          }
      }"
      x-init="init()">
    
    <!-- Volleyball Background Pattern -->
    <div class="volleyball-pattern fixed inset-0 z-0"></div>
    
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
                <span class="text-gray-900 dark:text-gray-100 font-medium">Procesando...</span>
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
    
    <!-- Header with Dark Mode Toggle -->
    <header class="relative z-10 p-4">
        <div class="flex justify-between items-center max-w-7xl mx-auto">
            <!-- Logo -->
            <a href="{{ route('home') }}" class="flex items-center space-x-3 group">
                <div class="volleyball-icon w-12 h-12 rounded-xl flex items-center justify-center transform group-hover:scale-105 transition-transform duration-200">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
                <div>
                    <h1 class="text-xl font-bold bg-gradient-to-r from-vp-primary-600 to-vp-secondary-600 bg-clip-text text-transparent">
                        VolleyPass
                    </h1>
                    <p class="text-xs text-gray-600 dark:text-gray-400">Liga de Voleibol Sucre</p>
                </div>
            </a>
            
            <!-- Dark Mode Toggle -->
            <button
                @click="$store.app.toggleDarkMode()"
                class="w-10 h-10 rounded-lg bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm flex items-center justify-center hover:bg-white dark:hover:bg-gray-700 transition-all duration-200 shadow-lg"
                :aria-label="$store.app.darkMode ? 'Activar modo claro' : 'Activar modo oscuro'"
            >
                <svg x-show="!$store.app.darkMode" class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                </svg>
                <svg x-show="$store.app.darkMode" class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
            </button>
        </div>
    </header>
    
    <!-- Main Auth Content -->
    <main class="relative z-10 flex-1 flex items-center justify-center px-4 py-8">
        <div class="w-full max-w-md">
            <!-- Auth Card -->
            <div class="auth-card rounded-2xl shadow-2xl p-8">
                {{ $slot }}
            </div>
            
            <!-- Back to Home Link -->
            <div class="text-center mt-6">
                <a href="{{ route('home') }}" 
                   class="inline-flex items-center text-sm text-gray-600 dark:text-gray-400 hover:text-vp-primary-600 dark:hover:text-vp-primary-400 transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Volver al inicio
                </a>
            </div>
        </div>
    </main>
    
    <!-- Footer -->
    <footer class="relative z-10 text-center py-6 px-4">
        <p class="text-sm text-gray-600 dark:text-gray-400">
            © {{ date('Y') }} VolleyPass. Liga de Voleibol de Sucre, Colombia.
        </p>
        <p class="text-xs text-gray-500 dark:text-gray-500 mt-1">
            Plataforma digital oficial para la gestión deportiva.
        </p>
    </footer>
    
    <!-- Livewire Scripts -->
    @livewireScripts
    
    <!-- Alpine.js is included with Livewire -->
    
    <!-- Custom Scripts -->
    <script>
        // Global utility functions for auth
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
            
            // Validate email
            validateEmail(email) {
                const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return re.test(email);
            },
            
            // Validate password strength
            validatePassword(password) {
                return {
                    length: password.length >= 8,
                    uppercase: /[A-Z]/.test(password),
                    lowercase: /[a-z]/.test(password),
                    number: /\d/.test(password),
                    special: /[!@#$%^&*(),.?":{}|<>]/.test(password)
                };
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
            
            Livewire.on('auth-success', (event) => {
                VolleyPass.notify('success', '¡Bienvenido!', 'Acceso exitoso al sistema');
                setTimeout(() => {
                    window.location.href = event.redirect || '/dashboard';
                }, 1500);
            });
            
            Livewire.on('auth-error', (event) => {
                VolleyPass.notify('error', 'Error de acceso', event.message || 'Credenciales incorrectas');
            });
        });
        
        // Form validation helpers
        document.addEventListener('DOMContentLoaded', () => {
            // Auto-focus first input
            const firstInput = document.querySelector('input[type="email"], input[type="text"]');
            if (firstInput) {
                firstInput.focus();
            }
            
            // Enter key navigation
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Enter') {
                    const form = e.target.closest('form');
                    if (form) {
                        const inputs = form.querySelectorAll('input, select, textarea');
                        const currentIndex = Array.from(inputs).indexOf(e.target);
                        const nextInput = inputs[currentIndex + 1];
                        
                        if (nextInput) {
                            nextInput.focus();
                            e.preventDefault();
                        }
                    }
                }
            });
        });
        
        // Security: Clear sensitive data on page unload
        window.addEventListener('beforeunload', () => {
            // Clear any sensitive form data
            const passwordInputs = document.querySelectorAll('input[type="password"]');
            passwordInputs.forEach(input => {
                input.value = '';
            });
        });
    </script>
    
    <!-- Additional Scripts -->
    @stack('scripts')
</body>
</html>