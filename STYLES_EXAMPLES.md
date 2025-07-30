//DashboardPage.tsx

import React, { useState } from 'react';
import { User, BarChart3, Activity, Award, Heart, MessageCircle, MapPin, Calendar, Trophy, Bell, Settings, LogOut } from 'lucide-react';

interface DashboardPageProps {
  onNavigate: (view: string) => void;
}

export default function DashboardPage({ onNavigate }: DashboardPageProps) {
  const [activeTab, setActiveTab] = useState('overview');

  const playerProfile = {
    id: 1,
    name: "María Fernández",
    number: 7,
    position: "Opuesta",
    height: "1.75m",
    age: 22,
    team: "Águilas Sucre",
    city: "Sincelejo, Sucre",
    photo: "https://images.pexels.com/photos/8007513/pexels-photo-8007513.jpeg?auto=compress&cs=tinysrgb&w=300&h=300&dpr=2",
    stats: {
      points: 247,
      aces: 38,
      blocks: 52,
      receptions: 189,
      attackEfficiency: 68,
      receptionEfficiency: 92
    },
    achievements: ["Mejor Opuesta Liga 2023", "MVP Semifinal"],
    medicalStatus: "APTA",
    nextMatch: "vs Tigres - 15 Nov",
    recentMatches: [
      { opponent: "Panteras", result: "W", score: "3-1", date: "10 Nov", points: 18 },
      { opponent: "Cóndores", result: "W", score: "3-0", date: "5 Nov", points: 22 },
      { opponent: "Jaguares", result: "L", score: "1-3", date: "1 Nov", points: 12 },
      { opponent: "Leones", result: "W", score: "3-2", date: "28 Oct", points: 25 }
    ]
  };

  const notifications = [
    { id: 1, type: "match", message: "Próximo partido vs Tigres mañana a las 20:00", time: "2h", unread: true },
    { id: 2, type: "medical", message: "Certificado médico vence en 15 días", time: "1d", unread: true },
    { id: 3, type: "achievement", message: "¡Felicidades! Alcanzaste 250 puntos esta temporada", time: "3d", unread: false },
    { id: 4, type: "team", message: "Entrenamiento cancelado para mañana", time: "5d", unread: false }
  ];

  const tabs = [
    { id: 'overview', name: 'Resumen', icon: <BarChart3 className="w-5 h-5" /> },
    { id: 'stats', name: 'Estadísticas', icon: <Activity className="w-5 h-5" /> },
    { id: 'matches', name: 'Partidos', icon: <Trophy className="w-5 h-5" /> },
    { id: 'medical', name: 'Médico', icon: <Heart className="w-5 h-5" /> }
  ];

  return (
    <div className="min-h-screen bg-gradient-to-br from-slate-900 via-blue-900 to-slate-800">
      {/* Header */}
      <header className="bg-gradient-to-r from-yellow-400 via-blue-600 to-red-600 shadow-2xl sticky top-0 z-50">
        <div className="container mx-auto px-4">
          <div className="flex items-center justify-between h-16">
            <button 
              onClick={() => onNavigate('home')}
              className="flex items-center space-x-3 hover:opacity-80 transition-opacity duration-200"
            >
              <div className="w-12 h-12 bg-white rounded-full flex items-center justify-center shadow-lg">
                <span className="text-2xl">🏐</span>
              </div>
              <div>
                <h1 className="text-2xl font-black text-white tracking-tight">VolleyPass</h1>
                <p className="text-xs text-yellow-100 font-medium">Liga de Voleibol Sucre</p>
              </div>
            </button>

            <nav className="hidden lg:flex items-center space-x-8">
              <button 
                onClick={() => onNavigate('matches')}
                className="text-white hover:text-yellow-200 font-semibold transition-colors duration-200"
              >
                Partidos
              </button>
              <button 
                onClick={() => onNavigate('tournaments')}
                className="text-white hover:text-yellow-200 font-semibold transition-colors duration-200"
              >
                Torneos
              </button>
              <button 
                onClick={() => onNavigate('dashboard')}
                className="text-white bg-white/20 px-3 py-2 rounded-lg font-semibold transition-colors duration-200"
              >
                Dashboard
              </button>
            </nav>

            <div className="flex items-center space-x-4">
              <button className="relative p-2 text-white hover:text-yellow-200 transition-colors duration-200">
                <Bell className="w-5 h-5" />
                <div className="absolute -top-1 -right-1 w-3 h-3 bg-red-500 rounded-full animate-pulse"></div>
              </button>
              <button className="p-2 text-white hover:text-yellow-200 transition-colors duration-200">
                <Settings className="w-5 h-5" />
              </button>
            </div>
          </div>
        </div>
      </header>

      {/* Player Header */}
      <div className="relative mb-8">
        <div 
          className="h-48 bg-gradient-to-r from-yellow-400 via-blue-600 to-red-600 relative overflow-hidden"
          style={{
            backgroundImage: `linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.4)), url('https://images.pexels.com/photos/1103844/pexels-photo-1103844.jpeg?auto=compress&cs=tinysrgb&w=1260&h=300&dpr=2')`,
            backgroundSize: 'cover',
            backgroundPosition: 'center'
          }}
        >
          <div className="absolute inset-0 bg-gradient-to-r from-black/60 to-transparent"></div>
          <div className="absolute bottom-6 left-6 flex items-end space-x-6">
            <div className="w-32 h-32 rounded-full overflow-hidden border-4 border-white shadow-2xl">
              <img 
                src={playerProfile.photo} 
                alt={playerProfile.name}
                className="w-full h-full object-cover"
              />
            </div>
            <div className="text-white pb-2">
              <h1 className="text-4xl font-black mb-2">
                {playerProfile.name} 
                <span className="text-yellow-400">#{playerProfile.number}</span>
              </h1>
              <p className="text-xl font-semibold mb-1">
                {playerProfile.position} • {playerProfile.height} • {playerProfile.age} años
              </p>
              <p className="text-lg text-yellow-200 flex items-center space-x-2">
                <span className="text-2xl">🏐</span>
                <span>{playerProfile.team}</span>
              </p>
              <p className="text-gray-300 flex items-center space-x-1 mt-1">
                <MapPin className="w-4 h-4" />
                <span>{playerProfile.city}</span>
              </p>
            </div>
          </div>
        </div>
      </div>

      <div className="container mx-auto px-4 pb-12">
        {/* Navigation Tabs */}
        <div className="flex flex-wrap gap-2 mb-8">
          {tabs.map((tab) => (
            <button
              key={tab.id}
              onClick={() => setActiveTab(tab.id)}
              className={`flex items-center space-x-2 px-6 py-3 rounded-lg font-bold transition-all duration-200 ${
                activeTab === tab.id
                  ? 'bg-gradient-to-r from-yellow-400 to-yellow-500 text-black shadow-lg'
                  : 'bg-slate-800 text-white hover:bg-slate-700'
              }`}
            >
              {tab.icon}
              <span>{tab.name}</span>
            </button>
          ))}
        </div>

        <div className="grid lg:grid-cols-3 gap-8">
          {/* Main Content */}
          <div className="lg:col-span-2 space-y-8">
            {activeTab === 'overview' && (
              <>
                {/* Season Stats */}
                <section className="bg-gradient-to-br from-slate-800 to-slate-700 rounded-2xl p-8 shadow-2xl border border-slate-600">
                  <h2 className="text-3xl font-black text-white mb-8 flex items-center space-x-3">
                    <BarChart3 className="w-8 h-8 text-yellow-400" />
                    <span>TEMPORADA 2024</span>
                  </h2>
                  
                  <div className="grid md:grid-cols-4 gap-6 mb-8">
                    <div className="text-center">
                      <div className="text-5xl font-mono font-black text-yellow-400 mb-2">
                        {playerProfile.stats.points}
                      </div>
                      <div className="text-white font-bold text-lg mb-2">PUNTOS</div>
                      <div className="w-full bg-slate-600 rounded-full h-3">
                        <div className="bg-gradient-to-r from-yellow-400 to-yellow-500 h-3 rounded-full" style={{width: '85%'}}></div>
                      </div>
                    </div>
                    
                    <div className="text-center">
                      <div className="text-5xl font-mono font-black text-blue-400 mb-2">
                        {playerProfile.stats.aces}
                      </div>
                      <div className="text-white font-bold text-lg mb-2">ACES</div>
                      <div className="w-full bg-slate-600 rounded-full h-3">
                        <div className="bg-gradient-to-r from-blue-400 to-blue-500 h-3 rounded-full" style={{width: '70%'}}></div>
                      </div>
                    </div>
                    
                    <div className="text-center">
                      <div className="text-5xl font-mono font-black text-red-400 mb-2">
                        {playerProfile.stats.blocks}
                      </div>
                      <div className="text-white font-bold text-lg mb-2">BLOQUEOS</div>
                      <div className="w-full bg-slate-600 rounded-full h-3">
                        <div className="bg-gradient-to-r from-red-400 to-red-500 h-3 rounded-full" style={{width: '90%'}}></div>
                      </div>
                    </div>
                    
                    <div className="text-center">
                      <div className="text-5xl font-mono font-black text-green-400 mb-2">
                        {playerProfile.stats.receptions}
                      </div>
                      <div className="text-white font-bold text-lg mb-2">RECEPCIONES</div>
                      <div className="w-full bg-slate-600 rounded-full h-3">
                        <div className="bg-gradient-to-r from-green-400 to-green-500 h-3 rounded-full" style={{width: '95%'}}></div>
                      </div>
                    </div>
                  </div>

                  {/* Efficiency Stats */}
                  <div className="grid md:grid-cols-2 gap-6">
                    <div className="bg-slate-700/50 rounded-xl p-6">
                      <h3 className="text-xl font-bold text-white mb-4 flex items-center space-x-2">
                        <Activity className="w-5 h-5 text-yellow-400" />
                        <span>Efectividad de Ataque</span>
                      </h3>
                      <div className="flex items-center space-x-4">
                        <div className="text-4xl font-mono font-black text-yellow-400">
                          {playerProfile.stats.attackEfficiency}%
                        </div>
                        <div className="flex-1">
                          <div className="w-full bg-slate-600 rounded-full h-4">
                            <div 
                              className="bg-gradient-to-r from-yellow-400 to-yellow-500 h-4 rounded-full transition-all duration-1000" 
                              style={{width: `${playerProfile.stats.attackEfficiency}%`}}
                            ></div>
                          </div>
                          <p className="text-gray-400 text-sm mt-2">Promedio Liga: 58%</p>
                        </div>
                      </div>
                    </div>
                    
                    <div className="bg-slate-700/50 rounded-xl p-6">
                      <h3 className="text-xl font-bold text-white mb-4 flex items-center space-x-2">
                        <Activity className="w-5 h-5 text-green-400" />
                        <span>Efectividad de Recepción</span>
                      </h3>
                      <div className="flex items-center space-x-4">
                        <div className="text-4xl font-mono font-black text-green-400">
                          {playerProfile.stats.receptionEfficiency}%
                        </div>
                        <div className="flex-1">
                          <div className="w-full bg-slate-600 rounded-full h-4">
                            <div 
                              className="bg-gradient-to-r from-green-400 to-green-500 h-4 rounded-full transition-all duration-1000" 
                              style={{width: `${playerProfile.stats.receptionEfficiency}%`}}
                            ></div>
                          </div>
                          <p className="text-gray-400 text-sm mt-2">Promedio Liga: 78%</p>
                        </div>
                      </div>
                    </div>
                  </div>
                </section>

                {/* Recent Matches */}
                <section className="bg-gradient-to-br from-slate-800 to-slate-700 rounded-2xl p-8 shadow-2xl border border-slate-600">
                  <h2 className="text-3xl font-black text-white mb-6 flex items-center space-x-3">
                    <Trophy className="w-8 h-8 text-blue-400" />
                    <span>ÚLTIMOS PARTIDOS</span>
                  </h2>
                  <div className="space-y-4">
                    {playerProfile.recentMatches.map((match, index) => (
                      <div key={index} className="flex items-center justify-between p-4 bg-slate-700/50 rounded-lg hover:bg-slate-700 transition-colors duration-200">
                        <div className="flex items-center space-x-4">
                          <div className={`w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm ${
                            match.result === 'W' ? 'bg-green-600 text-white' : 'bg-red-600 text-white'
                          }`}>
                            {match.result}
                          </div>
                          <div>
                            <p className="text-white font-bold">vs {match.opponent}</p>
                            <p className="text-gray-400 text-sm">{match.date}</p>
                          </div>
                        </div>
                        <div className="text-right">
                          <p className="text-white font-bold">{match.score}</p>
                          <p className="text-yellow-400 text-sm">{match.points} pts</p>
                        </div>
                      </div>
                    ))}
                  </div>
                </section>
              </>
            )}

            {activeTab === 'stats' && (
              <section className="bg-gradient-to-br from-slate-800 to-slate-700 rounded-2xl p-8 shadow-2xl border border-slate-600">
                <h2 className="text-3xl font-black text-white mb-8">ESTADÍSTICAS DETALLADAS</h2>
                <div className="space-y-6">
                  <div className="grid md:grid-cols-3 gap-6">
                    <div className="text-center p-6 bg-slate-700/50 rounded-xl">
                      <div className="text-3xl font-mono font-black text-yellow-400 mb-2">15.8</div>
                      <div className="text-white font-bold">Puntos por Partido</div>
                    </div>
                    <div className="text-center p-6 bg-slate-700/50 rounded-xl">
                      <div className="text-3xl font-mono font-black text-blue-400 mb-2">2.4</div>
                      <div className="text-white font-bold">Aces por Partido</div>
                    </div>
                    <div className="text-center p-6 bg-slate-700/50 rounded-xl">
                      <div className="text-3xl font-mono font-black text-red-400 mb-2">3.3</div>
                      <div className="text-white font-bold">Bloqueos por Partido</div>
                    </div>
                  </div>
                </div>
              </section>
            )}

            {activeTab === 'matches' && (
              <section className="bg-gradient-to-br from-slate-800 to-slate-700 rounded-2xl p-8 shadow-2xl border border-slate-600">
                <h2 className="text-3xl font-black text-white mb-8">CALENDARIO DE PARTIDOS</h2>
                <div className="space-y-4">
                  <div className="p-4 bg-blue-600/20 rounded-lg border border-blue-600/30">
                    <div className="flex justify-between items-center">
                      <div>
                        <p className="text-blue-400 font-bold text-lg">PRÓXIMO PARTIDO</p>
                        <p className="text-white">vs Tigres Sucre</p>
                        <p className="text-gray-400 text-sm">15 Nov 2024 - 20:00</p>
                      </div>
                      <div className="text-right">
                        <p className="text-gray-400 text-sm">Coliseo Municipal</p>
                        <button className="bg-blue-600 text-white px-4 py-2 rounded-lg font-bold mt-2">
                          Ver Detalles
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              </section>
            )}

            {activeTab === 'medical' && (
              <section className="bg-gradient-to-br from-slate-800 to-slate-700 rounded-2xl p-8 shadow-2xl border border-slate-600">
                <h2 className="text-3xl font-black text-white mb-8">ESTADO MÉDICO</h2>
                <div className="space-y-6">
                  <div className="flex items-center space-x-3 bg-green-600/20 p-4 rounded-lg border border-green-600/30">
                    <div className="w-4 h-4 bg-green-400 rounded-full animate-pulse"></div>
                    <span className="text-green-400 font-bold text-lg">✅ {playerProfile.medicalStatus}</span>
                  </div>
                  <div className="grid md:grid-cols-2 gap-6">
                    <div>
                      <h3 className="text-white font-bold mb-2">Certificado Médico</h3>
                      <p className="text-gray-400 text-sm">Válido hasta: 15 Dic 2024</p>
                    </div>
                    <div>
                      <h3 className="text-white font-bold mb-2">Última Revisión</h3>
                      <p className="text-gray-400 text-sm">10 Nov 2024</p>
                    </div>
                  </div>
                </div>
              </section>
            )}
          </div>

          {/* Sidebar */}
          <div className="space-y-8">
            {/* Digital ID Card */}
            <section className="bg-gradient-to-br from-slate-800 to-slate-700 rounded-2xl p-6 shadow-2xl border border-slate-600">
              <h3 className="text-2xl font-black text-white mb-6 flex items-center space-x-2">
                <User className="w-6 h-6 text-blue-400" />
                <span>CARNET DIGITAL</span>
              </h3>
              <div className="bg-gradient-to-r from-yellow-400 via-blue-600 to-red-600 p-1 rounded-xl">
                <div className="bg-white rounded-lg p-6">
                  <div className="text-center">
                    <div className="w-20 h-20 rounded-full overflow-hidden mx-auto mb-4 border-4 border-gray-200">
                      <img 
                        src={playerProfile.photo} 
                        alt={playerProfile.name}
                        className="w-full h-full object-cover"
                      />
                    </div>
                    <h4 className="font-black text-gray-800 text-lg">{playerProfile.name}</h4>
                    <p className="text-gray-600 font-semibold">#{playerProfile.number} • {playerProfile.position}</p>
                    <p className="text-gray-500 text-sm">{playerProfile.team}</p>
                    
                    <div className="mt-4 p-4 bg-gray-100 rounded-lg">
                      <div className="w-24 h-24 bg-gray-800 mx-auto rounded-lg flex items-center justify-center">
                        <span className="text-white text-xs">QR CODE</span>
                      </div>
                      <p className="text-xs text-gray-500 mt-2">ID: VPS-{playerProfile.id.toString().padStart(4, '0')}</p>
                    </div>
                  </div>
                </div>
              </div>
            </section>

            {/* Notifications */}
            <section className="bg-gradient-to-br from-slate-800 to-slate-700 rounded-2xl p-6 shadow-2xl border border-slate-600">
              <h3 className="text-2xl font-black text-white mb-6 flex items-center space-x-2">
                <Bell className="w-6 h-6 text-yellow-400" />
                <span>NOTIFICACIONES</span>
              </h3>
              <div className="space-y-3">
                {notifications.slice(0, 4).map((notification) => (
                  <div key={notification.id} className={`p-3 rounded-lg ${
                    notification.unread ? 'bg-blue-600/20 border border-blue-600/30' : 'bg-slate-700/50'
                  }`}>
                    <p className="text-white text-sm">{notification.message}</p>
                    <p className="text-gray-400 text-xs mt-1">{notification.time}</p>
                  </div>
                ))}
              </div>
            </section>

            {/* Quick Actions */}
            <section className="space-y-3">
              <button className="w-full bg-gradient-to-r from-yellow-400 to-yellow-500 text-black font-bold py-3 px-4 rounded-lg hover:from-yellow-500 hover:to-yellow-600 transition-all duration-200 flex items-center justify-center space-x-2">
                <BarChart3 className="w-5 h-5" />
                <span>VER ESTADÍSTICAS COMPLETAS</span>
              </button>
              <button className="w-full bg-gradient-to-r from-blue-600 to-blue-700 text-white font-bold py-3 px-4 rounded-lg hover:from-blue-700 hover:to-blue-800 transition-all duration-200 flex items-center justify-center space-x-2">
                <MessageCircle className="w-5 h-5" />
                <span>MENSAJE AL ENTRENADOR</span>
              </button>
            </section>
          </div>
        </div>
      </div>
    </div>
  );
}

