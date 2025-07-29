import React from 'react';
import { Head } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { UserIcon, ShieldCheckIcon, TrophyIcon, CalendarIcon } from '@heroicons/react/24/outline';

interface User {
    id: number;
    name: string;
    email: string;
    phone?: string;
}

interface Referee {
    id: number;
    license_number: string;
    category: string;
    experience_years: number;
    status: string;
    rating?: number;
}

interface Stats {
    total_matches: number;
    completed_matches: number;
    upcoming_matches: number;
}

interface Props {
    user: User;
    referee: Referee;
    stats: Stats;
}

export default function Profile({ user, referee, stats }: Props) {
    const getCategoryColor = (category: string) => {
        const colors = {
            'Nacional': 'bg-red-100 text-red-800',
            'Regional': 'bg-blue-100 text-blue-800',
            'Provincial': 'bg-green-100 text-green-800',
            'Local': 'bg-yellow-100 text-yellow-800',
        };
        return colors[category as keyof typeof colors] || 'bg-gray-100 text-gray-800';
    };

    const getStatusColor = (status: string) => {
        const colors = {
            'active': 'bg-green-100 text-green-800',
            'inactive': 'bg-red-100 text-red-800',
            'suspended': 'bg-yellow-100 text-yellow-800',
        };
        return colors[status as keyof typeof colors] || 'bg-gray-100 text-gray-800';
    };

    const getStatusLabel = (status: string) => {
        const labels = {
            'active': 'Activo',
            'inactive': 'Inactivo',
            'suspended': 'Suspendido',
        };
        return labels[status as keyof typeof labels] || status;
    };

    return (
        <AppLayout>
            <Head title="Mi Perfil" />
            
            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div className="p-6 bg-white border-b border-gray-200">
                            <div className="flex items-center justify-between mb-6">
                                <div>
                                    <h1 className="text-2xl font-bold text-gray-900">Mi Perfil</h1>
                                    <p className="text-gray-600">Información personal y estadísticas</p>
                                </div>
                                <UserIcon className="h-8 w-8 text-gray-400" />
                            </div>

                            <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
                                {/* Información Personal */}
                                <div className="lg:col-span-2 space-y-6">
                                    <div className="bg-gray-50 rounded-lg p-6">
                                        <h2 className="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                            <UserIcon className="h-5 w-5 mr-2" />
                                            Información Personal
                                        </h2>
                                        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <label className="block text-sm font-medium text-gray-700">Nombre Completo</label>
                                                <p className="mt-1 text-sm text-gray-900">{user.name}</p>
                                            </div>
                                            <div>
                                                <label className="block text-sm font-medium text-gray-700">Email</label>
                                                <p className="mt-1 text-sm text-gray-900">{user.email}</p>
                                            </div>
                                            {user.phone && (
                                                <div>
                                                    <label className="block text-sm font-medium text-gray-700">Teléfono</label>
                                                    <p className="mt-1 text-sm text-gray-900">{user.phone}</p>
                                                </div>
                                            )}
                                        </div>
                                    </div>

                                    {/* Información de Árbitro */}
                                    {referee && (
                                        <div className="bg-gray-50 rounded-lg p-6">
                                            <h2 className="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                                <ShieldCheckIcon className="h-5 w-5 mr-2" />
                                                Información de Árbitro
                                            </h2>
                                            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                <div>
                                                    <label className="block text-sm font-medium text-gray-700">Número de Licencia</label>
                                                    <p className="mt-1 text-sm text-gray-900">{referee.license_number}</p>
                                                </div>
                                                <div>
                                                    <label className="block text-sm font-medium text-gray-700">Categoría</label>
                                                    <span className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${getCategoryColor(referee.category)}`}>
                                                        {referee.category}
                                                    </span>
                                                </div>
                                                <div>
                                                    <label className="block text-sm font-medium text-gray-700">Años de Experiencia</label>
                                                    <p className="mt-1 text-sm text-gray-900">{referee.experience_years} años</p>
                                                </div>
                                                <div>
                                                    <label className="block text-sm font-medium text-gray-700">Estado</label>
                                                    <span className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${getStatusColor(referee.status)}`}>
                                                        {getStatusLabel(referee.status)}
                                                    </span>
                                                </div>
                                                {referee.rating && (
                                                    <div>
                                                        <label className="block text-sm font-medium text-gray-700">Calificación</label>
                                                        <div className="flex items-center mt-1">
                                                            <span className="text-sm text-gray-900">{referee.rating.toFixed(1)}</span>
                                                            <div className="flex ml-2">
                                                                {[1, 2, 3, 4, 5].map((star) => (
                                                                    <svg
                                                                        key={star}
                                                                        className={`h-4 w-4 ${
                                                                            star <= referee.rating! ? 'text-yellow-400' : 'text-gray-300'
                                                                        }`}
                                                                        fill="currentColor"
                                                                        viewBox="0 0 20 20"
                                                                    >
                                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                                    </svg>
                                                                ))}
                                                            </div>
                                                        </div>
                                                    </div>
                                                )}
                                            </div>
                                        </div>
                                    )}
                                </div>

                                {/* Estadísticas */}
                                <div className="space-y-6">
                                    <div className="bg-gray-50 rounded-lg p-6">
                                        <h2 className="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                            <TrophyIcon className="h-5 w-5 mr-2" />
                                            Estadísticas
                                        </h2>
                                        <div className="space-y-4">
                                            <div className="flex items-center justify-between">
                                                <span className="text-sm font-medium text-gray-700">Total de Partidos</span>
                                                <span className="text-lg font-bold text-gray-900">{stats.total_matches}</span>
                                            </div>
                                            <div className="flex items-center justify-between">
                                                <span className="text-sm font-medium text-gray-700">Partidos Completados</span>
                                                <span className="text-lg font-bold text-green-600">{stats.completed_matches}</span>
                                            </div>
                                            <div className="flex items-center justify-between">
                                                <span className="text-sm font-medium text-gray-700">Partidos Próximos</span>
                                                <span className="text-lg font-bold text-blue-600">{stats.upcoming_matches}</span>
                                            </div>
                                        </div>
                                    </div>

                                    {/* Acciones Rápidas */}
                                    <div className="bg-gray-50 rounded-lg p-6">
                                        <h2 className="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                            <CalendarIcon className="h-5 w-5 mr-2" />
                                            Acciones Rápidas
                                        </h2>
                                        <div className="space-y-3">
                                            <a
                                                href="/referee/schedule"
                                                className="block w-full text-center bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition-colors text-sm font-medium"
                                            >
                                                Ver Mi Calendario
                                            </a>
                                            <a
                                                href="/dashboard"
                                                className="block w-full text-center bg-gray-600 text-white py-2 px-4 rounded-md hover:bg-gray-700 transition-colors text-sm font-medium"
                                            >
                                                Ir al Dashboard
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}