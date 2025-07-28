import { Head } from '@inertiajs/react';
import { PageProps } from '@/types/global';
import AppLayout from '@/Layouts/AppLayout';
import PlayerDashboard from '@/Components/Dashboards/PlayerDashboard';
import CoachDashboard from '@/Components/Dashboards/CoachDashboard';
import RefereeDashboard from '@/Components/Dashboards/RefereeDashboard';
import AdminDashboard from '@/Components/Dashboards/AdminDashboard';
import { ChartBarIcon, BoltIcon, TrophyIcon, HeartIcon, UserIcon, MegaphoneIcon, CogIcon } from '@heroicons/react/24/outline';
import { DashboardCardSkeleton, StatsSkeleton, UserSkeleton } from '@/Components/Skeleton';

interface DashboardProps extends PageProps {
    userRole: string;
    dashboardData: {
        player?: any;
        coach?: any;
        referee?: any;
        admin?: any;
    };
}

export default function Dashboard({ auth, userRole, dashboardData }: DashboardProps) {
    const renderDashboard = () => {
        switch (userRole) {
            case 'player':
                return <PlayerDashboard user={auth.user} data={dashboardData.player} />;
            case 'coach':
                return <CoachDashboard user={auth.user} data={dashboardData.coach} />;
            case 'referee':
                return <RefereeDashboard user={auth.user} data={dashboardData.referee} />;
            case 'admin':
                return <AdminDashboard user={auth.user} data={dashboardData.admin} />;
            default:
                return (
                    <div className="text-center py-12">
                        <div className="text-gray-400 text-6xl mb-4">👤</div>
                        <h3 className="text-lg font-medium text-white mb-2">
                            Rol no reconocido
                        </h3>
                        <p className="text-gray-100">
                            No se pudo determinar tu rol en el sistema. Contacta al administrador.
                        </p>
                    </div>
                );
        }
    };

    const getDashboardTitle = () => {
        switch (userRole) {
            case 'player':
                return 'Dashboard - Jugador';
            case 'coach':
                return 'Dashboard - Entrenador';
            case 'referee':
                return 'Dashboard - Árbitro';
            case 'admin':
                return 'Dashboard - Administrador';
            default:
                return 'Dashboard';
        }
    };

    const getRoleIcon = () => {
        switch (userRole) {
            case 'player':
                return <UserIcon className="w-8 h-8 text-yellow-400" />;
            case 'coach':
                return <MegaphoneIcon className="w-8 h-8 text-blue-400" />;
            case 'referee':
                return <BoltIcon className="w-8 h-8 text-green-400" />;
            case 'admin':
                return <CogIcon className="w-8 h-8 text-purple-400" />;
            default:
                return <UserIcon className="w-8 h-8 text-gray-400" />;
        }
    };

    const getRoleDescription = () => {
        switch (userRole) {
            case 'player':
                return 'Panel de control para jugadores';
            case 'coach':
                return 'Panel de control para entrenadores';
            case 'referee':
                return 'Panel de control para árbitros';
            case 'admin':
                return 'Panel de administración del sistema';
            default:
                return 'Panel de control';
        }
    };

    return (
        <AppLayout user={auth.user}>
            <Head title={getDashboardTitle()} />
            <div className="min-h-screen bg-gradient-to-br from-slate-900 via-blue-900 to-slate-800">
                {/* Welcome Header */}
                <div className="relative mb-8">
                    <div 
                        className="h-48 bg-gradient-to-r from-yellow-400 via-blue-600 to-red-600 relative overflow-hidden"
                        style={{
                            backgroundImage: `linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.4)), url('https://images.pexels.com/photos/1103844/pexels-photo-1103844.jpeg?auto=compress&cs=tinysrgb&w=1260&h=300&dpr=2')`,
                            backgroundSize: 'cover',
                            backgroundPosition: 'center'
                        }}
                    >
                        <div className="absolute inset-0 bg-gradient-to-r from-black/60 to-transparent"></div>
                        <div className="absolute bottom-6 left-6 flex items-end space-x-6">
                            <div className="w-32 h-32 rounded-full overflow-hidden border-4 border-white shadow-2xl bg-gradient-to-br from-yellow-400 to-blue-600 flex items-center justify-center">
                                <span className="text-white font-black text-4xl">
                                    {auth.user.name.charAt(0).toUpperCase()}
                                </span>
                            </div>
                            <div className="text-white pb-2">
                                <h1 className="text-4xl font-black mb-2 flex items-center space-x-3">
                                    <span>¡Bienvenido, {auth.user.name}!</span>
                                    {getRoleIcon()}
                                </h1>
                                <p className="text-xl font-semibold mb-1 text-yellow-200">
                                    {getRoleDescription()}
                                </p>
                                <p className="text-lg text-gray-200 flex items-center space-x-2">
                                    <span className="text-2xl">🏐</span>
                                    <span>VolleyPass - Liga de Voleibol Sucre</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div className="container mx-auto px-4 pb-12">


                    {/* Dashboard Content */}
                    <div className="bg-gradient-to-br from-slate-800 to-slate-700 rounded-2xl p-8 shadow-2xl border border-slate-600">
                        {renderDashboard()}
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}