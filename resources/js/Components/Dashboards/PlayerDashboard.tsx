import { Link } from '@inertiajs/react';
import { User, Player, Match } from '@/types/global';
import DigitalID from '@/Components/DigitalID';
import { CalendarIcon, TrophyIcon, UserGroupIcon, DocumentTextIcon } from '@heroicons/react/24/outline';

interface PlayerDashboardProps {
    user: User;
    data: {
        player: Player;
        upcomingMatches: Match[];
        recentMatches: Match[];
        teamStats: {
            wins: number;
            losses: number;
            totalMatches: number;
        };
        notifications: any[];
    };
}

export default function PlayerDashboard({ data }: PlayerDashboardProps) {
    const { player, upcomingMatches, recentMatches, teamStats } = data;

    // Verificar si el player existe
    if (!player) {
        return (
            <div className="text-center py-12">
                <div className="text-gray-400 text-6xl mb-4">👤</div>
                <h3 className="text-lg font-medium text-gray-900 mb-2">
                    Perfil de jugador no encontrado
                </h3>
                <p className="text-gray-300">
                    No se encontró un perfil de jugador asociado a tu cuenta. Contacta al administrador.
                </p>
            </div>
        );
    }

    return (
        <div className="space-y-6">
            {/* Quick Stats */}
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div className="bg-white overflow-hidden shadow rounded-lg">
                    <div className="p-5">
                        <div className="flex items-center">
                            <div className="flex-shrink-0">
                                <TrophyIcon className="h-6 w-6 text-yellow-400" />
                            </div>
                            <div className="ml-5 w-0 flex-1">
                                <dl>
                                    <dt className="text-sm font-medium text-gray-300 truncate">
                                        Partidos Ganados
                                    </dt>
                                    <dd className="text-lg font-medium text-gray-900">
                                        {teamStats.wins}
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
                                <CalendarIcon className="h-6 w-6 text-blue-400" />
                            </div>
                            <div className="ml-5 w-0 flex-1">
                                <dl>
                                    <dt className="text-sm font-medium text-gray-500 truncate">
                                        Total Partidos
                                    </dt>
                                    <dd className="text-lg font-medium text-gray-900">
                                        {teamStats.totalMatches}
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
                                <UserGroupIcon className="h-6 w-6 text-green-400" />
                            </div>
                            <div className="ml-5 w-0 flex-1">
                                <dl>
                                    <dt className="text-sm font-medium text-gray-500 truncate">
                                        Equipo
                                    </dt>
                                    <dd className="text-lg font-medium text-gray-900">
                                        {player.team?.name || 'Sin equipo'}
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
                                <DocumentTextIcon className="h-6 w-6 text-purple-400" />
                            </div>
                            <div className="ml-5 w-0 flex-1">
                                <dl>
                                    <dt className="text-sm font-medium text-gray-500 truncate">
                                        Posición
                                    </dt>
                                    <dd className="text-lg font-medium text-gray-900">
                                        {player.position}
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {/* Digital ID */}
                <div className="lg:col-span-1">
                    <DigitalID player={player} />
                </div>

                {/* Upcoming Matches */}
                <div className="lg:col-span-2">
                    <div className="bg-white shadow rounded-lg">
                        <div className="px-4 py-5 sm:p-6">
                            <h3 className="text-lg leading-6 font-medium text-gray-900 mb-4">
                                Próximos Partidos
                            </h3>
                            {upcomingMatches.length > 0 ? (
                                <div className="space-y-4">
                                    {upcomingMatches.slice(0, 3).map((match) => (
                                        <div key={match.id} className="border border-gray-200 rounded-lg p-4">
                                            <div className="flex justify-between items-center">
                                                <div>
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
                                                    {match.status === 'upcoming' ? 'Próximo' : 'Programado'}
                                                </span>
                                            </div>
                                        </div>
                                    ))}
                                    {upcomingMatches.length > 3 && (
                                        <div className="text-center">
                                            <Link
                                                href="/player/matches"
                                                className="text-indigo-600 hover:text-indigo-500 font-medium"
                                            >
                                                Ver todos los partidos
                                            </Link>
                                        </div>
                                    )}
                                </div>
                            ) : (
                                <p className="text-gray-300 text-center py-4">
                                    No tienes partidos programados próximamente.
                                </p>
                            )}
                        </div>
                    </div>
                </div>
            </div>

            {/* Recent Matches */}
            <div className="bg-white shadow rounded-lg">
                <div className="px-4 py-5 sm:p-6">
                    <h3 className="text-lg leading-6 font-medium text-gray-900 mb-4">
                        Partidos Recientes
                    </h3>
                    {recentMatches.length > 0 ? (
                        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            {recentMatches.slice(0, 6).map((match) => (
                                <div key={match.id} className="border border-gray-200 rounded-lg p-4">
                                    <div className="text-center">
                                        <div className="flex justify-between items-center mb-2">
                                            <span className="text-sm font-medium">{match.home_team?.name}</span>
                                            <span className="text-lg font-bold text-indigo-600">
                                                {match.home_score}
                                            </span>
                                        </div>
                                        <div className="text-gray-600 text-sm mb-2">VS</div>
                                        <div className="flex justify-between items-center mb-2">
                                            <span className="text-sm font-medium">{match.away_team?.name}</span>
                                            <span className="text-lg font-bold text-indigo-600">
                                                {match.away_score}
                                            </span>
                                        </div>
                                        <p className="text-xs text-gray-300">
                                            {new Date(match.scheduled_at).toLocaleDateString()}
                                        </p>
                                        <span className="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 mt-2">
                                            Finalizado
                                        </span>
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

            {/* Quick Actions */}
            <div className="bg-white shadow rounded-lg">
                <div className="px-4 py-5 sm:p-6">
                    <h3 className="text-lg leading-6 font-medium text-gray-900 mb-4">
                        Acciones Rápidas
                    </h3>
                    <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <Link
                            href="/player/profile"
                            className="bg-indigo-50 hover:bg-indigo-100 p-4 rounded-lg text-center transition-colors"
                        >
                            <DocumentTextIcon className="h-8 w-8 text-indigo-600 mx-auto mb-2" />
                            <p className="text-sm font-medium text-indigo-900">Mi Perfil</p>
                        </Link>
                        
                        <Link
                            href="/player/matches"
                            className="bg-green-50 hover:bg-green-100 p-4 rounded-lg text-center transition-colors"
                        >
                            <CalendarIcon className="h-8 w-8 text-green-600 mx-auto mb-2" />
                            <p className="text-sm font-medium text-green-900">Mis Partidos</p>
                        </Link>
                        
                        <Link
                            href="/player/team"
                            className="bg-yellow-50 hover:bg-yellow-100 p-4 rounded-lg text-center transition-colors"
                        >
                            <UserGroupIcon className="h-8 w-8 text-yellow-600 mx-auto mb-2" />
                            <p className="text-sm font-medium text-yellow-900">Mi Equipo</p>
                        </Link>
                        
                        <Link
                            href="/player/statistics"
                            className="bg-purple-50 hover:bg-purple-100 p-4 rounded-lg text-center transition-colors"
                        >
                            <TrophyIcon className="h-8 w-8 text-purple-600 mx-auto mb-2" />
                            <p className="text-sm font-medium text-purple-900">Estadísticas</p>
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    );
}