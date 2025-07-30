import React, { useState, useEffect } from 'react';
import { Head } from '@inertiajs/react';
import { PlayIcon, EyeIcon, ClockIcon, TrophyIcon, UserIcon, MapPinIcon, CalendarIcon } from '@heroicons/react/24/outline';

interface Team {
  id: number;
  name: string;
  logo: string;
  score?: number;
  sets?: number;
}

interface Match {
  id: number;
  homeTeam: Team;
  awayTeam: Team;
  status: 'live' | 'upcoming' | 'finished';
  currentSet?: number;
  time?: string;
  venue?: string;
  viewers?: number;
  setScores?: Array<{home: number, away: number}>;
  tournament?: string;
  scheduledAt?: string;
}

interface Standing {
  position: number;
  team: string;
  matches: number;
  wins: number;
  losses: number;
  points: number;
}

interface MatchesPageProps {
  onNavigate: (page: string) => void;
  isLoggedIn: boolean;
  currentUser?: any;
  onLogout: () => void;
}

export default function MatchesPage({ onNavigate, isLoggedIn, currentUser, onLogout }: MatchesPageProps) {
  const [liveMatches] = useState<Match[]>([
    {
      id: 1,
      homeTeam: { id: 1, name: 'Águilas Sucre', logo: '🦅', score: 21, sets: 2 },
      awayTeam: { id: 2, name: 'Tigres Corozal', logo: '🐅', score: 18, sets: 1 },
      status: 'live',
      currentSet: 4,
      time: '23:45',
      venue: 'Coliseo Municipal',
      viewers: 1247,
      setScores: [
        {home: 25, away: 23},
        {home: 22, away: 25},
        {home: 25, away: 19},
        {home: 21, away: 18}
      ],
      tournament: 'Liga Departamental Femenina'
    },
    {
      id: 2,
      homeTeam: { id: 3, name: 'Leones Sincelejo', logo: '🦁', score: 15, sets: 0 },
      awayTeam: { id: 4, name: 'Halcones Sampués', logo: '🦅', score: 12, sets: 1 },
      status: 'live',
      currentSet: 2,
      time: '08:30',
      venue: 'Gimnasio Central',
      viewers: 892,
      setScores: [
        {home: 23, away: 25},
        {home: 15, away: 12}
      ],
      tournament: 'Copa Regional'
    },
    {
      id: 3,
      homeTeam: { id: 5, name: 'Panteras Tolú', logo: '🐆', score: 8, sets: 0 },
      awayTeam: { id: 6, name: 'Cóndores Ovejas', logo: '🦅', score: 11, sets: 0 },
      status: 'live',
      currentSet: 1,
      time: '12:15',
      venue: 'Polideportivo Norte',
      viewers: 634,
      setScores: [
        {home: 8, away: 11}
      ],
      tournament: 'Torneo Juvenil'
    }
  ]);

  const [upcomingMatches] = useState<Match[]>([
    {
      id: 4,
      homeTeam: { id: 7, name: 'Jaguares Majagual', logo: '🐆' },
      awayTeam: { id: 8, name: 'Búhos Galeras', logo: '🦉' },
      status: 'upcoming',
      scheduledAt: '2024-01-15 19:00',
      venue: 'Coliseo Cubierto',
      tournament: 'Liga Departamental Femenina'
    },
    {
      id: 5,
      homeTeam: { id: 9, name: 'Delfines San Onofre', logo: '🐬' },
      awayTeam: { id: 10, name: 'Zorros Coveñas', logo: '🦊' },
      status: 'upcoming',
      scheduledAt: '2024-01-15 21:00',
      venue: 'Gimnasio Municipal',
      tournament: 'Copa Regional'
    }
  ]);

  const [standings] = useState<Standing[]>([
    { position: 1, team: 'Águilas Sucre', matches: 12, wins: 10, losses: 2, points: 30 },
    { position: 2, team: 'Tigres Corozal', matches: 12, wins: 9, losses: 3, points: 27 },
    { position: 3, team: 'Leones Sincelejo', matches: 11, wins: 8, losses: 3, points: 24 },
    { position: 4, team: 'Halcones Sampués', matches: 12, wins: 7, losses: 5, points: 21 },
    { position: 5, team: 'Panteras Tolú', matches: 11, wins: 6, losses: 5, points: 18 }
  ]);

  const featuredMatch = liveMatches[0];

  return (
    <div className="min-h-screen bg-gradient-to-br from-slate-900 via-blue-900 to-slate-800">
      <Head title="Partidos - VolleyPass" />
      
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
                <p className="text-xs text-yellow-100 font-medium">Plataforma de Voleibol</p>
              </div>
            </button>

            <nav className="hidden md:flex items-center space-x-8">
              <button 
                onClick={() => onNavigate('matches')}
                className="text-white font-bold border-b-2 border-white pb-1"
              >
                Partidos
              </button>
              <button 
                onClick={() => onNavigate('tournaments')}
                className="text-yellow-100 hover:text-white font-semibold transition-colors duration-200"
              >
                Torneos
              </button>
              <button 
                onClick={() => onNavigate('dashboard')}
                className="text-yellow-100 hover:text-white font-semibold transition-colors duration-200"
              >
                Dashboard
              </button>
            </nav>

            <div className="flex items-center space-x-4">
              {isLoggedIn ? (
                <div className="flex items-center space-x-3">
                  <div className="text-white text-right">
                    <div className="font-semibold">{currentUser?.name}</div>
                    <div className="text-xs text-yellow-100">{currentUser?.role}</div>
                  </div>
                  <button 
                    onClick={onLogout}
                    className="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-semibold transition-colors duration-200"
                  >
                    Salir
                  </button>
                </div>
              ) : (
                <button 
                  onClick={() => onNavigate('login')}
                  className="bg-white text-blue-600 px-6 py-2 rounded-lg font-bold hover:bg-gray-100 transition-colors duration-200"
                >
                  Iniciar Sesión
                </button>
              )}
            </div>
          </div>
        </div>
      </header>

      <div className="container mx-auto px-4 py-8">
        {/* Hero Section - Featured Live Match */}
        {featuredMatch && (
          <div className="bg-gradient-to-r from-slate-800 to-slate-700 rounded-3xl p-8 mb-12 shadow-2xl border border-slate-600">
            <div className="text-center mb-6">
              <div className="flex items-center justify-center space-x-3 mb-4">
                <div className="w-3 h-3 bg-red-500 rounded-full animate-pulse"></div>
                <span className="text-red-400 font-black text-lg tracking-wide">EN VIVO</span>
                <div className="w-3 h-3 bg-red-500 rounded-full animate-pulse"></div>
              </div>
              <h2 className="text-4xl font-black text-white mb-2">
                {featuredMatch.homeTeam.name} vs {featuredMatch.awayTeam.name}
              </h2>
              <div className="flex items-center justify-center space-x-4 text-gray-300">
                <span>{featuredMatch.tournament}</span>
                <span>•</span>
                <span>Set {featuredMatch.currentSet}</span>
                <span>•</span>
                <span>{featuredMatch.time}</span>
                <span>•</span>
                <div className="flex items-center space-x-1">
                  <MapPinIcon className="w-4 h-4" />
                  <span>{featuredMatch.venue}</span>
                </div>
                <span>•</span>
                <div className="flex items-center space-x-1">
                  <EyeIcon className="w-4 h-4" />
                  <span>{featuredMatch.viewers?.toLocaleString()} viendo</span>
                </div>
              </div>
            </div>

            <div className="grid grid-cols-3 gap-8 items-center">
              {/* Home Team */}
              <div className="text-center">
                <div className="text-8xl mb-4">{featuredMatch.homeTeam.logo}</div>
                <h3 className="text-3xl font-black text-blue-400 mb-4">{featuredMatch.homeTeam.name}</h3>
                <div className="bg-blue-600 rounded-2xl p-6 shadow-xl">
                  <div className="text-6xl font-mono font-black text-white mb-2">
                    {featuredMatch.homeTeam.score?.toString().padStart(2, '0')}
                  </div>
                  <div className="text-blue-200 font-bold">Sets: {featuredMatch.homeTeam.sets}</div>
                </div>
              </div>

              {/* VS and Set Scores */}
              <div className="text-center space-y-6">
                <div className="text-6xl font-black text-white">VS</div>
                <div className="bg-slate-700/50 rounded-xl p-4">
                  <h4 className="text-white font-bold mb-3">Marcador por Sets</h4>
                  <div className="space-y-2">
                    {featuredMatch.setScores?.map((set, index) => (
                      <div key={index} className="flex justify-between items-center text-sm">
                        <span className="text-gray-300">Set {index + 1}:</span>
                        <div className="flex space-x-4">
                          <span className={`font-bold ${
                            set.home > set.away ? 'text-blue-400' : 'text-gray-400'
                          }`}>{set.home}</span>
                          <span className="text-gray-500">-</span>
                          <span className={`font-bold ${
                            set.away > set.home ? 'text-red-400' : 'text-gray-400'
                          }`}>{set.away}</span>
                        </div>
                      </div>
                    ))}
                  </div>
                </div>
                <button 
                  onClick={() => onNavigate(`match-control/${featuredMatch.id}`)}
                  className="bg-green-600 hover:bg-green-700 text-white px-8 py-3 rounded-lg font-bold transition-colors duration-200 flex items-center space-x-2 mx-auto"
                >
                  <EyeIcon className="w-5 h-5" />
                  <span>Ver Partido</span>
                </button>
              </div>

              {/* Away Team */}
              <div className="text-center">
                <div className="text-8xl mb-4">{featuredMatch.awayTeam.logo}</div>
                <h3 className="text-3xl font-black text-red-400 mb-4">{featuredMatch.awayTeam.name}</h3>
                <div className="bg-red-600 rounded-2xl p-6 shadow-xl">
                  <div className="text-6xl font-mono font-black text-white mb-2">
                    {featuredMatch.awayTeam.score?.toString().padStart(2, '0')}
                  </div>
                  <div className="text-red-200 font-bold">Sets: {featuredMatch.awayTeam.sets}</div>
                </div>
              </div>
            </div>
          </div>
        )}

        <div className="grid lg:grid-cols-3 gap-8">
          {/* Main Content */}
          <div className="lg:col-span-2 space-y-8">
            {/* Live Matches */}
            <div className="bg-gradient-to-br from-slate-800 to-slate-700 rounded-2xl p-6 shadow-2xl border border-slate-600">
              <div className="flex items-center justify-between mb-6">
                <h3 className="text-2xl font-black text-white flex items-center space-x-2">
                  <div className="w-3 h-3 bg-red-500 rounded-full animate-pulse"></div>
                  <span>Partidos en Vivo</span>
                </h3>
                <span className="text-gray-400">{liveMatches.length} partidos</span>
              </div>
              
              <div className="space-y-4">
                {liveMatches.map((match) => (
                  <div key={match.id} className="bg-slate-700/50 rounded-xl p-4 border border-slate-600/50 hover:border-slate-500 transition-colors duration-200">
                    <div className="flex items-center justify-between mb-3">
                      <div className="flex items-center space-x-3">
                        <div className="flex items-center space-x-2">
                          <div className="w-2 h-2 bg-red-500 rounded-full animate-pulse"></div>
                          <span className="text-red-400 font-bold text-sm">EN VIVO</span>
                        </div>
                        <span className="text-gray-400 text-sm">Set {match.currentSet}</span>
                        <span className="text-gray-400 text-sm">{match.time}</span>
                      </div>
                      <button 
                        onClick={() => onNavigate(`match-control/${match.id}`)}
                        className="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-semibold transition-colors duration-200 text-sm"
                      >
                        Ver
                      </button>
                    </div>
                    
                    <div className="grid grid-cols-3 gap-4 items-center">
                      <div className="text-center">
                        <div className="text-3xl mb-2">{match.homeTeam.logo}</div>
                        <div className="text-white font-bold">{match.homeTeam.name}</div>
                        <div className="text-2xl font-mono font-black text-blue-400 mt-2">
                          {match.homeTeam.score}
                        </div>
                      </div>
                      
                      <div className="text-center">
                        <div className="text-gray-400 font-bold mb-2">VS</div>
                        <div className="text-sm text-gray-400">
                          Sets: {match.homeTeam.sets} - {match.awayTeam.sets}
                        </div>
                      </div>
                      
                      <div className="text-center">
                        <div className="text-3xl mb-2">{match.awayTeam.logo}</div>
                        <div className="text-white font-bold">{match.awayTeam.name}</div>
                        <div className="text-2xl font-mono font-black text-red-400 mt-2">
                          {match.awayTeam.score}
                        </div>
                      </div>
                    </div>
                    
                    {/* Set by set scores */}
                    <div className="mt-4 pt-4 border-t border-slate-600">
                      <div className="flex justify-between items-center text-sm">
                        <div className="flex items-center space-x-2">
                          <EyeIcon className="w-4 h-4 text-gray-400" />
                          <span className="text-gray-400">{match.viewers?.toLocaleString()} viendo</span>
                        </div>
                        <div className="flex items-center space-x-2">
                          <MapPinIcon className="w-4 h-4 text-gray-400" />
                          <span className="text-gray-400">{match.venue}</span>
                        </div>
                      </div>
                    </div>
                  </div>
                ))}
              </div>
            </div>

            {/* Upcoming Matches */}
            <div className="bg-gradient-to-br from-slate-800 to-slate-700 rounded-2xl p-6 shadow-2xl border border-slate-600">
              <div className="flex items-center justify-between mb-6">
                <h3 className="text-2xl font-black text-white flex items-center space-x-2">
                  <ClockIcon className="w-6 h-6 text-blue-400" />
                  <span>Próximos Partidos</span>
                </h3>
                <span className="text-gray-400">{upcomingMatches.length} partidos</span>
              </div>
              
              <div className="space-y-4">
                {upcomingMatches.map((match) => (
                  <div key={match.id} className="bg-slate-700/50 rounded-xl p-4 border border-slate-600/50 hover:border-slate-500 transition-colors duration-200">
                    <div className="flex items-center justify-between mb-3">
                      <div className="flex items-center space-x-3">
                        <span className="text-blue-400 font-bold text-sm">PROGRAMADO</span>
                        <div className="flex items-center space-x-1 text-gray-400 text-sm">
                          <CalendarIcon className="w-4 h-4" />
                          <span>{match.scheduledAt}</span>
                        </div>
                      </div>
                      <button className="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-semibold transition-colors duration-200 text-sm">
                        Ver
                      </button>
                    </div>
                    
                    <div className="grid grid-cols-3 gap-4 items-center">
                      <div className="text-center">
                        <div className="text-3xl mb-2">{match.homeTeam.logo}</div>
                        <div className="text-white font-bold">{match.homeTeam.name}</div>
                      </div>
                      
                      <div className="text-center">
                        <div className="text-gray-400 font-bold">VS</div>
                      </div>
                      
                      <div className="text-center">
                        <div className="text-3xl mb-2">{match.awayTeam.logo}</div>
                        <div className="text-white font-bold">{match.awayTeam.name}</div>
                      </div>
                    </div>
                    
                    <div className="mt-4 pt-4 border-t border-slate-600">
                      <div className="flex justify-between items-center text-sm">
                        <div className="flex items-center space-x-2">
                          <TrophyIcon className="w-4 h-4 text-gray-400" />
                          <span className="text-gray-400">{match.tournament}</span>
                        </div>
                        <div className="flex items-center space-x-2">
                          <MapPinIcon className="w-4 h-4 text-gray-400" />
                          <span className="text-gray-400">{match.venue}</span>
                        </div>
                      </div>
                    </div>
                  </div>
                ))}
              </div>
            </div>
          </div>

          {/* Sidebar */}
          <div className="space-y-8">
            {/* Standings */}
            <div className="bg-gradient-to-br from-slate-800 to-slate-700 rounded-2xl p-6 shadow-2xl border border-slate-600">
              <h3 className="text-2xl font-black text-white mb-6 flex items-center space-x-2">
                <TrophyIcon className="w-6 h-6 text-yellow-400" />
                <span>Tabla de Posiciones</span>
              </h3>
              
              <div className="space-y-3">
                {standings.map((team) => (
                  <div key={team.position} className="flex items-center justify-between p-3 bg-slate-700/50 rounded-lg border border-slate-600/50">
                    <div className="flex items-center space-x-3">
                      <div className={`w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm ${
                        team.position <= 3 ? 'bg-yellow-500 text-yellow-900' : 'bg-gray-600 text-white'
                      }`}>
                        {team.position}
                      </div>
                      <div>
                        <div className="text-white font-bold">{team.team}</div>
                        <div className="text-gray-400 text-sm">{team.wins}V - {team.losses}D</div>
                      </div>
                    </div>
                    <div className="text-right">
                      <div className="text-white font-bold">{team.points}</div>
                      <div className="text-gray-400 text-sm">pts</div>
                    </div>
                  </div>
                ))}
              </div>
            </div>

            {/* Quick Stats */}
            <div className="bg-gradient-to-br from-slate-800 to-slate-700 rounded-2xl p-6 shadow-2xl border border-slate-600">
              <h3 className="text-2xl font-black text-white mb-6">Estadísticas Rápidas</h3>
              
              <div className="space-y-4">
                <div className="flex items-center justify-between p-3 bg-slate-700/50 rounded-lg">
                  <span className="text-gray-300">Partidos Hoy</span>
                  <span className="text-white font-bold text-xl">{liveMatches.length + upcomingMatches.length}</span>
                </div>
                <div className="flex items-center justify-between p-3 bg-slate-700/50 rounded-lg">
                  <span className="text-gray-300">En Vivo</span>
                  <span className="text-red-400 font-bold text-xl">{liveMatches.length}</span>
                </div>
                <div className="flex items-center justify-between p-3 bg-slate-700/50 rounded-lg">
                  <span className="text-gray-300">Espectadores</span>
                  <span className="text-blue-400 font-bold text-xl">
                    {liveMatches.reduce((total, match) => total + (match.viewers || 0), 0).toLocaleString()}
                  </span>
                </div>
                <div className="flex items-center justify-between p-3 bg-slate-700/50 rounded-lg">
                  <span className="text-gray-300">Equipos Activos</span>
                  <span className="text-green-400 font-bold text-xl">24</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      {/* Footer */}
      <footer className="bg-slate-900 border-t border-slate-700 mt-16">
        <div className="container mx-auto px-4 py-8">
          <div className="grid md:grid-cols-4 gap-8">
            <div>
              <div className="flex items-center space-x-2 mb-4">
                <span className="text-2xl">🏐</span>
                <span className="text-xl font-black text-white">VolleyPass</span>
              </div>
              <p className="text-gray-400 text-sm">
                La plataforma líder para el voleibol en Sucre. Conectando equipos, jugadores y aficionados.
              </p>
            </div>
            <div>
              <h4 className="text-white font-bold mb-4">Partidos</h4>
              <ul className="space-y-2 text-gray-400 text-sm">
                <li><button onClick={() => onNavigate('live-matches')}>En Vivo</button></li>
                <li><button onClick={() => onNavigate('upcoming-matches')}>Próximos</button></li>
                <li><button onClick={() => onNavigate('results')}>Resultados</button></li>
              </ul>
            </div>
            <div>
              <h4 className="text-white font-bold mb-4">Torneos</h4>
              <ul className="space-y-2 text-gray-400 text-sm">
                <li><button onClick={() => onNavigate('tournaments')}>Liga Departamental</button></li>
                <li><button onClick={() => onNavigate('tournaments')}>Copa Regional</button></li>
                <li><button onClick={() => onNavigate('tournaments')}>Torneo Juvenil</button></li>
              </ul>
            </div>
            <div>
              <h4 className="text-white font-bold mb-4">Soporte</h4>
              <ul className="space-y-2 text-gray-400 text-sm">
                <li><button onClick={() => onNavigate('help')}>Ayuda</button></li>
                <li><button onClick={() => onNavigate('contact')}>Contacto</button></li>
                <li><button onClick={() => onNavigate('about')}>Acerca de</button></li>
              </ul>
            </div>
          </div>
          <div className="border-t border-slate-700 mt-8 pt-8 text-center">
            <p className="text-gray-400 text-sm">
              © 2024 VolleyPass. Todos los derechos reservados. Desarrollado con ❤️ para el voleibol sucreño.
            </p>
          </div>
        </div>
      </footer>
    </div>
  );
}