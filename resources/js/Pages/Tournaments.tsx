import React from 'react';
import { Head, Link } from '@inertiajs/react';
import { User, Tournament } from '@/types/global';
import MainLayout from '@/Layouts/MainLayout';
import { TrophyIcon, CalendarIcon, MapPinIcon, UsersIcon, PlusIcon } from '@heroicons/react/24/outline';

interface TournamentsProps {
    user?: User;
    tournaments: Tournament[];
}

export default function Tournaments({ user, tournaments }: TournamentsProps) {
    const [searchTerm, setSearchTerm] = React.useState('');
    const [statusFilter, setStatusFilter] = React.useState('all');

    const filteredTournaments = tournaments.filter(tournament => {
        const matchesSearch = tournament.name.toLowerCase().includes(searchTerm.toLowerCase()) ||
                            tournament.description?.toLowerCase().includes(searchTerm.toLowerCase());
        const matchesStatus = statusFilter === 'all' || tournament.status === statusFilter;
        return matchesSearch && matchesStatus;
    });

    const getStatusBadge = (status: string) => {
        const statusConfig = {
            draft: { color: 'bg-gray-100 text-gray-800', text: 'Borrador' },
            registration_open: { color: 'bg-green-100 text-green-800', text: 'Inscripciones Abiertas' },
            registration_closed: { color: 'bg-yellow-100 text-yellow-800', text: 'Inscripciones Cerradas' },
            in_progress: { color: 'bg-blue-100 text-blue-800', text: 'En Progreso' },
            finished: { color: 'bg-cyan-100 text-cyan-800', text: 'Finalizado' },
            cancelled: { color: 'bg-red-100 text-red-800', text: 'Cancelado' },
            // Fallbacks para estados antiguos
            upcoming: { color: 'bg-blue-100 text-blue-800', text: 'Próximo' },
            active: { color: 'bg-green-100 text-green-800', text: 'Activo' },
            completed: { color: 'bg-gray-100 text-gray-800', text: 'Completado' }
        };

        const config = statusConfig[status as keyof typeof statusConfig] || statusConfig.draft;

        return (
            <span className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${config.color}`}>
                {config.text}
            </span>
        );
    };

    const formatDate = (dateString: string) => {
        return new Date(dateString).toLocaleDateString('es-ES', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
    };

    return (
        <MainLayout title="Torneos" user={user} currentRoute="/torneos">
            <div className="container mx-auto py-8 px-4">
                {/* Page Header */}
                <div className="flex justify-between items-center mb-8">
                    <div>
                        <h1 className="text-4xl font-black text-white mb-2 flex items-center space-x-3">
                            <TrophyIcon className="w-10 h-10 text-yellow-400" />
                            <span>Torneos</span>
                        </h1>
                        <p className="text-xl text-white">Gestiona y participa en torneos de voleibol.</p>
                    </div>

                    {(user?.roles?.some(role => ['admin', 'organizer'].includes(role.name))) && (
                        <Link
                            href="/tournaments/create"
                            className="bg-gradient-to-r from-yellow-400 to-yellow-500 text-black px-6 py-3 rounded-lg font-bold hover:from-yellow-500 hover:to-yellow-600 transition-all duration-200 shadow-xl transform hover:scale-105 flex items-center space-x-2"
                        >
                            <PlusIcon className="h-5 w-5" />
                            <span>Crear Torneo</span>
                        </Link>
                    )}
                </div>

                {/* Filters */}
                <div className="bg-gradient-to-br from-slate-800 to-slate-700 rounded-2xl shadow-2xl border border-slate-600 mb-8">
                    <div className="p-6">
                        <div className="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <div>
                                <label htmlFor="search" className="block text-sm font-bold text-white mb-2">
                                    Buscar torneos
                                </label>
                                <input
                                    type="text"
                                    id="search"
                                    className="block w-full px-4 py-3 bg-slate-700 border border-slate-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-transparent transition-all duration-200"
                                    placeholder="Nombre del torneo..."
                                    value={searchTerm}
                                    onChange={(e) => setSearchTerm(e.target.value)}
                                />
                            </div>

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
                                    <option value="draft">Borrador</option>
                                    <option value="registration_open">Inscripciones Abiertas</option>
                                    <option value="registration_closed">Inscripciones Cerradas</option>
                                    <option value="in_progress">En Progreso</option>
                                    <option value="finished">Finalizado</option>
                                    <option value="cancelled">Cancelado</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                {/* Tournaments Grid */}
                {filteredTournaments.length > 0 ? (
                    <div className="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                        {filteredTournaments.map((tournament) => (
                            <div key={tournament.id} className="bg-gradient-to-br from-slate-800 to-slate-700 rounded-2xl shadow-2xl border border-slate-600 hover:shadow-3xl transition-all duration-300 transform hover:scale-105">
                                <div className="p-6">
                                    <div className="flex items-center justify-between mb-4">
                                        <div className="flex items-center">
                                            <TrophyIcon className="h-8 w-8 text-yellow-500" />
                                            <div className="ml-3">
                                                <h3 className="text-xl font-black text-white">
                                                    {tournament.name}
                                                </h3>
                                            </div>
                                        </div>
                                        {getStatusBadge(tournament.status)}
                                    </div>

                                    {tournament.description && (
                                        <p className="text-white mb-4 line-clamp-2">
                                            {tournament.description}
                                        </p>
                                    )}

                                    <div className="space-y-2 mb-4">
                                        <div className="flex items-center text-sm text-gray-300">
                                            <CalendarIcon className="h-4 w-4 mr-2 text-yellow-400" />
                                            <span>
                                                {formatDate(tournament.start_date)} - {formatDate(tournament.end_date)}
                                            </span>
                                        </div>

                                        <div className="flex items-center text-sm text-gray-300">
                                            <MapPinIcon className="h-4 w-4 mr-2 text-blue-400" />
                                            <span>Liga: {tournament.league?.name || 'Sin asignar'}</span>
                                        </div>

                                        <div className="flex items-center text-sm text-gray-300">
                                            <UsersIcon className="h-4 w-4 mr-2 text-green-400" />
                                            <span>{tournament.matches?.length || 0} partidos programados</span>
                                        </div>
                                    </div>

                                    <div className="flex items-center justify-between">
                                        <div className="flex items-center space-x-2">
                                            <span className="text-sm text-gray-300">Estado:</span>
                                            {getStatusBadge(tournament.status)}
                                        </div>

                                        <Link
                                            href={`/tournaments/${tournament.id}`}
                                            className="bg-gradient-to-r from-blue-600 to-blue-700 text-white px-4 py-2 rounded-lg font-bold hover:from-blue-700 hover:to-blue-800 transition-all duration-200 shadow-lg"
                                        >
                                            Ver detalles
                                        </Link>
                                    </div>
                                </div>
                            </div>
                        ))}
                    </div>
                ) : (
                    <div className="bg-gradient-to-br from-slate-800 to-slate-700 rounded-2xl p-12 shadow-2xl border border-slate-600">
                        <div className="text-center">
                            <TrophyIcon className="mx-auto h-12 w-12 text-gray-400" />
                            <h3 className="mt-2 text-lg font-medium text-white">No hay torneos</h3>
                            <p className="mt-1 text-sm text-white">
                                {searchTerm || statusFilter !== 'all'
                                    ? 'No se encontraron torneos con los filtros aplicados.'
                                    : 'No hay torneos disponibles en este momento.'
                                }
                            </p>

                            {(user?.roles?.some(role => ['admin', 'organizer'].includes(role.name))) && (
                                <div className="mt-6">
                                    <Link
                                        href="/tournaments/create"
                                        className="bg-gradient-to-r from-yellow-400 to-yellow-500 text-black px-6 py-3 rounded-lg font-bold hover:from-yellow-500 hover:to-yellow-600 transition-all duration-200 shadow-xl transform hover:scale-105 flex items-center space-x-2"
                                    >
                                        <PlusIcon className="h-5 w-5" />
                                        <span>Crear primer torneo</span>
                                    </Link>
                                </div>
                            )}
                        </div>
                    </div>
                )}
            </div>
        </MainLayout>
    );
}
