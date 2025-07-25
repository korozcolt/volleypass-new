<div class="min-h-screen bg-gray-50">
    <!-- Header Sticky -->
    <header class="sticky top-0 z-50 bg-white shadow-lg border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-volley-500 to-volley-700 rounded-lg flex items-center justify-center">
                        <i class="fas fa-volleyball-ball text-white text-lg"></i>
                    </div>
                    <div>
                        <h1 class="font-poppins font-bold text-xl text-gray-900">VolleyPass</h1>
                        <p class="text-xs text-gray-500">Torneos de Voleibol</p>
                    </div>
                </div>

                <!-- Navigation Desktop -->
                <nav class="hidden md:flex space-x-8">
                    <a href="#torneos" class="text-gray-700 hover:text-volley-600 font-medium transition-colors">Torneos</a>
                    <a href="#en-vivo" class="text-gray-700 hover:text-volley-600 font-medium transition-colors">En Vivo</a>
                    <a href="#resultados" class="text-gray-700 hover:text-volley-600 font-medium transition-colors">Resultados</a>
                    <a href="#estadisticas" class="text-gray-700 hover:text-volley-600 font-medium transition-colors">Estadísticas</a>
                </nav>

                <!-- Search & Actions -->
                <div class="flex items-center space-x-4">
                    <div class="relative hidden sm:block">
                        <input type="text" placeholder="Buscar torneos, equipos..." 
                               class="w-64 pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-volley-500 focus:border-transparent">
                        <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                    </div>
                    <button class="md:hidden p-2 text-gray-600 hover:text-gray-900">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                </div>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="relative bg-gradient-to-br from-volley-600 via-volley-700 to-volley-800 text-white overflow-hidden">
        <div class="absolute inset-0 bg-black opacity-20"></div>
        <div class="absolute inset-0">
            <div class="absolute top-10 left-10 w-20 h-20 bg-white opacity-10 rounded-full animate-bounce-gentle"></div>
            <div class="absolute bottom-20 right-20 w-16 h-16 bg-white opacity-10 rounded-full animate-bounce-gentle" style="animation-delay: 1s"></div>
            <div class="absolute top-1/2 left-1/4 w-12 h-12 bg-white opacity-10 rounded-full animate-bounce-gentle" style="animation-delay: 2s"></div>
        </div>
        
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 lg:py-24">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <div class="space-y-6">
                    <div class="inline-flex items-center space-x-2 bg-white bg-opacity-20 rounded-full px-4 py-2">
                        <div class="w-2 h-2 bg-live-500 rounded-full animate-pulse-live"></div>
                        <span class="text-sm font-medium">3 Partidos en Vivo</span>
                    </div>
                    
                    <h1 class="font-poppins font-bold text-4xl lg:text-6xl leading-tight">
                        Torneos de <span class="text-transparent bg-clip-text bg-gradient-to-r from-yellow-300 to-orange-300">Voleibol</span> en Vivo
                    </h1>
                    
                    <p class="text-xl text-gray-200 leading-relaxed">
                        Sigue todos los torneos, resultados en tiempo real y estadísticas de los mejores equipos de voleibol.
                    </p>
                    
                    <div class="flex flex-col sm:flex-row gap-4">
                        <button class="bg-white text-volley-700 px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition-colors flex items-center justify-center space-x-2">
                            <i class="fas fa-play"></i>
                            <span>Ver Partidos en Vivo</span>
                        </button>
                        <button class="border-2 border-white text-white px-8 py-3 rounded-lg font-semibold hover:bg-white hover:text-volley-700 transition-colors">
                            Explorar Torneos
                        </button>
                    </div>
                </div>
                
                <!-- Featured Match Card -->
                <div class="bg-white bg-opacity-10 backdrop-blur-sm rounded-2xl p-6 border border-white border-opacity-20">
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-sm font-medium text-gray-200">PARTIDO DESTACADO</span>
                        <div class="flex items-center space-x-2">
                            <div class="w-2 h-2 bg-live-500 rounded-full animate-pulse-live"></div>
                            <span class="text-sm font-medium text-live-400">EN VIVO</span>
                        </div>
                    </div>
                    
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center">
                                    <span class="text-white font-bold text-sm">VB</span>
                                </div>
                                <div>
                                    <h3 class="font-semibold">Voleibol Bogotá</h3>
                                    <p class="text-sm text-gray-300">Grupo A</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-2xl font-bold">2</div>
                                <div class="text-sm text-gray-300">Sets</div>
                            </div>
                        </div>
                        
                        <div class="text-center py-2">
                            <div class="text-sm text-gray-300">VS</div>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="w-12 h-12 bg-gradient-to-br from-red-500 to-red-600 rounded-lg flex items-center justify-center">
                                    <span class="text-white font-bold text-sm">AM</span>
                                </div>
                                <div>
                                    <h3 class="font-semibold">Atlético Medellín</h3>
                                    <p class="text-sm text-gray-300">Grupo A</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-2xl font-bold">1</div>
                                <div class="text-sm text-gray-300">Sets</div>
                            </div>
                        </div>
                        
                        <div class="border-t border-white border-opacity-20 pt-4">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-300">Set Actual: 25-23</span>
                                <span class="text-gray-300">2do Tiempo</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Quick Stats -->
    <section class="bg-white py-8 border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                <div class="text-center">
                    <div class="text-3xl font-bold text-volley-600">12</div>
                    <div class="text-sm text-gray-600">Torneos Activos</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-live-600">3</div>
                    <div class="text-sm text-gray-600">Partidos en Vivo</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-upcoming-600">24</div>
                    <div class="text-sm text-gray-600">Próximos Partidos</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-finished-600">156</div>
                    <div class="text-sm text-gray-600">Equipos Registrados</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Content Tabs -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Tab Navigation -->
        <div class="border-b border-gray-200 mb-8">
            <nav class="-mb-px flex space-x-8 overflow-x-auto">
                <button class="tab-button active whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm" data-tab="torneos">
                    <i class="fas fa-trophy mr-2"></i>Torneos Activos
                </button>
                <button class="tab-button whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm" data-tab="en-vivo">
                    <i class="fas fa-circle mr-2 text-live-500 animate-pulse-live"></i>En Vivo
                </button>
                <button class="tab-button whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm" data-tab="resultados">
                    <i class="fas fa-chart-line mr-2"></i>Resultados
                </button>
                <button class="tab-button whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm" data-tab="estadisticas">
                    <i class="fas fa-chart-bar mr-2"></i>Estadísticas
                </button>
            </nav>
        </div>

        <!-- Tab Content -->
        <div id="tab-content">
            <!-- Torneos Tab -->
            <div id="torneos-content" class="tab-content">
                <div class="grid lg:grid-cols-3 gap-8">
                    <!-- Main Content -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Filters -->
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                            <div class="flex flex-wrap gap-4">
                                <select class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
                                    <option>Todos los Estados</option>
                                    <option>Inscripciones Abiertas</option>
                                    <option>En Curso</option>
                                    <option>Finalizados</option>
                                </select>
                                <select class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
                                    <option>Todas las Categorías</option>
                                    <option>Femenino</option>
                                    <option>Masculino</option>
                                    <option>Mixto</option>
                                </select>
                                <select class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
                                    <option>Todas las Ciudades</option>
                                    <option>Bogotá</option>
                                    <option>Medellín</option>
                                    <option>Cali</option>
                                </select>
                            </div>
                        </div>

                        <!-- Tournament Cards -->
                        <div class="space-y-4">
                            <!-- Tournament Card 1 -->
                            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow">
                                <div class="p-6">
                                    <div class="flex items-start justify-between mb-4">
                                        <div class="flex items-center space-x-4">
                                            <div class="w-16 h-16 bg-gradient-to-br from-volley-500 to-volley-600 rounded-lg flex items-center justify-center">
                                                <i class="fas fa-trophy text-white text-xl"></i>
                                            </div>
                                            <div>
                                                <h3 class="font-poppins font-semibold text-lg text-gray-900">Copa Nacional Femenina 2024</h3>
                                                <p class="text-gray-600">Bogotá • 16-24 Marzo</p>
                                                <div class="flex items-center space-x-2 mt-1">
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-live-100 text-live-800">
                                                        En Curso
                                                    </span>
                                                    <span class="text-sm text-gray-500">32 equipos</span>
                                                </div>
                                            </div>
                                        </div>
                                        <button class="text-gray-400 hover:text-gray-600">
                                            <i class="fas fa-heart"></i>
                                        </button>
                                    </div>
                                    
                                    <div class="grid grid-cols-3 gap-4 mb-4">
                                        <div class="text-center">
                                            <div class="text-lg font-semibold text-gray-900">8</div>
                                            <div class="text-xs text-gray-500">Partidos Hoy</div>
                                        </div>
                                        <div class="text-center">
                                            <div class="text-lg font-semibold text-gray-900">16</div>
                                            <div class="text-xs text-gray-500">Equipos Activos</div>
                                        </div>
                                        <div class="text-center">
                                            <div class="text-lg font-semibold text-gray-900">Cuartos</div>
                                            <div class="text-xs text-gray-500">Fase Actual</div>
                                        </div>
                                    </div>
                                    
                                    <div class="flex space-x-3">
                                        <button class="flex-1 bg-volley-600 text-white py-2 px-4 rounded-lg font-medium hover:bg-volley-700 transition-colors">
                                            Ver Detalles
                                        </button>
                                        <button class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                                            <i class="fas fa-share-alt"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Tournament Card 2 -->
                            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow">
                                <div class="p-6">
                                    <div class="flex items-start justify-between mb-4">
                                        <div class="flex items-center space-x-4">
                                            <div class="w-16 h-16 bg-gradient-to-br from-upcoming-500 to-upcoming-600 rounded-lg flex items-center justify-center">
                                                <i class="fas fa-calendar text-white text-xl"></i>
                                            </div>
                                            <div>
                                                <h3 class="font-poppins font-semibold text-lg text-gray-900">Liga Regional Antioquia</h3>
                                                <p class="text-gray-600">Medellín • 1-15 Abril</p>
                                                <div class="flex items-center space-x-2 mt-1">
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-upcoming-100 text-upcoming-800">
                                                        Inscripciones Abiertas
                                                    </span>
                                                    <span class="text-sm text-gray-500">24 equipos</span>
                                                </div>
                                            </div>
                                        </div>
                                        <button class="text-gray-400 hover:text-gray-600">
                                            <i class="far fa-heart"></i>
                                        </button>
                                    </div>
                                    
                                    <div class="grid grid-cols-3 gap-4 mb-4">
                                        <div class="text-center">
                                            <div class="text-lg font-semibold text-gray-900">15</div>
                                            <div class="text-xs text-gray-500">Días Restantes</div>
                                        </div>
                                        <div class="text-center">
                                            <div class="text-lg font-semibold text-gray-900">18/24</div>
                                            <div class="text-xs text-gray-500">Equipos Inscritos</div>
                                        </div>
                                        <div class="text-center">
                                            <div class="text-lg font-semibold text-gray-900">$150K</div>
                                            <div class="text-xs text-gray-500">Premio Total</div>
                                        </div>
                                    </div>
                                    
                                    <div class="flex space-x-3">
                                        <button class="flex-1 bg-upcoming-600 text-white py-2 px-4 rounded-lg font-medium hover:bg-upcoming-700 transition-colors">
                                            Inscribirse
                                        </button>
                                        <button class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                                            <i class="fas fa-info-circle"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <div class="space-y-6">
                        <!-- Quick Actions -->
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                            <h3 class="font-poppins font-semibold text-lg mb-4">Acciones Rápidas</h3>
                            <div class="space-y-3">
                                <button class="w-full text-left p-3 rounded-lg border border-gray-200 hover:bg-gray-50 transition-colors">
                                    <i class="fas fa-plus-circle text-volley-600 mr-3"></i>
                                    Crear Torneo
                                </button>
                                <button class="w-full text-left p-3 rounded-lg border border-gray-200 hover:bg-gray-50 transition-colors">
                                    <i class="fas fa-users text-volley-600 mr-3"></i>
                                    Registrar Equipo
                                </button>
                                <button class="w-full text-left p-3 rounded-lg border border-gray-200 hover:bg-gray-50 transition-colors">
                                    <i class="fas fa-calendar-check text-volley-600 mr-3"></i>
                                    Ver Calendario
                                </button>
                            </div>
                        </div>

                        <!-- Sponsors -->
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                            <h3 class="font-poppins font-semibold text-lg mb-4">Patrocinadores</h3>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="bg-gray-100 rounded-lg p-4 flex items-center justify-center">
                                    <span class="text-gray-500 text-sm">Logo 1</span>
                                </div>
                                <div class="bg-gray-100 rounded-lg p-4 flex items-center justify-center">
                                    <span class="text-gray-500 text-sm">Logo 2</span>
                                </div>
                                <div class="bg-gray-100 rounded-lg p-4 flex items-center justify-center">
                                    <span class="text-gray-500 text-sm">Logo 3</span>
                                </div>
                                <div class="bg-gray-100 rounded-lg p-4 flex items-center justify-center">
                                    <span class="text-gray-500 text-sm">Logo 4</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- En Vivo Tab -->
            <div id="en-vivo-content" class="tab-content hidden">
                <div class="grid lg:grid-cols-3 gap-8">
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Live Matches -->
                        <div class="space-y-4">
                            <!-- Live Match Card -->
                            <div class="bg-white rounded-lg shadow-sm border-l-4 border-live-500 overflow-hidden">
                                <div class="p-6">
                                    <div class="flex items-center justify-between mb-4">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-3 h-3 bg-live-500 rounded-full animate-pulse-live"></div>
                                            <span class="font-medium text-live-600">EN VIVO</span>
                                            <span class="text-sm text-gray-500">Set 3 • 2do Tiempo</span>
                                        </div>
                                        <span class="text-sm text-gray-500">Copa Nacional Femenina</span>
                                    </div>
                                    
                                    <div class="grid grid-cols-3 gap-4 items-center">
                                        <!-- Team 1 -->
                                        <div class="text-center">
                                            <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg mx-auto mb-2 flex items-center justify-center">
                                                <span class="text-white font-bold">VB</span>
                                            </div>
                                            <h4 class="font-semibold text-gray-900">Voleibol Bogotá</h4>
                                            <p class="text-sm text-gray-500">Grupo A</p>
                                        </div>
                                        
                                        <!-- Score -->
                                        <div class="text-center">
                                            <div class="text-4xl font-bold text-gray-900 mb-2">2 - 1</div>
                                            <div class="text-sm text-gray-500">Sets</div>
                                            <div class="mt-2 text-lg font-semibold text-live-600">25 - 23</div>
                                            <div class="text-xs text-gray-500">Set Actual</div>
                                        </div>
                                        
                                        <!-- Team 2 -->
                                        <div class="text-center">
                                            <div class="w-16 h-16 bg-gradient-to-br from-red-500 to-red-600 rounded-lg mx-auto mb-2 flex items-center justify-center">
                                                <span class="text-white font-bold">AM</span>
                                            </div>
                                            <h4 class="font-semibold text-gray-900">Atlético Medellín</h4>
                                            <p class="text-sm text-gray-500">Grupo A</p>
                                        </div>
                                    </div>
                                    
                                    <div class="mt-6 flex justify-between items-center">
                                        <button class="text-volley-600 hover:text-volley-700 font-medium">
                                            <i class="fas fa-chart-line mr-2"></i>Estadísticas
                                        </button>
                                        <button class="bg-volley-600 text-white px-4 py-2 rounded-lg hover:bg-volley-700 transition-colors">
                                            Ver Detalles
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Live Sidebar -->
                    <div class="space-y-6">
                        <!-- Live Stats -->
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                            <h3 class="font-poppins font-semibold text-lg mb-4">Estadísticas en Vivo</h3>
                            <div class="space-y-4">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Ataques Exitosos</span>
                                    <span class="font-semibold">12 - 8</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Bloqueos</span>
                                    <span class="font-semibold">3 - 5</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Saques Directos</span>
                                    <span class="font-semibold">2 - 1</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Errores</span>
                                    <span class="font-semibold">4 - 6</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Resultados Tab -->
            <div id="resultados-content" class="tab-content hidden">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="font-poppins font-semibold text-lg mb-4">Resultados Recientes</h3>
                    <div class="space-y-4">
                        <!-- Result Item -->
                        <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                            <div class="flex items-center space-x-4">
                                <div class="text-center">
                                    <div class="text-sm text-gray-500">Ayer</div>
                                    <div class="text-xs text-gray-400">18:00</div>
                                </div>
                                <div class="flex items-center space-x-3">
                                    <span class="font-medium">Deportivo Cali</span>
                                    <span class="text-2xl font-bold text-gray-900">3</span>
                                    <span class="text-gray-500">-</span>
                                    <span class="text-2xl font-bold text-gray-900">1</span>
                                    <span class="font-medium">Once Caldas</span>
                                </div>
                            </div>
                            <button class="text-volley-600 hover:text-volley-700">
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Estadísticas Tab -->
            <div id="estadisticas-content" class="tab-content hidden">
                <div class="grid lg:grid-cols-2 gap-8">
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <h3 class="font-poppins font-semibold text-lg mb-4">Mejores Equipos</h3>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <span class="w-6 h-6 bg-yellow-500 rounded-full flex items-center justify-center text-white text-xs font-bold">1</span>
                                    <span class="font-medium">Voleibol Bogotá</span>
                                </div>
                                <span class="text-gray-600">95.2%</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <h3 class="font-poppins font-semibold text-lg mb-4">Jugadoras Destacadas</h3>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 bg-gray-300 rounded-full"></div>
                                    <div>
                                        <div class="font-medium">María González</div>
                                        <div class="text-sm text-gray-500">Voleibol Bogotá</div>
                                    </div>
                                </div>
                                <span class="text-gray-600">24 pts</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Bottom Navigation Mobile -->
    <nav class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 md:hidden z-40">
        <div class="grid grid-cols-4 h-16">
            <button class="flex flex-col items-center justify-center space-y-1 text-volley-600">
                <i class="fas fa-home text-lg"></i>
                <span class="text-xs">Inicio</span>
            </button>
            <button class="flex flex-col items-center justify-center space-y-1 text-gray-500">
                <i class="fas fa-circle text-lg"></i>
                <span class="text-xs">En Vivo</span>
            </button>
            <button class="flex flex-col items-center justify-center space-y-1 text-gray-500">
                <i class="fas fa-trophy text-lg"></i>
                <span class="text-xs">Torneos</span>
            </button>
            <button class="flex flex-col items-center justify-center space-y-1 text-gray-500">
                <i class="fas fa-user text-lg"></i>
                <span class="text-xs">Perfil</span>
            </button>
        </div>
    </nav>

    <!-- Floating Action Button -->
    <button class="fixed bottom-20 right-6 md:bottom-6 w-14 h-14 bg-volley-600 text-white rounded-full shadow-lg hover:bg-volley-700 transition-colors z-30">
        <i class="fas fa-plus text-xl"></i>
    </button>

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
        
        .tab-button {
            @apply border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300;
        }
        
        .tab-button.active {
            @apply border-volley-500 text-volley-600;
        }
        
        .tab-content {
            @apply animate-fade-in;
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
    </style>

    <script>
        // Tab functionality
        document.addEventListener('DOMContentLoaded', function() {
            const tabButtons = document.querySelectorAll('.tab-button');
            const tabContents = document.querySelectorAll('.tab-content');
            
            tabButtons.forEach(button => {
                button.addEventListener('click', () => {
                    const tabName = button.getAttribute('data-tab');
                    
                    // Remove active class from all buttons
                    tabButtons.forEach(btn => btn.classList.remove('active'));
                    // Add active class to clicked button
                    button.classList.add('active');
                    
                    // Hide all tab contents
                    tabContents.forEach(content => content.classList.add('hidden'));
                    // Show selected tab content
                    document.getElementById(tabName + '-content').classList.remove('hidden');
                });
            });
            
            // Smooth scroll for anchor links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
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
</div>