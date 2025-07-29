import { Link } from '@inertiajs/react';
import { User, Referee, Match } from '@/types/global';
import { CalendarIcon, TrophyIcon, DocumentIcon, ClipboardDocumentCheckIcon, StarIcon } from '@heroicons/react/24/outline';

interface RefereeDashboardProps {
    user: User;
    data: {
        referee: Referee;
        upcomingMatches: Match[];
        recentMatches: Match[];
        stats: {
            totalMatches: number;
            thisMonth: number;
            thisYear: number;
            rating: number;
        };
        notifications: any[];
    };
}

export default function RefereeDashboard({ user, data }: RefereeDashboardProps) {
    const { referee, upcomingMatches, recentMatches, stats } = data;

    return (
        <div className="space-y-6">
            {/* Quick Stats */}
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div className="bg-white overflow-hidden shadow rounded-lg">
                    <div className="p-5">
                        <div className="flex items-center">
                            <div className="flex-shrink-0">
                                <TrophyIcon className="h-6 w-6 text-blue-400" />
                            </div>
                            <div className="ml-5 w-0 flex-1">
                                <dl>
                                    <dt className="text-sm font-medium text-gray-300 truncate">
                                        Total Partidos
                                    </dt>
                                    <dd className="text-lg font-medium text-gray-900">
                                        {stats.totalMatches}
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div className="bg-white overflow-hidden shadow rounded-lg">
                    <div className="p-5">
                        <div className="flex items-center">
                            <div className="flex-shrink-0">
                                <CalendarIcon className="h-6 w-6 text-green-400" />
                            </div>
                            <div className="ml-5 w-0 flex-1">
                                <dl>
                                    <dt className="text-sm font-medium text-gray-500 truncate">
                                        Este Mes
                                    </dt>
                                    <dd className="text-lg font-medium text-gray-900">
                                        {stats.thisMonth}
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div className="bg-white overflow-hidden shadow rounded-lg">
                    <div className="p-5">
                        <div className="flex items-center">
                            <div className="flex-shrink-0">
                                <DocumentIcon className="h-6 w-6 text-yellow-400" />
                            </div>
                            <div className="ml-5 w-0 flex-1">
                                <dl>
                                    <dt className="text-sm font-medium text-gray-500 truncate">
                                        Categoría
                                    </dt>
                                    <dd className="text-lg font-medium text-gray-900">
                                        {referee.category}
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div className="bg-white overflow-hidden shadow rounded-lg">
                    <div className="p-5">
                        <div className="flex items-center">
                            <div className="flex-shrink-0">
                                <StarIcon className="h-6 w-6 text-purple-400" />
                            </div>
                            <div className="ml-5 w-0 flex-1">
                                <dl>
                                    <dt className="text-sm font-medium text-gray-500 truncate">
                                        Experiencia
                                    </dt>
                                    <dd className="text-lg font-medium text-gray-900">
                                        {referee.experience_years || 0} años
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {/* Referee Info Card */}
            <div className="bg-white shadow rounded-lg">
                <div className="px-4 py-5 sm:p-6">
                    <h3 className="text-lg leading-6 font-medium text-gray-900 mb-4">
                        Información del Árbitro
                    </h3>
                    <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div className="text-center">
                            <div className="w-20 h-20 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                <DocumentIcon className="w-10 h-10 text-indigo-600" />
                            </div>
                            <h4 className="text-lg font-medium text-gray-900">{user.name}</h4>
                            <p className="text-sm text-gray-300">Árbitro de Voleibol</p>
                        </div>

                        <div className="space-y-3">
                            <div>
                                <label className="text-sm font-medium text-gray-300">Licencia</label>
                                <p className="text-sm text-gray-900">{referee.license_number}</p>
                            </div>
                            <div>
                                <label className="text-sm font-medium text-gray-500">Categoría</label>
                                <p className="text-sm text-gray-900">{referee.category}</p>
                            </div>
                        </div>

                        <div className="space-y-3">
                            <div>
                                <label className="text-sm font-medium text-gray-500">Experiencia</label>
                                <p className="text-sm text-gray-900">{referee.experience_years || 0} años</p>
                            </div>
                            <div>
                                <label className="text-sm font-medium text-gray-500">Calificación</label>
                                <div className="flex items-center">
                                    <div className="flex items-center">
                                        {[...Array(5)].map((_, i) => (
                                            <StarIcon
                                                key={i}
                                                className={`h-4 w-4 ${
                                                    i < Math.floor(stats.rating)
                                                        ? 'text-yellow-400 fill-current'
                                                        : 'text-gray-300'
                                                }`}
                                            />
                                        ))}
                                    </div>
                                    <span className="ml-2 text-sm text-gray-600">
                                        {stats.rating.toFixed(1)}/5.0
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {/* Upcoming Matches */}
                <div className="bg-white shadow rounded-lg">
                    <div className="px-4 py-5 sm:p-6">
                        <h3 className="text-lg leading-6 font-medium text-gray-900 mb-4">
                            Próximos Partidos Asignados
                        </h3>
                        {upcomingMatches.length > 0 ? (
                            <div className="space-y-4">
                                {upcomingMatches.slice(0, 4).map((match) => (
                                    <div key={match.id} className="border border-gray-200 rounded-lg p-4">
                                        <div className="flex justify-between items-start">
                                            <div className="flex-1">
                                                <p className="font-medium text-gray-900">
                                                    {match.home_team?.name} vs {match.away_team?.name}
                                                </p>
                                                <p className="text-sm text-gray-300">
                                                    {new Date(match.scheduled_at).toLocaleDateString('es-ES', {
                                                        weekday: 'long',
                                                        year: 'numeric',
                                                        month: 'long',
                                                        day: 'numeric',
                                                        hour: '2-digit',
                                                        minute: '2-digit'
                                                    })}
                                                </p>
                                                {match.tournament && (
                                                    <p className="text-xs text-gray-300">
                                                        {match.tournament.name}
                                                    </p>
                                                )}
                                            </div>
                                            <span className="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                Asignado
                                            </span>
                                        </div>
                                        <div className="mt-3">
                                            <Link
                                                href={`/referee/matches/${match.id}`}
                                                className="text-indigo-600 hover:text-indigo-500 text-sm font-medium"
                                            >
                                                Ver detalles →
                                            </Link>
                                        </div>
                                    </div>
                                ))}
                                {upcomingMatches.length > 4 && (
                                    <div className="text-center">
                                        <Link
                                            href="/referee/matches"
                                            className="text-indigo-600 hover:text-indigo-500 font-medium"
                                        >
                                            Ver todos los partidos
                                        </Link>
                                    </div>
                                )}
                            </div>
                        ) : (
                            <p className="text-gray-300 text-center py-4">
                                No tienes partidos asignados próximamente.
                            </p>
                        )}
                    </div>
                </div>

                {/* Recent Matches */}
                <div className="bg-white shadow rounded-lg">
                    <div className="px-4 py-5 sm:p-6">
                        <h3 className="text-lg leading-6 font-medium text-gray-900 mb-4">
                            Partidos Recientes
                        </h3>
                        {recentMatches.length > 0 ? (
                            <div className="space-y-4">
                                {recentMatches.slice(0, 4).map((match) => (
                                    <div key={match.id} className="border border-gray-200 rounded-lg p-4">
                                        <div className="flex justify-between items-center">
                                            <div>
                                                <p className="font-medium text-gray-900">
                                                    {match.home_team?.name} vs {match.away_team?.name}
                                                </p>
                                                <p className="text-sm text-gray-500">
                                                    {new Date(match.scheduled_at).toLocaleDateString()}
                                                </p>
                                                <div className="flex items-center mt-1">
                                                    <span className="text-lg font-bold text-indigo-600 mr-2">
                                                        {match.home_score} - {match.away_score}
                                                    </span>
                                                    <span className="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        Finalizado
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        ) : (
                            <p className="text-gray-500 text-center py-4">
                                No hay partidos recientes para mostrar.
                            </p>
                        )}
                    </div>
                </div>
            </div>

            {/* Quick Actions */}
            <div className="bg-white shadow rounded-lg">
                <div className="px-4 py-5 sm:p-6">
                    <h3 className="text-lg leading-6 font-medium text-gray-900 mb-4">
                        Acciones Rápidas
                    </h3>
                    <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <Link
                            href="/referee/matches"
                            className="bg-indigo-50 hover:bg-indigo-100 p-4 rounded-lg text-center transition-colors"
                        >
                            <CalendarIcon className="h-8 w-8 text-indigo-600 mx-auto mb-2" />
                            <p className="text-sm font-medium text-indigo-900">Mis Partidos</p>
                        </Link>

                        <Link
                            href="/referee/schedule"
                            className="bg-green-50 hover:bg-green-100 p-4 rounded-lg text-center transition-colors"
                        >
                            <ClipboardDocumentCheckIcon className="h-8 w-8 text-green-600 mx-auto mb-2" />
                            <p className="text-sm font-medium text-green-900">Disponibilidad</p>
                        </Link>

                        <Link
                            href="/referee/reports"
                            className="bg-yellow-50 hover:bg-yellow-100 p-4 rounded-lg text-center transition-colors"
                        >
                            <DocumentIcon className="h-8 w-8 text-yellow-600 mx-auto mb-2" />
                            <p className="text-sm font-medium text-yellow-900">Reportes</p>
                        </Link>

                        <Link
                            href="/referee/profile"
                            className="bg-purple-50 hover:bg-purple-100 p-4 rounded-lg text-center transition-colors"
                        >
                            <StarIcon className="h-8 w-8 text-purple-600 mx-auto mb-2" />
                            <p className="text-sm font-medium text-purple-900">Mi Perfil</p>
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    );
}
