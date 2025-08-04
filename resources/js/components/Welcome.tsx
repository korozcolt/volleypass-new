import React, { useState, useEffect } from 'react';
import { 
  ExternalLink, 
  Shield, 
  Users, 
  Trophy, 
  QrCode, 
  Zap, 
  Globe, 
  Star,
  ChevronRight,
  Play,
  Award,
  UserCheck,
  Calendar,
  BarChart3,
  Sparkles
} from 'lucide-react';

const Welcome = () => {
  const [isVisible, setIsVisible] = useState(false);
  const [currentFeature, setCurrentFeature] = useState(0);

  useEffect(() => {
    setIsVisible(true);
    const interval = setInterval(() => {
      setCurrentFeature((prev) => (prev + 1) % 4);
    }, 3000);
    return () => clearInterval(interval);
  }, []);

  const features = [
    { icon: QrCode, title: "Carnetización Digital", desc: "Sistema QR con verificación instantánea" },
    { icon: Trophy, title: "Gestión de Torneos", desc: "Algoritmos avanzados y marcadores en tiempo real" },
    { icon: Users, title: "Multi-Liga", desc: "Federados y descentralizados en una plataforma" },
    { icon: Shield, title: "Seguridad Total", desc: "Autenticación robusta y control de acceso" }
  ];

  const stats = [
    { number: "95%", label: "Completado", icon: BarChart3 },
    { number: "13+", label: "Módulos Admin", icon: Award },
    { number: "30+", label: "Configuraciones", icon: UserCheck },
    { number: "7", label: "Categorías", icon: Calendar }
  ];

  return (
    <div className="min-h-screen bg-gradient-to-br from-slate-900 via-blue-900 to-slate-800 relative overflow-hidden">
      {/* Animated Background Elements */}
      <div className="absolute inset-0">
        <div className="absolute top-20 left-10 w-72 h-72 bg-blue-500/10 rounded-full blur-3xl animate-pulse"></div>
        <div className="absolute bottom-20 right-10 w-96 h-96 bg-amber-500/10 rounded-full blur-3xl animate-pulse delay-1000"></div>
        <div className="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-gradient-to-r from-blue-500/5 to-amber-500/5 rounded-full blur-3xl"></div>
      </div>

      {/* Navigation */}
      <nav className="relative z-10 px-6 py-4">
        <div className="max-w-7xl mx-auto flex justify-between items-center">
          <div className="flex items-center space-x-3">
            <div className="w-10 h-10 bg-gradient-to-br from-blue-500 to-amber-500 rounded-lg flex items-center justify-center">
              <Trophy className="w-6 h-6 text-white" />
            </div>
            <span className="text-2xl font-bold bg-gradient-to-r from-blue-400 to-amber-400 bg-clip-text text-transparent">
              VolleyPass
            </span>
          </div>
          <div className="hidden md:flex space-x-6">
            <a href="#features" className="text-blue-200 hover:text-white transition-colors">Características</a>
            <a href="#about" className="text-blue-200 hover:text-white transition-colors">Acerca de</a>
            <a href="#contact" className="text-blue-200 hover:text-white transition-colors">Contacto</a>
          </div>
        </div>
      </nav>

      {/* Hero Section */}
      <div className={`relative z-10 container mx-auto px-6 pt-16 pb-24 transition-all duration-1000 ${isVisible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-10'}`}>
        <div className="text-center mb-20">
          <div className="inline-flex items-center px-4 py-2 bg-blue-500/20 backdrop-blur-sm rounded-full border border-blue-400/30 mb-8">
            <Sparkles className="w-4 h-4 text-amber-400 mr-2" />
            <span className="text-blue-200 text-sm font-medium">Plataforma Integral de Gestión Deportiva</span>
          </div>
          
          <h1 className="text-6xl md:text-7xl font-bold mb-8 leading-tight">
            <span className="bg-gradient-to-r from-white via-blue-100 to-amber-200 bg-clip-text text-transparent">
              VolleyPass
            </span>
            <br />
            <span className="text-4xl md:text-5xl bg-gradient-to-r from-blue-400 to-amber-400 bg-clip-text text-transparent">
              Liga de Voleibol Sucre
            </span>
          </h1>
          
          <p className="text-xl md:text-2xl text-blue-200 max-w-4xl mx-auto mb-12 leading-relaxed">
            Sistema de digitalización y carnetización deportiva que centraliza el registro, 
            verificación y gestión de <span className="text-amber-300 font-semibold">jugadoras</span>, 
            <span className="text-amber-300 font-semibold"> entrenadores</span> y 
            <span className="text-amber-300 font-semibold">clubes</span> federados y descentralizados.
          </p>

          <div className="flex flex-col sm:flex-row gap-4 justify-center items-center mb-16">
            <a
              href="/admin"
              className="group inline-flex items-center px-8 py-4 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-semibold rounded-xl transition-all duration-300 transform hover:scale-105 hover:shadow-2xl hover:shadow-blue-500/25"
            >
              <Play className="mr-3 w-5 h-5 group-hover:translate-x-1 transition-transform" />
              Acceder al Sistema
              <ChevronRight className="ml-2 w-4 h-4 group-hover:translate-x-1 transition-transform" />
            </a>
            <a
              href="/admin/login"
              className="group inline-flex items-center px-8 py-4 border-2 border-amber-400/50 text-amber-300 hover:text-white font-semibold rounded-xl hover:bg-amber-500/20 hover:border-amber-400 transition-all duration-300 backdrop-blur-sm"
            >
              <UserCheck className="mr-3 w-5 h-5" />
              Iniciar Sesión
            </a>
          </div>

          {/* Stats */}
          <div className="grid grid-cols-2 md:grid-cols-4 gap-6 max-w-4xl mx-auto">
            {stats.map((stat, index) => {
              const Icon = stat.icon;
              return (
                <div key={index} className="group bg-white/5 backdrop-blur-sm rounded-2xl p-6 border border-white/10 hover:border-amber-400/30 transition-all duration-300 hover:bg-white/10">
                  <Icon className="w-8 h-8 text-amber-400 mx-auto mb-3 group-hover:scale-110 transition-transform" />
                  <div className="text-3xl font-bold text-white mb-1">{stat.number}</div>
                  <div className="text-blue-200 text-sm">{stat.label}</div>
                </div>
              );
            })}
          </div>
        </div>

        {/* Features Section */}
        <div id="features" className="mb-24">
          <div className="text-center mb-16">
            <h2 className="text-4xl md:text-5xl font-bold text-white mb-6">
              Características <span className="bg-gradient-to-r from-amber-400 to-amber-600 bg-clip-text text-transparent">Principales</span>
            </h2>
            <p className="text-xl text-blue-200 max-w-3xl mx-auto">
              Una plataforma completa que revoluciona la gestión deportiva con tecnología de vanguardia
            </p>
          </div>

          <div className="grid md:grid-cols-2 lg:grid-cols-4 gap-8 mb-16">
            {features.map((feature, index) => {
              const Icon = feature.icon;
              const isActive = currentFeature === index;
              return (
                <div 
                  key={index} 
                  className={`group relative bg-gradient-to-br from-white/10 to-white/5 backdrop-blur-sm rounded-2xl p-8 border transition-all duration-500 hover:scale-105 cursor-pointer ${
                    isActive 
                      ? 'border-amber-400/50 shadow-2xl shadow-amber-500/20 bg-gradient-to-br from-amber-500/20 to-blue-500/20' 
                      : 'border-white/10 hover:border-blue-400/30'
                  }`}
                >
                  <div className={`w-16 h-16 rounded-2xl flex items-center justify-center mb-6 transition-all duration-300 ${
                    isActive 
                      ? 'bg-gradient-to-br from-amber-500 to-amber-600 shadow-lg shadow-amber-500/30' 
                      : 'bg-gradient-to-br from-blue-500 to-blue-600 group-hover:shadow-lg group-hover:shadow-blue-500/30'
                  }`}>
                    <Icon className="w-8 h-8 text-white" />
                  </div>
                  <h3 className="text-xl font-bold text-white mb-3 group-hover:text-amber-300 transition-colors">
                    {feature.title}
                  </h3>
                  <p className="text-blue-200 group-hover:text-blue-100 transition-colors">
                    {feature.desc}
                  </p>
                  {isActive && (
                    <div className="absolute inset-0 bg-gradient-to-r from-amber-500/10 to-blue-500/10 rounded-2xl animate-pulse"></div>
                  )}
                </div>
              );
            })}
          </div>

          {/* Detailed Features */}
          <div className="grid lg:grid-cols-3 gap-8">
            <div className="group bg-gradient-to-br from-blue-500/20 to-blue-600/10 backdrop-blur-sm rounded-3xl p-8 border border-blue-400/20 hover:border-blue-400/40 transition-all duration-300 hover:shadow-2xl hover:shadow-blue-500/20">
              <div className="flex items-center mb-6">
                <div className="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center mr-4">
                  <QrCode className="w-6 h-6 text-white" />
                </div>
                <h3 className="text-2xl font-bold text-white">Sistema QR Avanzado</h3>
              </div>
              <ul className="space-y-3 text-blue-200">
                <li className="flex items-center"><ChevronRight className="w-4 h-4 mr-2 text-amber-400" />Generación automática tras aprobación</li>
                <li className="flex items-center"><ChevronRight className="w-4 h-4 mr-2 text-amber-400" />Hash SHA-256 y tokens de verificación</li>
                <li className="flex items-center"><ChevronRight className="w-4 h-4 mr-2 text-amber-400" />Estados: Activo, vencido, suspendido</li>
                <li className="flex items-center"><ChevronRight className="w-4 h-4 mr-2 text-amber-400" />API de verificación en tiempo real</li>
              </ul>
            </div>

            <div className="group bg-gradient-to-br from-amber-500/20 to-amber-600/10 backdrop-blur-sm rounded-3xl p-8 border border-amber-400/20 hover:border-amber-400/40 transition-all duration-300 hover:shadow-2xl hover:shadow-amber-500/20">
              <div className="flex items-center mb-6">
                <div className="w-12 h-12 bg-gradient-to-br from-amber-500 to-amber-600 rounded-xl flex items-center justify-center mr-4">
                  <Trophy className="w-6 h-6 text-white" />
                </div>
                <h3 className="text-2xl font-bold text-white">Gestión de Torneos</h3>
              </div>
              <ul className="space-y-3 text-blue-200">
                <li className="flex items-center"><ChevronRight className="w-4 h-4 mr-2 text-blue-400" />Liga Regular, Copa, Mixto, Relámpago</li>
                <li className="flex items-center"><ChevronRight className="w-4 h-4 mr-2 text-blue-400" />Algoritmos de distribución balanceados</li>
                <li className="flex items-center"><ChevronRight className="w-4 h-4 mr-2 text-blue-400" />Sistema de puntuación configurable</li>
                <li className="flex items-center"><ChevronRight className="w-4 h-4 mr-2 text-blue-400" />Cache inteligente para performance</li>
              </ul>
            </div>

            <div className="group bg-gradient-to-br from-green-500/20 to-green-600/10 backdrop-blur-sm rounded-3xl p-8 border border-green-400/20 hover:border-green-400/40 transition-all duration-300 hover:shadow-2xl hover:shadow-green-500/20">
              <div className="flex items-center mb-6">
                <div className="w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center mr-4">
                  <Users className="w-6 h-6 text-white" />
                </div>
                <h3 className="text-2xl font-bold text-white">Multi-Rol & Jerarquía</h3>
              </div>
              <ul className="space-y-3 text-blue-200">
                <li className="flex items-center"><ChevronRight className="w-4 h-4 mr-2 text-green-400" />8 roles especializados</li>
                <li className="flex items-center"><ChevronRight className="w-4 h-4 mr-2 text-green-400" />Gestión dual: Federados/Descentralizados</li>
                <li className="flex items-center"><ChevronRight className="w-4 h-4 mr-2 text-green-400" />7 categorías por edad</li>
                <li className="flex items-center"><ChevronRight className="w-4 h-4 mr-2 text-green-400" />30+ configuraciones por liga</li>
              </ul>
            </div>
          </div>
        </div>

        {/* About Section */}
        <div id="about" className="text-center mb-24">
          <div className="bg-gradient-to-r from-white/10 to-white/5 backdrop-blur-sm rounded-3xl p-12 border border-white/10">
            <h2 className="text-4xl font-bold text-white mb-8">
              Sobre <span className="bg-gradient-to-r from-amber-400 to-amber-600 bg-clip-text text-transparent">VolleyPass Sucre</span>
            </h2>
            <p className="text-xl text-blue-200 max-w-4xl mx-auto mb-8 leading-relaxed">
              Plataforma integral diseñada para digitalizar y modernizar la gestión de la Liga de Voleibol de Sucre, Colombia. 
              Centraliza el registro, verificación y gestión de jugadoras, entrenadores y clubes, garantizando 
              <span className="text-amber-300 font-semibold"> transparencia</span>, 
              <span className="text-amber-300 font-semibold">eficiencia</span> y 
              <span className="text-amber-300 font-semibold">control</span> en torneos oficiales y no oficiales.
            </p>
            <div className="flex flex-wrap justify-center gap-4">
              {['Laravel 12.x', 'Filament 3.x', 'Livewire 3.x', 'PHP 8.2+', 'React + TypeScript'].map((tech, index) => (
                <span key={index} className="px-4 py-2 bg-blue-500/20 text-blue-200 rounded-full border border-blue-400/30 text-sm font-medium">
                  {tech}
                </span>
              ))}
            </div>
          </div>
        </div>

        {/* CTA Section */}
        <div className="text-center">
          <div className="bg-gradient-to-r from-blue-600/20 to-amber-600/20 backdrop-blur-sm rounded-3xl p-12 border border-amber-400/30">
            <h2 className="text-4xl font-bold text-white mb-6">
              ¿Listo para <span className="bg-gradient-to-r from-amber-400 to-amber-600 bg-clip-text text-transparent">Digitalizar</span> tu Liga?
            </h2>
            <p className="text-xl text-blue-200 mb-8 max-w-2xl mx-auto">
              Únete a la revolución digital del voleibol con VolleyPass. Gestión profesional, verificación instantánea y control total.
            </p>
            <div className="flex flex-col sm:flex-row gap-4 justify-center">
              <a
                href="/admin"
                className="group inline-flex items-center px-8 py-4 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-white font-bold rounded-xl transition-all duration-300 transform hover:scale-105 hover:shadow-2xl hover:shadow-amber-500/30"
              >
                <Zap className="mr-3 w-5 h-5 group-hover:rotate-12 transition-transform" />
                Comenzar Ahora
                <ChevronRight className="ml-2 w-4 h-4 group-hover:translate-x-1 transition-transform" />
              </a>
              <a
                href="#contact"
                className="group inline-flex items-center px-8 py-4 border-2 border-blue-400/50 text-blue-300 hover:text-white font-semibold rounded-xl hover:bg-blue-500/20 hover:border-blue-400 transition-all duration-300 backdrop-blur-sm"
              >
                <Globe className="mr-3 w-5 h-5" />
                Más Información
              </a>
            </div>
          </div>
        </div>
      </div>

      {/* Footer */}
      <footer id="contact" className="relative z-10 bg-black/20 backdrop-blur-sm border-t border-white/10 py-12">
        <div className="container mx-auto px-6">
          <div className="grid md:grid-cols-3 gap-8 text-center md:text-left">
            <div>
              <div className="flex items-center justify-center md:justify-start space-x-3 mb-4">
                <div className="w-8 h-8 bg-gradient-to-br from-blue-500 to-amber-500 rounded-lg flex items-center justify-center">
                  <Trophy className="w-5 h-5 text-white" />
                </div>
                <span className="text-xl font-bold bg-gradient-to-r from-blue-400 to-amber-400 bg-clip-text text-transparent">
                  VolleyPass
                </span>
              </div>
              <p className="text-blue-200 text-sm">
                Plataforma Integral de Gestión<br />
                Liga de Voleibol Sucre, Colombia
              </p>
            </div>
            <div>
              <h4 className="text-white font-semibold mb-4">Enlaces Rápidos</h4>
              <div className="space-y-2">
                <a href="/admin" className="block text-blue-200 hover:text-amber-300 transition-colors text-sm">Panel Administrativo</a>
                <a href="/admin/login" className="block text-blue-200 hover:text-amber-300 transition-colors text-sm">Iniciar Sesión</a>
                <a href="#features" className="block text-blue-200 hover:text-amber-300 transition-colors text-sm">Características</a>
              </div>
            </div>
            <div>
              <h4 className="text-white font-semibold mb-4">Tecnologías</h4>
              <div className="space-y-2 text-sm text-blue-200">
                <div>Laravel 12.x + Filament 3.x</div>
                <div>React + TypeScript</div>
                <div>Livewire 3.x + PHP 8.2+</div>
              </div>
            </div>
          </div>
          <div className="border-t border-white/10 mt-8 pt-8 text-center">
            <p className="text-blue-300 text-sm">
              © 2024 VolleyPass Sucre. Sistema de Gestión Deportiva Digital.
            </p>
          </div>
        </div>
      </footer>
    </div>
  );
};

export default Welcome;