<footer class="bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700">
    <!-- Volleyball background pattern -->
    <div class="relative overflow-hidden">
        <div class="absolute inset-0 opacity-5 dark:opacity-10">
            <svg class="w-full h-full" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <pattern id="volleyball-pattern" x="0" y="0" width="20" height="20" patternUnits="userSpaceOnUse">
                        <circle cx="10" cy="10" r="8" fill="none" stroke="currentColor" stroke-width="0.5"/>
                        <path d="M2 10 L18 10 M10 2 L10 18" stroke="currentColor" stroke-width="0.3"/>
                    </pattern>
                </defs>
                <rect width="100" height="100" fill="url(#volleyball-pattern)"/>
            </svg>
        </div>
        
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- Brand Section -->
                <div class="col-span-1 md:col-span-2">
                    <div class="flex items-center mb-4">
                        <x-app-logo class="h-8 w-auto" />
                        <span class="ml-3 text-xl font-bold text-gray-900 dark:text-white">VolleyPass</span>
                    </div>
                    <p class="text-gray-600 dark:text-gray-400 mb-4 max-w-md">
                        Sistema integral para la gestión de la Liga de Voleibol de Sucre. 
                        Conectando jugadoras, entrenadores, árbitros y personal médico.
                    </p>
                    <div class="text-sm text-gray-500 dark:text-gray-500">
                        <p class="font-semibold">Liga de Voleibol de Sucre</p>
                        <p>Promoviendo el deporte y la excelencia</p>
                    </div>
                </div>
                
                <!-- Quick Links -->
                <div>
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white uppercase tracking-wider mb-4">
                        Enlaces Rápidos
                    </h3>
                    <ul class="space-y-2">
                        <li>
                            <a href="{{ route('home') }}" class="text-gray-600 dark:text-gray-400 hover:text-vp-primary-600 dark:hover:text-vp-primary-400 transition-colors">
                                Inicio
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('public.matches') }}" class="text-gray-600 dark:text-gray-400 hover:text-vp-primary-600 dark:hover:text-vp-primary-400 transition-colors">
                                Partidos en Vivo
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('public.results') }}" class="text-gray-600 dark:text-gray-400 hover:text-vp-primary-600 dark:hover:text-vp-primary-400 transition-colors">
                                Resultados
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('public.stats') }}" class="text-gray-600 dark:text-gray-400 hover:text-vp-primary-600 dark:hover:text-vp-primary-400 transition-colors">
                                Estadísticas
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('public.teams') }}" class="text-gray-600 dark:text-gray-400 hover:text-vp-primary-600 dark:hover:text-vp-primary-400 transition-colors">
                                Equipos
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('public.standings') }}" class="text-gray-600 dark:text-gray-400 hover:text-vp-primary-600 dark:hover:text-vp-primary-400 transition-colors">
                                Tabla de Posiciones
                            </a>
                        </li>
                    </ul>
                </div>
                
                <!-- Contact & Social -->
                <div>
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white uppercase tracking-wider mb-4">
                        Contacto
                    </h3>
                    <div class="space-y-3">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-gray-400 mt-0.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <div class="text-sm text-gray-600 dark:text-gray-400">
                                <p>Sucre, Bolivia</p>
                                <p>Liga de Voleibol</p>
                            </div>
                        </div>
                        
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            <a href="mailto:info@volleypass.bo" class="text-sm text-gray-600 dark:text-gray-400 hover:text-vp-primary-600 dark:hover:text-vp-primary-400 transition-colors">
                                info@volleypass.bo
                            </a>
                        </div>
                        
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                            <span class="text-sm text-gray-600 dark:text-gray-400">
                                +591 4 123-4567
                            </span>
                        </div>
                    </div>
                    
                    <!-- Social Media Links -->
                    <div class="mt-6">
                        <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Síguenos</h4>
                        <div class="flex space-x-3">
                            <a href="#" class="text-gray-400 hover:text-vp-primary-600 dark:hover:text-vp-primary-400 transition-colors">
                                <span class="sr-only">Twitter</span>
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84"/>
                                </svg>
                            </a>
                            <a href="#" class="text-gray-400 hover:text-vp-primary-600 dark:hover:text-vp-primary-400 transition-colors">
                                <span class="sr-only">Facebook</span>
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" clip-rule="evenodd"/>
                                </svg>
                            </a>
                            <a href="#" class="text-gray-400 hover:text-vp-primary-600 dark:hover:text-vp-primary-400 transition-colors">
                                <span class="sr-only">Instagram</span>
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path fill-rule="evenodd" d="M12.017 0C5.396 0 .029 5.367.029 11.987c0 6.62 5.367 11.987 11.988 11.987s11.987-5.367 11.987-11.987C24.014 5.367 18.647.001 12.017.001zM8.449 16.988c-1.297 0-2.448-.49-3.323-1.297C4.198 14.895 3.708 13.744 3.708 12.447s.49-2.448 1.297-3.323C5.902 8.198 7.053 7.708 8.35 7.708s2.448.49 3.323 1.297c.897.875 1.387 2.026 1.387 3.323s-.49 2.448-1.297 3.323c-.875.897-2.026 1.387-3.323 1.387zm7.718 0c-1.297 0-2.448-.49-3.323-1.297-.897-.875-1.387-2.026-1.387-3.323s.49-2.448 1.297-3.323c.875-.897 2.026-1.387 3.323-1.387s2.448.49 3.323 1.297c.897.875 1.387 2.026 1.387 3.323s-.49 2.448-1.297 3.323c-.875.897-2.026 1.387-3.323 1.387z" clip-rule="evenodd"/>
                                </svg>
                            </a>
                            <a href="#" class="text-gray-400 hover:text-vp-primary-600 dark:hover:text-vp-primary-400 transition-colors">
                                <span class="sr-only">YouTube</span>
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bottom Bar -->
    <div class="bg-gray-50 dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="text-sm text-gray-500 dark:text-gray-400">
                    <p>&copy; {{ date('Y') }} VolleyPass - Liga de Voleibol de Sucre. Todos los derechos reservados.</p>
                </div>
                <div class="flex space-x-6 mt-2 md:mt-0">
                    <a href="#" class="text-sm text-gray-500 dark:text-gray-400 hover:text-vp-primary-600 dark:hover:text-vp-primary-400 transition-colors">
                        Términos de Servicio
                    </a>
                    <a href="#" class="text-sm text-gray-500 dark:text-gray-400 hover:text-vp-primary-600 dark:hover:text-vp-primary-400 transition-colors">
                        Política de Privacidad
                    </a>
                    <a href="#" class="text-sm text-gray-500 dark:text-gray-400 hover:text-vp-primary-600 dark:hover:text-vp-primary-400 transition-colors">
                        Contacto
                    </a>
                </div>
            </div>
        </div>
    </div>
</footer>