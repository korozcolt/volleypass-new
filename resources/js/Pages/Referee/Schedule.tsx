import React from 'react';
import { Head } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { CalendarIcon, ClockIcon, MapPinIcon } from '@heroicons/react/24/outline';

interface Team {
    id: number;
    name: string;
    logo?: string;
}

interface Tournament {
    id: number;
    name: string;
    category: string;
}

interface Match {
    id: number;
    home_team: Team;
    away_team: Team;
    tournament: Tournament;
    scheduled_at: string;
    status: string;
    venue?: string;
}

interface Props {
    matches: Match[];
}

export default function Schedule({ matches }: Props) {
    const getStatusBadge = (status: string) => {
        const statusConfig = {
            scheduled: { label: 'Programado', color: 'bg-blue-100 text-blue-800' },
            in_progress: { label: 'En Progreso', color: 'bg-green-100 text-green-800' },
            finished: { label: 'Finalizado', color: 'bg-gray-100 text-gray-800' },
            cancelled: { label: 'Cancelado', color: 'bg-red-100 text-red-800' },
        };

        const config = statusConfig[status as keyof typeof statusConfig] || statusConfig.scheduled;
        
        return (
            <span className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${config.color}`}>
                {config.label}
            </span>
        );
    };

    const formatDate = (dateString: string) => {
        const date = new Date(dateString);
        return date.toLocaleDateString('es-ES', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
    };

    const formatTime = (dateString: string) => {
        const date = new Date(dateString);
        return date.toLocaleTimeString('es-ES', {
            hour: '2-digit',
            minute: '2-digit'
        });
    };

    const groupMatchesByDate = (matches: Match[]) => {
        const grouped: { [key: string]: Match[] } = {};
        
        matches.forEach(match => {
            const date = new Date(match.scheduled_at).toISOString().split('T')[0];
            if (!grouped[date]) {
                grouped[date] = [];
            }
            grouped[date].push(match);
        });
        
        return grouped;
    };

    const groupedMatches = groupMatchesByDate(matches);

    return (
        <AppLayout>
            <Head title="Mi Calendario" />
            
            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div className="p-6 bg-white border-b border-gray-200">
                            <div className="flex items-center justify-between mb-6">
                                <div>
                                    <h1 className="text-2xl font-bold text-gray-900">Mi Calendario</h1>
                                    <p className="text-gray-600">Partidos asignados como árbitro</p>
                                </div>
                                <div className="flex items-center space-x-2 text-sm text-gray-500">
                                    <CalendarIcon className="h-5 w-5" />
                                    <span>{matches.length} partidos programados</span>
                                </div>
                            </div>

                            {Object.keys(groupedMatches).length === 0 ? (
                                <div className="text-center py-12">
                                    <CalendarIcon className="mx-auto h-12 w-12 text-gray-400" />
                                    <h3 className="mt-2 text-sm font-medium text-gray-900">No hay partidos programados</h3>
                                    <p className="mt-1 text-sm text-gray-500">
                                        No tienes partidos asignados en este momento.
                                    </p>
                                </div>
                            ) : (
                                <div className="space-y-8">
                                    {Object.entries(groupedMatches)
                                        .sort(([a], [b]) => new Date(a).getTime() - new Date(b).getTime())
                                        .map(([date, dayMatches]) => (
                                        <div key={date} className="space-y-4">
                                            <div className="flex items-center space-x-2">
                                                <h2 className="text-lg font-semibold text-gray-900">
                                                    {formatDate(date)}
                                                </h2>
                                                <span className="text-sm text-gray-500">({dayMatches.length} partidos)</span>
                                            </div>
                                            
                                            <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                                                {dayMatches
                                                    .sort((a, b) => new Date(a.scheduled_at).getTime() - new Date(b.scheduled_at).getTime())
                                                    .map((match) => (
                                                    <div key={match.id} className="bg-gray-50 rounded-lg p-4 hover:bg-gray-100 transition-colors">
                                                        <div className="flex items-center justify-between mb-3">
                                                            <div className="flex items-center space-x-2">
                                                                <ClockIcon className="h-4 w-4 text-gray-500" />
                                                                <span className="text-sm font-medium text-gray-900">
                                                                    {formatTime(match.scheduled_at)}
                                                                </span>
                                                            </div>
                                                            {getStatusBadge(match.status)}
                                                        </div>
                                                        
                                                        <div className="space-y-2">
                                                            <div className="flex items-center justify-between">
                                                                <span className="text-sm font-medium text-gray-900">
                                                                    {match.home_team.name}
                                                                </span>
                                                                <span className="text-xs text-gray-500">vs</span>
                                                                <span className="text-sm font-medium text-gray-900">
                                                                    {match.away_team.name}
                                                                </span>
                                                            </div>
                                                            
                                                            <div className="text-xs text-gray-600">
                                                                <div className="flex items-center space-x-1">
                                                                    <span>🏆</span>
                                                                    <span>{match.tournament.name}</span>
                                                                </div>
                                                                {match.venue && (
                                                                    <div className="flex items-center space-x-1 mt-1">
                                                                        <MapPinIcon className="h-3 w-3" />
                                                                        <span>{match.venue}</span>
                                                                    </div>
                                                                )}
                                                            </div>
                                                        </div>
                                                        
                                                        {match.status === 'scheduled' && (
                                                            <div className="mt-3 pt-3 border-t border-gray-200">
                                                                <a
                                                                    href={`/referee/match/${match.id}/control`}
                                                                    className="text-xs text-blue-600 hover:text-blue-800 font-medium"
                                                                >
                                                                    Panel de Control →
                                                                </a>
                                                            </div>
                                                        )}
                                                    </div>
                                                ))}
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            )}
                        </div>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}