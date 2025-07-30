import React, { useState, useEffect } from 'react';
import { Head } from '@inertiajs/react';
import { PlayIcon, PauseIcon, ArrowLeftIcon, ArrowPathIcon, ClockIcon, StarIcon, CheckCircleIcon } from '@heroicons/react/24/outline';

interface Player {
  id: number;
  name: string;
  number: number;
  position: string;
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

interface MatchControlPageProps {
  onNavigate: (page: string) => void;
  isLoggedIn: boolean;
  currentUser?: any;
  onLogout: () => void;
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
    let interval: ReturnType<typeof setInterval>;
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
      <Head title="Control de Partido - VolleyPass" />
      
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
              <ClockIcon className="w-4 h-4 text-blue-400" />
            </div>
            <div className="flex items-center space-x-2">
              <ClockIcon className="w-4 h-4 text-red-400" />
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
                    <PlayIcon className="w-5 h-5" />
                    <span>Iniciar Partido</span>
                  </button>
                ) : (
                  <button
                    onClick={pauseMatch}
                    className="bg-yellow-600 hover:bg-yellow-700 text-white px-6 py-3 rounded-lg font-bold transition-colors duration-200 flex items-center space-x-2 mx-auto"
                  >
                    <PauseIcon className="w-5 h-5" />
                    <span>Pausar</span>
                  </button>
                )}
                <button
                  onClick={undoLastAction}
                  className="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-bold transition-colors duration-200 flex items-center space-x-2 mx-auto"
                >
                  <ArrowLeftIcon className="w-4 h-4" />
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
                  <ArrowPathIcon className="w-4 h-4" />
                </button>
                <button
                  onClick={() => useTimeout('home')}
                  disabled={homeTeam.timeouts === 0}
                  className="bg-yellow-600 hover:bg-yellow-700 disabled:bg-gray-600 text-white p-2 rounded-lg transition-colors duration-200"
                  title="Tiempo fuera"
                >
                  <ClockIcon className="w-4 h-4" />
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
                      <StarIcon className="w-2 h-2 text-yellow-800" />
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
                        {player.isCaptain && <StarIcon className="w-4 h-4 text-yellow-400" />}
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
                  <ArrowPathIcon className="w-4 h-4" />
                </button>
                <button
                  onClick={() => useTimeout('away')}
                  disabled={awayTeam.timeouts === 0}
                  className="bg-yellow-600 hover:bg-yellow-700 disabled:bg-gray-600 text-white p-2 rounded-lg transition-colors duration-200"
                  title="Tiempo fuera"
                >
                  <ClockIcon className="w-4 h-4" />
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
                      <StarIcon className="w-2 h-2 text-yellow-800" />
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
                        {player.isCaptain && <StarIcon className="w-4 h-4 text-yellow-400" />}
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
            <CheckCircleIcon className="w-6 h-6 text-green-400" />
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