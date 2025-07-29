import React from 'react';
import { Head } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { CalendarIcon, ClockIcon, TrophyIcon, PlayIcon, EyeIcon } from '@heroicons/react/24/outline';

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

interface Stats {
    total_matches: number;
    upcoming_matches: number;
    recent_matches: number;
    live_matches: number;
}

interface Props {
    upcomingMatches: Match[];
    recentMatches: Match[];
    stats: Stats;
}

export default function Dashboard({ upcomingMatches, recentMatches, stats }: Props) {
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

    return (
        <AppLayout>
            <Head title="Dashboard Árbitro" />
            
            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div className="p-6 bg-white border-b border-gray-200">
                            <div className="mb-8">
                                <h1 className="text-3xl font-bold text-gray-900">Dashboard del Árbitro</h1>
                                <p className="text-gray-600 mt-2">Gestiona tus partidos asignados</p>
                            </div>

                            {/* Estadísticas */}
                            <div className="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                                <div className="bg-blue-50 rounded-lg p-6">
                                    <div className="flex items-center">
                                        <TrophyIcon className="h-8 w-8 text-blue-600" />
                                        <div className="ml-4">
                                            <p className="text-sm font-medium text-blue-600">Total Partidos</p>
                                            <p className="text-2xl font-bold text-blue-900">{stats.total_matches}</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div className="bg-green-50 rounded-lg p-6">
                                    <div className="flex items-center">
                                        <CalendarIcon className="h-8 w-8 text-green-600" />
                                        <div className="ml-4">
                                            <p className="text-sm font-medium text-green-600">Próximos</p>
                                            <p className="text-2xl font-bold text-green-900">{stats.upcoming_matches}</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div className="bg-yellow-50 rounded-lg p-6">
                                    <div className="flex items-center">
                                        <PlayIcon className="h-8 w-8 text-yellow-600" />
                                        <div className="ml-4">
                                            <p className="text-sm font-medium text-yellow-600">En Vivo</p>
                                            <p className="text-2xl font-bold text-yellow-900">{stats.live_matches}</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div className="bg-purple-50 rounded-lg p-6">
                                    <div className="flex items-center">
                                        <ClockIcon className="h-8 w-8 text-purple-600" />
                                        <div className="ml-4">
                                            <p className="text-sm font-medium text-purple-600">Recientes</p>
                                            <p className="text-2xl font-bold text-purple-900">{stats.recent_matches}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {/* Acciones Rápidas */}
                            <div className="mb-8">
                                <h2 className="text-xl font-semibold text-gray-900 mb-4">Acciones Rápidas</h2>
                                <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <a
                                        href="/referee/schedule"
                                        className="bg-blue-600 text-white p-4 rounded-lg hover:bg-blue-700 transition-colors text-center"
                                    >
                                        <CalendarIcon className="h-6 w-6 mx-auto mb-2" />
                                        <span className="block font-medium">Ver Calendario</span>
                                    </a>
                                    <a
                                        href="/referee/matches"
                                        className="bg-green-600 text-white p-4 rounded-lg hover:bg-green-700 transition-colors text-center"
                                    >
                                        <TrophyIcon className="h-6 w-6 mx-auto mb-2" />
                                        <span className="block font-medium">Mis Partidos</span>
                                    </a>
                                    <a
                                        href="/referee/profile"
                                        className="bg-purple-600 text-white p-4 rounded-lg hover:bg-purple-700 transition-colors text-center"
                                    >
                                        <EyeIcon className="h-6 w-6 mx-auto mb-2" />
                                        <span className="block font-medium">Mi Perfil</span>
                                    </a>
                                </div>
                            </div>

                            <div className="grid grid-cols-1 lg:grid-cols-2 gap-8">
                                {/* Próximos Partidos */}
                                <div>
                                    <h2 className="text-xl font-semibold text-gray-900 mb-4">Próximos Partidos</h2>
                                    {upcomingMatches.length > 0 ? (
                                        <div className="space-y-4">
                                            {upcomingMatches.map((match) => (
                                                <div key={match.id} className="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                                    <div className="flex items-center justify-between mb-2">
                                                        <div className="flex items-center space-x-2">
                                                            <span className="text-sm font-medium text-gray-900">
                                                                {match.home_team.name}
                                                            </span>
                                                            <span className="text-xs text-gray-500">vs</span>
                                                            <span className="text-sm font-medium text-gray-900">
                                                                {match.away_team.name}
                                                            </span>
                                                        </div>
                                                        {getStatusBadge(match.status)}
                                                    </div>
                                                    
                                                    <div className="text-xs text-gray-600 mb-3">
                                                        <div className="flex items-center space-x-4">
                                                            <span>🏆 {match.tournament.name}</span>
                                                            <span>📅 {formatDate(match.scheduled_at)}</span>
                                                            <span>🕐 {formatTime(match.scheduled_at)}</span>
                                                        </div>
                                                        {match.venue && (
                                                            <div className="mt-1">📍 {match.venue}</div>
                                                        )}
                                                    </div>
                                                    
                                                    {match.status === 'scheduled' && (
                                                        <div className="flex space-x-2">
                                                            <a
                                                                href={`/referee/match/${match.id}/control`}
                                                                className="text-xs bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700 transition-colors"
                                                            >
                                                                Panel de Control
                                                            </a>
                                                            <a
                                                                href={`/referee/match/${match.id}/details`}
                                                                className="text-xs bg-gray-600 text-white px-3 py-1 rounded hover:bg-gray-700 transition-colors"
                                                            >
                                                                Ver Detalles
                                                            </a>
                                                        </div>
                                                    )}
                                                </div>
                                            ))}
                                        </div>
                                    ) : (
                                        <div className="text-center py-8">
                                            <CalendarIcon className="mx-auto h-12 w-12 text-gray-400" />
                                            <h3 className="mt-2 text-sm font-medium text-gray-900">No hay partidos próximos</h3>
                                            <p className="mt-1 text-sm text-gray-500">Los partidos asignados aparecerán aquí.</p>
                                        </div>
                                    )}
                                </div>

                                {/* Partidos Recientes */}
                                <div>
                                    <h2 className="text-xl font-semibold text-gray-900 mb-4">Partidos Recientes</h2>
                                    {recentMatches.length > 0 ? (
                                        <div className="space-y-4">
                                            {recentMatches.map((match) => (
                                                <div key={match.id} className="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                                    <div className="flex items-center justify-between mb-2">
                                                        <div className="flex items-center space-x-2">
                                                            <span className="text-sm font-medium text-gray-900">
                                                                {match.home_team.name}
                                                            </span>
                                                            <span className="text-xs text-gray-500">vs</span>
                                                            <span className="text-sm font-medium text-gray-900">
                                                                {match.away_team.name}
                                                            </span>
                                                        </div>
                                                        {getStatusBadge(match.status)}
                                                    </div>
                                                    
                                                    <div className="text-xs text-gray-600 mb-3">
                                                        <div className="flex items-center space-x-4">
                                                            <span>🏆 {match.tournament.name}</span>
                                                            <span>📅 {formatDate(match.scheduled_at)}</span>
                                                        </div>
                                                        {match.venue && (
                                                            <div className="mt-1">📍 {match.venue}</div>
                                                        )}
                                                    </div>
                                                    
                                                    <div className="flex space-x-2">
                                                        <a
                                                            href={`/referee/match/${match.id}/details`}
                                                            className="text-xs bg-gray-600 text-white px-3 py-1 rounded hover:bg-gray-700 transition-colors"
                                                        >
                                                            Ver Detalles
                                                        </a>
                                                    </div>
                                                </div>
                                            ))}
                                        </div>
                                    ) : (
                                        <div className="text-center py-8">
                                            <ClockIcon className="mx-auto h-12 w-12 text-gray-400" />
                                            <h3 className="mt-2 text-sm font-medium text-gray-900">No hay partidos recientes</h3>
                                            <p className="mt-1 text-sm text-gray-500">Los partidos completados aparecerán aquí.</p>
                                        </div>
                                    )}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}