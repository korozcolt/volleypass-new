import React from 'react';
import { Head, Link } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { User, Match } from '@/types/global';
import { PlayIcon, ClockIcon, CheckCircleIcon, XCircleIcon, CalendarIcon, MapPinIcon } from '@heroicons/react/24/outline';

interface MatchesProps {
    user: User;
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
            live: { color: 'bg-red-100 text-red-800', text: 'En vivo' },
            scheduled: { color: 'bg-blue-100 text-blue-800', text: 'Programado' },
            finished: { color: 'bg-green-100 text-green-800', text: 'Finalizado' },
            cancelled: { color: 'bg-gray-100 text-gray-800', text: 'Cancelado' }
        };
        
        const config = statusConfig[status as keyof typeof statusConfig] || statusConfig.scheduled;
        
        return (
            <span className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${config.color}`}>
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
        <AppLayout title="Partidos" user={user}>
            <Head title="Partidos" />
            
            <div className="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
                {/* Header */}
                <div className="flex justify-between items-center mb-8">
                    <div>
                        <h1 className="text-3xl font-bold text-gray-900">Partidos</h1>
                        <p className="mt-2 text-gray-600">Consulta los partidos programados y resultados.</p>
                    </div>
                </div>

                {/* Filters */}
                <div className="bg-white shadow rounded-lg mb-6">
                    <div className="px-4 py-5 sm:p-6">
                        <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <label htmlFor="status" className="block text-sm font-medium text-gray-700">
                                    Estado
                                </label>
                                <select
                                    id="status"
                                    className="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
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
                                <label htmlFor="date" className="block text-sm font-medium text-gray-700">
                                    Fecha
                                </label>
                                <input
                                    type="date"
                                    id="date"
                                    className="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                    value={dateFilter}
                                    onChange={(e) => setDateFilter(e.target.value)}
                                />
                            </div>
                        </div>
                    </div>
                </div>

                {/* Matches List */}
                {filteredMatches.length > 0 ? (
                    <div className="bg-white shadow overflow-hidden sm:rounded-md">
                        <ul className="divide-y divide-gray-200">
                            {filteredMatches.map((match) => {
                                const dateTime = formatDateTime(match.scheduled_at);
                                const result = getMatchResult(match);
                                
                                return (
                                    <li key={match.id}>
                                        <Link
                                            href={`/matches/${match.id}`}
                                            className="block hover:bg-gray-50 px-4 py-4 sm:px-6"
                                        >
                                            <div className="flex items-center justify-between">
                                                <div className="flex items-center">
                                                    <div className="flex-shrink-0">
                                                        {getStatusIcon(match.status)}
                                                    </div>
                                                    <div className="ml-4 flex-1">
                                                        <div className="flex items-center justify-between">
                                                            <div className="flex items-center space-x-4">
                                                                {/* Teams */}
                                                                <div className="flex items-center space-x-2">
                                                                    <span className="text-sm font-medium text-gray-900">
                                                                        {match.home_team?.name || 'Equipo Local'}
                                                                    </span>
                                                                    <span className="text-gray-500">vs</span>
                                                                    <span className="text-sm font-medium text-gray-900">
                                                                        {match.away_team?.name || 'Equipo Visitante'}
                                                                    </span>
                                                                </div>
                                                                
                                                                {/* Result */}
                                                                {result && (
                                                                    <div className="text-lg font-bold text-gray-900">
                                                                        {result}
                                                                    </div>
                                                                )}
                                                            </div>
                                                            
                                                            <div className="flex items-center space-x-4">
                                                                {/* Date and Time */}
                                                                <div className="text-sm text-gray-500">
                                                                    <div className="flex items-center">
                                                                        <CalendarIcon className="h-4 w-4 mr-1" />
                                                                        {dateTime.date} - {dateTime.time}
                                                                    </div>
                                                                </div>
                                                                
                                                                {/* Status Badge */}
                                                                {getStatusBadge(match.status)}
                                                            </div>
                                                        </div>
                                                        
                                                        <div className="mt-2 flex items-center text-sm text-gray-500">
                                                            {/* Tournament */}
                                                            {match.tournament && (
                                                                <div className="flex items-center mr-4">
                                                                    <MapPinIcon className="h-4 w-4 mr-1" />
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
                                                            <div className="mt-2">
                                                                <div className="flex items-center space-x-2">
                                                                    <span className="text-xs text-gray-500">Sets:</span>
                                                                    {match.sets.map((set) => (
                                                                        <span key={set.id} className="text-xs bg-gray-100 px-2 py-1 rounded">
                                                                            {set.home_score}-{set.away_score}
                                                                        </span>
                                                                    ))}
                                                                </div>
                                                            </div>
                                                        )}
                                                    </div>
                                                </div>
                                            </div>
                                        </Link>
                                    </li>
                                );
                            })}
                        </ul>
                    </div>
                ) : (
                    <div className="text-center py-12">
                        <PlayIcon className="mx-auto h-12 w-12 text-gray-400" />
                        <h3 className="mt-2 text-sm font-medium text-gray-900">No hay partidos</h3>
                        <p className="mt-1 text-sm text-gray-500">
                            {statusFilter !== 'all' || dateFilter
                                ? 'No se encontraron partidos con los filtros aplicados.'
                                : 'No hay partidos programados en este momento.'
                            }
                        </p>
                    </div>
                )}
            </div>
        </AppLayout>
    );
}