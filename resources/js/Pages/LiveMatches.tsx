import { useState } from 'react';
import { Head } from '@inertiajs/react';
import { PageProps, Match } from '@/types/global';
import AppLayout from '@/Layouts/AppLayout';
import LiveMatchCard from '@/Components/LiveMatchCard';
import { useRealTimeUpdates } from '@/hooks/useRealTimeUpdates';
import { PlayIcon, ClockIcon, CheckCircleIcon } from '@heroicons/react/24/outline';
import { MatchSkeleton } from '@/Components/Skeleton';

interface LiveMatchesProps extends PageProps {
    matches: Match[];
}

export default function LiveMatches({ auth, matches: initialMatches }: LiveMatchesProps) {
    const [matches, setMatches] = useState<Match[]>(initialMatches);
    const [filter, setFilter] = useState<'all' | 'live' | 'upcoming' | 'finished'>('all');

    // Hook para actualizaciones en tiempo real
    useRealTimeUpdates({
        onMatchUpdate: (updatedMatch: Match) => {
            setMatches(prev => prev.map(match => 
                match.id === updatedMatch.id ? updatedMatch : match
            ));
        },
        onMatchScoreUpdate: (matchId: number, homeScore: number, awayScore: number) => {
            setMatches(prev => prev.map(match => 
                match.id === matchId ? { ...match, home_score: homeScore, away_score: awayScore } : match
            ));
        }
    });

    const filteredMatches = matches.filter(match => {
        if (filter === 'all') return true;
        return match.status === filter;
    });

    const liveMatchesCount = matches.filter(match => match.status === 'live').length;

    return (
        <AppLayout user={auth.user}>
            <Head title="Partidos en Vivo" />
            <div className="min-h-screen bg-gradient-to-br from-slate-900 via-blue-900 to-slate-800">
            
            {/* Header */}
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
                        <div className="w-32 h-32 rounded-full overflow-hidden border-4 border-white shadow-2xl bg-gradient-to-br from-red-500 to-yellow-500 flex items-center justify-center">
                            <PlayIcon className="w-16 h-16 text-white" />
                        </div>
                        <div className="text-white pb-2">
                            <h1 className="text-4xl font-black mb-2 flex items-center space-x-3">
                                <span>Partidos en Vivo</span>
                                <PlayIcon className="w-8 h-8 text-red-400" />
                            </h1>
                            <p className="text-xl font-semibold mb-1 text-yellow-200">
                                {liveMatchesCount > 0 ? (
                                    <span className="flex items-center">
                                        <span className="w-3 h-3 bg-red-500 rounded-full mr-2 animate-pulse"></span>
                                        {liveMatchesCount} partido{liveMatchesCount !== 1 ? 's' : ''} en vivo
                                    </span>
                                ) : (
                                    'No hay partidos en vivo en este momento'
                                )}
                            </p>
                            <p className="text-lg text-gray-100 flex items-center space-x-2">
                                <span className="text-2xl">🏐</span>
                                <span>Liga de Voleibol Sucre</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div className="container mx-auto px-4 pb-12">
                {/* Filtros */}
                <div className="mb-8">
                    <div className="flex flex-wrap gap-2">
                        {[
                            { key: 'all', label: 'Todos', icon: <CheckCircleIcon className="w-5 h-5" /> },
                            { key: 'live', label: 'En Vivo', icon: <PlayIcon className="w-5 h-5" /> },
                            { key: 'upcoming', label: 'Próximos', icon: <ClockIcon className="w-5 h-5" /> },
                            { key: 'finished', label: 'Finalizados', icon: <CheckCircleIcon className="w-5 h-5" /> }
                        ].map(({ key, label, icon }) => (
                            <button
                                key={key}
                                onClick={() => setFilter(key as any)}
                                className={`flex items-center space-x-2 px-6 py-3 rounded-lg font-bold transition-all duration-200 ${
                                    filter === key
                                        ? 'bg-gradient-to-r from-yellow-400 to-yellow-500 text-black shadow-lg'
                                        : 'bg-slate-800 text-white hover:bg-slate-700'
                                }`}
                            >
                                {icon}
                                <span>{label}</span>
                                {key === 'live' && liveMatchesCount > 0 && (
                                    <span className="bg-red-500 text-white text-xs px-2 py-1 rounded-full font-bold">
                                        {liveMatchesCount}
                                    </span>
                                )}
                            </button>
                        ))}
                    </div>
                </div>

                {/* Matches Grid */}
                {filteredMatches.length > 0 ? (
                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        {filteredMatches.map((match) => (
                            <div key={match.id} className="bg-gradient-to-br from-slate-800 to-slate-700 rounded-2xl p-6 shadow-2xl border border-slate-600">
                                <LiveMatchCard match={match} />
                            </div>
                        ))}
                    </div>
                ) : (
                    <div className="bg-gradient-to-br from-slate-800 to-slate-700 rounded-2xl p-12 shadow-2xl border border-slate-600">
                        <div className="text-center">
                            <div className="text-gray-400 text-6xl mb-4">⚐</div>
                            <h3 className="text-lg font-medium text-white mb-2">
                                No hay partidos {filter === 'all' ? '' : filter === 'live' ? 'en vivo' : filter === 'upcoming' ? 'próximos' : 'finalizados'}
                            </h3>
                            <p className="text-gray-100">
                                {filter === 'live' 
                                    ? 'No hay partidos en vivo en este momento. ¡Vuelve pronto!'
                                    : filter === 'upcoming'
                                    ? 'No hay partidos programados próximamente.'
                                    : filter === 'finished'
                                    ? 'No hay partidos finalizados para mostrar.'
                                    : 'No hay partidos disponibles en este momento.'
                                }
                            </p>
                        </div>
                    </div>
                )}

                {/* Auto-refresh indicator */}
                {liveMatchesCount > 0 && (
                    <div className="fixed bottom-4 right-4 bg-gradient-to-r from-red-600 to-red-700 text-white px-6 py-3 rounded-xl shadow-2xl border border-red-500">
                        <div className="flex items-center space-x-2">
                            <div className="w-3 h-3 bg-white rounded-full animate-pulse"></div>
                            <span className="text-sm font-bold">Actualizando en tiempo real</span>
                        </div>
                    </div>
                )}
            </div>
            </div>
        </AppLayout>
    );
}