//HomePage.tsx

import React from 'react';
import { Play, Users, Trophy, Shield, Zap, CheckCircle, ArrowRight, Star, Globe, Database, Lock, BarChart3 } from 'lucide-react';

interface HomePageProps {
  onNavigate: (view: string) => void;
}

export default function HomePage({ onNavigate }: HomePageProps) {
  const features = [
    {
      icon: <Shield className="w-8 h-8 text-yellow-400" />,
      title: "Sistema Dual de Gestión",
      description: "Equipos federados (liga oficial) y descentralizados (ligas alternas) en una sola plataforma"
    },
    {
      icon: <Zap className="w-8 h-8 text-blue-400" />,
      title: "Verificación QR Instantánea",
      description: "API de verificación en tiempo real con códigos QR seguros SHA-256"
    },
    {
      icon: <Trophy className="w-8 h-8 text-red-400" />,
      title: "Gestión Completa de Torneos",
      description: "Desde inscripción hasta premiación con algoritmos de distribución balanceados"
    },
    {
      icon: <Users className="w-8 h-8 text-green-400" />,
      title: "Carnetización Digital",
      description: "Carnets digitales con estados avanzados y renovación automática por temporadas"
    },
    {
      icon: <BarChart3 className="w-8 h-8 text-purple-400" />,
      title: "Panel Administrativo",
      description: "13+ recursos Filament operativos con métricas en tiempo real"
    },
    {
      icon: <Globe className="w-8 h-8 text-indigo-400" />,
      title: "Configuraciones Flexibles",
      description: "30+ configuraciones organizadas en 6 grupos para cada liga"
    }
  ];

  const stats = [
    { number: "95%", label: "Completado", color: "text-green-400" },
    { number: "45+", label: "Tablas BD", color: "text-blue-400" },
    { number: "13+", label: "Resources", color: "text-yellow-400" },
    { number: "30+", label: "Configuraciones", color: "text-red-400" }
  ];

  const technologies = [
    { name: "Laravel", version: "12.x", color: "bg-red-600" },
    { name: "Filament", version: "3.x", color: "bg-yellow-600" },
    { name: "Livewire", version: "3.x", color: "bg-purple-600" },
    { name: "MySQL", version: "8.0+", color: "bg-blue-600" }
  ];

  return (
    <div className="min-h-screen bg-gradient-to-br from-slate-900 via-blue-900 to-slate-800">
      {/* Hero Section */}
      <section className="relative overflow-hidden">
        <div className="absolute inset-0 bg-gradient-to-r from-black/70 via-black/50 to-black/70"></div>
        <div 
          className="relative bg-cover bg-center min-h-[600px] flex items-center"
          style={{
            backgroundImage: `url('https://images.pexels.com/photos/1103844/pexels-photo-1103844.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=2')`
          }}
        >
          <div className="container mx-auto px-4">
            <div className="max-w-4xl">
              <div className="flex items-center space-x-4 mb-8">
                <div className="w-16 h-16 bg-white rounded-full flex items-center justify-center shadow-2xl">
                  <span className="text-3xl">🏐</span>
                </div>
                <div>
                  <h1 className="text-6xl lg:text-7xl font-black text-white tracking-tight">
                    VolleyPass
                  </h1>
                  <p className="text-xl text-yellow-200 font-bold">Liga de Voleibol de Sucre</p>
                </div>
              </div>
              
              <h2 className="text-4xl lg:text-5xl font-black text-white mb-6 leading-tight">
                Plataforma Integral de
                <span className="block text-yellow-400">Gestión Deportiva</span>
              </h2>
              
              <p className="text-xl text-gray-200 mb-8 leading-relaxed max-w-3xl">
                Sistema de digitalización y carnetización deportiva que centraliza el registro, 
                verificación y gestión de jugadoras, entrenadores y clubes federados y descentralizados.
              </p>
              
              <div className="flex flex-col sm:flex-row gap-4 mb-8">
                <button 
                  onClick={() => onNavigate('matches')}
                  className="bg-gradient-to-r from-yellow-400 to-yellow-500 text-black px-8 py-4 rounded-lg font-bold text-lg hover:from-yellow-500 hover:to-yellow-600 transition-all duration-200 shadow-xl transform hover:scale-105 flex items-center justify-center space-x-2"
                >
                  <Play className="w-6 h-6" />
                  <span>Ver Partidos en Vivo</span>
                </button>
                <button 
                  onClick={() => onNavigate('tournaments')}
                  className="bg-gradient-to-r from-blue-600 to-blue-700 text-white px-8 py-4 rounded-lg font-bold text-lg hover:from-blue-700 hover:to-blue-800 transition-all duration-200 shadow-xl transform hover:scale-105 flex items-center justify-center space-x-2"
                >
                  <Trophy className="w-6 h-6" />
                  <span>Explorar Torneos</span>
                </button>
              </div>
              
              <div className="inline-flex items-center space-x-2 bg-green-600/20 backdrop-blur-sm text-green-400 px-4 py-2 rounded-full border border-green-600/30">
                <CheckCircle className="w-5 h-5" />
                <span className="font-bold">95% Completado - Listo para Producción</span>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* Stats Section */}
      <section className="py-16 bg-gradient-to-r from-slate-800 to-slate-700">
        <div className="container mx-auto px-4">
          <div className="grid md:grid-cols-4 gap-8">
            {stats.map((stat, index) => (
              <div key={index} className="text-center">
                <div className={`text-5xl font-mono font-black ${stat.color} mb-2`}>
                  {stat.number}
                </div>
                <div className="text-white font-bold text-lg">{stat.label}</div>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* Features Section */}
      <section className="py-20">
        <div className="container mx-auto px-4">
          <div className="text-center mb-16">
            <h3 className="text-4xl font-black text-white mb-4">
              Características Principales
            </h3>
            <p className="text-xl text-gray-300 max-w-3xl mx-auto">
              Una plataforma completa que revoluciona la gestión deportiva en Colombia
            </p>
          </div>
          
          <div className="grid lg:grid-cols-3 gap-8">
            {features.map((feature, index) => (
              <div key={index} className="bg-gradient-to-br from-slate-800 to-slate-700 rounded-2xl p-8 shadow-2xl hover:shadow-3xl transition-all duration-300 transform hover:scale-105 border border-slate-600">
                <div className="flex items-center space-x-4 mb-6">
                  {feature.icon}
                  <h4 className="text-2xl font-black text-white">{feature.title}</h4>
                </div>
                <p className="text-gray-300 leading-relaxed">{feature.description}</p>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* Technology Stack */}
      <section className="py-16 bg-gradient-to-r from-slate-800 to-slate-700">
        <div className="container mx-auto px-4">
          <div className="text-center mb-12">
            <h3 className="text-3xl font-black text-white mb-4">
              Tecnologías de Vanguardia
            </h3>
            <p className="text-lg text-gray-300">
              Construido con las mejores herramientas del ecosistema PHP
            </p>
          </div>
          
          <div className="flex flex-wrap justify-center gap-6">
            {technologies.map((tech, index) => (
              <div key={index} className={`${tech.color} text-white px-6 py-3 rounded-lg font-bold text-lg shadow-lg`}>
                {tech.name} {tech.version}
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* CTA Section */}
      <section className="py-20">
        <div className="container mx-auto px-4 text-center">
          <div className="max-w-4xl mx-auto">
            <h3 className="text-4xl font-black text-white mb-6">
              ¿Listo para Digitalizar tu Liga?
            </h3>
            <p className="text-xl text-gray-300 mb-8">
              Únete a la revolución deportiva digital con VolleyPass
            </p>
            
            <div className="flex flex-col sm:flex-row gap-4 justify-center">
              <button 
                onClick={() => onNavigate('dashboard')}
                className="bg-gradient-to-r from-yellow-400 to-yellow-500 text-black px-8 py-4 rounded-lg font-bold text-lg hover:from-yellow-500 hover:to-yellow-600 transition-all duration-200 shadow-xl transform hover:scale-105 flex items-center justify-center space-x-2"
              >
                <Users className="w-6 h-6" />
                <span>Acceder al Dashboard</span>
              </button>
              <button className="bg-gradient-to-r from-blue-600 to-blue-700 text-white px-8 py-4 rounded-lg font-bold text-lg hover:from-blue-700 hover:to-blue-800 transition-all duration-200 shadow-xl transform hover:scale-105 flex items-center justify-center space-x-2">
                <ArrowRight className="w-6 h-6" />
                <span>Solicitar Demo</span>
              </button>
            </div>
          </div>
        </div>
      </section>

      {/* Footer */}
      <footer className="bg-black/50 backdrop-blur-sm py-12 border-t border-slate-700">
        <div className="container mx-auto px-4">
          <div className="grid md:grid-cols-3 gap-8">
            <div>
              <div className="flex items-center space-x-3 mb-4">
                <div className="w-10 h-10 bg-white rounded-full flex items-center justify-center">
                  <span className="text-xl">🏐</span>
                </div>
                <div>
                  <h4 className="text-xl font-black text-white">VolleyPass</h4>
                  <p className="text-sm text-gray-400">Liga de Voleibol Sucre</p>
                </div>
              </div>
              <p className="text-gray-400">
                Digitalizando el deporte, fortaleciendo la comunidad
              </p>
            </div>
            
            <div>
              <h5 className="text-lg font-bold text-white mb-4">Contacto</h5>
              <div className="space-y-2 text-gray-400">
                <p>📧 liga@volleypass.sucre.gov.co</p>
                <p>📱 +57 (5) 282-5555</p>
                <p>🏢 Cra. 25 #16-50, Sincelejo, Sucre</p>
              </div>
            </div>
            
            <div>
              <h5 className="text-lg font-bold text-white mb-4">Enlaces</h5>
              <div className="space-y-2">
                <button 
                  onClick={() => onNavigate('matches')}
                  className="block text-gray-400 hover:text-yellow-400 transition-colors duration-200"
                >
                  Partidos en Vivo
                </button>
                <button 
                  onClick={() => onNavigate('tournaments')}
                  className="block text-gray-400 hover:text-yellow-400 transition-colors duration-200"
                >
                  Torneos
                </button>
                <button 
                  onClick={() => onNavigate('dashboard')}
                  className="block text-gray-400 hover:text-yellow-400 transition-colors duration-200"
                >
                  Dashboard
                </button>
              </div>
            </div>
          </div>
          
          <div className="border-t border-slate-700 mt-8 pt-8 text-center">
            <p className="text-gray-400">
              © 2024 VolleyPass Sucre. Desarrollado con ❤️ para el voleibol sucreño.
            </p>
          </div>
        </div>
      </footer>
    </div>
  );
}

//MatchesPage.tsx
import React, { useState, useEffect } from 'react';
import { Play, Calendar, Trophy, Users, Zap, Clock, MapPin, ChevronRight, Bell, Search, Menu, X, User, BarChart3, Activity, Award, Heart, MessageCircle } from 'lucide-react';

interface MatchesPageProps {
  onNavigate: (view: string) => void;
}

export default function MatchesPage({ onNavigate }: MatchesPageProps) {
  const [isMenuOpen, setIsMenuOpen] = useState(false);
  const [currentTime, setCurrentTime] = useState(new Date());

  useEffect(() => {
    const timer = setInterval(() => setCurrentTime(new Date()), 1000);
    return () => clearInterval(timer);
  }, []);

  const liveMatches = [
    {
      id: 1,
      teamA: { name: "Águilas Doradas", logo: "🦅", sets: 2 },
      teamB: { name: "Tigres Sucre", logo: "🐅", sets: 1 },
      status: "EN VIVO",
      currentSet: 4,
      time: "45:23",
      viewers: 1245,
      venue: "Coliseo Municipal",
      setScores: [
        { teamA: 25, teamB: 20 },
        { teamA: 23, teamB: 25 },
        { teamA: 25, teamB: 18 },
        { teamA: 15, teamB: 12 }
      ]
    },
    {
      id: 2,
      teamA: { name: "Cóndores FC", logo: "🦅", sets: 0 },
      teamB: { name: "Panteras", logo: "🐆", sets: 3 },
      status: "FINALIZADO",
      currentSet: 3,
      time: "78:45",
      viewers: 892,
      venue: "Polideportivo Central",
      setScores: [
        { teamA: 18, teamB: 25 },
        { teamA: 22, teamB: 25 },
        { teamA: 20, teamB: 25 },
        { teamA: 0, teamB: 0 }
      ]
    }
  ];

  const upcomingMatches = [
    {
      id: 3,
      teamA: { name: "Jaguares", logo: "🐆" },
      teamB: { name: "Leones", logo: "🦁" },
      date: "Hoy",
      time: "20:00",
      venue: "Coliseo Municipal"
    },
    {
      id: 4,
      teamA: { name: "Halcones", logo: "🦅" },
      teamB: { name: "Lobos", logo: "🐺" },
      date: "Mañana",
      time: "18:30",
      venue: "Polideportivo Central"
    }
  ];

  const standings = [
    { pos: 1, team: "Águilas Doradas", logo: "🦅", points: 45, wins: 15, losses: 3, setsFor: 47, setsAgainst: 18, form: ['W', 'W', 'W', 'W', 'W'] },
    { pos: 2, team: "Tigres Sucre", logo: "🐅", points: 42, wins: 14, losses: 4, setsFor: 44, setsAgainst: 22, form: ['W', 'W', 'L', 'W', 'W'] },
    { pos: 3, team: "Cóndores FC", logo: "🦅", points: 39, wins: 13, losses: 5, setsFor: 41, setsAgainst: 25, form: ['W', 'L', 'W', 'W', 'L'] },
    { pos: 4, team: "Panteras", logo: "🐆", points: 36, wins: 12, losses: 6, setsFor: 38, setsAgainst: 28, form: ['L', 'W', 'W', 'L', 'W'] }
  ];

  return (
    <div className="min-h-screen bg-gradient-to-br from-slate-900 via-blue-900 to-slate-800">
      {/* Header */}
      <header className="bg-gradient-to-r from-yellow-400 via-blue-600 to-red-600 shadow-2xl sticky top-0 z-50">
        <div className="container mx-auto px-4">
          <div className="flex items-center justify-between h-16">
            {/* Logo */}
            <button 
              onClick={() => onNavigate('home')}
              className="flex items-center space-x-3 hover:opacity-80 transition-opacity duration-200"
            >
              <div className="w-12 h-12 bg-white rounded-full flex items-center justify-center shadow-lg">
                <span className="text-2xl">🏐</span>
              </div>
              <div>
                <h1 className="text-2xl font-black text-white tracking-tight">VolleyPass</h1>
                <p className="text-xs text-yellow-100 font-medium">Liga de Voleibol Sucre</p>
              </div>
            </button>

            {/* Desktop Navigation */}
            <nav className="hidden lg:flex items-center space-x-8">
              <button 
                onClick={() => onNavigate('matches')}
                className="text-white bg-white/20 px-3 py-2 rounded-lg font-semibold transition-colors duration-200 flex items-center space-x-1"
              >
                <Play className="w-4 h-4" />
                <span>Partidos</span>
              </button>
              <button 
                onClick={() => onNavigate('tournaments')}
                className="text-white hover:text-yellow-200 font-semibold transition-colors duration-200 flex items-center space-x-1"
              >
                <Trophy className="w-4 h-4" />
                <span>Torneos</span>
              </button>
              <button 
                onClick={() => onNavigate('dashboard')}
                className="text-white hover:text-yellow-200 font-semibold transition-colors duration-200 flex items-center space-x-1"
              >
                <User className="w-4 h-4" />
                <span>Dashboard</span>
              </button>
            </nav>

            {/* Actions */}
            <div className="flex items-center space-x-4">
              <button className="hidden md:flex items-center space-x-2 bg-white/20 backdrop-blur-sm px-3 py-2 rounded-lg text-white hover:bg-white/30 transition-all duration-200">
                <Search className="w-4 h-4" />
                <span className="text-sm font-medium">Buscar</span>
              </button>
              <button className="relative p-2 text-white hover:text-yellow-200 transition-colors duration-200">
                <Bell className="w-5 h-5" />
                <div className="absolute -top-1 -right-1 w-3 h-3 bg-red-500 rounded-full animate-pulse"></div>
              </button>
              <button 
                className="lg:hidden p-2 text-white hover:text-yellow-200 transition-colors duration-200"
                onClick={() => setIsMenuOpen(!isMenuOpen)}
              >
                {isMenuOpen ? <X className="w-6 h-6" /> : <Menu className="w-6 h-6" />}
              </button>
            </div>
          </div>
        </div>

        {/* Mobile Navigation */}
        {isMenuOpen && (
          <div className="lg:hidden bg-black/90 backdrop-blur-md border-t border-white/20">
            <nav className="container mx-auto px-4 py-4 space-y-2">
              <button 
                onClick={() => onNavigate('matches')}
                className="flex items-center space-x-3 text-yellow-400 py-2 font-medium w-full text-left"
              >
                <Play className="w-5 h-5" />
                <span>Partidos</span>
              </button>
              <button 
                onClick={() => onNavigate('tournaments')}
                className="flex items-center space-x-3 text-white hover:text-yellow-200 py-2 font-medium w-full text-left"
              >
                <Trophy className="w-5 h-5" />
                <span>Torneos</span>
              </button>
              <button 
                onClick={() => onNavigate('dashboard')}
                className="flex items-center space-x-3 text-white hover:text-yellow-200 py-2 font-medium w-full text-left"
              >
                <User className="w-5 h-5" />
                <span>Dashboard</span>
              </button>
            </nav>
          </div>
        )}
      </header>

      {/* Hero Section - Featured Match */}
      <section className="relative overflow-hidden">
        <div className="absolute inset-0 bg-gradient-to-r from-black/70 via-black/50 to-black/70"></div>
        <div 
          className="relative bg-cover bg-center min-h-[400px] lg:min-h-[500px] flex items-center"
          style={{
            backgroundImage: `url('https://images.pexels.com/photos/1103844/pexels-photo-1103844.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=2')`
          }}
        >
          <div className="container mx-auto px-4">
            <div className="max-w-4xl">
              <div className="inline-flex items-center space-x-2 bg-red-600 text-white px-4 py-2 rounded-full mb-6 shadow-lg animate-pulse">
                <div className="w-3 h-3 bg-white rounded-full animate-ping"></div>
                <span className="text-sm font-bold">PARTIDO DESTACADO EN VIVO</span>
              </div>
              <h2 className="text-5xl lg:text-6xl font-black text-white mb-4 leading-tight">
                Águilas Doradas
                <span className="block text-yellow-400">VS</span>
                Tigres Sucre
              </h2>
              <div className="flex items-center space-x-8 text-white mb-6">
                <div className="text-center">
                  <div className="text-6xl font-mono font-black text-yellow-400">2</div>
                  <div className="text-sm font-bold">SETS</div>
                </div>
                <div className="text-4xl font-black">-</div>
                <div className="text-center">
                  <div className="text-6xl font-mono font-black text-yellow-400">1</div>
                  <div className="text-sm font-bold">SETS</div>
                </div>
              </div>
              <div className="flex items-center space-x-4 text-white mb-4">
                <div className="bg-black/50 px-3 py-1 rounded-lg">
                  <span className="text-sm font-bold">Set 4: 15-12</span>
                </div>
                <div className="bg-red-600 px-3 py-1 rounded-lg animate-pulse">
                  <span className="text-sm font-bold">EN CURSO</span>
                </div>
              </div>
              <div className="flex items-center space-x-6 text-white mb-8">
                <div className="flex items-center space-x-2">
                  <Clock className="w-5 h-5 text-yellow-400" />
                  <span className="font-mono text-lg font-bold">45:23</span>
                </div>
                <div className="flex items-center space-x-2">
                  <MapPin className="w-5 h-5 text-yellow-400" />
                  <span className="font-medium">Coliseo Municipal</span>
                </div>
                <div className="flex items-center space-x-2">
                  <Users className="w-5 h-5 text-yellow-400" />
                  <span className="font-medium">1,245 viendo</span>
                </div>
              </div>
              <button className="bg-gradient-to-r from-yellow-400 to-yellow-500 text-black px-8 py-4 rounded-lg font-bold text-lg hover:from-yellow-500 hover:to-yellow-600 transition-all duration-200 shadow-xl transform hover:scale-105 flex items-center space-x-2">
                <Play className="w-6 h-6" />
                <span>Ver en Vivo</span>
              </button>
            </div>
          </div>
        </div>
      </section>

      {/* Main Content */}
      <div className="container mx-auto px-4 py-12">
        <div className="grid lg:grid-cols-3 gap-8">
          {/* Main Content */}
          <div className="lg:col-span-2 space-y-12">
            {/* Live Matches */}
            <section>
              <div className="flex items-center justify-between mb-8">
                <h3 className="text-3xl font-black text-white flex items-center space-x-3">
                  <Zap className="w-8 h-8 text-yellow-400" />
                  <span>Partidos en Vivo</span>
                </h3>
                <button className="text-yellow-400 hover:text-yellow-300 font-medium flex items-center space-x-1">
                  <span>Ver todos</span>
                  <ChevronRight className="w-4 h-4" />
                </button>
              </div>
              <div className="grid gap-6">
                {liveMatches.map((match) => (
                  <div key={match.id} className="bg-gradient-to-r from-slate-800 to-slate-700 rounded-2xl p-6 shadow-2xl hover:shadow-3xl transition-all duration-300 transform hover:scale-[1.02] border border-slate-600">
                    <div className="flex items-center justify-between mb-4">
                      <div className={`inline-flex items-center space-x-2 px-3 py-1 rounded-full text-sm font-bold ${
                        match.status === 'EN VIVO' 
                          ? 'bg-red-600 text-white animate-pulse' 
                          : 'bg-gray-600 text-gray-300'
                      }`}>
                        {match.status === 'EN VIVO' && (
                          <div className="w-2 h-2 bg-white rounded-full animate-ping"></div>
                        )}
                        <span>{match.status}</span>
                      </div>
                      <div className="text-right text-gray-300">
                        <p className="text-sm font-medium">Set {match.currentSet}</p>
                        <p className="text-xs">{match.time}</p>
                      </div>
                    </div>
                    
                    <div className="flex items-center justify-between mb-6">
                      <div className="flex items-center space-x-4 flex-1">
                        <div className="text-center">
                          <div className="text-4xl mb-2">{match.teamA.logo}</div>
                          <p className="text-white font-bold text-lg">{match.teamA.name}</p>
                          <p className="text-6xl font-mono font-black text-yellow-400 mt-2">{match.teamA.sets}</p>
                        </div>
                      </div>
                      
                      <div className="text-center px-4">
                        <div className="text-2xl font-black text-white mb-2">VS</div>
                        <div className="w-px h-16 bg-gradient-to-b from-yellow-400 to-blue-600 mx-auto"></div>
                      </div>
                      
                      <div className="flex items-center space-x-4 flex-1 justify-end">
                        <div className="text-center">
                          <div className="text-4xl mb-2">{match.teamB.logo}</div>
                          <p className="text-white font-bold text-lg">{match.teamB.name}</p>
                          <p className="text-6xl font-mono font-black text-yellow-400 mt-2">{match.teamB.sets}</p>
                        </div>
                      </div>
                    </div>
                    
                    {/* Set by Set Scores */}
                    <div className="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6">
                      {match.setScores.map((set, index) => (
                        <div key={index} className={`bg-slate-700/50 rounded-lg p-3 text-center ${
                          index === match.currentSet - 1 && match.status === 'EN VIVO' 
                            ? 'border-2 border-yellow-400 animate-pulse' 
                            : 'border border-slate-600'
                        }`}>
                          <div className="text-xs font-bold text-gray-400 mb-1">
                            Set {index + 1}
                            {index === match.currentSet - 1 && match.status === 'EN VIVO' && (
                              <span className="ml-1 text-red-400">EN CURSO</span>
                            )}
                          </div>
                          <div className="flex items-center justify-center space-x-2">
                            <span className={`font-mono font-bold ${
                              set.teamA > set.teamB ? 'text-green-400' : 'text-white'
                            }`}>
                              {set.teamA}
                            </span>
                            <span className="text-gray-400">-</span>
                            <span className={`font-mono font-bold ${
                              set.teamB > set.teamA ? 'text-green-400' : 'text-white'
                            }`}>
                              {set.teamB}
                            </span>
                          </div>
                        </div>
                      ))}
                    </div>
                    
                    <div className="mt-6 flex items-center justify-between">
                      <div className="flex items-center space-x-4 text-gray-400">
                        <div className="flex items-center space-x-2">
                          <Users className="w-4 h-4" />
                          <span className="text-sm">{match.viewers.toLocaleString()} viendo</span>
                        </div>
                        <div className="flex items-center space-x-2">
                          <MapPin className="w-4 h-4" />
                          <span className="text-sm">{match.venue}</span>
                        </div>
                      </div>
                      <button className="bg-yellow-400 text-black px-6 py-2 rounded-lg font-bold hover:bg-yellow-500 transition-colors duration-200 flex items-center space-x-2">
                        <Play className="w-4 h-4" />
                        <span>Ver</span>
                      </button>
                    </div>
                  </div>
                ))}
              </div>
            </section>

            {/* Upcoming Matches */}
            <section>
              <div className="flex items-center justify-between mb-8">
                <h3 className="text-3xl font-black text-white flex items-center space-x-3">
                  <Calendar className="w-8 h-8 text-blue-400" />
                  <span>Próximos Partidos</span>
                </h3>
              </div>
              <div className="grid md:grid-cols-2 gap-6">
                {upcomingMatches.map((match) => (
                  <div key={match.id} className="bg-gradient-to-br from-slate-800 to-slate-700 rounded-xl p-6 shadow-xl hover:shadow-2xl transition-all duration-300 border border-slate-600">
                    <div className="flex items-center justify-between mb-4">
                      <div className="text-center flex-1">
                        <div className="text-3xl mb-2">{match.teamA.logo}</div>
                        <p className="text-white font-bold">{match.teamA.name}</p>
                      </div>
                      
                      <div className="text-center px-4">
                        <p className="text-white font-black text-lg">VS</p>
                        <div className="w-8 h-px bg-gradient-to-r from-yellow-400 to-blue-600 mx-auto mt-2"></div>
                      </div>
                      
                      <div className="text-center flex-1">
                        <div className="text-3xl mb-2">{match.teamB.logo}</div>
                        <p className="text-white font-bold">{match.teamB.name}</p>
                      </div>
                    </div>
                    
                    <div className="text-center border-t border-slate-600 pt-4">
                      <p className="text-yellow-400 font-bold text-lg">{match.date} - {match.time}</p>
                      <p className="text-gray-400 text-sm mt-1">{match.venue}</p>
                    </div>
                  </div>
                ))}
              </div>
            </section>
          </div>

          {/* Sidebar */}
          <div className="space-y-8">
            {/* Standings */}
            <section className="bg-gradient-to-br from-slate-800 to-slate-700 rounded-2xl p-6 shadow-2xl border border-slate-600">
              <h3 className="text-2xl font-black text-white mb-6 flex items-center space-x-2">
                <Trophy className="w-6 h-6 text-yellow-400" />
                <span>Tabla de Posiciones</span>
              </h3>
              <div className="space-y-4">
                {standings.map((team) => (
                  <div key={team.pos} className="flex items-center justify-between p-3 bg-slate-700/50 rounded-lg hover:bg-slate-700 transition-colors duration-200">
                    <div className="flex items-center space-x-3">
                      <div className={`w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm ${
                        team.pos === 1 ? 'bg-yellow-400 text-black' :
                        team.pos <= 3 ? 'bg-blue-600 text-white' : 'bg-gray-600 text-white'
                      }`}>
                        {team.pos}
                      </div>
                      <div className="text-2xl">{team.logo}</div>
                      <div>
                        <p className="text-white font-bold text-sm">{team.team}</p>
                        <div className="flex items-center space-x-2">
                          <p className="text-gray-400 text-xs">{team.wins}G - {team.losses}P</p>
                          <div className="flex space-x-1">
                            {team.form.slice(-3).map((result, index) => (
                              <div key={index} className={`w-2 h-2 rounded-full ${
                                result === 'W' ? 'bg-green-400' : 'bg-red-400'
                              }`}></div>
                            ))}
                          </div>
                        </div>
                      </div>
                    </div>
                    <div className="text-right">
                      <p className="text-yellow-400 font-mono font-bold">{team.points}</p>
                      <p className="text-gray-400 text-xs">{team.setsFor}-{team.setsAgainst}</p>
                    </div>
                  </div>
                ))}
              </div>
            </section>

            {/* Quick Stats */}
            <section className="bg-gradient-to-br from-slate-800 to-slate-700 rounded-2xl p-6 shadow-2xl border border-slate-600">
              <h3 className="text-2xl font-black text-white mb-6">Estadísticas Rápidas</h3>
              <div className="space-y-4">
                <div className="flex justify-between items-center">
                  <span className="text-gray-300">Partidos Hoy</span>
                  <span className="text-yellow-400 font-mono font-bold text-xl">4</span>
                </div>
                <div className="flex justify-between items-center">
                  <span className="text-gray-300">En Vivo</span>
                  <span className="text-red-400 font-mono font-bold text-xl">2</span>
                </div>
                <div className="flex justify-between items-center">
                  <span className="text-gray-300">Espectadores</span>
                  <span className="text-blue-400 font-mono font-bold text-xl">2.1K</span>
                </div>
                <div className="flex justify-between items-center">
                  <span className="text-gray-300">Equipos Activos</span>
                  <span className="text-green-400 font-mono font-bold text-xl">12</span>
                </div>
              </div>
            </section>
          </div>
        </div>
      </div>
    </div>
  );
}


//MatchControlPage.tsx
import React, { useState, useEffect } from 'react';
import { 
  Play, 
  Pause, 
  RotateCcw, 
  Users, 
  Clock, 
  RefreshCw, 
  ArrowLeft, 
  Settings,
  Star,
  Timer,
  UserCheck,
  Zap,
  AlertTriangle,
  CheckCircle
} from 'lucide-react';

interface MatchControlPageProps {
  onNavigate: (view: string) => void;
  isLoggedIn?: boolean;
  currentUser?: any;
  onLogout?: () => void;
}

interface Player {
  id: number;
  name: string;
  number: number;
  position: 'Centro' | 'Colocadora' | 'Punta' | 'Opuesto' | 'Libero';
  isCaptain: boolean;
  isActive: boolean;
  courtPosition: number; // 1-6 para posiciones en cancha
}

interface Team {
  id: number;
  name: string;
  logo: string;
  color: string;
  score: number;
  sets: number;
  timeouts: number;
  substitutions: number;
  players: Player[];
  servingPlayer?: number;
}

interface MatchState {
  isActive: boolean;
  currentSet: number;
  timer: string;
  servingTeam: 'home' | 'away';
  lastAction: string;
  setScores: Array<{home: number, away: number}>;
}

export default function MatchControlPage({ onNavigate, isLoggedIn, currentUser, onLogout }: MatchControlPageProps) {
  const [matchState, setMatchState] = useState<MatchState>({
    isActive: false,
    currentSet: 1,
    timer: '00:00',
    servingTeam: 'home',
    lastAction: '',
    setScores: [{home: 0, away: 0}]
  });

  const [homeTeam, setHomeTeam] = useState<Team>({
    id: 1,
    name: 'Águilas Sucre',
    logo: '🦅',
    color: 'blue',
    score: 0,
    sets: 0,
    timeouts: 2,
    substitutions: 6,
    servingPlayer: 1,
    players: [
      { id: 1, name: 'Ana García', number: 5, position: 'Punta', isCaptain: true, isActive: true, courtPosition: 1 },
      { id: 2, name: 'María López', number: 4, position: 'Centro', isCaptain: false, isActive: true, courtPosition: 2 },
      { id: 3, name: 'Carmen Silva', number: 6, position: 'Colocadora', isCaptain: false, isActive: true, courtPosition: 3 },
      { id: 4, name: 'Rosa Díaz', number: 3, position: 'Centro', isCaptain: false, isActive: true, courtPosition: 4 },
      { id: 5, name: 'Sofía Cruz', number: 1, position: 'Punta', isCaptain: false, isActive: true, courtPosition: 5 },
      { id: 6, name: 'Laura Ruiz', number: 2, position: 'Opuesto', isCaptain: false, isActive: true, courtPosition: 6 },
      { id: 7, name: 'Elena Vega', number: 7, position: 'Libero', isCaptain: false, isActive: false, courtPosition: 0 }
    ]
  });

  const [awayTeam, setAwayTeam] = useState<Team>({
    id: 2,
    name: 'Tigres Corozal',
    logo: '🐅',
    color: 'red',
    score: 0,
    sets: 0,
    timeouts: 2,
    substitutions: 6,
    servingPlayer: 2,
    players: [
      { id: 8, name: 'Paola Herrera', number: 2, position: 'Punta', isCaptain: false, isActive: true, courtPosition: 1 },
      { id: 9, name: 'Diana Torres', number: 1, position: 'Centro', isCaptain: false, isActive: true, courtPosition: 2 },
      { id: 10, name: 'Lucía Morales', number: 3, position: 'Colocadora', isCaptain: true, isActive: true, courtPosition: 3 },
      { id: 11, name: 'Andrea Jiménez', number: 6, position: 'Centro', isCaptain: false, isActive: true, courtPosition: 4 },
      { id: 12, name: 'Valeria Castro', number: 5, position: 'Punta', isCaptain: false, isActive: true, courtPosition: 5 },
      { id: 13, name: 'Natalia Rojas', number: 4, position: 'Opuesto', isCaptain: false, isActive: true, courtPosition: 6 },
      { id: 14, name: 'Camila Vargas', number: 8, position: 'Libero', isCaptain: false, isActive: false, courtPosition: 0 }
    ]
  });

  const [actionHistory, setActionHistory] = useState<string[]>([]);
  const [showSubstitutions, setShowSubstitutions] = useState(false);
  const [selectedTeamForSub, setSelectedTeamForSub] = useState<'home' | 'away' | null>(null);

  // Timer effect
  useEffect(() => {
    let interval: NodeJS.Timeout;
    if (matchState.isActive) {
      interval = setInterval(() => {
        setMatchState(prev => {
          const [minutes, seconds] = prev.timer.split(':').map(Number);
          const totalSeconds = minutes * 60 + seconds + 1;
          const newMinutes = Math.floor(totalSeconds / 60);
          const newSeconds = totalSeconds % 60;
          return {
            ...prev,
            timer: `${newMinutes.toString().padStart(2, '0')}:${newSeconds.toString().padStart(2, '0')}`
          };
        });
      }, 1000);
    }
    return () => clearInterval(interval);
  }, [matchState.isActive]);

  const startMatch = () => {
    setMatchState(prev => ({ ...prev, isActive: true, lastAction: 'Partido iniciado' }));
    addToHistory('Partido iniciado');
  };

  const pauseMatch = () => {
    setMatchState(prev => ({ ...prev, isActive: false, lastAction: 'Partido pausado' }));
    addToHistory('Partido pausado');
  };

  const addPoint = (team: 'home' | 'away') => {
    const currentTeam = team === 'home' ? homeTeam : awayTeam;
    const newScore = currentTeam.score + 1;
    
    if (team === 'home') {
      setHomeTeam(prev => ({ ...prev, score: newScore }));
    } else {
      setAwayTeam(prev => ({ ...prev, score: newScore }));
    }

    // Update set scores
    setMatchState(prev => {
      const newSetScores = [...prev.setScores];
      newSetScores[prev.currentSet - 1] = {
        ...newSetScores[prev.currentSet - 1],
        [team]: newScore
      };
      
      return {
        ...prev,
        setScores: newSetScores,
        servingTeam: team,
        lastAction: `Punto para ${currentTeam.name}`
      };
    });

    addToHistory(`Punto para ${currentTeam.name} (${newScore})`);

    // Check for set win
    checkSetWin(team, newScore);
  };

  const checkSetWin = (team: 'home' | 'away', score: number) => {
    const otherTeam = team === 'home' ? awayTeam : homeTeam;
    const otherScore = otherTeam.score;

    if (score >= 25 && score - otherScore >= 2) {
      // Set won
      if (team === 'home') {
        setHomeTeam(prev => ({ ...prev, sets: prev.sets + 1 }));
      } else {
        setAwayTeam(prev => ({ ...prev, sets: prev.sets + 1 }));
      }

      // Start new set
      setMatchState(prev => ({
        ...prev,
        currentSet: prev.currentSet + 1,
        setScores: [...prev.setScores, {home: 0, away: 0}]
      }));

      setHomeTeam(prev => ({ ...prev, score: 0 }));
      setAwayTeam(prev => ({ ...prev, score: 0 }));

      addToHistory(`Set ${matchState.currentSet} ganado por ${team === 'home' ? homeTeam.name : awayTeam.name}`);
    }
  };

  const undoLastAction = () => {
    if (actionHistory.length > 0) {
      const newHistory = [...actionHistory];
      const lastAction = newHistory.pop();
      setActionHistory(newHistory);
      setMatchState(prev => ({ ...prev, lastAction: `Deshecho: ${lastAction}` }));
    }
  };

  const rotateTeam = (team: 'home' | 'away') => {
    const currentTeam = team === 'home' ? homeTeam : awayTeam;
    const setTeam = team === 'home' ? setHomeTeam : setAwayTeam;
    
    const activePlayers = currentTeam.players.filter(p => p.isActive && p.position !== 'Libero');
    const rotatedPlayers = activePlayers.map(player => ({
      ...player,
      courtPosition: player.courtPosition === 6 ? 1 : player.courtPosition + 1
    }));

    const newPlayers = currentTeam.players.map(player => {
      const rotatedPlayer = rotatedPlayers.find(rp => rp.id === player.id);
      return rotatedPlayer || player;
    });

    setTeam(prev => ({ ...prev, players: newPlayers }));
    addToHistory(`Rotación ${currentTeam.name}`);
  };

  const useTimeout = (team: 'home' | 'away') => {
    const currentTeam = team === 'home' ? homeTeam : awayTeam;
    const setTeam = team === 'home' ? setHomeTeam : setAwayTeam;
    
    if (currentTeam.timeouts > 0) {
      setTeam(prev => ({ ...prev, timeouts: prev.timeouts - 1 }));
      addToHistory(`Tiempo fuera ${currentTeam.name}`);
    }
  };

  const addToHistory = (action: string) => {
    setActionHistory(prev => [...prev, `${matchState.timer} - ${action}`]);
  };

  const getCourtPositions = (team: Team) => {
    const activePlayers = team.players.filter(p => p.isActive).sort((a, b) => a.courtPosition - b.courtPosition);
    return activePlayers;
  };

  const isServingPlayer = (player: Player, team: Team) => {
    return matchState.servingTeam === (team.id === homeTeam.id ? 'home' : 'away') && 
           player.courtPosition === 1;
  };

  return (
    <div className="min-h-screen bg-gradient-to-br from-slate-900 via-blue-900 to-slate-800">
      {/* Header */}
      <header className="bg-gradient-to-r from-yellow-400 via-blue-600 to-red-600 shadow-2xl sticky top-0 z-50">
        <div className="container mx-auto px-4">
          <div className="flex items-center justify-between h-16">
            <button 
              onClick={() => onNavigate('home')}
              className="flex items-center space-x-3 hover:opacity-80 transition-opacity duration-200"
            >
              <div className="w-12 h-12 bg-white rounded-full flex items-center justify-center shadow-lg">
                <span className="text-2xl">🏐</span>
              </div>
              <div>
                <h1 className="text-2xl font-black text-white tracking-tight">VolleyPass</h1>
                <p className="text-xs text-yellow-100 font-medium">Control de Partido</p>
              </div>
            </button>

            <div className="flex items-center space-x-4">
              <div className="text-white text-center">
                <div className="text-sm font-bold">Set {matchState.currentSet}</div>
                <div className="text-xs">{matchState.timer}</div>
              </div>
              <button 
                onClick={() => onNavigate('matches')}
                className="text-white hover:text-yellow-200 font-semibold transition-colors duration-200"
              >
                ← Volver a Partidos
              </button>
            </div>
          </div>
        </div>
      </header>

      <div className="container mx-auto px-4 py-8">
        {/* Match Header */}
        <div className="bg-gradient-to-r from-slate-800 to-slate-700 rounded-2xl p-6 mb-8 shadow-2xl border border-slate-600">
          <div className="text-center mb-6">
            <h2 className="text-3xl font-black text-white mb-2">
              {homeTeam.name} vs {awayTeam.name}
            </h2>
            <div className="flex items-center justify-center space-x-4 text-gray-300">
              <span>Set {matchState.currentSet}</span>
              <span>•</span>
              <span>{matchState.timer}</span>
              <span>•</span>
              <span className={`px-3 py-1 rounded-full text-sm font-bold ${
                matchState.isActive ? 'bg-green-600 text-white animate-pulse' : 'bg-gray-600 text-gray-300'
              }`}>
                {matchState.isActive ? 'EN CURSO' : 'PAUSADO'}
              </span>
            </div>
          </div>

          {/* Timeouts indicators */}
          <div className="flex justify-center space-x-8 mb-6">
            <div className="flex items-center space-x-2">
              <span className="text-blue-400 font-bold">{homeTeam.name}</span>
              <div className="flex space-x-1">
                {[...Array(2)].map((_, i) => (
                  <div key={i} className={`w-3 h-3 rounded-full ${
                    i < homeTeam.timeouts ? 'bg-blue-400' : 'bg-gray-600'
                  }`}></div>
                ))}
              </div>
              <Clock className="w-4 h-4 text-blue-400" />
            </div>
            <div className="flex items-center space-x-2">
              <Clock className="w-4 h-4 text-red-400" />
              <div className="flex space-x-1">
                {[...Array(2)].map((_, i) => (
                  <div key={i} className={`w-3 h-3 rounded-full ${
                    i < awayTeam.timeouts ? 'bg-red-400' : 'bg-gray-600'
                  }`}></div>
                ))}
              </div>
              <span className="text-red-400 font-bold">{awayTeam.name}</span>
            </div>
          </div>

          {/* Main Scoreboard */}
          <div className="grid grid-cols-3 gap-8 items-center">
            {/* Home Team */}
            <div className="text-center">
              <div className="text-6xl mb-2">{homeTeam.logo}</div>
              <h3 className="text-2xl font-black text-blue-400 mb-2">{homeTeam.name}</h3>
              <div className="bg-blue-600 rounded-2xl p-6 shadow-xl">
                <div className="text-8xl font-mono font-black text-white mb-2">
                  {homeTeam.score.toString().padStart(2, '0')}
                </div>
                <div className="text-blue-200 font-bold">Sets: {homeTeam.sets}</div>
              </div>
            </div>

            {/* VS and Controls */}
            <div className="text-center space-y-4">
              <div className="text-4xl font-black text-white">VS</div>
              <div className="space-y-2">
                {!matchState.isActive ? (
                  <button
                    onClick={startMatch}
                    className="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-bold transition-colors duration-200 flex items-center space-x-2 mx-auto"
                  >
                    <Play className="w-5 h-5" />
                    <span>Iniciar Partido</span>
                  </button>
                ) : (
                  <button
                    onClick={pauseMatch}
                    className="bg-yellow-600 hover:bg-yellow-700 text-white px-6 py-3 rounded-lg font-bold transition-colors duration-200 flex items-center space-x-2 mx-auto"
                  >
                    <Pause className="w-5 h-5" />
                    <span>Pausar</span>
                  </button>
                )}
                <button
                  onClick={undoLastAction}
                  className="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-bold transition-colors duration-200 flex items-center space-x-2 mx-auto"
                >
                  <ArrowLeft className="w-4 h-4" />
                  <span>Deshacer</span>
                </button>
              </div>
            </div>

            {/* Away Team */}
            <div className="text-center">
              <div className="text-6xl mb-2">{awayTeam.logo}</div>
              <h3 className="text-2xl font-black text-red-400 mb-2">{awayTeam.name}</h3>
              <div className="bg-red-600 rounded-2xl p-6 shadow-xl">
                <div className="text-8xl font-mono font-black text-white mb-2">
                  {awayTeam.score.toString().padStart(2, '0')}
                </div>
                <div className="text-red-200 font-bold">Sets: {awayTeam.sets}</div>
              </div>
            </div>
          </div>

          {/* Action Buttons */}
          <div className="grid grid-cols-2 gap-4 mt-8">
            <button
              onClick={() => addPoint('home')}
              disabled={!matchState.isActive}
              className="bg-blue-600 hover:bg-blue-700 disabled:bg-gray-600 text-white px-6 py-4 rounded-lg font-bold transition-colors duration-200 text-xl"
            >
              + Punto {homeTeam.name}
            </button>
            <button
              onClick={() => addPoint('away')}
              disabled={!matchState.isActive}
              className="bg-red-600 hover:bg-red-700 disabled:bg-gray-600 text-white px-6 py-4 rounded-lg font-bold transition-colors duration-200 text-xl"
            >
              + Punto {awayTeam.name}
            </button>
          </div>
        </div>

        {/* Court Positions and Players */}
        <div className="grid lg:grid-cols-2 gap-8">
          {/* Home Team Court */}
          <div className="bg-gradient-to-br from-slate-800 to-slate-700 rounded-2xl p-6 shadow-2xl border border-slate-600">
            <div className="flex items-center justify-between mb-6">
              <h3 className="text-2xl font-black text-blue-400 flex items-center space-x-2">
                <span>{homeTeam.logo}</span>
                <span>{homeTeam.name}</span>
              </h3>
              <div className="flex space-x-2">
                <button
                  onClick={() => rotateTeam('home')}
                  className="bg-blue-600 hover:bg-blue-700 text-white p-2 rounded-lg transition-colors duration-200"
                  title="Rotar equipo"
                >
                  <RefreshCw className="w-4 h-4" />
                </button>
                <button
                  onClick={() => useTimeout('home')}
                  disabled={homeTeam.timeouts === 0}
                  className="bg-yellow-600 hover:bg-yellow-700 disabled:bg-gray-600 text-white p-2 rounded-lg transition-colors duration-200"
                  title="Tiempo fuera"
                >
                  <Timer className="w-4 h-4" />
                </button>
              </div>
            </div>

            {/* Court Positions */}
            <div className="grid grid-cols-3 gap-3 mb-6 bg-blue-900/20 p-4 rounded-lg border border-blue-600/30">
              {getCourtPositions(homeTeam).map((player) => (
                <div
                  key={player.id}
                  className={`relative bg-white rounded-full w-16 h-16 flex items-center justify-center font-bold text-lg ${
                    isServingPlayer(player, homeTeam) ? 'ring-4 ring-yellow-400' : ''
                  }`}
                >
                  <span className="text-blue-600">{player.number}</span>
                  {isServingPlayer(player, homeTeam) && (
                    <div className="absolute -top-2 -right-2 w-6 h-6 bg-yellow-400 rounded-full flex items-center justify-center">
                      <span className="text-xs">🏐</span>
                    </div>
                  )}
                  {player.isCaptain && (
                    <div className="absolute -top-1 -left-1 w-4 h-4 bg-yellow-400 rounded-full flex items-center justify-center">
                      <Star className="w-2 h-2 text-yellow-800" />
                    </div>
                  )}
                </div>
              ))}
            </div>

            {/* Players List */}
            <div className="space-y-2">
              <h4 className="text-lg font-bold text-white mb-3">Nómina</h4>
              {homeTeam.players.map((player) => (
                <div
                  key={player.id}
                  className={`flex items-center justify-between p-3 rounded-lg ${
                    player.isActive ? 'bg-blue-600/20 border border-blue-600/30' : 'bg-gray-600/20 border border-gray-600/30'
                  }`}
                >
                  <div className="flex items-center space-x-3">
                    <div className={`w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm ${
                      player.isActive ? 'bg-blue-600 text-white' : 'bg-gray-600 text-gray-300'
                    }`}>
                      {player.number}
                    </div>
                    <div>
                      <div className="flex items-center space-x-2">
                        <span className="text-white font-medium">{player.name}</span>
                        {player.isCaptain && <Star className="w-4 h-4 text-yellow-400" />}
                      </div>
                      <span className="text-gray-400 text-sm">{player.position}</span>
                    </div>
                  </div>
                  <div className="flex items-center space-x-2">
                    {isServingPlayer(player, homeTeam) && (
                      <span className="text-yellow-400 text-lg">🏐</span>
                    )}
                    <span className={`px-2 py-1 rounded text-xs font-bold ${
                      player.isActive ? 'bg-green-600 text-white' : 'bg-gray-600 text-gray-300'
                    }`}>
                      {player.isActive ? 'EN CANCHA' : 'BANCA'}
                    </span>
                  </div>
                </div>
              ))}
            </div>
          </div>

          {/* Away Team Court */}
          <div className="bg-gradient-to-br from-slate-800 to-slate-700 rounded-2xl p-6 shadow-2xl border border-slate-600">
            <div className="flex items-center justify-between mb-6">
              <h3 className="text-2xl font-black text-red-400 flex items-center space-x-2">
                <span>{awayTeam.logo}</span>
                <span>{awayTeam.name}</span>
              </h3>
              <div className="flex space-x-2">
                <button
                  onClick={() => rotateTeam('away')}
                  className="bg-red-600 hover:bg-red-700 text-white p-2 rounded-lg transition-colors duration-200"
                  title="Rotar equipo"
                >
                  <RefreshCw className="w-4 h-4" />
                </button>
                <button
                  onClick={() => useTimeout('away')}
                  disabled={awayTeam.timeouts === 0}
                  className="bg-yellow-600 hover:bg-yellow-700 disabled:bg-gray-600 text-white p-2 rounded-lg transition-colors duration-200"
                  title="Tiempo fuera"
                >
                  <Timer className="w-4 h-4" />
                </button>
              </div>
            </div>

            {/* Court Positions */}
            <div className="grid grid-cols-3 gap-3 mb-6 bg-red-900/20 p-4 rounded-lg border border-red-600/30">
              {getCourtPositions(awayTeam).map((player) => (
                <div
                  key={player.id}
                  className={`relative bg-white rounded-full w-16 h-16 flex items-center justify-center font-bold text-lg ${
                    isServingPlayer(player, awayTeam) ? 'ring-4 ring-yellow-400' : ''
                  }`}
                >
                  <span className="text-red-600">{player.number}</span>
                  {isServingPlayer(player, awayTeam) && (
                    <div className="absolute -top-2 -right-2 w-6 h-6 bg-yellow-400 rounded-full flex items-center justify-center">
                      <span className="text-xs">🏐</span>
                    </div>
                  )}
                  {player.isCaptain && (
                    <div className="absolute -top-1 -left-1 w-4 h-4 bg-yellow-400 rounded-full flex items-center justify-center">
                      <Star className="w-2 h-2 text-yellow-800" />
                    </div>
                  )}
                </div>
              ))}
            </div>

            {/* Players List */}
            <div className="space-y-2">
              <h4 className="text-lg font-bold text-white mb-3">Nómina</h4>
              {awayTeam.players.map((player) => (
                <div
                  key={player.id}
                  className={`flex items-center justify-between p-3 rounded-lg ${
                    player.isActive ? 'bg-red-600/20 border border-red-600/30' : 'bg-gray-600/20 border border-gray-600/30'
                  }`}
                >
                  <div className="flex items-center space-x-3">
                    <div className={`w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm ${
                      player.isActive ? 'bg-red-600 text-white' : 'bg-gray-600 text-gray-300'
                    }`}>
                      {player.number}
                    </div>
                    <div>
                      <div className="flex items-center space-x-2">
                        <span className="text-white font-medium">{player.name}</span>
                        {player.isCaptain && <Star className="w-4 h-4 text-yellow-400" />}
                      </div>
                      <span className="text-gray-400 text-sm">{player.position}</span>
                    </div>
                  </div>
                  <div className="flex items-center space-x-2">
                    {isServingPlayer(player, awayTeam) && (
                      <span className="text-yellow-400 text-lg">🏐</span>
                    )}
                    <span className={`px-2 py-1 rounded text-xs font-bold ${
                      player.isActive ? 'bg-green-600 text-white' : 'bg-gray-600 text-gray-300'
                    }`}>
                      {player.isActive ? 'EN CANCHA' : 'BANCA'}
                    </span>
                  </div>
                </div>
              ))}
            </div>
          </div>
        </div>

        {/* Action History */}
        <div className="mt-8 bg-gradient-to-br from-slate-800 to-slate-700 rounded-2xl p-6 shadow-2xl border border-slate-600">
          <h3 className="text-2xl font-black text-white mb-4 flex items-center space-x-2">
            <CheckCircle className="w-6 h-6 text-green-400" />
            <span>Historial de Acciones</span>
          </h3>
          <div className="max-h-40 overflow-y-auto space-y-2">
            {actionHistory.slice(-10).reverse().map((action, index) => (
              <div key={index} className="text-gray-300 text-sm p-2 bg-slate-700/50 rounded">
                {action}
              </div>
            ))}
            {actionHistory.length === 0 && (
              <div className="text-gray-500 text-center py-4">
                No hay acciones registradas
              </div>
            )}
          </div>
        </div>
      </div>
    </div>
  );
}
