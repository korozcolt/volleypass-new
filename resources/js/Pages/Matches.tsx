import React from 'react';
import { Link } from '@inertiajs/react';
import { User, Match } from '@/types/global';
import { PlayIcon, ClockIcon, CheckCircleIcon, XCircleIcon, CalendarIcon, MapPinIcon, TrophyIcon } from '@heroicons/react/24/outline';
import MainLayout from '@/Layouts/MainLayout';

interface MatchesProps {
    user?: User;
    matches: Match[];
}

export default function Matches({ user, matches }: MatchesProps) {
    const [statusFilter, setStatusFilter] = React.useState('all');
    const [dateFilter, setDateFilter] = React.useState('');

    const filteredMatches = matches.filter(match => {
        const matchesStatus = statusFilter === 'all' || match.status === statusFilter;
        const matchesDate = !dateFilter || match.scheduled_at.startsWith(dateFilter);
        return matchesStatus && matchesDate;
    });

    const getStatusIcon = (status: string) => {
        switch (status) {
            case 'live':
                return <PlayIcon className="h-5 w-5 text-red-500" />;
            case 'scheduled':
                return <ClockIcon className="h-5 w-5 text-blue-500" />;
            case 'finished':
                return <CheckCircleIcon className="h-5 w-5 text-green-500" />;
            case 'cancelled':
                return <XCircleIcon className="h-5 w-5 text-gray-500" />;
            default:
                return <ClockIcon className="h-5 w-5 text-gray-500" />;
        }
    };

    const getStatusBadge = (status: string) => {
        const statusConfig = {
            live: { color: 'bg-red-600 text-white', text: 'En vivo' },
            scheduled: { color: 'bg-blue-600 text-white', text: 'Programado' },
            finished: { color: 'bg-green-600 text-white', text: 'Finalizado' },
            cancelled: { color: 'bg-gray-600 text-white', text: 'Cancelado' }
        };

        const config = statusConfig[status as keyof typeof statusConfig] || statusConfig.scheduled;

        return (
            <span className={`inline-flex items-center px-3 py-1 rounded-full text-xs font-bold ${config.color}`}>
                {getStatusIcon(status)}
                <span className="ml-1">{config.text}</span>
            </span>
        );
    };

    const formatDateTime = (dateString: string) => {
        const date = new Date(dateString);
        return {
            date: date.toLocaleDateString('es-ES', {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            }),
            time: date.toLocaleTimeString('es-ES', {
                hour: '2-digit',
                minute: '2-digit'
            })
        };
    };

    const getMatchResult = (match: Match) => {
        if (match.status === 'finished' && match.home_score !== undefined && match.away_score !== undefined) {
            return `${match.home_score} - ${match.away_score}`;
        }
        return null;
    };

    return (
        <MainLayout title="Partidos" user={user} currentRoute="/partidos">
            <div className="container mx-auto py-8 px-4">
                    {/* Page Header */}
                    <div className="flex justify-between items-center mb-8">
                        <div>
                            <h1 className="text-4xl font-black text-white mb-2 flex items-center space-x-3">
                                <TrophyIcon className="w-10 h-10 text-yellow-400" />
                                <span>Partidos</span>
                            </h1>
                            <p className="text-xl text-white">Consulta los partidos programados y resultados.</p>
                        </div>
                    </div>

                    {/* Filters */}
                    <div className="bg-gradient-to-br from-slate-800 to-slate-700 rounded-2xl shadow-2xl border border-slate-600 mb-8">
                        <div className="p-6">
                            <div className="grid grid-cols-1 gap-6 sm:grid-cols-2">
                                <div>
                                    <label htmlFor="status" className="block text-sm font-bold text-white mb-2">
                                        Estado
                                    </label>
                                    <select
                                        id="status"
                                        className="block w-full px-4 py-3 bg-slate-700 border border-slate-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-transparent transition-all duration-200"
                                        value={statusFilter}
                                        onChange={(e) => setStatusFilter(e.target.value)}
                                    >
                                        <option value="all">Todos los estados</option>
                                        <option value="live">En vivo</option>
                                        <option value="scheduled">Programados</option>
                                        <option value="finished">Finalizados</option>
                                        <option value="cancelled">Cancelados</option>
                                    </select>
                                </div>

                                <div>
                                    <label htmlFor="date" className="block text-sm font-bold text-white mb-2">
                                        Fecha
                                    </label>
                                    <input
                                        type="date"
                                        id="date"
                                        className="block w-full px-4 py-3 bg-slate-700 border border-slate-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-transparent transition-all duration-200"
                                        value={dateFilter}
                                        onChange={(e) => setDateFilter(e.target.value)}
                                    />
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Matches Grid */}
                    {filteredMatches.length > 0 ? (
                        <div className="grid grid-cols-1 gap-6">
                            {filteredMatches.map((match) => {
                                const dateTime = formatDateTime(match.scheduled_at);
                                const result = getMatchResult(match);

                                return (
                                    <div key={match.id} className="bg-gradient-to-br from-slate-800 to-slate-700 rounded-2xl shadow-2xl border border-slate-600 hover:shadow-3xl transition-all duration-300 transform hover:scale-[1.02]">
                                        <Link
                                            href={`/matches/${match.id}`}
                                            className="block p-6"
                                        >
                                            <div className="flex items-center justify-between">
                                                <div className="flex items-center space-x-6">
                                                    <div className="flex-shrink-0">
                                                        {getStatusIcon(match.status)}
                                                    </div>

                                                    {/* Teams */}
                                                    <div className="flex items-center space-x-4">
                                                        <div className="text-center">
                                                            <div className="text-lg font-bold text-white">
                                                                {match.home_team?.name || 'Equipo Local'}
                                                            </div>
                                                            <div className="text-sm text-gray-300">Local</div>
                                                        </div>

                                                        <div className="text-center px-4">
                                                            {result ? (
                                                                <div className="text-2xl font-mono font-black text-yellow-400">
                                                                    {result}
                                                                </div>
                                                            ) : (
                                                                <div className="text-lg text-gray-400">vs</div>
                                                            )}
                                                        </div>

                                                        <div className="text-center">
                                                            <div className="text-lg font-bold text-white">
                                                                {match.away_team?.name || 'Equipo Visitante'}
                                                            </div>
                                                            <div className="text-sm text-gray-300">Visitante</div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div className="flex items-center space-x-6">
                                                    {/* Date and Time */}
                                                    <div className="text-right">
                                                        <div className="flex items-center text-white">
                                                            <CalendarIcon className="h-4 w-4 mr-2 text-yellow-400" />
                                                            <span className="font-bold">{dateTime.date}</span>
                                                        </div>
                                                        <div className="text-sm text-gray-300 mt-1">
                                                            {dateTime.time}
                                                        </div>
                                                    </div>

                                                    {/* Status Badge */}
                                                    {getStatusBadge(match.status)}
                                                </div>
                                            </div>

                                            {/* Additional Info */}
                                            <div className="mt-4 flex items-center justify-between text-sm text-gray-300">
                                                <div className="flex items-center space-x-4">
                                                    {/* Tournament */}
                                                    {match.tournament && (
                                                        <div className="flex items-center">
                                                            <MapPinIcon className="h-4 w-4 mr-1 text-blue-400" />
                                                            <span>{match.tournament.name}</span>
                                                        </div>
                                                    )}

                                                    {/* Referee */}
                                                    {match.referee && (
                                                        <div className="flex items-center">
                                                            <span>Árbitro: {match.referee.license_number}</span>
                                                        </div>
                                                    )}
                                                </div>

                                                {/* Sets */}
                                                {match.sets && match.sets.length > 0 && (
                                                    <div className="flex items-center space-x-2">
                                                        <span className="text-xs text-gray-400">Sets:</span>
                                                        {match.sets.map((set) => (
                                                            <span key={set.id} className="text-xs bg-slate-600 px-2 py-1 rounded text-white">
                                                                {set.home_score}-{set.away_score}
                                                            </span>
                                                        ))}
                                                    </div>
                                                )}
                                            </div>
                                        </Link>
                                    </div>
                                );
                            })}
                        </div>
                    ) : (
                        <div className="bg-gradient-to-br from-slate-800 to-slate-700 rounded-2xl p-12 shadow-2xl border border-slate-600">
                            <div className="text-center">
                                <PlayIcon className="mx-auto h-12 w-12 text-gray-400" />
                                <h3 className="mt-2 text-lg font-medium text-white">No hay partidos</h3>
                                <p className="mt-1 text-sm text-white">
                                    {statusFilter !== 'all' || dateFilter
                                        ? 'No se encontraron partidos con los filtros aplicados.'
                                        : 'No hay partidos programados en este momento.'
                                    }
                                </p>
                            </div>
                        </div>
                    )}
            </div>
        </MainLayout>
    );
}