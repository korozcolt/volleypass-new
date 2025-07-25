<!DOCTYPE html>
<html lang="es" x-data="{ darkMode: false }" :class="{ 'dark': darkMode }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VolleyPass Sucre - Plataforma Integral de Gestión</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    animation: {
                        'fade-in-up': 'fadeInUp 0.6s ease-out',
                        'fade-in-left': 'fadeInLeft 0.6s ease-out',
                        'fade-in-right': 'fadeInRight 0.6s ease-out',
                        'bounce-in': 'bounceIn 0.8s ease-out',
                        'slide-in': 'slideIn 0.5s ease-out',
                    }
                }
            }
        }
    </script>
    <style>
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes fadeInLeft {
            from { opacity: 0; transform: translateX(-30px); }
            to { opacity: 1; transform: translateX(0); }
        }
        @keyframes fadeInRight {
            from { opacity: 0; transform: translateX(30px); }
            to { opacity: 1; transform: translateX(0); }
        }
        @keyframes bounceIn {
            0% { opacity: 0; transform: scale(0.3); }
            50% { opacity: 1; transform: scale(1.05); }
            70% { transform: scale(0.9); }
            100% { opacity: 1; transform: scale(1); }
        }
        @keyframes slideIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-on-scroll {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.6s ease-out;
        }
        .animate-on-scroll.visible {
            opacity: 1;
            transform: translateY(0);
        }
    </style>
    @livewireStyles
