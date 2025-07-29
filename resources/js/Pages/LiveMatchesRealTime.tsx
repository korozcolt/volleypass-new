import React, { useState, useEffect } from 'react';
import { Head } from '@inertiajs/react';
import { PageProps, Match } from '@/types/global';
import MainLayout from '@/Layouts/MainLayout';
import LiveMatchCard from '@/Components/LiveMatch/LiveMatchCard';
import { useLiveMatchesUpdates } from '@/hooks/useMatchRealTimeUpdates';
import { PlayIcon, ClockIcon, TrophyIcon, EyeIcon } from '@heroicons/react/24/outline';

interface LiveMatchesRealTimeProps extends PageProps {
    matches: Match[];
    stats: {
        total_matches: number;
        live_matches: number;
        upcoming_matches: number;
        finished_matches: number;
    };
}

export default function LiveMatchesRealTime({ auth, matches: initialMatches, stats }: LiveMatchesRealTimeProps) {
    const [matches, setMatches] = useState<Match[]>(initialMatches);
    const [filter, setFilter] = useState<'all' | 'in_progress' | 'scheduled' | 'finished'>('all');
    const [isLoading, setIsLoading] = useState(false);

    // Hook para actualizaciones en tiempo real
    useLiveMatchesUpdates(
        (updatedMatch: Match) => {
            setMatches(prev => prev.map(match =>
                match.id === updatedMatch.id ? updatedMatch : match
            ));
        },
        (newLiveMatch: Match) => {
            setMatches(prev => {
                const exists = prev.find(match => match.id === newLiveMatch.id);
                if (exists) {
                    return prev.map(match =>
                        match.id === newLiveMatch.id ? newLiveMatch : match
                    );
                }
                return [newLiveMatch, ...prev];
            });
        }
    );

    // Refrescar datos
    const refreshMatches = async () => {
        setIsLoading(true);
        try {
            const response = await fetch('/api/v1/matches/live');
            const result = await response.json();
            if (result.success) {
                setMatches(result.data);
            }
        } catch (error) {
            console.error('Error refreshing matches:', error);
        } finally {
            setIsLoading(false);
        }
    };

    // Filtrar partidos
    const filteredMatches = matches.filter(match => {
        if (filter === 'all') return true;
        return match.status === filter;
    });

    const liveMatchesCount = matches.filter(match => match.status === 'in_progress').length;
    const upcomingMatchesCount = matches.filter(match => match.status === 'scheduled').length;
    const finishedMatchesCount = matches.filter(match => match.status === 'finished').length;

    return (
        <MainLayout title="Partidos en Tiempo Real" user={auth?.user} currentRoute="/live-matches-realtime">
            <Head title="Partidos en Tiempo Real" />
            
            {/* Header */}
            <div className="relative mb-8">
                <div className="h-48 bg-gradient-to-r from-red-600 via-orange-500 to-yellow-500 relative overflow-hidden rounded-2xl">
                    <div className="absolute inset-0 bg-black/30"></div>
                    <div className="relative z-10 h-full flex items-center justify-center">
                        <div className="text-center">
                            <div className="flex items-center justify-center mb-4">
                                <PlayIcon className="w-16 h-16 text-white mr-4" />
                                <h1 className="text-5xl font-black text-white">
                                    PARTIDOS EN VIVO
                                </h1>
                            </div>
                            <p className="text-xl text-white/90 font-medium">
                                Sigue todos los partidos en tiempo real
                            </p>
                            {liveMatchesCount > 0 && (
                                <div className="mt-4 inline-flex items-center bg-red-500 text-white px-4 py-2 rounded-full font-bold">
                                    <div className="w-3 h-3 bg-white rounded-full mr-2 animate-pulse"></div>
                                    {liveMatchesCount} partido{liveMatchesCount !== 1 ? 's' : ''} en vivo
                                </div>
                            )}
                        </div>
                    </div>
                </div>
            </div>

            {/* Stats */}
            <div className="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div className="bg-gradient-to-br from-slate-800 to-slate-700 rounded-xl p-6 border border-slate-600">
                    <div className="flex items-center">
                        <TrophyIcon className="w-8 h-8 text-yellow-400 mr-3" />
                        <div>
                            <div className="text-2xl font-bold text-white">{matches.length}</div>
                            <div className="text-gray-400 text-sm">Total</div>
                        </div>
                    </div>
                </div>
                <div className="bg-gradient-to-br from-red-600 to-red-500 rounded-xl p-6">
                    <div className="flex items-center">
                        <PlayIcon className="w-8 h-8 text-white mr-3" />
                        <div>
                            <div className="text-2xl font-bold text-white">{liveMatchesCount}</div>
                            <div className="text-red-100 text-sm">En Vivo</div>
                        </div>
                    </div>
                </div>
                <div className="bg-gradient-to-br from-blue-600 to-blue-500 rounded-xl p-6">
                    <div className="flex items-center">
                        <ClockIcon className="w-8 h-8 text-white mr-3" />
                        <div>
                            <div className="text-2xl font-bold text-white">{upcomingMatchesCount}</div>
                            <div className="text-blue-100 text-sm">Próximos</div>
                        </div>
                    </div>
                </div>
                <div className="bg-gradient-to-br from-gray-600 to-gray-500 rounded-xl p-6">
                    <div className="flex items-center">
                        <EyeIcon className="w-8 h-8 text-white mr-3" />
                        <div>
                            <div className="text-2xl font-bold text-white">{finishedMatchesCount}</div>
                            <div className="text-gray-100 text-sm">Finalizados</div>
                        </div>
                    </div>
                </div>
            </div>

            {/* Filters */}
            <div className="flex flex-wrap gap-4 mb-8">
                <button
                    onClick={() => setFilter('all')}
                    className={`px-6 py-3 rounded-lg font-semibold transition-all ${
                        filter === 'all'
                            ? 'bg-blue-600 text-white shadow-lg'
                            : 'bg-slate-700 text-gray-300 hover:bg-slate-600'
                    }`}
                >
                    Todos ({matches.length})
                </button>
                <button
                    onClick={() => setFilter('in_progress')}
                    className={`px-6 py-3 rounded-lg font-semibold transition-all ${
                        filter === 'in_progress'
                            ? 'bg-red-600 text-white shadow-lg'
                            : 'bg-slate-700 text-gray-300 hover:bg-slate-600'
                    }`}
                >
                    En Vivo ({liveMatchesCount})
                </button>
                <button
                    onClick={() => setFilter('scheduled')}
                    className={`px-6 py-3 rounded-lg font-semibold transition-all ${
                        filter === 'scheduled'
                            ? 'bg-blue-600 text-white shadow-lg'
                            : 'bg-slate-700 text-gray-300 hover:bg-slate-600'
                    }`}
                >
                    Próximos ({upcomingMatchesCount})
                </button>
                <button
                    onClick={() => setFilter('finished')}
                    className={`px-6 py-3 rounded-lg font-semibold transition-all ${
                        filter === 'finished'
                            ? 'bg-gray-600 text-white shadow-lg'
                            : 'bg-slate-700 text-gray-300 hover:bg-slate-600'
                    }`}
                >
                    Finalizados ({finishedMatchesCount})
                </button>
                <button
                    onClick={refreshMatches}
                    disabled={isLoading}
                    className="px-6 py-3 rounded-lg font-semibold bg-green-600 text-white hover:bg-green-700 transition-all disabled:opacity-50"
                >
                    {isLoading ? 'Actualizando...' : 'Actualizar'}
                </button>
            </div>

            {/* Matches Grid */}
            {filteredMatches.length > 0 ? (
                <div className="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-8">
                    {filteredMatches.map((match) => (
                        <LiveMatchCard
                            key={match.id}
                            match={match}
                            onMatchUpdate={(updatedMatch) => {
                                setMatches(prev => prev.map(m =>
                                    m.id === updatedMatch.id ? updatedMatch : m
                                ));
                            }}
                        />
                    ))}
                </div>
            ) : (
                <div className="text-center py-16">
                    <div className="bg-slate-800 rounded-2xl p-12 border border-slate-600">
                        <TrophyIcon className="w-16 h-16 text-gray-400 mx-auto mb-4" />
                        <h3 className="text-2xl font-bold text-white mb-2">
                            No hay partidos {filter === 'all' ? '' : getFilterText(filter)}
                        </h3>
                        <p className="text-gray-400">
                            {filter === 'in_progress' && 'No hay partidos en vivo en este momento.'}
                            {filter === 'scheduled' && 'No hay partidos programados.'}
                            {filter === 'finished' && 'No hay partidos finalizados.'}
                            {filter === 'all' && 'No hay partidos disponibles.'}
                        </p>
                    </div>
                </div>
            )}
        </MainLayout>
    );
}

function getFilterText(filter: string): string {
    switch (filter) {
        case 'in_progress':
            return 'en vivo';
        case 'scheduled':
            return 'programados';
        case 'finished':
            return 'finalizados';
        default:
            return '';
    }
}