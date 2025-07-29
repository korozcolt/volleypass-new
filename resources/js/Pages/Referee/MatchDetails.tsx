import React from 'react';
import { Head, Link } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { ClockIcon, MapPinIcon, TrophyIcon, UserGroupIcon, ArrowLeftIcon } from '@heroicons/react/24/outline';

interface Team {
    id: number;
    name: string;
    logo?: string;
}

interface Tournament {
    id: number;
    name: string;
}

interface Set {
    id: number;
    set_number: number;
    home_score: number;
    away_score: number;
    status: 'pending' | 'in_progress' | 'completed';
}

interface Match {
    id: number;
    home_team: Team;
    away_team: Team;
    tournament: Tournament;
    match_date: string;
    match_time: string;
    venue: string;
    status: 'scheduled' | 'live' | 'completed' | 'cancelled';
    home_sets: number;
    away_sets: number;
    sets: Set[];
    referees: string[];
}

interface Props {
    match: Match;
    canControl: boolean;
    currentSet?: Set;
    completedSets: Set[];
}

const getStatusBadge = (status: string) => {
    const baseClasses = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium';
    
    switch (status) {
        case 'scheduled':
            return `${baseClasses} bg-blue-100 text-blue-800`;
        case 'live':
            return `${baseClasses} bg-green-100 text-green-800`;
        case 'completed':
            return `${baseClasses} bg-gray-100 text-gray-800`;
        case 'cancelled':
            return `${baseClasses} bg-red-100 text-red-800`;
        default:
            return `${baseClasses} bg-gray-100 text-gray-800`;
    }
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

const formatTime = (timeString: string) => {
    return timeString.slice(0, 5);
};

export default function MatchDetails({ match, canControl, currentSet, completedSets }: Props) {
    return (
        <AppLayout>
            <Head title={`Partido: ${match.home_team.name} vs ${match.away_team.name}`} />
            
            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    {/* Header */}
                    <div className="mb-8">
                        <Link
                            href={route('referee.matches')}
                            className="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 mb-4"
                        >
                            <ArrowLeftIcon className="h-4 w-4 mr-1" />
                            Volver a mis partidos
                        </Link>
                        
                        <div className="flex items-center justify-between">
                            <h1 className="text-3xl font-bold text-gray-900">
                                Detalles del Partido
                            </h1>
                            <span className={getStatusBadge(match.status)}>
                                {match.status === 'scheduled' && 'Programado'}
                                {match.status === 'live' && 'En Vivo'}
                                {match.status === 'completed' && 'Finalizado'}
                                {match.status === 'cancelled' && 'Cancelado'}
                            </span>
                        </div>
                    </div>

                    <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        {/* Match Info */}
                        <div className="lg:col-span-2">
                            <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                                <div className="p-6">
                                    {/* Teams */}
                                    <div className="text-center mb-8">
                                        <div className="flex items-center justify-between">
                                            <div className="flex-1">
                                                <div className="text-2xl font-bold text-gray-900">
                                                    {match.home_team.name}
                                                </div>
                                                <div className="text-sm text-gray-500 mt-1">Local</div>
                                            </div>
                                            
                                            <div className="mx-8">
                                                <div className="text-4xl font-bold text-gray-900">
                                                    {match.home_sets} - {match.away_sets}
                                                </div>
                                                <div className="text-sm text-gray-500 mt-1">Sets</div>
                                            </div>
                                            
                                            <div className="flex-1">
                                                <div className="text-2xl font-bold text-gray-900">
                                                    {match.away_team.name}
                                                </div>
                                                <div className="text-sm text-gray-500 mt-1">Visitante</div>
                                            </div>
                                        </div>
                                    </div>

                                    {/* Match Details */}
                                    <div className="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                                        <div className="flex items-center">
                                            <TrophyIcon className="h-5 w-5 text-gray-400 mr-3" />
                                            <div>
                                                <div className="text-sm font-medium text-gray-900">
                                                    Torneo
                                                </div>
                                                <div className="text-sm text-gray-500">
                                                    {match.tournament.name}
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div className="flex items-center">
                                            <ClockIcon className="h-5 w-5 text-gray-400 mr-3" />
                                            <div>
                                                <div className="text-sm font-medium text-gray-900">
                                                    Fecha y Hora
                                                </div>
                                                <div className="text-sm text-gray-500">
                                                    {formatDate(match.match_date)} - {formatTime(match.match_time)}
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div className="flex items-center">
                                            <MapPinIcon className="h-5 w-5 text-gray-400 mr-3" />
                                            <div>
                                                <div className="text-sm font-medium text-gray-900">
                                                    Sede
                                                </div>
                                                <div className="text-sm text-gray-500">
                                                    {match.venue}
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div className="flex items-center">
                                            <UserGroupIcon className="h-5 w-5 text-gray-400 mr-3" />
                                            <div>
                                                <div className="text-sm font-medium text-gray-900">
                                                    Árbitros
                                                </div>
                                                <div className="text-sm text-gray-500">
                                                    {match.referees.join(', ')}
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {/* Control Panel Link */}
                                    {canControl && match.status === 'scheduled' && (
                                        <div className="border-t pt-6">
                                            <Link
                                                href={route('referee.match.control', match.id)}
                                                className="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150"
                                            >
                                                Ir al Panel de Control
                                            </Link>
                                        </div>
                                    )}
                                </div>
                            </div>
                        </div>

                        {/* Sets Details */}
                        <div>
                            <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                                <div className="p-6">
                                    <h3 className="text-lg font-medium text-gray-900 mb-4">
                                        Detalles de Sets
                                    </h3>
                                    
                                    {match.sets.length > 0 ? (
                                        <div className="space-y-4">
                                            {match.sets.map((set) => (
                                                <div
                                                    key={set.id}
                                                    className={`p-4 rounded-lg border ${
                                                        set.status === 'in_progress'
                                                            ? 'border-green-200 bg-green-50'
                                                            : set.status === 'completed'
                                                            ? 'border-gray-200 bg-gray-50'
                                                            : 'border-gray-200 bg-white'
                                                    }`}
                                                >
                                                    <div className="flex items-center justify-between">
                                                        <div className="text-sm font-medium text-gray-900">
                                                            Set {set.set_number}
                                                        </div>
                                                        <div className="text-sm text-gray-500">
                                                            {set.status === 'in_progress' && 'En Progreso'}
                                                            {set.status === 'completed' && 'Completado'}
                                                            {set.status === 'pending' && 'Pendiente'}
                                                        </div>
                                                    </div>
                                                    
                                                    {set.status !== 'pending' && (
                                                        <div className="mt-2 text-lg font-bold text-gray-900">
                                                            {set.home_score} - {set.away_score}
                                                        </div>
                                                    )}
                                                </div>
                                            ))}
                                        </div>
                                    ) : (
                                        <p className="text-sm text-gray-500">
                                            No hay sets registrados aún.
                                        </p>
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