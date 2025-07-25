<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Plataforma de torneos de voleibol - Sigue partidos en vivo, resultados y estadísticas">
    <meta name="keywords" content="voleibol, torneos, partidos en vivo, resultados, estadísticas">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="VolleyPass - Torneos de Voleibol">
    <meta property="og:description" content="Plataforma de torneos de voleibol - Sigue partidos en vivo, resultados y estadísticas">
    <meta property="og:image" content="{{ asset('images/og-image.jpg') }}">
    
    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ url()->current() }}">
    <meta property="twitter:title" content="VolleyPass - Torneos de Voleibol">
    <meta property="twitter:description" content="Plataforma de torneos de voleibol - Sigue partidos en vivo, resultados y estadísticas">
    <meta property="twitter:image" content="{{ asset('images/og-image.jpg') }}">

    <title>{{ $title ?? 'VolleyPass - Torneos de Voleibol' }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="icon" type="image/png" href="{{ asset('favicon.ico') }}">
    
    <!-- PWA Meta Tags -->
    <meta name="theme-color" content="#0ea5e9">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="VolleyPass">
    
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Tailwind Config -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'inter': ['Inter', 'sans-serif'],
                        'poppins': ['Poppins', 'sans-serif']
                    },
                    colors: {
                        'volley': {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            200: '#bae6fd',
                            300: '#7dd3fc',
                            400: '#38bdf8',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#0369a1',
                            800: '#075985',
                            900: '#0c4a6e'
                        },
                        'live': {
                            50: '#f0fdf4',
                            500: '#22c55e',
                            600: '#16a34a'
                        },
                        'upcoming': {
                            50: '#fef3c7',
                            500: '#f59e0b',
                            600: '#d97706'
                        },
                        'finished': {
                            50: '#f8fafc',
                            500: '#64748b',
                            600: '#475569'
                        }
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.5s ease-in-out',
                        'slide-up': 'slideUp 0.3s ease-out',
                        'pulse-live': 'pulseLive 2s infinite',
                        'bounce-gentle': 'bounceGentle 1s ease-in-out infinite'
                    }
                }
            }
        }
    </script>
    
    <!-- Custom Styles -->
    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        @keyframes slideUp {
            from { transform: translateY(100%); }
            to { transform: translateY(0); }
        }
        
        @keyframes pulseLive {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
        
        @keyframes bounceGentle {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
        
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
        
        /* Loading skeleton */
        .skeleton {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: loading 1.5s infinite;
        }
        
        @keyframes loading {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }
        
        /* Smooth transitions */
        * {
            transition: color 0.2s ease, background-color 0.2s ease, border-color 0.2s ease, transform 0.2s ease, box-shadow 0.2s ease;
        }
        
        /* Focus styles for accessibility */
        .focus-visible:focus {
            outline: 2px solid #0ea5e9;
            outline-offset: 2px;
        }
        
        /* Print styles */
        @media print {
            .no-print {
                display: none !important;
            }
        }
        
        /* Enhanced Animations from tournaments component */
        .animate-fade-in-up {
            animation: fadeInUp 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(40px) scale(0.95);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        @keyframes fade-in-up {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-pulse {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        @keyframes pulse {
            0%, 100% {
                opacity: 1;
                transform: scale(1);
            }
            50% {
                opacity: 0.7;
                transform: scale(1.05);
            }
        }

        /* Custom Gradient Text */
        .gradient-text {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Enhanced Shadows */
        .shadow-3xl {
            box-shadow: 0 35px 60px -12px rgba(0, 0, 0, 0.25);
        }

        /* Backdrop Blur Support */
        .backdrop-blur-xl {
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
        }

        .backdrop-blur-lg {
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
        }

        .backdrop-blur-sm {
            backdrop-filter: blur(4px);
            -webkit-backdrop-filter: blur(4px);
        }

        /* Hover Effects */
        .hover-lift:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }

        /* Loading Animation */
        @keyframes shimmer {
            0% {
                background-position: -200px 0;
            }
            100% {
                background-position: calc(200px + 100%) 0;
            }
        }

        .shimmer {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200px 100%;
            animation: shimmer 1.5s infinite;
        }

        /* Dark mode adjustments */
        @media (prefers-color-scheme: dark) {
            .shimmer {
                background: linear-gradient(90deg, #374151 25%, #4b5563 50%, #374151 75%);
            }
        }
    </style>
    
    @livewireStyles
</head>

<body class="font-inter bg-gray-50 min-h-screen antialiased">
    <!-- Skip to main content for accessibility -->
    <a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 bg-volley-600 text-white px-4 py-2 rounded-lg z-50">
        Saltar al contenido principal
    </a>
    
    <!-- Main Content -->
    <main id="main-content" class="min-h-screen">
        {{ $slot }}
    </main>
    
    <!-- Loading Overlay -->
    <div id="loading-overlay" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-lg p-6 flex items-center space-x-4">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-volley-600"></div>
            <span class="text-gray-700">Cargando...</span>
        </div>
    </div>
    
    <!-- Toast Notifications Container -->
    <div id="toast-container" class="fixed top-4 right-4 z-50 space-y-2"></div>
    
    @livewireScripts
    
    <!-- Custom JavaScript -->
    <script>
        // Global utilities
        window.VolleyPass = {
            // Show loading overlay
            showLoading() {
                document.getElementById('loading-overlay').classList.remove('hidden');
            },
            
            // Hide loading overlay
            hideLoading() {
                document.getElementById('loading-overlay').classList.add('hidden');
            },
            
            // Show toast notification
            showToast(message, type = 'info', duration = 5000) {
                const container = document.getElementById('toast-container');
                const toast = document.createElement('div');
                
                const colors = {
                    success: 'bg-green-500',
                    error: 'bg-red-500',
                    warning: 'bg-yellow-500',
                    info: 'bg-blue-500'
                };
                
                toast.className = `${colors[type]} text-white px-6 py-3 rounded-lg shadow-lg transform transition-all duration-300 translate-x-full opacity-0`;
                toast.innerHTML = `
                    <div class="flex items-center space-x-2">
                        <span>${message}</span>
                        <button onclick="this.parentElement.parentElement.remove()" class="ml-2 text-white hover:text-gray-200">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                `;
                
                container.appendChild(toast);
                
                // Animate in
                setTimeout(() => {
                    toast.classList.remove('translate-x-full', 'opacity-0');
                }, 100);
                
                // Auto remove
                setTimeout(() => {
                    toast.classList.add('translate-x-full', 'opacity-0');
                    setTimeout(() => toast.remove(), 300);
                }, duration);
            },
            
            // Format time ago
            timeAgo(date) {
                const now = new Date();
                const diffInSeconds = Math.floor((now - new Date(date)) / 1000);
                
                if (diffInSeconds < 60) return 'Hace un momento';
                if (diffInSeconds < 3600) return `Hace ${Math.floor(diffInSeconds / 60)} min`;
                if (diffInSeconds < 86400) return `Hace ${Math.floor(diffInSeconds / 3600)} h`;
                return `Hace ${Math.floor(diffInSeconds / 86400)} días`;
            },
            
            // Copy to clipboard
            async copyToClipboard(text) {
                try {
                    await navigator.clipboard.writeText(text);
                    this.showToast('Copiado al portapapeles', 'success');
                } catch (err) {
                    this.showToast('Error al copiar', 'error');
                }
            },
            
            // Share content
            async share(data) {
                if (navigator.share) {
                    try {
                        await navigator.share(data);
                    } catch (err) {
                        this.copyToClipboard(data.url || data.text);
                    }
                } else {
                    this.copyToClipboard(data.url || data.text);
                }
            }
        };
        
        // Livewire event listeners
        document.addEventListener('livewire:init', () => {
            // Show loading on Livewire requests
            Livewire.hook('morph.updating', () => {
                VolleyPass.showLoading();
            });
            
            Livewire.hook('morph.updated', () => {
                VolleyPass.hideLoading();
            });
            
            // Listen for custom events
            Livewire.on('tournament-favorited', (data) => {
                VolleyPass.showToast('Torneo agregado a favoritos', 'success');
            });
            
            Livewire.on('share-tournament', (data) => {
                VolleyPass.share({
                    title: data.title,
                    url: data.url
                });
            });
        });
        
        // Auto-refresh for live data
        setInterval(() => {
            if (typeof Livewire !== 'undefined') {
                // Only refresh if page is visible
                if (!document.hidden) {
                    Livewire.dispatch('refresh-live-data');
                }
            }
        }, 30000); // Every 30 seconds
        
        // Service Worker for PWA (if available)
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then(registration => {
                        console.log('SW registered: ', registration);
                    })
                    .catch(registrationError => {
                        console.log('SW registration failed: ', registrationError);
                    });
            });
        }
        
        // Keyboard shortcuts
        document.addEventListener('keydown', (e) => {
            // Ctrl/Cmd + K for search
            if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                e.preventDefault();
                const searchInput = document.querySelector('input[type="text"][placeholder*="Buscar"]');
                if (searchInput) {
                    searchInput.focus();
                }
            }
        });
        
        // Smooth scroll for anchor links
        document.addEventListener('DOMContentLoaded', function() {
            const links = document.querySelectorAll('a[href^="#"]');
            links.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({ 
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                });
            });
        });
    </script>
    
    <!-- Analytics (if needed) -->
    @if(config('app.env') === 'production')
    <!-- Add your analytics code here -->
    @endif
</body>
</html>