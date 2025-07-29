import React, { useState, useEffect } from 'react';
import { Head } from '@inertiajs/react';
import { PageProps, Match } from '@/types/global';
import MainLayout from '@/Layouts/MainLayout';
import MatchControlPanel from '@/Components/Referee/MatchControlPanel';
import LiveMatchCard from '@/Components/LiveMatch/LiveMatchCard';
import { useMatchRealTimeUpdates } from '@/hooks/useMatchRealTimeUpdates';
import { PlayIcon, ClockIcon, TrophyIcon, UserIcon } from '@heroicons/react/24/outline';

interface MatchControlProps extends PageProps {
    match: Match;
    canControl: boolean;
}

export default function MatchControl({ auth, match: initialMatch, canControl }: MatchControlProps) {
    const [match, setMatch] = useState<Match>(initialMatch);
    const [activeTab, setActiveTab] = useState<'control' | 'preview'>('control');

    // Hook para actualizaciones en tiempo real
    useMatchRealTimeUpdates(match.id, {
        onMatchUpdate: (updatedMatch) => {
            setMatch(updatedMatch);
        },
    });

    const getStatusColor = (status: string) => {
        switch (status) {
            case 'in_progress':
                return 'text-red-400';
            case 'finished':
                return 'text-gray-400';
            case 'scheduled':
                return 'text-blue-400';
            default:
                return 'text-gray-400';
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
        <MainLayout title="Control de Partido" user={auth?.user} currentRoute="/referee/match-control">
            <Head title={`Control de Partido - ${match.home_team?.name} vs ${match.away_team?.name}`} />
            
            {/* Header */}
            <div className="relative mb-8">
                <div className="h-32 bg-gradient-to-r from-blue-600 via-purple-600 to-indigo-600 relative overflow-hidden rounded-2xl">
                    <div className="absolute inset-0 bg-black/20"></div>
                    <div className="relative z-10 h-full flex items-center justify-between px-8">
                        <div className="flex items-center space-x-4">
                            <PlayIcon className="w-12 h-12 text-white" />
                            <div>
                                <h1 className="text-3xl font-black text-white">
                                    CONTROL DE PARTIDO
                                </h1>
                                <p className="text-white/80 font-medium">
                                    {match.home_team?.name} vs {match.away_team?.name}
                                </p>
                            </div>
                        </div>
                        <div className="text-right">
                            <div className={`text-2xl font-bold ${getStatusColor(match.status)}`}>
                                {getStatusText(match.status)}
                            </div>
                            <div className="text-white/80 text-sm">
                                {match.tournament?.name}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {/* Permission Check */}
            {!canControl && (
                <div className="bg-red-600/20 border border-red-600 rounded-lg p-4 mb-6">
                    <div className="flex items-center space-x-3">
                        <UserIcon className="w-6 h-6 text-red-400" />
                        <div>
                            <h3 className="text-red-400 font-semibold">Acceso Restringido</h3>
                            <p className="text-red-300 text-sm">
                                No tienes permisos para controlar este partido. Solo puedes ver la información.
                            </p>
                        </div>
                    </div>
                </div>
            )}

            {/* Match Info */}
            <div className="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div className="bg-gradient-to-br from-slate-800 to-slate-700 rounded-xl p-6 border border-slate-600">
                    <div className="flex items-center mb-3">
                        <TrophyIcon className="w-6 h-6 text-yellow-400 mr-2" />
                        <h3 className="text-white font-semibold">Información del Partido</h3>
                    </div>
                    <div className="space-y-2 text-sm">
                        <div className="flex justify-between">
                            <span className="text-gray-400">Torneo:</span>
                            <span className="text-white">{match.tournament?.name || 'N/A'}</span>
                        </div>
                        <div className="flex justify-between">
                            <span className="text-gray-400">Sede:</span>
                            <span className="text-white">{match.venue || 'N/A'}</span>
                        </div>
                        <div className="flex justify-between">
                            <span className="text-gray-400">Fecha:</span>
                            <span className="text-white">
                                {match.scheduled_at ? new Date(match.scheduled_at).toLocaleDateString() : 'N/A'}
                            </span>
                        </div>
                    </div>
                </div>

                <div className="bg-gradient-to-br from-slate-800 to-slate-700 rounded-xl p-6 border border-slate-600">
                    <div className="flex items-center mb-3">
                        <ClockIcon className="w-6 h-6 text-blue-400 mr-2" />
                        <h3 className="text-white font-semibold">Estado del Partido</h3>
                    </div>
                    <div className="space-y-2 text-sm">
                        <div className="flex justify-between">
                            <span className="text-gray-400">Estado:</span>
                            <span className={`font-semibold ${getStatusColor(match.status)}`}>
                                {getStatusText(match.status)}
                            </span>
                        </div>
                        {match.started_at && (
                            <div className="flex justify-between">
                                <span className="text-gray-400">Iniciado:</span>
                                <span className="text-white">
                                    {new Date(match.started_at).toLocaleTimeString()}
                                </span>
                            </div>
                        )}
                        {match.duration_minutes && (
                            <div className="flex justify-between">
                                <span className="text-gray-400">Duración:</span>
                                <span className="text-white">{match.duration_minutes} min</span>
                            </div>
                        )}
                    </div>
                </div>

                <div className="bg-gradient-to-br from-slate-800 to-slate-700 rounded-xl p-6 border border-slate-600">
                    <div className="flex items-center mb-3">
                        <UserIcon className="w-6 h-6 text-green-400 mr-2" />
                        <h3 className="text-white font-semibold">Resultado Actual</h3>
                    </div>
                    <div className="text-center">
                        <div className="text-3xl font-bold text-yellow-400 mb-2">
                            {match.home_sets || 0} - {match.away_sets || 0}
                        </div>
                        <div className="text-gray-400 text-sm">Sets ganados</div>
                    </div>
                </div>
            </div>

            {/* Tabs */}
            <div className="flex space-x-4 mb-6">
                <button
                    onClick={() => setActiveTab('control')}
                    className={`px-6 py-3 rounded-lg font-semibold transition-all ${
                        activeTab === 'control'
                            ? 'bg-blue-600 text-white shadow-lg'
                            : 'bg-slate-700 text-gray-300 hover:bg-slate-600'
                    }`}
                >
                    Panel de Control
                </button>
                <button
                    onClick={() => setActiveTab('preview')}
                    className={`px-6 py-3 rounded-lg font-semibold transition-all ${
                        activeTab === 'preview'
                            ? 'bg-blue-600 text-white shadow-lg'
                            : 'bg-slate-700 text-gray-300 hover:bg-slate-600'
                    }`}
                >
                    Vista Previa
                </button>
            </div>

            {/* Content */}
            <div className="space-y-8">
                {activeTab === 'control' && canControl && (
                    <MatchControlPanel
                        match={match}
                        onMatchUpdate={(updatedMatch) => setMatch(updatedMatch)}
                    />
                )}

                {activeTab === 'control' && !canControl && (
                    <div className="bg-slate-800 rounded-2xl p-12 border border-slate-600 text-center">
                        <UserIcon className="w-16 h-16 text-gray-400 mx-auto mb-4" />
                        <h3 className="text-2xl font-bold text-white mb-2">
                            Control No Disponible
                        </h3>
                        <p className="text-gray-400">
                            No tienes permisos para controlar este partido.
                        </p>
                    </div>
                )}

                {activeTab === 'preview' && (
                    <div className="max-w-2xl mx-auto">
                        <h3 className="text-xl font-bold text-white mb-4 text-center">
                            Vista Previa del Partido
                        </h3>
                        <LiveMatchCard
                            match={match}
                            onMatchUpdate={(updatedMatch) => setMatch(updatedMatch)}
                        />
                    </div>
                )}
            </div>
        </MainLayout>
    );
}