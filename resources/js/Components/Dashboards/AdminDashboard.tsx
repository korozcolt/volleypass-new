import { Link } from '@inertiajs/react';
import { User } from '@/types/global';
import { 
    CalendarIcon, 
    TrophyIcon, 
    UserGroupIcon, 
    DocumentTextIcon, 
    ChartBarIcon,
    CogIcon,
    ShieldCheckIcon,
    ClipboardDocumentListIcon
} from '@heroicons/react/24/outline';

interface AdminDashboardProps {
    user: User;
    data: {
        stats: {
            totalUsers: number;
            totalPlayers: number;
            totalCoaches: number;
            totalReferees: number;
            totalClubs: number;
            totalTeams: number;
            totalTournaments: number;
            activeMatches: number;
        };
        recentActivity: any[];
        systemHealth: {
            status: 'healthy' | 'warning' | 'critical';
            uptime: string;
            lastBackup: string;
        };
        notifications: any[];
    };
}

export default function AdminDashboard({ data }: AdminDashboardProps) {
    const { stats, recentActivity, systemHealth } = data;

    const getStatusColor = (status: string) => {
        switch (status) {
            case 'healthy':
                return 'text-green-600 bg-green-100';
            case 'warning':
                return 'text-yellow-600 bg-yellow-100';
            case 'critical':
                return 'text-red-600 bg-red-100';
            default:
                return 'text-gray-600 bg-gray-100';
        }
    };

    return (
        <div className="space-y-6">
            {/* System Health */}
            <div className="bg-white shadow rounded-lg">
                <div className="px-4 py-5 sm:p-6">
                    <div className="flex items-center justify-between">
                        <div>
                            <h3 className="text-lg leading-6 font-medium text-gray-900">
                                Estado del Sistema
                            </h3>
                            <p className="mt-1 text-sm text-gray-600">
                                Monitoreo en tiempo real del sistema VolleyPass
                            </p>
                        </div>
                        <div className="flex items-center">
                            <span className={`inline-flex items-center px-3 py-1 rounded-full text-sm font-medium ${getStatusColor(systemHealth.status)}`}>
                                <ShieldCheckIcon className="w-4 h-4 mr-1" />
                                {systemHealth.status === 'healthy' ? 'Saludable' : 
                                 systemHealth.status === 'warning' ? 'Advertencia' : 'Crítico'}
                            </span>
                        </div>
                    </div>
                    <div className="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label className="text-sm font-medium text-gray-600">Tiempo de actividad</label>
                            <p className="text-sm text-gray-900">{systemHealth.uptime}</p>
                        </div>
                        <div>
                            <label className="text-sm font-medium text-gray-300">Último respaldo</label>
                            <p className="text-sm text-gray-900">{systemHealth.lastBackup}</p>
                        </div>
                    </div>
                </div>
            </div>

            {/* Quick Stats */}
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div className="bg-white overflow-hidden shadow rounded-lg">
                    <div className="p-5">
                        <div className="flex items-center">
                            <div className="flex-shrink-0">
                                <UserGroupIcon className="h-6 w-6 text-blue-400" />
                            </div>
                            <div className="ml-5 w-0 flex-1">
                                <dl>
                                    <dt className="text-sm font-medium text-gray-600 truncate">
                                        Total Usuarios
                                    </dt>
                                    <dd className="text-lg font-medium text-gray-900">
                                        {stats.totalUsers}
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
                                <DocumentTextIcon className="h-6 w-6 text-green-400" />
                            </div>
                            <div className="ml-5 w-0 flex-1">
                                <dl>
                                    <dt className="text-sm font-medium text-gray-600 truncate">
                                        Jugadores
                                    </dt>
                                    <dd className="text-lg font-medium text-gray-900">
                                        {stats.totalPlayers}
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
                                <TrophyIcon className="h-6 w-6 text-yellow-400" />
                            </div>
                            <div className="ml-5 w-0 flex-1">
                                <dl>
                                    <dt className="text-sm font-medium text-gray-600 truncate">
                                        Torneos
                                    </dt>
                                    <dd className="text-lg font-medium text-gray-900">
                                        {stats.totalTournaments}
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
                                <CalendarIcon className="h-6 w-6 text-purple-400" />
                            </div>
                            <div className="ml-5 w-0 flex-1">
                                <dl>
                                    <dt className="text-sm font-medium text-gray-600 truncate">
                                        Partidos
                                    </dt>
                                    <dd className="text-lg font-medium text-gray-900">
                                        {stats.activeMatches}
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {/* Additional Stats */}
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div className="bg-white overflow-hidden shadow rounded-lg">
                    <div className="p-5">
                        <div className="flex items-center">
                            <div className="flex-shrink-0">
                                <UserGroupIcon className="h-6 w-6 text-indigo-400" />
                            </div>
                            <div className="ml-5 w-0 flex-1">
                                <dl>
                                    <dt className="text-sm font-medium text-gray-600 truncate">
                                        Entrenadores
                                    </dt>
                                    <dd className="text-lg font-medium text-gray-900">
                                        {stats.totalCoaches}
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
                                <ShieldCheckIcon className="h-6 w-6 text-red-400" />
                            </div>
                            <div className="ml-5 w-0 flex-1">
                                <dl>
                                    <dt className="text-sm font-medium text-gray-600 truncate">
                                        Árbitros
                                    </dt>
                                    <dd className="text-lg font-medium text-gray-900">
                                        {stats.totalReferees}
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
                                <DocumentTextIcon className="h-6 w-6 text-orange-400" />
                            </div>
                            <div className="ml-5 w-0 flex-1">
                                <dl>
                                    <dt className="text-sm font-medium text-gray-600 truncate">
                                        Clubes
                                    </dt>
                                    <dd className="text-lg font-medium text-gray-900">
                                        {stats.totalClubs}
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
                                <UserGroupIcon className="h-6 w-6 text-pink-400" />
                            </div>
                            <div className="ml-5 w-0 flex-1">
                                <dl>
                                    <dt className="text-sm font-medium text-gray-300 truncate">
                                        Equipos
                                    </dt>
                                    <dd className="text-lg font-medium text-gray-900">
                                        {stats.totalTeams}
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {/* Recent Activity */}
            <div className="bg-white shadow rounded-lg">
                <div className="px-4 py-5 sm:p-6">
                    <h3 className="text-lg leading-6 font-medium text-gray-900 mb-4">
                        Actividad Reciente
                    </h3>
                    {recentActivity.length > 0 ? (
                        <div className="space-y-3">
                            {recentActivity.slice(0, 5).map((activity, index) => (
                                <div key={index} className="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg">
                                    <div className="flex-shrink-0">
                                        <div className="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center">
                                            <DocumentTextIcon className="w-4 h-4 text-indigo-600" />
                                        </div>
                                    </div>
                                    <div className="flex-1 min-w-0">
                                        <p className="text-sm font-medium text-gray-900">
                                            {activity.description || 'Actividad del sistema'}
                                        </p>
                                        <p className="text-sm text-gray-300">
                                            {activity.timestamp || 'Hace unos momentos'}
                                        </p>
                                    </div>
                                </div>
                            ))}
                        </div>
                    ) : (
                        <p className="text-gray-300 text-center py-4">
                            No hay actividad reciente para mostrar.
                        </p>
                    )}
                </div>
            </div>

            {/* Admin Actions */}
            <div className="bg-white shadow rounded-lg">
                <div className="px-4 py-5 sm:p-6">
                    <h3 className="text-lg leading-6 font-medium text-gray-900 mb-4">
                        Acciones de Administración
                    </h3>
                    <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <Link
                            href="/admin/users"
                            className="bg-indigo-50 hover:bg-indigo-100 p-4 rounded-lg text-center transition-colors"
                        >
                            <UserGroupIcon className="h-8 w-8 text-indigo-600 mx-auto mb-2" />
                            <p className="text-sm font-medium text-indigo-900">Gestionar Usuarios</p>
                        </Link>
                        
                        <Link
                            href="/admin/tournaments"
                            className="bg-green-50 hover:bg-green-100 p-4 rounded-lg text-center transition-colors"
                        >
                            <TrophyIcon className="h-8 w-8 text-green-600 mx-auto mb-2" />
                            <p className="text-sm font-medium text-green-900">Torneos</p>
                        </Link>
                        
                        <Link
                            href="/admin/reports"
                            className="bg-yellow-50 hover:bg-yellow-100 p-4 rounded-lg text-center transition-colors"
                        >
                            <ChartBarIcon className="h-8 w-8 text-yellow-600 mx-auto mb-2" />
                            <p className="text-sm font-medium text-yellow-900">Reportes</p>
                        </Link>
                        
                        <Link
                            href="/admin/settings"
                            className="bg-purple-50 hover:bg-purple-100 p-4 rounded-lg text-center transition-colors"
                        >
                            <CogIcon className="h-8 w-8 text-purple-600 mx-auto mb-2" />
                            <p className="text-sm font-medium text-purple-900">Configuración</p>
                        </Link>
                        
                        <Link
                            href="/admin/clubs"
                            className="bg-red-50 hover:bg-red-100 p-4 rounded-lg text-center transition-colors"
                        >
                            <DocumentTextIcon className="h-8 w-8 text-red-600 mx-auto mb-2" />
                            <p className="text-sm font-medium text-red-900">Clubes</p>
                        </Link>
                        
                        <Link
                            href="/admin/matches"
                            className="bg-orange-50 hover:bg-orange-100 p-4 rounded-lg text-center transition-colors"
                        >
                            <CalendarIcon className="h-8 w-8 text-orange-600 mx-auto mb-2" />
                            <p className="text-sm font-medium text-orange-900">Partidos</p>
                        </Link>
                        
                        <Link
                            href="/admin/backup"
                            className="bg-pink-50 hover:bg-pink-100 p-4 rounded-lg text-center transition-colors"
                        >
                            <ClipboardDocumentListIcon className="h-8 w-8 text-pink-600 mx-auto mb-2" />
                            <p className="text-sm font-medium text-pink-900">Respaldos</p>
                        </Link>
                        
                        <Link
                            href="/admin/system"
                            className="bg-gray-50 hover:bg-gray-100 p-4 rounded-lg text-center transition-colors"
                        >
                            <ShieldCheckIcon className="h-8 w-8 text-gray-600 mx-auto mb-2" />
                            <p className="text-sm font-medium text-gray-900">Sistema</p>
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    );
}