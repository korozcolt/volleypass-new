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
  Sparkles,
  Settings,
  Database,
  Clock,
  CheckCircle,
  ArrowRight,
  ArrowDown,
  Building,
  UserCog,
  FileText,
  CreditCard,
  Bell,
  BarChart,
  Workflow,
  Network,
  Layers,
  Target,
  Cpu,
  Lock,
  Smartphone,
  Monitor,
  GitBranch
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
    { icon: QrCode, title: "Carnetización Digital", desc: "Sistema QR con verificación instantánea y gestión de estados" },
    { icon: Trophy, title: "Gestión de Torneos", desc: "Algoritmos avanzados, marcadores en tiempo real y múltiples formatos" },
    { icon: Users, title: "Multi-Liga", desc: "Federados y descentralizados con 8 roles especializados" },
    { icon: Shield, title: "Seguridad Total", desc: "Autenticación robusta, control de acceso y auditoría completa" }
  ];

  const processes = [
    {
      title: "Registro y Validación",
      icon: UserCheck,
      steps: [
        "Registro de jugadoras/entrenadores",
        "Validación de documentos médicos",
        "Aprobación por administradores",
        "Generación automática de carnet QR"
      ],
      color: "blue"
    },
    {
      title: "Gestión de Torneos",
      icon: Trophy,
      steps: [
        "Configuración de liga y categorías",
        "Distribución automática de equipos",
        "Programación de partidos",
        "Seguimiento en tiempo real"
      ],
      color: "amber"
    },
    {
      title: "Control y Verificación",
      icon: Shield,
      steps: [
        "Verificación QR instantánea",
        "Control de elegibilidad",
        "Auditoría de transferencias",
        "Reportes y estadísticas"
      ],
      color: "green"
    }
  ];

  const managementTypes = [
    {
      title: "Gestión Federada",
      description: "Control centralizado con validación oficial",
      features: ["Registro oficial", "Transferencias controladas", "Certificaciones médicas", "Auditoría completa"],
      icon: Building,
      color: "blue"
    },
    {
      title: "Gestión Descentralizada",
      description: "Flexibilidad para torneos no oficiales",
      features: ["Registro simplificado", "Configuración flexible", "Gestión independiente", "Reportes básicos"],
      icon: Network,
      color: "green"
    }
  ];

  const hierarchyLevels = [
    { level: "Super Admin", description: "Control total del sistema", permissions: "Todas las funciones", icon: UserCog, color: "red" },
    { level: "Admin Liga", description: "Gestión de liga específica", permissions: "Configuración y torneos", icon: Settings, color: "blue" },
    { level: "Admin Club", description: "Gestión de club", permissions: "Jugadoras y equipos", icon: Users, color: "green" },
    { level: "Entrenador", description: "Gestión de equipo", permissions: "Consulta y reportes", icon: Award, color: "amber" }
  ];

  const stats = [
    { number: "100%", label: "Completado", icon: CheckCircle },
    { number: "15+", label: "Módulos Admin", icon: Award },
    { number: "25+", label: "Servicios", icon: Cpu },
    { number: "8", label: "Roles de Usuario", icon: UserCog },
    { number: "30+", label: "Configuraciones", icon: Settings },
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
        <div className="mb-20">
          <div className="text-center mb-8">
            <div className="inline-flex items-center px-4 py-2 bg-blue-500/20 backdrop-blur-sm rounded-full border border-blue-400/30">
              <Sparkles className="w-4 h-4 text-amber-400 mr-2" />
              <span className="text-blue-200 text-sm font-medium">Plataforma Integral de Gestión Deportiva</span>
            </div>
          </div>
          
          {/* Hero Grid: Logo + Text */}
          <div className="grid lg:grid-cols-2 gap-12 items-center mb-12">
            {/* Logo Section */}
            <div className="flex justify-center lg:justify-end">
              <div className="relative">
                <img 
                  src="/images/logo-volley_pass_white_back.png" 
                  alt="VolleyPass Logo" 
                  className="w-64 h-64 md:w-80 md:h-80 lg:w-96 lg:h-96 rounded-3xl shadow-2xl transform hover:scale-105 transition-all duration-300"
                />
                <div className="absolute inset-0 bg-gradient-to-tr from-blue-500/20 to-amber-500/20 rounded-3xl"></div>
              </div>
            </div>
            
            {/* Text Section */}
            <div className="text-center lg:text-left">
              <h1 className="text-5xl md:text-6xl lg:text-7xl font-bold mb-6 leading-tight">
                <span className="bg-gradient-to-r from-white via-blue-100 to-amber-200 bg-clip-text text-transparent">
                  VolleyPass
                </span>
                <br />
                <span className="text-3xl md:text-4xl lg:text-5xl bg-gradient-to-r from-blue-400 to-amber-400 bg-clip-text text-transparent">
                  Liga de Voleibol Sucre
                </span>
              </h1>
              
              <p className="text-lg md:text-xl lg:text-2xl text-blue-200 mb-8 leading-relaxed">
                Sistema de digitalización y carnetización deportiva que centraliza el registro, 
                verificación y gestión de <span className="text-amber-300 font-semibold">jugadoras</span>, 
                <span className="text-amber-300 font-semibold"> entrenadores</span> y 
                <span className="text-amber-300 font-semibold">clubes</span> federados y descentralizados.
              </p>
              
              <div className="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
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
            </div>
          </div>



          {/* Stats */}
          <div className="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 max-w-6xl mx-auto">
            {stats.map((stat, index) => {
              const Icon = stat.icon;
              return (
                <div key={index} className="group bg-white/5 backdrop-blur-sm rounded-2xl p-4 border border-white/10 hover:border-amber-400/30 transition-all duration-300 hover:bg-white/10">
                  <Icon className="w-6 h-6 text-amber-400 mx-auto mb-2 group-hover:scale-110 transition-transform" />
                  <div className="text-2xl font-bold text-white mb-1">{stat.number}</div>
                  <div className="text-blue-200 text-xs">{stat.label}</div>
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

        {/* Process Flow Section */}
        <div className="mb-24">
          <div className="text-center mb-16">
            <h2 className="text-4xl md:text-5xl font-bold text-white mb-6">
              Flujo de <span className="bg-gradient-to-r from-blue-400 to-green-400 bg-clip-text text-transparent">Procesos</span>
            </h2>
            <p className="text-xl text-blue-200 max-w-3xl mx-auto">
              Procesos automatizados y optimizados que garantizan eficiencia y control total
            </p>
          </div>

          <div className="grid lg:grid-cols-3 gap-8 mb-16">
            {processes.map((process, index) => {
              const Icon = process.icon;
              const colorClasses = {
                blue: 'from-blue-500/20 to-blue-600/10 border-blue-400/20 hover:border-blue-400/40 hover:shadow-blue-500/20',
                amber: 'from-amber-500/20 to-amber-600/10 border-amber-400/20 hover:border-amber-400/40 hover:shadow-amber-500/20',
                green: 'from-green-500/20 to-green-600/10 border-green-400/20 hover:border-green-400/40 hover:shadow-green-500/20'
              };
              const iconColorClasses = {
                blue: 'from-blue-500 to-blue-600',
                amber: 'from-amber-500 to-amber-600',
                green: 'from-green-500 to-green-600'
              };
              
              return (
                <div key={index} className={`group bg-gradient-to-br ${colorClasses[process.color as keyof typeof colorClasses]} backdrop-blur-sm rounded-3xl p-8 border transition-all duration-300 hover:shadow-2xl`}>
                  <div className="flex items-center mb-6">
                    <div className={`w-12 h-12 bg-gradient-to-br ${iconColorClasses[process.color as keyof typeof iconColorClasses]} rounded-xl flex items-center justify-center mr-4`}>
                      <Icon className="w-6 h-6 text-white" />
                    </div>
                    <h3 className="text-2xl font-bold text-white">{process.title}</h3>
                  </div>
                  <div className="space-y-4">
                    {process.steps.map((step, stepIndex) => (
                      <div key={stepIndex} className="flex items-start">
                        <div className="w-6 h-6 bg-white/20 rounded-full flex items-center justify-center mr-3 mt-0.5 flex-shrink-0">
                          <span className="text-white text-xs font-bold">{stepIndex + 1}</span>
                        </div>
                        <span className="text-blue-200 text-sm">{step}</span>
                      </div>
                    ))}
                  </div>
                  {index < processes.length - 1 && (
                    <div className="hidden lg:block absolute top-1/2 -right-4 transform -translate-y-1/2">
                      <ArrowRight className="w-8 h-8 text-amber-400/50" />
                    </div>
                  )}
                </div>
              );
            })}
          </div>
        </div>

        {/* Management Types Section */}
        <div className="mb-24">
          <div className="text-center mb-16">
            <h2 className="text-4xl md:text-5xl font-bold text-white mb-6">
              Tipos de <span className="bg-gradient-to-r from-green-400 to-blue-400 bg-clip-text text-transparent">Gestión</span>
            </h2>
            <p className="text-xl text-blue-200 max-w-3xl mx-auto">
              Flexibilidad total para adaptarse a diferentes necesidades organizacionales
            </p>
          </div>

          <div className="grid lg:grid-cols-2 gap-8 mb-16">
            {managementTypes.map((type, index) => {
              const Icon = type.icon;
              const colorClasses = {
                blue: 'from-blue-500/20 to-blue-600/10 border-blue-400/20 hover:border-blue-400/40 hover:shadow-blue-500/20',
                green: 'from-green-500/20 to-green-600/10 border-green-400/20 hover:border-green-400/40 hover:shadow-green-500/20'
              };
              const iconColorClasses = {
                blue: 'from-blue-500 to-blue-600',
                green: 'from-green-500 to-green-600'
              };
              
              return (
                <div key={index} className={`group bg-gradient-to-br ${colorClasses[type.color as keyof typeof colorClasses]} backdrop-blur-sm rounded-3xl p-8 border transition-all duration-300 hover:shadow-2xl`}>
                  <div className="flex items-center mb-6">
                    <div className={`w-16 h-16 bg-gradient-to-br ${iconColorClasses[type.color as keyof typeof iconColorClasses]} rounded-2xl flex items-center justify-center mr-6`}>
                      <Icon className="w-8 h-8 text-white" />
                    </div>
                    <div>
                      <h3 className="text-2xl font-bold text-white mb-2">{type.title}</h3>
                      <p className="text-blue-200">{type.description}</p>
                    </div>
                  </div>
                  <div className="grid grid-cols-2 gap-3">
                    {type.features.map((feature, featureIndex) => (
                      <div key={featureIndex} className="flex items-center">
                        <CheckCircle className="w-4 h-4 text-green-400 mr-2 flex-shrink-0" />
                        <span className="text-blue-200 text-sm">{feature}</span>
                      </div>
                    ))}
                  </div>
                </div>
              );
            })}
          </div>

          {/* Management Flow Diagram */}
          <div className="bg-gradient-to-r from-white/10 to-white/5 backdrop-blur-sm rounded-3xl p-8 border border-white/10">
            <h3 className="text-2xl font-bold text-white mb-8 text-center">Flujo de Gestión Integrada</h3>
            <div className="flex flex-col lg:flex-row items-center justify-center space-y-6 lg:space-y-0 lg:space-x-8">
              <div className="flex flex-col items-center">
                <div className="w-20 h-20 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center mb-4">
                  <Building className="w-10 h-10 text-white" />
                </div>
                <span className="text-white font-semibold">Liga Federada</span>
                <span className="text-blue-200 text-sm text-center">Control oficial<br />y certificado</span>
              </div>
              
              <div className="hidden lg:block">
                <ArrowRight className="w-8 h-8 text-amber-400" />
              </div>
              <div className="lg:hidden">
                <ArrowDown className="w-8 h-8 text-amber-400" />
              </div>
              
              <div className="flex flex-col items-center">
                <div className="w-20 h-20 bg-gradient-to-br from-amber-500 to-amber-600 rounded-2xl flex items-center justify-center mb-4">
                  <Workflow className="w-10 h-10 text-white" />
                </div>
                <span className="text-white font-semibold">VolleyPass</span>
                <span className="text-blue-200 text-sm text-center">Plataforma<br />unificada</span>
              </div>
              
              <div className="hidden lg:block">
                <ArrowRight className="w-8 h-8 text-amber-400" />
              </div>
              <div className="lg:hidden">
                <ArrowDown className="w-8 h-8 text-amber-400" />
              </div>
              
              <div className="flex flex-col items-center">
                <div className="w-20 h-20 bg-gradient-to-br from-green-500 to-green-600 rounded-2xl flex items-center justify-center mb-4">
                  <Network className="w-10 h-10 text-white" />
                </div>
                <span className="text-white font-semibold">Liga Descentralizada</span>
                <span className="text-blue-200 text-sm text-center">Gestión flexible<br />e independiente</span>
              </div>
            </div>
          </div>
        </div>

        {/* Hierarchy Section */}
        <div className="mb-24">
          <div className="text-center mb-16">
            <h2 className="text-4xl md:text-5xl font-bold text-white mb-6">
              Niveles <span className="bg-gradient-to-r from-red-400 to-amber-400 bg-clip-text text-transparent">Jerárquicos</span>
            </h2>
            <p className="text-xl text-blue-200 max-w-3xl mx-auto">
              Sistema de roles y permisos que garantiza control granular y seguridad
            </p>
          </div>

          <div className="space-y-6">
            {hierarchyLevels.map((level, index) => {
              const Icon = level.icon;
              const colorClasses = {
                red: 'from-red-500/20 to-red-600/10 border-red-400/20 hover:border-red-400/40 hover:shadow-red-500/20',
                blue: 'from-blue-500/20 to-blue-600/10 border-blue-400/20 hover:border-blue-400/40 hover:shadow-blue-500/20',
                green: 'from-green-500/20 to-green-600/10 border-green-400/20 hover:border-green-400/40 hover:shadow-green-500/20',
                amber: 'from-amber-500/20 to-amber-600/10 border-amber-400/20 hover:border-amber-400/40 hover:shadow-amber-500/20'
              };
              const iconColorClasses = {
                red: 'from-red-500 to-red-600',
                blue: 'from-blue-500 to-blue-600',
                green: 'from-green-500 to-green-600',
                amber: 'from-amber-500 to-amber-600'
              };
              
              return (
                <div key={index} className={`group bg-gradient-to-br ${colorClasses[level.color as keyof typeof colorClasses]} backdrop-blur-sm rounded-3xl p-6 border transition-all duration-300 hover:shadow-2xl`}>
                  <div className="flex items-center justify-between">
                    <div className="flex items-center">
                      <div className={`w-16 h-16 bg-gradient-to-br ${iconColorClasses[level.color as keyof typeof iconColorClasses]} rounded-2xl flex items-center justify-center mr-6`}>
                        <Icon className="w-8 h-8 text-white" />
                      </div>
                      <div>
                        <h3 className="text-2xl font-bold text-white mb-1">{level.level}</h3>
                        <p className="text-blue-200 mb-2">{level.description}</p>
                        <div className="flex items-center">
                          <Lock className="w-4 h-4 text-amber-400 mr-2" />
                          <span className="text-amber-300 text-sm font-medium">{level.permissions}</span>
                        </div>
                      </div>
                    </div>
                    <div className="text-right">
                      <div className="text-3xl font-bold text-white mb-1">#{index + 1}</div>
                      <div className="text-blue-200 text-sm">Nivel</div>
                    </div>
                  </div>
                  {index < hierarchyLevels.length - 1 && (
                    <div className="flex justify-center mt-4">
                      <ArrowDown className="w-6 h-6 text-amber-400/50" />
                    </div>
                  )}
                </div>
              );
            })}
          </div>
        </div>

        {/* Technical Architecture Section */}
        <div className="mb-24">
          <div className="text-center mb-16">
            <h2 className="text-4xl md:text-5xl font-bold text-white mb-6">
              Arquitectura <span className="bg-gradient-to-r from-purple-400 to-pink-400 bg-clip-text text-transparent">Técnica</span>
            </h2>
            <p className="text-xl text-blue-200 max-w-3xl mx-auto">
              Tecnologías modernas y escalables para máximo rendimiento
            </p>
          </div>

          <div className="grid lg:grid-cols-3 gap-8">
            <div className="bg-gradient-to-br from-purple-500/20 to-purple-600/10 backdrop-blur-sm rounded-3xl p-8 border border-purple-400/20 hover:border-purple-400/40 transition-all duration-300 hover:shadow-2xl hover:shadow-purple-500/20">
              <div className="flex items-center mb-6">
                <div className="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center mr-4">
                  <Monitor className="w-6 h-6 text-white" />
                </div>
                <h3 className="text-2xl font-bold text-white">Frontend</h3>
              </div>
              <ul className="space-y-3 text-blue-200">
                <li className="flex items-center"><ChevronRight className="w-4 h-4 mr-2 text-purple-400" />React + TypeScript</li>
                <li className="flex items-center"><ChevronRight className="w-4 h-4 mr-2 text-purple-400" />Tailwind CSS</li>
                <li className="flex items-center"><ChevronRight className="w-4 h-4 mr-2 text-purple-400" />Livewire 3.x</li>
                <li className="flex items-center"><ChevronRight className="w-4 h-4 mr-2 text-purple-400" />Responsive Design</li>
              </ul>
            </div>

            <div className="bg-gradient-to-br from-blue-500/20 to-blue-600/10 backdrop-blur-sm rounded-3xl p-8 border border-blue-400/20 hover:border-blue-400/40 transition-all duration-300 hover:shadow-2xl hover:shadow-blue-500/20">
              <div className="flex items-center mb-6">
                <div className="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center mr-4">
                  <Cpu className="w-6 h-6 text-white" />
                </div>
                <h3 className="text-2xl font-bold text-white">Backend</h3>
              </div>
              <ul className="space-y-3 text-blue-200">
                <li className="flex items-center"><ChevronRight className="w-4 h-4 mr-2 text-blue-400" />Laravel 12.x</li>
                <li className="flex items-center"><ChevronRight className="w-4 h-4 mr-2 text-blue-400" />PHP 8.2+</li>
                <li className="flex items-center"><ChevronRight className="w-4 h-4 mr-2 text-blue-400" />Filament 3.x</li>
                <li className="flex items-center"><ChevronRight className="w-4 h-4 mr-2 text-blue-400" />API RESTful</li>
              </ul>
            </div>

            <div className="bg-gradient-to-br from-green-500/20 to-green-600/10 backdrop-blur-sm rounded-3xl p-8 border border-green-400/20 hover:border-green-400/40 transition-all duration-300 hover:shadow-2xl hover:shadow-green-500/20">
              <div className="flex items-center mb-6">
                <div className="w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center mr-4">
                  <Database className="w-6 h-6 text-white" />
                </div>
                <h3 className="text-2xl font-bold text-white">Infraestructura</h3>
              </div>
              <ul className="space-y-3 text-blue-200">
                <li className="flex items-center"><ChevronRight className="w-4 h-4 mr-2 text-green-400" />MySQL 8.0+</li>
                <li className="flex items-center"><ChevronRight className="w-4 h-4 mr-2 text-green-400" />Redis Cache</li>
                <li className="flex items-center"><ChevronRight className="w-4 h-4 mr-2 text-green-400" />Queue System</li>
                <li className="flex items-center"><ChevronRight className="w-4 h-4 mr-2 text-green-400" />File Storage</li>
              </ul>
            </div>
          </div>
        </div>

        {/* Services Section */}
        <div className="mb-24">
          <div className="text-center mb-16">
            <h2 className="text-4xl md:text-5xl font-bold text-white mb-6">
              Servicios <span className="bg-gradient-to-r from-cyan-400 to-blue-400 bg-clip-text text-transparent">Disponibles</span>
            </h2>
            <p className="text-xl text-blue-200 max-w-3xl mx-auto">
              Más de 25 servicios especializados que cubren todas las necesidades de gestión deportiva
            </p>
          </div>

          <div className="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
            {[
              { icon: CreditCard, title: "Carnetización", desc: "Generación y validación QR", color: "blue" },
              { icon: Trophy, title: "Torneos", desc: "Gestión completa de competencias", color: "amber" },
              { icon: Users, title: "Jugadoras", desc: "Registro y transferencias", color: "green" },
              { icon: FileText, title: "Certificados", desc: "Médicos y documentación", color: "purple" },
              { icon: Bell, title: "Notificaciones", desc: "Sistema de alertas", color: "red" },
              { icon: BarChart, title: "Estadísticas", desc: "Reportes y análisis", color: "cyan" },
              { icon: Settings, title: "Configuración", desc: "Personalización de liga", color: "gray" },
              { icon: Smartphone, title: "API Móvil", desc: "Verificación en tiempo real", color: "pink" }
            ].map((service, index) => {
              const Icon = service.icon;
              const colorClasses = {
                blue: 'from-blue-500 to-blue-600',
                amber: 'from-amber-500 to-amber-600',
                green: 'from-green-500 to-green-600',
                purple: 'from-purple-500 to-purple-600',
                red: 'from-red-500 to-red-600',
                cyan: 'from-cyan-500 to-cyan-600',
                gray: 'from-gray-500 to-gray-600',
                pink: 'from-pink-500 to-pink-600'
              };
              
              return (
                <div key={index} className="group bg-white/5 backdrop-blur-sm rounded-2xl p-6 border border-white/10 hover:border-white/20 transition-all duration-300 hover:bg-white/10 hover:scale-105">
                  <div className={`w-12 h-12 bg-gradient-to-br ${colorClasses[service.color as keyof typeof colorClasses]} rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform`}>
                    <Icon className="w-6 h-6 text-white" />
                  </div>
                  <h3 className="text-lg font-bold text-white mb-2 group-hover:text-amber-300 transition-colors">
                    {service.title}
                  </h3>
                  <p className="text-blue-200 text-sm group-hover:text-blue-100 transition-colors">
                    {service.desc}
                  </p>
                </div>
              );
            })}
          </div>
        </div>

        {/* Benefits Section */}
        <div className="mb-24">
          <div className="text-center mb-16">
            <h2 className="text-4xl md:text-5xl font-bold text-white mb-6">
              Beneficios <span className="bg-gradient-to-r from-emerald-400 to-teal-400 bg-clip-text text-transparent">Clave</span>
            </h2>
            <p className="text-xl text-blue-200 max-w-3xl mx-auto">
              Ventajas competitivas que transforman la gestión deportiva
            </p>
          </div>

          <div className="grid lg:grid-cols-2 gap-8">
            <div className="space-y-6">
              {[
                { icon: Clock, title: "Eficiencia Operativa", desc: "Reducción del 80% en tiempos de gestión administrativa" },
                { icon: Shield, title: "Seguridad Garantizada", desc: "Encriptación SHA-256 y control de acceso granular" },
                { icon: Target, title: "Precisión Total", desc: "Eliminación de errores humanos en verificaciones" },
                { icon: Zap, title: "Tiempo Real", desc: "Actualizaciones instantáneas y sincronización automática" }
              ].map((benefit, index) => {
                const Icon = benefit.icon;
                return (
                  <div key={index} className="flex items-start space-x-4">
                    <div className="w-12 h-12 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-xl flex items-center justify-center flex-shrink-0">
                      <Icon className="w-6 h-6 text-white" />
                    </div>
                    <div>
                      <h3 className="text-xl font-bold text-white mb-2">{benefit.title}</h3>
                      <p className="text-blue-200">{benefit.desc}</p>
                    </div>
                  </div>
                );
              })}
            </div>
            
            <div className="bg-gradient-to-br from-emerald-500/20 to-teal-600/10 backdrop-blur-sm rounded-3xl p-8 border border-emerald-400/20">
              <h3 className="text-2xl font-bold text-white mb-6">ROI Comprobado</h3>
              <div className="space-y-4">
                <div className="flex justify-between items-center">
                  <span className="text-blue-200">Reducción de costos</span>
                  <span className="text-emerald-300 font-bold">65%</span>
                </div>
                <div className="flex justify-between items-center">
                  <span className="text-blue-200">Tiempo de verificación</span>
                  <span className="text-emerald-300 font-bold">2 segundos</span>
                </div>
                <div className="flex justify-between items-center">
                  <span className="text-blue-200">Precisión de datos</span>
                  <span className="text-emerald-300 font-bold">99.9%</span>
                </div>
                <div className="flex justify-between items-center">
                  <span className="text-blue-200">Satisfacción usuarios</span>
                  <span className="text-emerald-300 font-bold">98%</span>
                </div>
              </div>
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
            <div className="grid md:grid-cols-3 gap-8 mb-8">
              <div className="text-center">
                <div className="text-3xl font-bold text-amber-400 mb-2">100%</div>
                <div className="text-blue-200">Sistema Completado</div>
              </div>
              <div className="text-center">
                <div className="text-3xl font-bold text-amber-400 mb-2">25+</div>
                <div className="text-blue-200">Servicios Especializados</div>
              </div>
              <div className="text-center">
                <div className="text-3xl font-bold text-amber-400 mb-2">8</div>
                <div className="text-blue-200">Roles de Usuario</div>
              </div>
            </div>
            <div className="flex flex-wrap justify-center gap-4">
              {['Laravel 12.x', 'Filament 3.x', 'Livewire 3.x', 'PHP 8.2+', 'React + TypeScript', 'MySQL 8.0+', 'Redis Cache'].map((tech, index) => (
                <span key={index} className="px-4 py-2 bg-blue-500/20 text-blue-200 rounded-full border border-blue-400/30 text-sm font-medium">
                  {tech}
                </span>
              ))}
            </div>
          </div>
        </div>

        {/* Value Proposition Section */}
        <div className="mb-24">
          <div className="bg-gradient-to-r from-indigo-600/20 to-purple-600/20 backdrop-blur-sm rounded-3xl p-12 border border-indigo-400/30">
            <div className="text-center mb-12">
              <h2 className="text-4xl md:text-5xl font-bold text-white mb-6">
                ¿Por qué elegir <span className="bg-gradient-to-r from-indigo-400 to-purple-400 bg-clip-text text-transparent">VolleyPass</span>?
              </h2>
              <p className="text-xl text-blue-200 max-w-3xl mx-auto">
                La solución más completa y avanzada para la gestión deportiva moderna
              </p>
            </div>
            
            <div className="grid lg:grid-cols-3 gap-8 mb-12">
              <div className="text-center">
                <div className="w-20 h-20 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl flex items-center justify-center mx-auto mb-6">
                  <Layers className="w-10 h-10 text-white" />
                </div>
                <h3 className="text-2xl font-bold text-white mb-4">Solución Integral</h3>
                <p className="text-blue-200">
                  Todo lo que necesitas en una sola plataforma: desde registro hasta estadísticas avanzadas
                </p>
              </div>
              
              <div className="text-center">
                <div className="w-20 h-20 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-2xl flex items-center justify-center mx-auto mb-6">
                  <GitBranch className="w-10 h-10 text-white" />
                </div>
                <h3 className="text-2xl font-bold text-white mb-4">Escalabilidad</h3>
                <p className="text-blue-200">
                  Crece con tu organización, desde clubes locales hasta federaciones nacionales
                </p>
              </div>
              
              <div className="text-center">
                <div className="w-20 h-20 bg-gradient-to-br from-amber-500 to-orange-600 rounded-2xl flex items-center justify-center mx-auto mb-6">
                  <Star className="w-10 h-10 text-white" />
                </div>
                <h3 className="text-2xl font-bold text-white mb-4">Soporte Experto</h3>
                <p className="text-blue-200">
                  Equipo especializado en deportes y tecnología para garantizar tu éxito
                </p>
              </div>
            </div>
            
            <div className="bg-white/5 backdrop-blur-sm rounded-2xl p-8 border border-white/10">
              <div className="grid md:grid-cols-2 gap-8 items-center">
                <div>
                  <h3 className="text-2xl font-bold text-white mb-4">Implementación Inmediata</h3>
                  <ul className="space-y-3 text-blue-200">
                    <li className="flex items-center">
                      <CheckCircle className="w-5 h-5 text-green-400 mr-3" />
                      Sistema 100% completado y probado
                    </li>
                    <li className="flex items-center">
                      <CheckCircle className="w-5 h-5 text-green-400 mr-3" />
                      Migración de datos sin interrupciones
                    </li>
                    <li className="flex items-center">
                      <CheckCircle className="w-5 h-5 text-green-400 mr-3" />
                      Capacitación incluida para todo el equipo
                    </li>
                    <li className="flex items-center">
                      <CheckCircle className="w-5 h-5 text-green-400 mr-3" />
                      Soporte técnico 24/7 durante el primer mes
                    </li>
                  </ul>
                </div>
                
                <div className="text-center">
                  <div className="bg-gradient-to-br from-green-500/20 to-emerald-600/10 rounded-2xl p-6 border border-green-400/20">
                    <div className="text-4xl font-bold text-green-400 mb-2">ROI</div>
                    <div className="text-2xl font-bold text-white mb-4">300%+</div>
                    <div className="text-green-300 text-sm mb-4">Retorno de inversión en 6 meses</div>
                    <div className="space-y-2 text-sm text-blue-200">
                      <div>✓ Reducción de costos operativos</div>
                      <div>✓ Eliminación de errores manuales</div>
                      <div>✓ Optimización de recursos</div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        {/* CTA Section */}
        <div className="text-center">
          <div className="bg-gradient-to-r from-blue-600/20 to-amber-600/20 backdrop-blur-sm rounded-3xl p-12 border border-amber-400/30">
            <h2 className="text-4xl md:text-5xl font-bold text-white mb-6">
              ¿Listo para <span className="bg-gradient-to-r from-amber-400 to-amber-600 bg-clip-text text-transparent">Digitalizar</span> tu Liga?
            </h2>
            <p className="text-xl text-blue-200 mb-8 max-w-3xl mx-auto">
              Únete a la revolución digital del voleibol con VolleyPass. Gestión profesional, verificación instantánea y control total. 
              <span className="text-amber-300 font-semibold">¡Implementación inmediata disponible!</span>
            </p>
            
            <div className="grid md:grid-cols-3 gap-6 mb-8 max-w-4xl mx-auto">
              <div className="bg-white/5 rounded-xl p-4 border border-white/10">
                <div className="text-2xl font-bold text-amber-400 mb-1">0</div>
                <div className="text-blue-200 text-sm">Días de configuración</div>
              </div>
              <div className="bg-white/5 rounded-xl p-4 border border-white/10">
                <div className="text-2xl font-bold text-amber-400 mb-1">24/7</div>
                <div className="text-blue-200 text-sm">Soporte técnico</div>
              </div>
              <div className="bg-white/5 rounded-xl p-4 border border-white/10">
                <div className="text-2xl font-bold text-amber-400 mb-1">100%</div>
                <div className="text-blue-200 text-sm">Garantía de funcionamiento</div>
              </div>
            </div>
            
            <div className="flex flex-col sm:flex-row gap-4 justify-center">
              <a
                href="/admin"
                className="group inline-flex items-center px-8 py-4 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-white font-bold rounded-xl transition-all duration-300 transform hover:scale-105 hover:shadow-2xl hover:shadow-amber-500/30"
              >
                <Zap className="mr-3 w-5 h-5 group-hover:rotate-12 transition-transform" />
                Acceder al Sistema
                <ChevronRight className="ml-2 w-4 h-4 group-hover:translate-x-1 transition-transform" />
              </a>
              <a
                href="/admin/login"
                className="group inline-flex items-center px-8 py-4 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-semibold rounded-xl transition-all duration-300 transform hover:scale-105 hover:shadow-2xl hover:shadow-blue-500/30"
              >
                <UserCheck className="mr-3 w-5 h-5" />
                Iniciar Sesión
                <ChevronRight className="ml-2 w-4 h-4 group-hover:translate-x-1 transition-transform" />
              </a>
              <a
                href="/contacto"
                className="group inline-flex items-center px-8 py-4 border-2 border-green-400/50 text-green-300 hover:text-white font-semibold rounded-xl hover:bg-green-500/20 hover:border-green-400 transition-all duration-300 backdrop-blur-sm"
              >
                <Globe className="mr-3 w-5 h-5" />
                Solicitar Demo
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