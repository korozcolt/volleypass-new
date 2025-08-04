function PlainWelcome() {
  const div = document.createElement('div');
  div.className = 'min-h-screen bg-gradient-to-br from-slate-700 via-slate-600 to-slate-500 relative overflow-hidden';
  
  // Meta tags are handled by welcome.blade.php, no need for custom styles here
  
  div.innerHTML = `
    <!-- Background Pattern -->
    <div class="absolute inset-0 opacity-10">
      <div class="absolute top-20 left-20 w-32 h-32 bg-yellow-400 rounded-full blur-xl"></div>
      <div class="absolute top-40 right-32 w-24 h-24 bg-blue-300 rounded-full blur-lg"></div>
      <div class="absolute bottom-32 left-1/3 w-40 h-40 bg-yellow-300 rounded-full blur-2xl"></div>
      <div class="absolute bottom-20 right-20 w-28 h-28 bg-blue-400 rounded-full blur-xl"></div>
    </div>
    
    <!-- Navigation -->
    <nav class="relative z-10 px-6 py-4">
      <div class="max-w-7xl mx-auto flex justify-between items-center">
        <div class="flex items-center gap-4">
           <img src="/images/logo-volley_pass_white_back.png" alt="VolleyPass Logo" class="w-12 h-12 rounded-xl shadow-lg">
           <div>
             <span class="volleypass-logo text-white font-bold text-xl block">VolleyPass</span>
             <span class="text-slate-300 text-sm">Sucre, Colombia</span>
           </div>
         </div>
        <button class="bg-gradient-to-r from-slate-200 to-slate-300 text-slate-700 px-6 py-2 rounded-lg font-semibold hover:from-slate-100 hover:to-slate-200 transition-all duration-300 transform hover:scale-105 shadow-lg">
          Iniciar Sesión
        </button>
      </div>
    </nav>
    
    <!-- Hero Section -->
    <div class="relative z-10 max-w-7xl mx-auto px-6 py-20">
      <div class="grid lg:grid-cols-2 gap-12 items-center mb-16">
        <!-- Texto del Hero -->
        <div class="text-center lg:text-left">
          <h1 class="text-5xl md:text-7xl text-white mb-6 leading-tight tracking-tight">
            <span class="volleypass-logo bg-gradient-to-r from-slate-200 to-slate-100 bg-clip-text text-transparent">VolleyPass</span>
          </h1>
          <p class="text-xl md:text-2xl text-slate-100 mb-8 leading-relaxed">
            Plataforma Integral de Gestión para Ligas de Voleibol
          </p>
          <p class="text-lg text-slate-200 mb-12">
            Sistema de Digitalización y Carnetización Deportiva
          </p>
          
          <span class="volleypass-logo text-white font-bold text-xl block mb-4">VolleyPass</span>
          
          <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
            <button class="bg-gradient-to-r from-slate-200 to-slate-300 text-slate-700 px-8 py-4 rounded-xl font-bold text-lg hover:from-slate-100 hover:to-slate-200 transition-all duration-300 transform hover:scale-105 shadow-xl">
              🚀 Acceder al Sistema
            </button>
            <button class="border-2 border-slate-300 text-slate-300 px-8 py-4 rounded-xl font-bold text-lg hover:bg-slate-300 hover:text-slate-700 transition-all duration-300 transform hover:scale-105">
              📖 Ver Documentación
            </button>
          </div>
        </div>
        
        <!-- Logo del Hero -->
        <div class="flex justify-center lg:justify-end">
          <div class="relative">
            <div class="absolute inset-0 bg-gradient-to-r from-yellow-400 to-yellow-500 rounded-3xl blur-xl opacity-30 animate-pulse"></div>
            <img src="/images/logo-volley_pass_white_back.png" alt="VolleyPass Logo" class="relative w-64 h-64 md:w-80 md:h-80 rounded-3xl shadow-2xl transform hover:scale-105 transition-all duration-300">
          </div>
        </div>
      </div>

      
      <!-- Features Grid -->
       <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8 mb-20">
         <!-- Feature 1 -->
         <div class="feature-card bg-white/10 backdrop-blur-lg rounded-2xl p-6 border border-white/20 hover:bg-white/15 transition-all duration-300 transform hover:scale-105 hover:shadow-2xl hover:shadow-yellow-400/20">
           <div class="w-12 h-12 bg-gradient-to-r from-yellow-400 to-yellow-500 rounded-xl flex items-center justify-center mb-4 transform transition-transform duration-300 hover:rotate-12">
             <span class="text-blue-900 text-2xl">🆔</span>
           </div>
           <h3 class="text-xl font-heading-semibold text-white mb-3">Carnetización Digital</h3>
           <p class="text-slate-300 font-body-medium">Sistema de carnets digitales con códigos QR únicos para verificación instantánea en partidos oficiales.</p>
         </div>
         
         <!-- Feature 2 -->
         <div class="feature-card bg-white/10 backdrop-blur-lg rounded-2xl p-6 border border-white/20 hover:bg-white/15 transition-all duration-300 transform hover:scale-105 hover:shadow-2xl hover:shadow-yellow-400/20">
           <div class="w-12 h-12 bg-gradient-to-r from-yellow-400 to-yellow-500 rounded-xl flex items-center justify-center mb-4 transform transition-transform duration-300 hover:rotate-12">
             <span class="text-blue-900 text-2xl">🏆</span>
           </div>
           <h3 class="text-xl font-heading-semibold text-white mb-3">Gestión de Torneos</h3>
           <p class="text-slate-300 font-body-medium">Sistema completo para organizar torneos oficiales y alternos con marcadores en tiempo real.</p>
         </div>
         
         <!-- Feature 3 -->
         <div class="feature-card bg-white/10 backdrop-blur-lg rounded-2xl p-6 border border-white/20 hover:bg-white/15 transition-all duration-300 transform hover:scale-105 hover:shadow-2xl hover:shadow-yellow-400/20">
           <div class="w-12 h-12 bg-gradient-to-r from-yellow-400 to-yellow-500 rounded-xl flex items-center justify-center mb-4 transform transition-transform duration-300 hover:rotate-12">
             <span class="text-blue-900 text-2xl">⚖️</span>
           </div>
           <h3 class="text-xl font-heading-semibold text-white mb-3">Sistema Dual</h3>
           <p class="text-slate-300 font-body-medium">Gestión de equipos federados (oficiales) y descentralizados (ligas alternas) con reglas configurables.</p>
         </div>
         
         <!-- Feature 4 -->
         <div class="feature-card bg-white/10 backdrop-blur-lg rounded-2xl p-6 border border-white/20 hover:bg-white/15 transition-all duration-300 transform hover:scale-105 hover:shadow-2xl hover:shadow-yellow-400/20">
           <div class="w-12 h-12 bg-gradient-to-r from-yellow-400 to-yellow-500 rounded-xl flex items-center justify-center mb-4 transform transition-transform duration-300 hover:rotate-12">
             <span class="text-blue-900 text-2xl">🏥</span>
           </div>
           <h3 class="text-xl font-heading-semibold text-white mb-3">Módulo Médico</h3>
           <p class="text-slate-300 font-body-medium">Gestión integral de certificados médicos con alertas automáticas y seguimiento de lesiones.</p>
         </div>
         
         <!-- Feature 5 -->
         <div class="feature-card bg-white/10 backdrop-blur-lg rounded-2xl p-6 border border-white/20 hover:bg-white/15 transition-all duration-300 transform hover:scale-105 hover:shadow-2xl hover:shadow-yellow-400/20">
           <div class="w-12 h-12 bg-gradient-to-r from-yellow-400 to-yellow-500 rounded-xl flex items-center justify-center mb-4 transform transition-transform duration-300 hover:rotate-12">
             <span class="text-blue-900 text-2xl">📱</span>
           </div>
           <h3 class="text-xl font-heading-semibold text-white mb-3">API de Verificación</h3>
           <p class="text-slate-300 font-body-medium">Verificación instantánea con códigos QR optimizada para aplicaciones móviles y eventos grandes.</p>
         </div>
         
         <!-- Feature 6 -->
         <div class="feature-card bg-white/10 backdrop-blur-lg rounded-2xl p-6 border border-white/20 hover:bg-white/15 transition-all duration-300 transform hover:scale-105 hover:shadow-2xl hover:shadow-yellow-400/20">
           <div class="w-12 h-12 bg-gradient-to-r from-yellow-400 to-yellow-500 rounded-xl flex items-center justify-center mb-4 transform transition-transform duration-300 hover:rotate-12">
             <span class="text-blue-900 text-2xl">👥</span>
           </div>
           <h3 class="text-xl font-bold text-white mb-3">Multi-Rol</h3>
           <p class="text-slate-300">Sistema de usuarios con 8 roles diferentes: jugadoras, entrenadores, árbitros, directivos y más.</p>
         </div>
       </div>
      
      <!-- Stats Section -->
      <div class="bg-white/10 backdrop-blur-lg rounded-3xl p-8 border border-white/20 mb-20">
        <h2 class="text-3xl font-bold text-white text-center mb-8">Estado del Proyecto</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
          <div class="text-center">
            <div class="text-4xl font-bold text-slate-200 mb-2">95%</div>
            <div class="text-slate-300">Completado</div>
          </div>
          <div class="text-center">
            <div class="text-4xl font-bold text-slate-200 mb-2">13+</div>
            <div class="text-slate-300">Módulos Admin</div>
          </div>
          <div class="text-center">
            <div class="text-4xl font-bold text-slate-200 mb-2">30+</div>
            <div class="text-slate-300">Modelos</div>
          </div>
          <div class="text-center">
            <div class="text-4xl font-bold text-slate-200 mb-2">8</div>
            <div class="text-slate-300">Roles de Usuario</div>
          </div>
        </div>
      </div>
      
      <!-- Technology Stack -->
        <div class="text-center mb-20">
          <h2 class="text-3xl font-bold text-white mb-8">Tecnologías</h2>
          <div class="flex flex-wrap justify-center gap-4">
            <span class="tech-badge bg-slate-600 text-slate-100 px-4 py-2 rounded-lg font-semibold cursor-pointer hover:bg-slate-500 transition-colors">Laravel 12.x</span>
            <span class="tech-badge bg-slate-500 text-slate-100 px-4 py-2 rounded-lg font-semibold cursor-pointer hover:bg-slate-400 transition-colors">Filament 3.x</span>
            <span class="tech-badge bg-slate-700 text-slate-100 px-4 py-2 rounded-lg font-semibold cursor-pointer hover:bg-slate-600 transition-colors">Livewire 3.x</span>
            <span class="tech-badge bg-slate-600 text-slate-100 px-4 py-2 rounded-lg font-semibold cursor-pointer hover:bg-slate-500 transition-colors">PHP 8.2+</span>
            <span class="tech-badge bg-slate-500 text-slate-100 px-4 py-2 rounded-lg font-semibold cursor-pointer hover:bg-slate-400 transition-colors">MySQL</span>
            <span class="tech-badge bg-slate-700 text-slate-100 px-4 py-2 rounded-lg font-semibold cursor-pointer hover:bg-slate-600 transition-colors">Tailwind CSS</span>
          </div>
        </div>
    </div>
    
    <!-- Footer -->
    <footer class="relative z-10 bg-black/20 backdrop-blur-lg border-t border-white/20">
      <div class="max-w-7xl mx-auto px-6 py-8">
        <div class="grid md:grid-cols-3 gap-8">
          <div>
            <div class="flex items-center space-x-3 mb-4">
              <div class="w-8 h-8 bg-gradient-to-r from-yellow-400 to-yellow-500 rounded-lg flex items-center justify-center">
                <span class="text-blue-900 font-bold">🏐</span>
              </div>
              <span class="volleypass-logo text-white font-bold text-lg">VolleyPass</span> <span class="text-white font-bold text-lg">Sucre</span>
            </div>
            <p class="text-blue-200">Digitalizando el voleibol en Sucre, Colombia</p>
          </div>
          
          <div>
            <h3 class="text-white font-bold mb-4">Enlaces Rápidos</h3>
            <ul class="space-y-2 text-blue-200">
              <li><a href="#" class="hover:text-yellow-400 transition-colors">Dashboard Público</a></li>
              <li><a href="#" class="hover:text-yellow-400 transition-colors">Torneos Activos</a></li>
              <li><a href="#" class="hover:text-yellow-400 transition-colors">Documentación</a></li>
              <li><a href="#" class="hover:text-yellow-400 transition-colors">Soporte</a></li>
            </ul>
          </div>
          
          <div>
            <h3 class="text-white font-bold mb-4">Contacto</h3>
            <div class="space-y-2 text-blue-200">
              <p>📧 soporte@volleypass.co</p>
              <p>📱 +57 300 123 4567</p>
              <p>📍 Sucre, Colombia</p>
            </div>
          </div>
        </div>
        
        <div class="border-t border-white/20 mt-8 pt-8 text-center text-blue-200">
          <p>&copy; 2024 <span class="volleypass-logo">VolleyPass</span> Sucre. Todos los derechos reservados.</p>
        </div>
      </div>
    </footer>
  `;
  
  return div;
}

export default PlainWelcome;