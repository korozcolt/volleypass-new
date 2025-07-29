import React, { useState, useEffect } from 'react';
import { Match, MatchSet } from '@/types/global';
import { PlayIcon, ClockIcon, TrophyIcon, UsersIcon } from '@heroicons/react/24/outline';
import { useMatchRealTimeUpdates } from '@/hooks/useRealTimeUpdates';

interface LiveMatchCardProps {
    match: Match;
    onMatchUpdate?: (match: Match) => void;
}

interface LiveMatchData {
    match: Match;
    current_set?: MatchSet;
    sets: MatchSet[];
    is_live: boolean;
    events: any[];
}

export default function LiveMatchCard({ match: initialMatch, onMatchUpdate }: LiveMatchCardProps) {
    const [matchData, setMatchData] = useState<LiveMatchData>({
        match: initialMatch,
        sets: [],
        is_live: false,
        events: [],
    });
    const [timeElapsed, setTimeElapsed] = useState<string>('00:00');

    // Hook para actualizaciones en tiempo real
    useMatchRealTimeUpdates(initialMatch.id, {
        onMatchUpdate: (updatedMatch) => {
            setMatchData(prev => ({ ...prev, match: updatedMatch }));
            onMatchUpdate?.(updatedMatch);
        },
        onMatchScoreUpdate: (matchId, homeScore, awayScore) => {
            if (matchId === initialMatch.id) {
                // Actualizar el set actual
                setMatchData(prev => ({
                    ...prev,
                    current_set: prev.current_set ? {
                        ...prev.current_set,
                        home_score: homeScore,
                        away_score: awayScore,
                    } : undefined,
                }));
            }
        },
        onSetUpdate: (matchId, setData) => {
            if (matchId === initialMatch.id) {
                // Actualizar la lista de sets
                fetchMatchData();
            }
        },
    });

    // Obtener datos del partido
    const fetchMatchData = async () => {
        try {
            const response = await fetch(`/api/v1/matches/${initialMatch.id}/realtime`);
            const result = await response.json();
            if (result.success) {
                setMatchData(result.data);
            }
        } catch (error) {
            console.error('Error fetching match data:', error);
        }
    };

    // Calcular tiempo transcurrido
    useEffect(() => {
        if (matchData.match.status === 'in_progress' && matchData.match.started_at) {
            const interval = setInterval(() => {
                const startTime = new Date(matchData.match.started_at!);
                const now = new Date();
                const diff = Math.floor((now.getTime() - startTime.getTime()) / 1000);
                const minutes = Math.floor(diff / 60);
                const seconds = diff % 60;
                setTimeElapsed(`${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`);
            }, 1000);

            return () => clearInterval(interval);
        }
    }, [matchData.match.status, matchData.match.started_at]);

    // Cargar datos iniciales
    useEffect(() => {
        fetchMatchData();
    }, [initialMatch.id]);

    const getStatusColor = (status: string) => {
        switch (status) {
            case 'in_progress':
                return 'bg-red-500 text-white';
            case 'finished':
                return 'bg-gray-500 text-white';
            case 'scheduled':
                return 'bg-blue-500 text-white';
            default:
                return 'bg-gray-400 text-white';
        }
    };

    const getStatusText = (status: string) => {
        switch (status) {
            case 'in_progress':
                return 'EN VIVO';
            case 'finished':
                return 'FINALIZADO';
            case 'scheduled':
                return 'PROGRAMADO';
            default:
                return status.toUpperCase();
        }
    };

    return (
        <div className="bg-gradient-to-br from-slate-800 to-slate-700 rounded-2xl shadow-2xl border border-slate-600 overflow-hidden">
            {/* Header */}
            <div className="bg-gradient-to-r from-blue-600 to-purple-600 p-4">
                <div className="flex items-center justify-between">
                    <div className="flex items-center space-x-3">
                        <div className={`px-3 py-1 rounded-full text-xs font-bold ${getStatusColor(matchData.match.status)}`}>
                            {getStatusText(matchData.match.status)}
                        </div>
                        {matchData.match.status === 'in_progress' && (
                            <div className="flex items-center text-white text-sm">
                                <ClockIcon className="w-4 h-4 mr-1" />
                                {timeElapsed}
                            </div>
                        )}
                    </div>
                    {matchData.current_set && (
                        <div className="text-white text-sm font-semibold">
                            Set {matchData.current_set.set_number}
                        </div>
                    )}
                </div>
            </div>

            {/* Teams and Score */}
            <div className="p-6">
                <div className="flex items-center justify-between mb-4">
                    {/* Home Team */}
                    <div className="flex-1 text-center">
                        <div className="text-white font-bold text-lg mb-1">
                            {matchData.match.home_team?.name || 'Equipo Local'}
                        </div>
                        <div className="text-4xl font-black text-yellow-400">
                            {matchData.match.home_sets || 0}
                        </div>
                        <div className="text-gray-400 text-sm">Sets</div>
                    </div>

                    {/* VS */}
                    <div className="px-4">
                        <div className="text-gray-400 text-2xl font-bold">VS</div>
                    </div>

                    {/* Away Team */}
                    <div className="flex-1 text-center">
                        <div className="text-white font-bold text-lg mb-1">
                            {matchData.match.away_team?.name || 'Equipo Visitante'}
                        </div>
                        <div className="text-4xl font-black text-yellow-400">
                            {matchData.match.away_sets || 0}
                        </div>
                        <div className="text-gray-400 text-sm">Sets</div>
                    </div>
                </div>

                {/* Current Set Score */}
                {matchData.current_set && (
                    <div className="bg-slate-600/50 rounded-lg p-4 mb-4">
                        <div className="text-center text-gray-300 text-sm mb-2">
                            Set {matchData.current_set.set_number} - Marcador Actual
                        </div>
                        <div className="flex items-center justify-center space-x-8">
                            <div className="text-center">
                                <div className="text-2xl font-bold text-white">
                                    {matchData.current_set.home_score}
                                </div>
                            </div>
                            <div className="text-gray-400">-</div>
                            <div className="text-center">
                                <div className="text-2xl font-bold text-white">
                                    {matchData.current_set.away_score}
                                </div>
                            </div>
                        </div>
                    </div>
                )}

                {/* Sets History */}
                {matchData.sets.length > 0 && (
                    <div className="space-y-2">
                        <div className="text-gray-300 text-sm font-semibold mb-2">Historial de Sets</div>
                        <div className="grid grid-cols-1 gap-2">
                            {matchData.sets
                                .filter(set => set.status === 'completed')
                                .map((set) => (
                                <div key={set.id} className="flex items-center justify-between bg-slate-600/30 rounded-lg p-3">
                                    <div className="text-gray-300 text-sm">
                                        Set {set.set_number}
                                    </div>
                                    <div className="flex items-center space-x-4">
                                        <div className={`text-sm font-semibold ${
                                            set.home_score > set.away_score ? 'text-green-400' : 'text-gray-400'
                                        }`}>
                                            {set.home_score}
                                        </div>
                                        <div className="text-gray-500">-</div>
                                        <div className={`text-sm font-semibold ${
                                            set.away_score > set.home_score ? 'text-green-400' : 'text-gray-400'
                                        }`}>
                                            {set.away_score}
                                        </div>
                                    </div>
                                </div>
                            ))}
                        </div>
                    </div>
                )}

                {/* Match Info */}
                <div className="mt-4 pt-4 border-t border-slate-600">
                    <div className="flex items-center justify-between text-sm text-gray-400">
                        <div className="flex items-center">
                            <TrophyIcon className="w-4 h-4 mr-1" />
                            {matchData.match.tournament?.name || 'Torneo'}
                        </div>
                        {matchData.match.venue && (
                            <div className="flex items-center">
                                <UsersIcon className="w-4 h-4 mr-1" />
                                {matchData.match.venue}
                            </div>
                        )}
                    </div>
                </div>
            </div>
        </div>
    );
}