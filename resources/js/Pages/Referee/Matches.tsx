import React, { useState } from 'react';
import { Head, Link } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { CalendarIcon, ClockIcon, TrophyIcon, PlayIcon, EyeIcon, FunnelIcon } from '@heroicons/react/24/outline';

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
    home_sets?: number;
    away_sets?: number;
}

interface Stats {
    total: number;
    scheduled: number;
    in_progress: number;
    finished: number;
}

interface Props {
    matches: Match[];
    currentStatus: string;
    stats: Stats;
}

export default function Matches({ matches, currentStatus, stats }: Props) {
    const [selectedStatus, setSelectedStatus] = useState(currentStatus);

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
            day: '2-digit',
            month: '2-digit',
            year: 'numeric'
        });
    };

    const formatTime = (dateString: string) => {
        const date = new Date(dateString);
        return date.toLocaleTimeString('es-ES', {
            hour: '2-digit',
            minute: '2-digit'
        });
    };

    const handleStatusFilter = (status: string) => {
        const url = new URL(window.location.href);
        if (status === 'all') {
            url.searchParams.delete('status');
        } else {
            url.searchParams.set('status', status);
        }
        window.location.href = url.toString();
    };

    const filteredMatches = selectedStatus === 'all' 
        ? matches 
        : matches.filter(match => match.status === selectedStatus);

    return (
        <AppLayout>
            <Head title="Mis Partidos" />
            
            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div className="p-6 bg-white border-b border-gray-200">
                            <div className="flex items-center justify-between mb-8">
                                <div>
                                    <h1 className="text-3xl font-bold text-gray-900">Mis Partidos</h1>
                                    <p className="text-gray-600 mt-2">Gestiona todos tus partidos asignados como árbitro</p>
                                </div>
                                <div className="flex items-center space-x-2 text-sm text-gray-500">
                                    <TrophyIcon className="h-5 w-5" />
                                    <span>{matches.length} partidos asignados</span>
                                </div>
                            </div>

                            {/* Estadísticas */}
                            <div className="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                                <div className="bg-blue-50 rounded-lg p-6">
                                    <div className="flex items-center">
                                        <TrophyIcon className="h-8 w-8 text-blue-600" />
                                        <div className="ml-4">
                                            <p className="text-sm font-medium text-blue-600">Total</p>
                                            <p className="text-2xl font-bold text-blue-900">{stats.total}</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div className="bg-yellow-50 rounded-lg p-6">
                                    <div className="flex items-center">
                                        <CalendarIcon className="h-8 w-8 text-yellow-600" />
                                        <div className="ml-4">
                                            <p className="text-sm font-medium text-yellow-600">Programados</p>
                                            <p className="text-2xl font-bold text-yellow-900">{stats.scheduled}</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div className="bg-green-50 rounded-lg p-6">
                                    <div className="flex items-center">
                                        <PlayIcon className="h-8 w-8 text-green-600" />
                                        <div className="ml-4">
                                            <p className="text-sm font-medium text-green-600">En Progreso</p>
                                            <p className="text-2xl font-bold text-green-900">{stats.in_progress}</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div className="bg-gray-50 rounded-lg p-6">
                                    <div className="flex items-center">
                                        <ClockIcon className="h-8 w-8 text-gray-600" />
                                        <div className="ml-4">
                                            <p className="text-sm font-medium text-gray-600">Finalizados</p>
                                            <p className="text-2xl font-bold text-gray-900">{stats.finished}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {/* Filtros */}
                            <div className="mb-6">
                                <div className="flex items-center space-x-4">
                                    <FunnelIcon className="h-5 w-5 text-gray-400" />
                                    <span className="text-sm font-medium text-gray-700">Filtrar por estado:</span>
                                    <div className="flex space-x-2">
                                        <button
                                            onClick={() => handleStatusFilter('all')}
                                            className={`px-3 py-1 rounded-full text-xs font-medium transition-colors ${
                                                currentStatus === 'all'
                                                    ? 'bg-blue-600 text-white'
                                                    : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                                            }`}
                                        >
                                            Todos
                                        </button>
                                        <button
                                            onClick={() => handleStatusFilter('scheduled')}
                                            className={`px-3 py-1 rounded-full text-xs font-medium transition-colors ${
                                                currentStatus === 'scheduled'
                                                    ? 'bg-yellow-600 text-white'
                                                    : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                                            }`}
                                        >
                                            Programados
                                        </button>
                                        <button
                                            onClick={() => handleStatusFilter('in_progress')}
                                            className={`px-3 py-1 rounded-full text-xs font-medium transition-colors ${
                                                currentStatus === 'in_progress'
                                                    ? 'bg-green-600 text-white'
                                                    : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                                            }`}
                                        >
                                            En Progreso
                                        </button>
                                        <button
                                            onClick={() => handleStatusFilter('finished')}
                                            className={`px-3 py-1 rounded-full text-xs font-medium transition-colors ${
                                                currentStatus === 'finished'
                                                    ? 'bg-gray-600 text-white'
                                                    : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                                            }`}
                                        >
                                            Finalizados
                                        </button>
                                    </div>
                                </div>
                            </div>

                            {/* Lista de Partidos */}
                            {matches.length > 0 ? (
                                <div className="space-y-4">
                                    {matches.map((match) => (
                                        <div key={match.id} className="bg-gray-50 rounded-lg p-6 border border-gray-200 hover:shadow-md transition-shadow">
                                            <div className="flex items-center justify-between mb-4">
                                                <div className="flex items-center space-x-4">
                                                    <div className="text-center">
                                                        <div className="text-lg font-bold text-gray-900">
                                                            {match.home_team.name}
                                                        </div>
                                                        <div className="text-xs text-gray-500">Local</div>
                                                    </div>
                                                    
                                                    <div className="text-center px-4">
                                                        <div className="text-2xl font-bold text-gray-600">VS</div>
                                                        {match.status === 'finished' && (
                                                            <div className="text-lg font-bold text-blue-600">
                                                                {match.home_sets || 0} - {match.away_sets || 0}
                                                            </div>
                                                        )}
                                                    </div>
                                                    
                                                    <div className="text-center">
                                                        <div className="text-lg font-bold text-gray-900">
                                                            {match.away_team.name}
                                                        </div>
                                                        <div className="text-xs text-gray-500">Visitante</div>
                                                    </div>
                                                </div>
                                                
                                                <div className="text-right">
                                                    {getStatusBadge(match.status)}
                                                </div>
                                            </div>
                                            
                                            <div className="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                                <div className="flex items-center text-sm text-gray-600">
                                                    <TrophyIcon className="h-4 w-4 mr-2" />
                                                    <span>{match.tournament.name}</span>
                                                </div>
                                                <div className="flex items-center text-sm text-gray-600">
                                                    <CalendarIcon className="h-4 w-4 mr-2" />
                                                    <span>{formatDate(match.scheduled_at)} - {formatTime(match.scheduled_at)}</span>
                                                </div>
                                                {match.venue && (
                                                    <div className="flex items-center text-sm text-gray-600">
                                                        <span className="mr-2">📍</span>
                                                        <span>{match.venue}</span>
                                                    </div>
                                                )}
                                            </div>
                                            
                                            <div className="flex space-x-3">
                                                {match.status === 'scheduled' && (
                                                    <a
                                                        href={`/referee/match/${match.id}/control`}
                                                        className="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 transition-colors"
                                                    >
                                                        <PlayIcon className="h-4 w-4 mr-2" />
                                                        Panel de Control
                                                    </a>
                                                )}
                                                {match.status === 'in_progress' && (
                                                    <a
                                                        href={`/referee/match/${match.id}/control`}
                                                        className="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700 transition-colors"
                                                    >
                                                        <PlayIcon className="h-4 w-4 mr-2" />
                                                        Controlar Partido
                                                    </a>
                                                )}
                                                <Link
                                                    href={route('referee.match.details', match.id)}
                                                    className="inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-md hover:bg-gray-700 transition-colors"
                                                >
                                                    <EyeIcon className="h-4 w-4 mr-2" />
                                                    Ver Detalles
                                                </Link>
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            ) : (
                                <div className="text-center py-12">
                                    <TrophyIcon className="mx-auto h-12 w-12 text-gray-400" />
                                    <h3 className="mt-2 text-sm font-medium text-gray-900">No hay partidos asignados</h3>
                                    <p className="mt-1 text-sm text-gray-500">
                                        {currentStatus === 'all' 
                                            ? 'Aún no tienes partidos asignados como árbitro.'
                                            : `No tienes partidos con estado "${currentStatus}".`
                                        }
                                    </p>
                                </div>
                            )}
                        </div>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}