</head>
<body class="transition-colors duration-300" x-data="{
    mobileMenuOpen: false,
    init() {
        // Intersection Observer para animaciones al scroll
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                }
            });
        }, { threshold: 0.1 });

        document.querySelectorAll('.animate-on-scroll').forEach(el => {
            observer.observe(el);
        });
    },
    scrollToSection(sectionId) {
        document.getElementById(sectionId)?.scrollIntoView({ behavior: 'smooth' });
        this.mobileMenuOpen = false;
    }
}">

    <!-- Header -->
    <header class="fixed top-0 w-full bg-white/90 dark:bg-gray-900/90 backdrop-blur-md z-50 border-b border-gray-200 dark:border-gray-700">
        <div class="container mx-auto px-4 lg:px-6 h-16 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <!-- Logo oficial de VolleyPass -->
                <div class="w-10 h-10 flex items-center justify-center">
                    <img x-show="!darkMode" 
                         src="{{ asset('images/logo-volley_pass_black_back.png') }}" 
                         alt="VolleyPass Logo" 
                         class="w-full h-full object-contain">
                    <img x-show="darkMode" 
                         src="{{ asset('images/logo-volley_pass_white_back.png') }}" 
                         alt="VolleyPass Logo" 
                         class="w-full h-full object-contain">
                </div>
                <span class="text-xl font-bold text-gray-900 dark:text-white">
                    VolleyPass Sucre
                </span>
            </div>

            <!-- Desktop Navigation -->
            <nav class="hidden md:flex items-center space-x-6">
                <button @click="scrollToSection('inicio')" class="text-sm font-medium hover:text-blue-600 transition-colors">
                    Inicio
                </button>
                <button @click="scrollToSection('caracteristicas')" class="text-sm font-medium hover:text-blue-600 transition-colors">
                    Características
                </button>
                <button @click="scrollToSection('demo')" class="text-sm font-medium hover:text-blue-600 transition-colors">
                    Demo
                </button>
                <button @click="scrollToSection('progreso')" class="text-sm font-medium hover:text-blue-600 transition-colors">
                    Progreso
                </button>
                <button @click="scrollToSection('contacto')" class="text-sm font-medium hover:text-blue-600 transition-colors">
                    Contacto
                </button>
            </nav>

            <div class="flex items-center space-x-4">
                <button @click="darkMode = !darkMode" class="w-9 h-9 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 flex items-center justify-center transition-colors">
                    <svg x-show="!darkMode" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                    </svg>
                    <svg x-show="darkMode" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </button>

                <!-- Mobile Menu Button -->
                <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden w-9 h-9 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 flex items-center justify-center transition-colors">
                    <svg x-show="!mobileMenuOpen" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                    <svg x-show="mobileMenuOpen" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div x-show="mobileMenuOpen"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 transform -translate-y-2"
             x-transition:enter-end="opacity-100 transform translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 transform translate-y-0"
             x-transition:leave-end="opacity-0 transform -translate-y-2"
             class="md:hidden bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700">
            <nav class="container mx-auto px-4 py-4 space-y-2">
                <button @click="scrollToSection('inicio')" class="block w-full text-left py-2 text-sm font-medium hover:text-blue-600 transition-colors">
                    Inicio
                </button>
                <button @click="scrollToSection('caracteristicas')" class="block w-full text-left py-2 text-sm font-medium hover:text-blue-600 transition-colors">
                    Características
                </button>
                <button @click="scrollToSection('demo')" class="block w-full text-left py-2 text-sm font-medium hover:text-blue-600 transition-colors">
                    Demo
                </button>
                <button @click="scrollToSection('progreso')" class="block w-full text-left py-2 text-sm font-medium hover:text-blue-600 transition-colors">
                    Progreso
                </button>
                <button @click="scrollToSection('contacto')" class="block w-full text-left py-2 text-sm font-medium hover:text-blue-600 transition-colors">
                    Contacto
                </button>
            </nav>
        </div>
    </header>

    <!-- Hero Section -->
    <section id="inicio" class="pt-16 min-h-screen flex items-center bg-gradient-to-br from-blue-50 via-white to-purple-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900">
        <div class="container mx-auto px-4 lg:px-6">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <div class="space-y-8 animate-on-scroll">
                    <div class="space-y-4">
                        <div class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                            🏐 Plataforma Integral de Gestión
                        </div>
                        <h1 class="text-4xl md:text-6xl font-bold leading-tight">
                            <span class="bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                                VolleyPass
                            </span>
                            <br />
                            <span class="text-gray-900 dark:text-white">Sucre</span>
                        </h1>
                        <p class="text-xl text-gray-600 dark:text-gray-300 leading-relaxed">
                            Digitaliza y moderniza la gestión de la Liga de Voleibol de Sucre. Centraliza el registro,
                            verificación y gestión de jugadoras, entrenadores y clubes con transparencia y eficiencia total.
                        </p>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-4">
                        <button class="inline-flex items-center justify-center px-6 py-3 rounded-lg bg-gradient-to-r from-blue-600 to-purple-600 text-white font-medium hover:from-blue-700 hover:to-purple-700 transition-all transform hover:scale-105">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h.01M19 10a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Ver Demo
                        </button>
                        <button class="inline-flex items-center justify-center px-6 py-3 rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-medium hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                            Conocer Más
                        </button>
                    </div>

                    <!-- Stats Component -->
                    @livewire('welcome-stats')
                </div>

                <div class="relative animate-on-scroll">
                    <div class="relative z-10 bg-white dark:bg-gray-800 rounded-2xl shadow-2xl p-8 transform rotate-3 hover:rotate-0 transition-transform duration-300">
                        <div class="space-y-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-500 rounded-lg flex items-center justify-center">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h4"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-semibold">Verificación QR</h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Instantánea en partidos</p>
                                </div>
                            </div>
                            <div class="bg-gray-100 dark:bg-gray-700 rounded-lg p-4">
                                <div class="grid grid-cols-8 gap-1">
                                    <template x-for="i in 64">
                                        <div class="aspect-square rounded-sm"
                                             :class="Math.random() > 0.5 ? 'bg-gray-800 dark:bg-white' : 'bg-white dark:bg-gray-800'">
                                        </div>
                                    </template>
                                </div>
                            </div>
                            <div class="text-center text-sm text-gray-600 dark:text-gray-400">
                                Escanea para verificar jugadora
                            </div>
                        </div>
                    </div>
                    <div class="absolute inset-0 bg-gradient-to-br from-blue-400 to-purple-400 rounded-2xl transform rotate-6 opacity-20"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Grid -->
    <section id="caracteristicas" class="py-20 bg-white dark:bg-gray-900">
        <div class="container mx-auto px-4 lg:px-6">
            <div class="text-center mb-16 animate-on-scroll">
                <div class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 mb-4">
                    Características Principales
                </div>
                <h2 class="text-3xl md:text-4xl font-bold mb-4">Ecosistema Digital Completo</h2>
                <p class="text-xl text-gray-600 dark:text-gray-300 max-w-3xl mx-auto">
                    Una plataforma integral que centraliza toda la gestión deportiva con tecnología moderna y procesos
                    eficientes.
                </p>
            </div>

            @livewire('features-grid')
        </div>
    </section>

    <!-- Interactive Demo -->
    <section id="demo" class="py-20 bg-gray-50 dark:bg-gray-800">
        <div class="container mx-auto px-4 lg:px-6">
            <div class="text-center mb-16 animate-on-scroll">
                <div class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 mb-4">
                    Demostración Interactiva
                </div>
                <h2 class="text-3xl md:text-4xl font-bold mb-4">Explora las Funcionalidades</h2>
                <p class="text-xl text-gray-600 dark:text-gray-300 max-w-3xl mx-auto">
                    Descubre cómo VolleyPass Sucre transforma la gestión deportiva con herramientas modernas e intuitivas.
                </p>
            </div>

            @livewire('interactive-demo')
        </div>
    </section>

    <!-- Project Progress -->
    <section id="progreso" class="py-20 bg-white dark:bg-gray-900">
        <div class="container mx-auto px-4 lg:px-6">
            <div class="text-center mb-16 animate-on-scroll">
                <div class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 mb-4">
                    Estado del Proyecto
                </div>
                <h2 class="text-3xl md:text-4xl font-bold mb-4">Progreso de Desarrollo</h2>
                <p class="text-xl text-gray-600 dark:text-gray-300 max-w-3xl mx-auto">
                    Seguimiento transparente del avance en cada módulo del sistema VolleyPass Sucre.
                </p>
            </div>

            @livewire('project-progress')
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 bg-gradient-to-br from-blue-600 to-purple-600 text-white">
        <div class="container mx-auto px-4 lg:px-6 text-center animate-on-scroll">
            <div class="max-w-3xl mx-auto space-y-8">
                <h2 class="text-3xl md:text-4xl font-bold">¿Listo para Modernizar tu Liga?</h2>
                <p class="text-xl opacity-90">
                    Únete a la revolución digital del voleibol en Sucre. Transparencia, eficiencia y control total en una sola
                    plataforma.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <button class="inline-flex items-center justify-center px-6 py-3 rounded-lg bg-white text-blue-600 font-medium hover:bg-gray-100 transition-all transform hover:scale-105">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                        </svg>
                        Solicitar Demo
                    </button>
                    <button class="inline-flex items-center justify-center px-6 py-3 rounded-lg border border-white text-white font-medium hover:bg-white hover:text-blue-600 transition-all bg-transparent">
                        Contactar Equipo
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer id="contacto" class="bg-gray-900 text-white py-16">
        <div class="container mx-auto px-4 lg:px-6">
            <div class="grid md:grid-cols-4 gap-8">
                <div class="space-y-4">
                    <div class="flex items-center space-x-2">
                        <div class="w-8 h-8 bg-gradient-to-br from-blue-600 to-purple-600 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                            </svg>
                        </div>
                        <span class="text-xl font-bold">VolleyPass Sucre</span>
                    </div>
                    <p class="text-gray-400">Modernizando la gestión deportiva del voleibol en Sucre, Colombia.</p>
                </div>

                <div>
                    <h3 class="font-semibold mb-4">Producto</h3>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="#" class="hover:text-white transition-colors">Características</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Precios</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Demo</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Documentación</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="font-semibold mb-4">Soporte</h3>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="#" class="hover:text-white transition-colors">Centro de Ayuda</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Contacto</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Capacitación</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Estado del Sistema</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="font-semibold mb-4">Legal</h3>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="#" class="hover:text-white transition-colors">Privacidad</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Términos</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Cookies</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Licencias</a></li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-gray-800 mt-12 pt-8 text-center text-gray-400">
                <p>&copy; 2024 VolleyPass Sucre. Todos los derechos reservados.</p>
            </div>
        </div>
    </footer>

    @livewireScripts
</body>
</html>
