import React, { useState, useEffect } from 'react';
import { Match, MatchSet, Player } from '@/types/global';
import { useRefereeMatchControl, useMatchRealTimeUpdates } from '@/hooks/useMatchRealTimeUpdates';
import { PlayIcon, PauseIcon, StopIcon, PlusIcon, MinusIcon, CheckIcon, ArrowPathIcon } from '@heroicons/react/24/outline';

interface MatchControlPanelProps {
    match: Match;
    onMatchUpdate?: (match: Match) => void;
}

interface CurrentSetScore {
    home_score: number;
    away_score: number;
    set_number: number;
}

interface TeamPlayer {
    id: number;
    name: string;
    jersey_number: number;
    position: string;
    is_captain: boolean;
}

interface TeamRotation {
    [position: number]: number; // position -> player_id
}

export default function MatchControlPanel({ match: initialMatch, onMatchUpdate }: MatchControlPanelProps) {
    const [match, setMatch] = useState<Match>(initialMatch);
    const [currentSet, setCurrentSet] = useState<CurrentSetScore>({
        home_score: 0,
        away_score: 0,
        set_number: 1,
    });
    const [isUpdating, setIsUpdating] = useState(false);
    const [lastAction, setLastAction] = useState<string>('');
    const [homePlayers, setHomePlayers] = useState<TeamPlayer[]>([]);
    const [awayPlayers, setAwayPlayers] = useState<TeamPlayer[]>([]);
    const [homeRotation, setHomeRotation] = useState<TeamRotation>({});
    const [awayRotation, setAwayRotation] = useState<TeamRotation>({});
    const [showPlayersList, setShowPlayersList] = useState(false);
    const [servingTeam, setServingTeam] = useState<'home' | 'away'>('home'); // Quien saca
    const [servingPosition, setServingPosition] = useState<number>(1); // Posición que saca

    // Hook para control de árbitro
    const { updateScore, startNewSet, endSet, updateMatchStatus } = useRefereeMatchControl(
        match.id,
        (homeScore, awayScore) => {
            setCurrentSet(prev => ({ ...prev, home_score: homeScore, away_score: awayScore }));
        },
        (setData) => {
            // Set completado, iniciar nuevo set
            setCurrentSet({
                home_score: 0,
                away_score: 0,
                set_number: (setData.set_number || 0) + 1,
            });
        }
    );

    // Hook para actualizaciones en tiempo real
    useMatchRealTimeUpdates(match.id, {
        onMatchUpdate: (updatedMatch) => {
            setMatch(updatedMatch);
            onMatchUpdate?.(updatedMatch);
        },
        onMatchScoreUpdate: (matchId, homeScore, awayScore) => {
            if (matchId === match.id) {
                setCurrentSet(prev => ({ ...prev, home_score: homeScore, away_score: awayScore }));
            }
        },
    });

    // Cargar jugadoras de los equipos
    useEffect(() => {
        const loadTeamPlayers = async () => {
            try {
                // Simular datos de jugadoras (en producción esto vendría de una API)
                const mockHomePlayers: TeamPlayer[] = [
                    { id: 1, name: 'Ana García', jersey_number: 1, position: 'Libero', is_captain: false },
                    { id: 2, name: 'María López', jersey_number: 2, position: 'Setter', is_captain: true },
                    { id: 3, name: 'Carmen Ruiz', jersey_number: 3, position: 'Outside Hitter', is_captain: false },
                    { id: 4, name: 'Sofia Torres', jersey_number: 4, position: 'Middle Blocker', is_captain: false },
                    { id: 5, name: 'Laura Díaz', jersey_number: 5, position: 'Outside Hitter', is_captain: false },
                    { id: 6, name: 'Elena Morales', jersey_number: 6, position: 'Opposite', is_captain: false },
                ];
                
                const mockAwayPlayers: TeamPlayer[] = [
                    { id: 7, name: 'Valentina Cruz', jersey_number: 1, position: 'Libero', is_captain: false },
                    { id: 8, name: 'Isabella Vargas', jersey_number: 2, position: 'Setter', is_captain: true },
                    { id: 9, name: 'Camila Herrera', jersey_number: 3, position: 'Outside Hitter', is_captain: false },
                    { id: 10, name: 'Daniela Castro', jersey_number: 4, position: 'Middle Blocker', is_captain: false },
                    { id: 11, name: 'Natalia Ramos', jersey_number: 5, position: 'Outside Hitter', is_captain: false },
                    { id: 12, name: 'Andrea Silva', jersey_number: 6, position: 'Opposite', is_captain: false },
                ];
                
                setHomePlayers(mockHomePlayers);
                setAwayPlayers(mockAwayPlayers);
                
                // Inicializar rotación básica (posiciones 1-6)
                const initialHomeRotation: TeamRotation = {
                    1: mockHomePlayers[0]?.id || 1,
                    2: mockHomePlayers[1]?.id || 2,
                    3: mockHomePlayers[2]?.id || 3,
                    4: mockHomePlayers[3]?.id || 4,
                    5: mockHomePlayers[4]?.id || 5,
                    6: mockHomePlayers[5]?.id || 6,
                };
                
                const initialAwayRotation: TeamRotation = {
                    1: mockAwayPlayers[0]?.id || 7,
                    2: mockAwayPlayers[1]?.id || 8,
                    3: mockAwayPlayers[2]?.id || 9,
                    4: mockAwayPlayers[3]?.id || 10,
                    5: mockAwayPlayers[4]?.id || 11,
                    6: mockAwayPlayers[5]?.id || 12,
                };
                
                setHomeRotation(initialHomeRotation);
                setAwayRotation(initialAwayRotation);
            } catch (error) {
                console.error('Error loading team players:', error);
            }
        };
        
        loadTeamPlayers();
    }, [match.id]);

    // Función para rotar jugadoras según el patrón solicitado
    const rotateTeam = (team: 'home' | 'away') => {
        if (team === 'home') {
            setHomeRotation(prev => {
                const newRotation: TeamRotation = {};
                // Rotación solicitada: Pos 4 - Pos 3 - Pos 2, Pos 5 - Pos 6 - Pos 1
                newRotation[1] = prev[5];
                newRotation[2] = prev[4];
                newRotation[3] = prev[2];
                newRotation[4] = prev[3];
                newRotation[5] = prev[6];
                newRotation[6] = prev[1];
                return newRotation;
            });
        } else {
            setAwayRotation(prev => {
                const newRotation: TeamRotation = {};
                // Rotación solicitada: Pos 4 - Pos 3 - Pos 2, Pos 5 - Pos 6 - Pos 1
                newRotation[1] = prev[5];
                newRotation[2] = prev[4];
                newRotation[3] = prev[2];
                newRotation[4] = prev[3];
                newRotation[5] = prev[6];
                newRotation[6] = prev[1];
                return newRotation;
            });
        }
    };

    // Actualizar puntuación con rotación automática
    const handleScoreUpdate = async (team: 'home' | 'away', increment: number) => {
        setIsUpdating(true);
        try {
            const action = increment > 0 ? 'increment' : 'decrement';
            const result = await updateScore(team, action, currentSet.set_number);
            
            setLastAction(`${team === 'home' ? match.home_team?.name : match.away_team?.name} ${increment > 0 ? '+' : ''}${increment}`);
            
            // Aplicar rotación si el equipo ganó un punto (regla básica de voleibol)
            if (increment > 0) {
                rotateTeam(team);
            }
            
            // Actualizar puntajes locales si están en el resultado
            if (result?.data) {
                setCurrentSet(prev => ({
                    ...prev,
                    home_score: result.data.home_score || prev.home_score,
                    away_score: result.data.away_score || prev.away_score
                }));
            }
            
        } catch (error) {
            console.error('Error updating score:', error);
            setLastAction('Error al actualizar el puntaje');
        } finally {
            setIsUpdating(false);
        }
    };

    // Función para hacer clic en el marcador y asignar punto
    const handleScoreClick = (team: 'home' | 'away') => {
        handleScoreUpdate(team, 1);
    };

    // Iniciar nuevo set
    const handleNewSet = async () => {
        setIsUpdating(true);
        try {
            await startNewSet();
            setLastAction('Nuevo set iniciado');
        } catch (error) {
            console.error('Error starting new set:', error);
        } finally {
            setIsUpdating(false);
        }
    };

    // Finalizar set actual
    const handleEndSet = async () => {
        setIsUpdating(true);
        try {
            await endSet();
            setLastAction('Set finalizado');
        } catch (error) {
            console.error('Error ending set:', error);
        } finally {
            setIsUpdating(false);
        }
    };

    // Cambiar estado del partido
    const handleStatusChange = async (newStatus: string) => {
        setIsUpdating(true);
        try {
            await updateMatchStatus(newStatus);
            setLastAction(`Estado cambiado a ${newStatus}`);
        } catch (error) {
            console.error('Error updating match status:', error);
        } finally {
            setIsUpdating(false);
        }
    };

    const getStatusColor = (status: string) => {
        switch (status) {
            case 'in_progress':
                return 'bg-red-500';
            case 'finished':
                return 'bg-gray-500';
            case 'scheduled':
                return 'bg-blue-500';
            default:
                return 'bg-gray-400';
        }
    };

    return (
        <div className="bg-gradient-to-br from-slate-800 to-slate-700 rounded-2xl shadow-2xl border border-slate-600 p-6">
            {/* Header */}
            <div className="flex items-center justify-between mb-6">
                <div>
                    <h2 className="text-2xl font-bold text-white mb-1">
                        Panel de Control - Árbitro
                    </h2>
                    <div className="flex items-center space-x-3">
                        <div className={`px-3 py-1 rounded-full text-xs font-bold text-white ${getStatusColor(match.status)}`}>
                            {match.status?.toUpperCase()}
                        </div>
                        <div className="text-gray-400 text-sm">
                            Set {currentSet.set_number}
                        </div>
                    </div>
                </div>
                {lastAction && (
                    <div className="bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-semibold">
                        {lastAction}
                    </div>
                )}
            </div>

            {/* Teams and Score Control - Clickeable */}
            <div className="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                {/* Home Team */}
                <div className="bg-slate-600/50 rounded-xl p-6">
                    <div 
                        className="text-center mb-4 cursor-pointer hover:bg-slate-500/30 rounded-lg p-4 transition-colors duration-200 border-2 border-transparent hover:border-yellow-400/50"
                        onClick={() => handleScoreClick('home')}
                    >
                        <h3 className="text-xl font-bold text-white mb-2">
                            {match.home_team?.name || 'Equipo Local'}
                        </h3>
                        <div className="text-6xl font-black text-yellow-400 mb-4">
                            {currentSet.home_score}
                        </div>
                        <div className="text-gray-400 text-sm mb-2">
                            Sets: {match.home_sets || 0}
                        </div>
                        <div className="text-xs text-yellow-300">
                            Clic para punto
                        </div>
                    </div>
                    
                    <div className="flex justify-center space-x-4">
                        <button
                            onClick={() => handleScoreUpdate('home', -1)}
                            disabled={isUpdating || currentSet.home_score === 0}
                            className="bg-red-600 hover:bg-red-700 disabled:opacity-50 disabled:cursor-not-allowed text-white p-3 rounded-lg transition-all"
                        >
                            <MinusIcon className="w-6 h-6" />
                        </button>
                        <button
                            onClick={() => handleScoreUpdate('home', 1)}
                            disabled={isUpdating}
                            className="bg-green-600 hover:bg-green-700 disabled:opacity-50 disabled:cursor-not-allowed text-white p-3 rounded-lg transition-all"
                        >
                            <PlusIcon className="w-6 h-6" />
                        </button>
                    </div>
                </div>

                {/* Away Team */}
                <div className="bg-slate-600/50 rounded-xl p-6">
                    <div 
                        className="text-center mb-4 cursor-pointer hover:bg-slate-500/30 rounded-lg p-4 transition-colors duration-200 border-2 border-transparent hover:border-yellow-400/50"
                        onClick={() => handleScoreClick('away')}
                    >
                        <h3 className="text-xl font-bold text-white mb-2">
                            {match.away_team?.name || 'Equipo Visitante'}
                        </h3>
                        <div className="text-6xl font-black text-yellow-400 mb-4">
                            {currentSet.away_score}
                        </div>
                        <div className="text-gray-400 text-sm mb-2">
                            Sets: {match.away_sets || 0}
                        </div>
                        <div className="text-xs text-yellow-300">
                            Clic para punto
                        </div>
                    </div>
                    
                    <div className="flex justify-center space-x-4">
                        <button
                            onClick={() => handleScoreUpdate('away', -1)}
                            disabled={isUpdating || currentSet.away_score === 0}
                            className="bg-red-600 hover:bg-red-700 disabled:opacity-50 disabled:cursor-not-allowed text-white p-3 rounded-lg transition-all"
                        >
                            <MinusIcon className="w-6 h-6" />
                        </button>
                        <button
                            onClick={() => handleScoreUpdate('away', 1)}
                            disabled={isUpdating}
                            className="bg-green-600 hover:bg-green-700 disabled:opacity-50 disabled:cursor-not-allowed text-white p-3 rounded-lg transition-all"
                        >
                            <PlusIcon className="w-6 h-6" />
                        </button>
                    </div>
                </div>
            </div>

            {/* Match Controls */}
            <div className="flex flex-wrap gap-4 justify-center">
                {match.status === 'scheduled' && (
                    <button
                        onClick={() => handleStatusChange('in_progress')}
                        disabled={isUpdating}
                        className="bg-green-600 hover:bg-green-700 disabled:opacity-50 text-white px-6 py-3 rounded-lg font-semibold flex items-center space-x-2 transition-all"
                    >
                        <PlayIcon className="w-5 h-5" />
                        <span>Iniciar Partido</span>
                    </button>
                )}
                
                {match.status === 'in_progress' && (
                    <>
                        <button
                            onClick={() => handleStatusChange('finished')}
                            disabled={isUpdating}
                            className="bg-red-600 hover:bg-red-700 disabled:opacity-50 text-white px-6 py-3 rounded-lg font-semibold flex items-center space-x-2 transition-all"
                        >
                            <StopIcon className="w-5 h-5" />
                            <span>Finalizar Partido</span>
                        </button>
                        
                        <button
                            onClick={handleNewSet}
                            disabled={isUpdating}
                            className="bg-blue-600 hover:bg-blue-700 disabled:opacity-50 text-white px-6 py-3 rounded-lg font-semibold flex items-center space-x-2 transition-all"
                        >
                            <CheckIcon className="w-5 h-5" />
                            <span>Nuevo Set</span>
                        </button>
                        
                        <button
                            onClick={handleEndSet}
                            disabled={isUpdating}
                            className="bg-orange-600 hover:bg-orange-700 disabled:opacity-50 text-white px-6 py-3 rounded-lg font-semibold flex items-center space-x-2 transition-all"
                        >
                            <StopIcon className="w-5 h-5" />
                            <span>Finalizar Set</span>
                        </button>
                    </>
                )}
            </div>

            {/* Match Info */}
            <div className="mt-6 pt-6 border-t border-slate-600">
                <div className="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm text-gray-400">
                    <div>
                        <span className="font-semibold">Torneo:</span> {match.tournament?.name || 'N/A'}
                    </div>
                    <div>
                        <span className="font-semibold">Sede:</span> {match.venue || 'N/A'}
                    </div>
                    <div>
                        <span className="font-semibold">Fecha:</span> {match.scheduled_at ? new Date(match.scheduled_at).toLocaleDateString() : 'N/A'}
                    </div>
                </div>
            </div>

            {/* Lista de Jugadoras */}
            <div className="mt-6 pt-6 border-t border-slate-600">
                <div className="flex items-center justify-between mb-6">
                    <h3 className="text-xl font-bold text-white">Jugadoras en Cancha</h3>
                    <div className="flex items-center gap-3">
                        <div className="flex items-center gap-2 text-sm">
                            <span className="text-gray-400">Saque:</span>
                            <span className="text-yellow-400 font-bold">
                                {servingTeam === 'home' ? (match.home_team?.name || 'Local') : (match.away_team?.name || 'Visitante')} - Pos. {servingPosition}
                            </span>
                        </div>
                        <button
                            onClick={() => setServingTeam(servingTeam === 'home' ? 'away' : 'home')}
                            className="px-3 py-1 bg-purple-600 text-white rounded hover:bg-purple-700 transition-colors text-sm"
                        >
                            Cambiar Saque
                        </button>
                        <button
                            onClick={() => setShowPlayersList(!showPlayersList)}
                            className="flex items-center gap-2 px-4 py-2 bg-yellow-500 text-black rounded-lg hover:bg-yellow-400 transition-colors"
                        >
                            <ArrowPathIcon className="w-4 h-4" />
                            {showPlayersList ? 'Ocultar Lista' : 'Ver Lista Completa'}
                        </button>
                    </div>
                </div>

                <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {/* Jugadoras Equipo Local */}
                    <div>
                        <div className="flex items-center justify-between mb-4">
                            <h4 className="text-lg font-semibold text-blue-300">
                                {match.home_team?.name || 'Equipo Local'}
                            </h4>
                            <button
                                onClick={() => rotateTeam('home')}
                                className="flex items-center gap-1 px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors text-sm"
                            >
                                <ArrowPathIcon className="w-4 h-4" />
                                Rotar
                            </button>
                        </div>
                        <div className="space-y-2">
                            <div className="text-sm text-gray-400 mb-3">Rotación Actual (Posiciones 1-6):</div>
                            <div className="grid grid-cols-3 gap-2">
                                {[1, 2, 3, 4, 5, 6].map(position => {
                                    const playerId = homeRotation[position];
                                    const player = homePlayers.find(p => p.id === playerId);
                                    const isServing = servingTeam === 'home' && servingPosition === position;
                                    return (
                                        <div key={position} className={`${isServing ? 'bg-yellow-600/50 border-2 border-yellow-400' : 'bg-blue-600/30'} rounded-lg p-3 text-center relative`}>
                                            <div className="text-xs text-blue-300">Pos. {position}</div>
                                            <div className="text-lg font-bold text-white">#{player?.jersey_number || position}</div>
                                            <div className="text-xs text-blue-200 truncate">{player?.name || `Jugadora ${position}`}</div>
                                            <div className="text-xs text-blue-400">{player?.position || 'N/A'}</div>
                                            {player?.is_captain && <div className="text-xs text-yellow-300">★ Capitana</div>}
                                            {isServing && <div className="absolute -top-1 -right-1 bg-yellow-500 text-black text-xs px-1 rounded-full font-bold">SAQUE</div>}
                                        </div>
                                    );
                                })}
                            </div>
                            
                            {showPlayersList && (
                                <div className="mt-4">
                                    <div className="text-sm text-gray-400 mb-2">Lista Completa:</div>
                                    <div className="space-y-1">
                                        {homePlayers.map(player => (
                                            <div key={player.id} className="flex items-center justify-between bg-blue-600/20 rounded p-2">
                                                <div className="flex items-center gap-2">
                                                    <span className="font-bold text-blue-300">#{player.jersey_number}</span>
                                                    <span className="text-white">{player.name}</span>
                                                    {player.is_captain && <span className="text-yellow-300">★</span>}
                                                </div>
                                                <span className="text-xs text-blue-400">{player.position}</span>
                                            </div>
                                        ))}
                                    </div>
                                </div>
                            )}
                        </div>
                    </div>

                    {/* Jugadoras Equipo Visitante */}
                    <div>
                        <div className="flex items-center justify-between mb-4">
                            <h4 className="text-lg font-semibold text-red-300">
                                {match.away_team?.name || 'Equipo Visitante'}
                            </h4>
                            <button
                                onClick={() => rotateTeam('away')}
                                className="flex items-center gap-1 px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700 transition-colors text-sm"
                            >
                                <ArrowPathIcon className="w-4 h-4" />
                                Rotar
                            </button>
                        </div>
                        <div className="space-y-2">
                            <div className="text-sm text-gray-400 mb-3">Rotación Actual (Posiciones 1-6):</div>
                            <div className="grid grid-cols-3 gap-2">
                                {[1, 2, 3, 4, 5, 6].map(position => {
                                    const playerId = awayRotation[position];
                                    const player = awayPlayers.find(p => p.id === playerId);
                                    const isServing = servingTeam === 'away' && servingPosition === position;
                                    return (
                                        <div key={position} className={`${isServing ? 'bg-yellow-600/50 border-2 border-yellow-400' : 'bg-red-600/30'} rounded-lg p-3 text-center relative`}>
                                            <div className="text-xs text-red-300">Pos. {position}</div>
                                            <div className="text-lg font-bold text-white">#{player?.jersey_number || position}</div>
                                            <div className="text-xs text-red-200 truncate">{player?.name || `Jugadora ${position}`}</div>
                                            <div className="text-xs text-red-400">{player?.position || 'N/A'}</div>
                                            {player?.is_captain && <div className="text-xs text-yellow-300">★ Capitana</div>}
                                            {isServing && <div className="absolute -top-1 -right-1 bg-yellow-500 text-black text-xs px-1 rounded-full font-bold">SAQUE</div>}
                                        </div>
                                    );
                                })}
                            </div>
                            
                            {showPlayersList && (
                                <div className="mt-4">
                                    <div className="text-sm text-gray-400 mb-2">Lista Completa:</div>
                                    <div className="space-y-1">
                                        {awayPlayers.map(player => (
                                            <div key={player.id} className="flex items-center justify-between bg-red-600/20 rounded p-2">
                                                <div className="flex items-center gap-2">
                                                    <span className="font-bold text-red-300">#{player.jersey_number}</span>
                                                    <span className="text-white">{player.name}</span>
                                                    {player.is_captain && <span className="text-yellow-300">★</span>}
                                                </div>
                                                <span className="text-xs text-red-400">{player.position}</span>
                                            </div>
                                        ))}
                                    </div>
                                </div>
                            )}
                        </div>
                    </div>
                </div>
            </div>

            {isUpdating && (
                <div className="absolute inset-0 bg-black/50 flex items-center justify-center rounded-2xl">
                    <div className="bg-white rounded-lg p-4 flex items-center space-x-3">
                        <div className="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600"></div>
                        <span className="text-gray-700 font-semibold">Actualizando...</span>
                    </div>
                </div>
            )}
        </div>
    );
}