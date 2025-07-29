import React from 'react';
import { User, Match } from '@/types/global';
import MainLayout from '@/Layouts/MainLayout';
import { TrophyIcon, CalendarIcon, PlayIcon } from '@heroicons/react/24/outline';

interface MatchesProps {
    user?: User;
    matches: Match[];
}

export default function MatchesSimple({ user, matches }: MatchesProps) {
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

                {/* Stats */}
                <div className="bg-gradient-to-br from-slate-800 to-slate-700 rounded-2xl shadow-2xl border border-slate-600 mb-8 p-6">
                    <div className="grid grid-cols-1 md:grid-cols-3 gap-6 text-center">
                        <div>
                            <div className="text-3xl font-mono font-black text-yellow-400 mb-2">
                                {matches.length}
                            </div>
                            <div className="text-white font-bold">Total Partidos</div>
                        </div>
                        <div>
                            <div className="text-3xl font-mono font-black text-green-400 mb-2">
                                {matches.filter(m => m.status === 'live').length}
                            </div>
                            <div className="text-white font-bold">En Vivo</div>
                        </div>
                        <div>
                            <div className="text-3xl font-mono font-black text-blue-400 mb-2">
                                {matches.filter(m => m.status === 'scheduled').length}
                            </div>
                            <div className="text-white font-bold">Programados</div>
                        </div>
                    </div>
                </div>

                {/* Matches List */}
                {matches.length > 0 ? (
                    <div className="grid grid-cols-1 gap-6">
                        {matches.slice(0, 10).map((match) => (
                            <div key={match.id} className="bg-gradient-to-br from-slate-800 to-slate-700 rounded-2xl shadow-2xl border border-slate-600 p-6">
                                <div className="flex items-center justify-between">
                                    <div className="flex items-center space-x-6">
                                        <div className="flex-shrink-0">
                                            <PlayIcon className="h-6 w-6 text-yellow-400" />
                                        </div>

                                        <div className="flex items-center space-x-4">
                                            <div className="text-center">
                                                <div className="text-lg font-bold text-white">
                                                    {match.home_team?.name || 'Equipo Local'}
                                                </div>
                                                <div className="text-sm text-gray-300">Local</div>
                                            </div>

                                            <div className="text-center px-4">
                                                <div className="text-lg text-gray-400">vs</div>
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
                                        <div className="text-right">
                                            <div className="flex items-center text-white">
                                                <CalendarIcon className="h-4 w-4 mr-2 text-yellow-400" />
                                                <span className="font-bold">
                                                    {new Date(match.scheduled_at).toLocaleDateString('es-ES')}
                                                </span>
                                            </div>
                                            <div className="text-sm text-gray-300 mt-1">
                                                {new Date(match.scheduled_at).toLocaleTimeString('es-ES', {
                                                    hour: '2-digit',
                                                    minute: '2-digit'
                                                })}
                                            </div>
                                        </div>

                                        <div className={`px-3 py-1 rounded-full text-xs font-bold ${
                                            match.status === 'live' ? 'bg-red-600 text-white' :
                                            match.status === 'finished' ? 'bg-green-600 text-white' :
                                            'bg-blue-600 text-white'
                                        }`}>
                                            {match.status === 'live' ? 'En Vivo' :
                                             match.status === 'finished' ? 'Finalizado' :
                                             'Programado'}
                                        </div>
                                    </div>
                                </div>

                                {match.tournament && (
                                    <div className="mt-4 text-sm text-gray-300">
                                        <span>Torneo: {match.tournament.name}</span>
                                    </div>
                                )}
                            </div>
                        ))}
                    </div>
                ) : (
                    <div className="bg-gradient-to-br from-slate-800 to-slate-700 rounded-2xl p-12 shadow-2xl border border-slate-600">
                        <div className="text-center">
                            <PlayIcon className="mx-auto h-12 w-12 text-gray-400" />
                            <h3 className="mt-2 text-lg font-medium text-white">No hay partidos</h3>
                            <p className="mt-1 text-sm text-white">
                                No hay partidos programados en este momento.
                            </p>
                        </div>
                    </div>
                )}
            </div>
        </MainLayout>
    );
}
