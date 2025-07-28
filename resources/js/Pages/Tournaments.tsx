import React from 'react';
import { Head, Link } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { User, Tournament } from '@/types/global';
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
            upcoming: { color: 'bg-blue-100 text-blue-800', text: 'Próximo' },
            active: { color: 'bg-green-100 text-green-800', text: 'Activo' },
            completed: { color: 'bg-gray-100 text-gray-800', text: 'Completado' },
            cancelled: { color: 'bg-red-100 text-red-800', text: 'Cancelado' }
        };
        
        const config = statusConfig[status as keyof typeof statusConfig] || statusConfig.upcoming;
        
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
        <AppLayout title="Torneos" user={user}>
            <Head title="Torneos" />
            
            <div className="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
                {/* Header */}
                <div className="flex justify-between items-center mb-8">
                    <div>
                        <h1 className="text-3xl font-bold text-gray-900">Torneos</h1>
                        <p className="mt-2 text-gray-600">Gestiona y participa en torneos de voleibol.</p>
                    </div>
                    
                    {(user?.roles?.some(role => ['admin', 'organizer'].includes(role.name))) && (
                        <Link
                            href="/tournaments/create"
                            className="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                        >
                            <PlusIcon className="h-5 w-5 mr-2" />
                            Crear Torneo
                        </Link>
                    )}
                </div>

                {/* Filters */}
                <div className="bg-white shadow rounded-lg mb-6">
                    <div className="px-4 py-5 sm:p-6">
                        <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <label htmlFor="search" className="block text-sm font-medium text-gray-700">
                                    Buscar torneos
                                </label>
                                <input
                                    type="text"
                                    id="search"
                                    className="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                    placeholder="Nombre del torneo..."
                                    value={searchTerm}
                                    onChange={(e) => setSearchTerm(e.target.value)}
                                />
                            </div>
                            
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
                                    <option value="upcoming">Próximos</option>
                                    <option value="active">Activos</option>
                                    <option value="completed">Completados</option>
                                    <option value="cancelled">Cancelados</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                {/* Tournaments Grid */}
                {filteredTournaments.length > 0 ? (
                    <div className="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                        {filteredTournaments.map((tournament) => (
                            <div key={tournament.id} className="bg-white overflow-hidden shadow rounded-lg hover:shadow-lg transition-shadow duration-200">
                                <div className="p-6">
                                    <div className="flex items-center justify-between mb-4">
                                        <div className="flex items-center">
                                            <TrophyIcon className="h-8 w-8 text-yellow-500" />
                                            <div className="ml-3">
                                                <h3 className="text-lg font-medium text-gray-900">
                                                    {tournament.name}
                                                </h3>
                                            </div>
                                        </div>
                                        {getStatusBadge(tournament.status)}
                                    </div>
                                    
                                    {tournament.description && (
                                        <p className="text-sm text-gray-600 mb-4 line-clamp-2">
                                            {tournament.description}
                                        </p>
                                    )}
                                    
                                    <div className="space-y-2 mb-4">
                                        <div className="flex items-center text-sm text-gray-500">
                                            <CalendarIcon className="h-4 w-4 mr-2" />
                                            <span>
                                                {formatDate(tournament.start_date)} - {formatDate(tournament.end_date)}
                                            </span>
                                        </div>
                                        
                                        <div className="flex items-center text-sm text-gray-500">
                                            <MapPinIcon className="h-4 w-4 mr-2" />
                                            <span>Liga: {tournament.league?.name || 'Sin asignar'}</span>
                                        </div>
                                        
                                        <div className="flex items-center text-sm text-gray-500">
                                            <UsersIcon className="h-4 w-4 mr-2" />
                                            <span>{tournament.matches?.length || 0} partidos programados</span>
                                        </div>
                                    </div>
                                    
                                    <div className="flex items-center justify-between">
                                        <div className="text-sm text-gray-500">
                                            Estado: <span className="font-medium capitalize">{tournament.status}</span>
                                        </div>
                                        
                                        <Link
                                            href={`/tournaments/${tournament.id}`}
                                            className="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                        >
                                            Ver detalles
                                        </Link>
                                    </div>
                                </div>
                            </div>
                        ))}
                    </div>
                ) : (
                    <div className="text-center py-12">
                        <TrophyIcon className="mx-auto h-12 w-12 text-gray-400" />
                        <h3 className="mt-2 text-sm font-medium text-gray-900">No hay torneos</h3>
                        <p className="mt-1 text-sm text-gray-500">
                            {searchTerm || statusFilter !== 'all' 
                                ? 'No se encontraron torneos con los filtros aplicados.'
                                : 'No hay torneos disponibles en este momento.'
                            }
                        </p>
                        
                        {(user?.roles?.some(role => ['admin', 'organizer'].includes(role.name))) && (
                            <div className="mt-6">
                                <Link
                                    href="/tournaments/create"
                                    className="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                >
                                    <PlusIcon className="h-5 w-5 mr-2" />
                                    Crear primer torneo
                                </Link>
                            </div>
                        )}
                    </div>
                )}
            </div>
        </AppLayout>
    );
}