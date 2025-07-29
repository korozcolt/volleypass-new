import React, { useState } from 'react';
import { Link } from '@inertiajs/react';
import { User, Player, Match } from '@/types/global';
import {
    User as UserIcon,
    BarChart3,
    Activity,
    Heart,
    MessageCircle,
    MapPin,
    Trophy,
    Bell
} from 'lucide-react';

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

export default function PlayerDashboard({ user, data }: PlayerDashboardProps) {
    const { player, upcomingMatches, recentMatches, teamStats } = data;
    const [activeTab, setActiveTab] = useState('overview');

    // Verificar si el player existe
    if (!player) {
        return (
            <div className="text-center py-12">
                <div className="text-gray-400 text-6xl mb-4">👤</div>
                <h3 className="text-lg font-medium text-white mb-2">
                    Perfil de jugador no encontrado
                </h3>
                <p className="text-gray-300">
                    No se encontró un perfil de jugador asociado a tu cuenta. Contacta al administrador.
                </p>
            </div>
        );
    }

    // Calcular edad desde birth_date
    const calculateAge = (birthDate: string) => {
        const today = new Date();
        const birth = new Date(birthDate);
        let age = today.getFullYear() - birth.getFullYear();
        const monthDiff = today.getMonth() - birth.getMonth();
        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birth.getDate())) {
            age--;
        }
        return age;
    };

    const playerProfile = {
        id: player.id,
        name: user.name,
        number: player.jersey_number || 7,
        position: player.position || "Opuesta",
        height: player.height ? `${(player.height / 100).toFixed(2)}m` : "1.75m",
        age: calculateAge(player.birth_date),
        team: player.team?.name || "Equipo VolleyPass",
        city: "Sincelejo, Sucre",
        photo: "https://images.pexels.com/photos/8007513/pexels-photo-8007513.jpeg?auto=compress&cs=tinysrgb&w=300&h=300&dpr=2",
        stats: {
            points: teamStats.wins * 15 || 247,
            aces: Math.floor(teamStats.wins * 2.5) || 38,
            blocks: Math.floor(teamStats.wins * 3.2) || 52,
            receptions: Math.floor(teamStats.wins * 12) || 189,
            attackEfficiency: Math.min(68 + teamStats.wins, 95) || 68,
            receptionEfficiency: Math.min(85 + teamStats.wins, 98) || 92
        },
        achievements: ["Mejor Jugadora Liga 2024", "MVP Semifinal"],
        medicalStatus: "APTA",
        nextMatch: upcomingMatches[0] ? `vs ${upcomingMatches[0].away_team?.name || upcomingMatches[0].home_team?.name}` : "Sin partidos programados",
        recentMatches: recentMatches.slice(0, 4).map(match => ({
            opponent: match.home_team?.name === player.team?.name ? match.away_team?.name : match.home_team?.name,
            result: Math.random() > 0.5 ? "W" : "L",
            score: "3-1",
            date: new Date(match.scheduled_at).toLocaleDateString('es-ES', { day: 'numeric', month: 'short' }),
            points: Math.floor(Math.random() * 20) + 10
        }))
    };

    const notifications = [
        { id: 1, type: "match", message: "Próximo partido mañana a las 20:00", time: "2h", unread: true },
        { id: 2, type: "medical", message: "Certificado médico vence en 15 días", time: "1d", unread: true },
        { id: 3, type: "achievement", message: "¡Felicidades! Alcanzaste 250 puntos esta temporada", time: "3d", unread: false },
        { id: 4, type: "team", message: "Entrenamiento cancelado para mañana", time: "5d", unread: false }
    ];

    const tabs = [
        { id: 'overview', name: 'Resumen', icon: <BarChart3 className="w-5 h-5" /> },
        { id: 'stats', name: 'Estadísticas', icon: <Activity className="w-5 h-5" /> },
        { id: 'matches', name: 'Partidos', icon: <Trophy className="w-5 h-5" /> },
        { id: 'medical', name: 'Médico', icon: <Heart className="w-5 h-5" /> }
    ];

    return (
        <div className="min-h-screen bg-gradient-to-br from-slate-900 via-blue-900 to-slate-800">
            {/* Header */}
            <header className="bg-gradient-to-r from-yellow-400 via-blue-600 to-red-600 shadow-2xl sticky top-0 z-50">
                <div className="container mx-auto px-4">
                    <div className="flex items-center justify-between h-16">
                        <Link
                            href="/"
                            className="flex items-center space-x-3 hover:opacity-80 transition-opacity duration-200"
                        >
                            <div className="w-12 h-12 bg-white rounded-full flex items-center justify-center shadow-lg">
                                <span className="text-2xl">🏐</span>
                            </div>
                            <div>
                                <h1 className="text-2xl font-black text-white tracking-tight">VolleyPass</h1>
                                <p className="text-xs text-yellow-100 font-medium">Liga de Voleibol Sucre</p>
                            </div>
                        </Link>

                        <nav className="hidden lg:flex items-center space-x-8">
                            <Link
                                href="/matches"
                                className="text-white hover:text-yellow-200 font-semibold transition-colors duration-200"
                            >
                                Partidos
                            </Link>
                            <Link
                                href="/tournaments"
                                className="text-white hover:text-yellow-200 font-semibold transition-colors duration-200"
                            >
                                Torneos
                            </Link>
                            <Link
                                href="/dashboard"
                                className="text-white bg-white/20 px-3 py-2 rounded-lg font-semibold transition-colors duration-200"
                            >
                                Dashboard
                            </Link>
                        </nav>

                        <div className="flex items-center space-x-4">
                            <button className="relative p-2 text-white hover:text-yellow-200 transition-colors duration-200">
                                <Bell className="w-5 h-5" />
                                <div className="absolute -top-1 -right-1 w-3 h-3 bg-red-500 rounded-full animate-pulse"></div>
                            </button>
                            <Link
                                href="/logout"
                                method="post"
                                as="button"
                                className="p-2 text-white hover:text-yellow-200 transition-colors duration-200"
                            >
                                <UserIcon className="w-5 h-5" />
                            </Link>
                        </div>
                    </div>
                </div>
            </header>

            {/* Player Header */}
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
                        <div className="w-32 h-32 rounded-full overflow-hidden border-4 border-white shadow-2xl">
                            <img
                                src={playerProfile.photo}
                                alt={playerProfile.name}
                                className="w-full h-full object-cover"
                            />
                        </div>
                        <div className="text-white pb-2">
                            <h1 className="text-4xl font-black mb-2">
                                {playerProfile.name}
                                <span className="text-yellow-400">#{playerProfile.number}</span>
                            </h1>
                            <p className="text-xl font-semibold mb-1">
                                {playerProfile.position} • {playerProfile.height} • {playerProfile.age} años
                            </p>
                            <p className="text-lg text-yellow-200 flex items-center space-x-2">
                                <span className="text-2xl">🏐</span>
                                <span>{playerProfile.team}</span>
                            </p>
                            <p className="text-gray-300 flex items-center space-x-1 mt-1">
                                <MapPin className="w-4 h-4" />
                                <span>{playerProfile.city}</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div className="container mx-auto px-4 pb-12">
                {/* Navigation Tabs */}
                <div className="flex flex-wrap gap-2 mb-8">
                    {tabs.map((tab) => (
                        <button
                            key={tab.id}
                            onClick={() => setActiveTab(tab.id)}
                            className={`flex items-center space-x-2 px-6 py-3 rounded-lg font-bold transition-all duration-200 ${activeTab === tab.id
                                ? 'bg-gradient-to-r from-yellow-400 to-yellow-500 text-black shadow-lg'
                                : 'bg-slate-800 text-white hover:bg-slate-700'
                                }`}
                        >
                            {tab.icon}
                            <span>{tab.name}</span>
                        </button>
                    ))}
                </div>

                <div className="grid lg:grid-cols-3 gap-8">
                    {/* Main Content */}
                    <div className="lg:col-span-2 space-y-8">
                        {activeTab === 'overview' && (
                            <>
                                {/* Season Stats */}
                                <section className="bg-gradient-to-br from-slate-800 to-slate-700 rounded-2xl p-8 shadow-2xl border border-slate-600">
                                    <h2 className="text-3xl font-black text-white mb-8 flex items-center space-x-3">
                                        <BarChart3 className="w-8 h-8 text-yellow-400" />
                                        <span>TEMPORADA 2024</span>
                                    </h2>

                                    <div className="grid md:grid-cols-4 gap-6 mb-8">
                                        <div className="text-center">
                                            <div className="text-5xl font-mono font-black text-yellow-400 mb-2">
                                                {playerProfile.stats.points}
                                            </div>
                                            <div className="text-white font-bold text-lg mb-2">PUNTOS</div>
                                            <div className="w-full bg-slate-600 rounded-full h-3">
                                                <div className="bg-gradient-to-r from-yellow-400 to-yellow-500 h-3 rounded-full" style={{ width: '85%' }}></div>
                                            </div>
                                        </div>

                                        <div className="text-center">
                                            <div className="text-5xl font-mono font-black text-blue-400 mb-2">
                                                {playerProfile.stats.aces}
                                            </div>
                                            <div className="text-white font-bold text-lg mb-2">ACES</div>
                                            <div className="w-full bg-slate-600 rounded-full h-3">
                                                <div className="bg-gradient-to-r from-blue-400 to-blue-500 h-3 rounded-full" style={{ width: '70%' }}></div>
                                            </div>
                                        </div>

                                        <div className="text-center">
                                            <div className="text-5xl font-mono font-black text-red-400 mb-2">
                                                {playerProfile.stats.blocks}
                                            </div>
                                            <div className="text-white font-bold text-lg mb-2">BLOQUEOS</div>
                                            <div className="w-full bg-slate-600 rounded-full h-3">
                                                <div className="bg-gradient-to-r from-red-400 to-red-500 h-3 rounded-full" style={{ width: '90%' }}></div>
                                            </div>
                                        </div>

                                        <div className="text-center">
                                            <div className="text-5xl font-mono font-black text-green-400 mb-2">
                                                {playerProfile.stats.receptions}
                                            </div>
                                            <div className="text-white font-bold text-lg mb-2">RECEPCIONES</div>
                                            <div className="w-full bg-slate-600 rounded-full h-3">
                                                <div className="bg-gradient-to-r from-green-400 to-green-500 h-3 rounded-full" style={{ width: '95%' }}></div>
                                            </div>
                                        </div>
                                    </div>

                                    {/* Efficiency Stats */}
                                    <div className="grid md:grid-cols-2 gap-6">
                                        <div className="bg-slate-700/50 rounded-xl p-6">
                                            <h3 className="text-xl font-bold text-white mb-4 flex items-center space-x-2">
                                                <Activity className="w-5 h-5 text-yellow-400" />
                                                <span>Efectividad de Ataque</span>
                                            </h3>
                                            <div className="flex items-center space-x-4">
                                                <div className="text-4xl font-mono font-black text-yellow-400">
                                                    {playerProfile.stats.attackEfficiency}%
                                                </div>
                                                <div className="flex-1">
                                                    <div className="w-full bg-slate-600 rounded-full h-4">
                                                        <div
                                                            className="bg-gradient-to-r from-yellow-400 to-yellow-500 h-4 rounded-full transition-all duration-1000"
                                                            style={{ width: `${playerProfile.stats.attackEfficiency}%` }}
                                                        ></div>
                                                    </div>
                                                    <p className="text-gray-400 text-sm mt-2">Promedio Liga: 58%</p>
                                                </div>
                                            </div>
                                        </div>

                                        <div className="bg-slate-700/50 rounded-xl p-6">
                                            <h3 className="text-xl font-bold text-white mb-4 flex items-center space-x-2">
                                                <Activity className="w-5 h-5 text-green-400" />
                                                <span>Efectividad de Recepción</span>
                                            </h3>
                                            <div className="flex items-center space-x-4">
                                                <div className="text-4xl font-mono font-black text-green-400">
                                                    {playerProfile.stats.receptionEfficiency}%
                                                </div>
                                                <div className="flex-1">
                                                    <div className="w-full bg-slate-600 rounded-full h-4">
                                                        <div
                                                            className="bg-gradient-to-r from-green-400 to-green-500 h-4 rounded-full transition-all duration-1000"
                                                            style={{ width: `${playerProfile.stats.receptionEfficiency}%` }}
                                                        ></div>
                                                    </div>
                                                    <p className="text-gray-400 text-sm mt-2">Promedio Liga: 78%</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </section>

                                {/* Recent Matches */}
                                <section className="bg-gradient-to-br from-slate-800 to-slate-700 rounded-2xl p-8 shadow-2xl border border-slate-600">
                                    <h2 className="text-3xl font-black text-white mb-6 flex items-center space-x-3">
                                        <Trophy className="w-8 h-8 text-blue-400" />
                                        <span>ÚLTIMOS PARTIDOS</span>
                                    </h2>
                                    <div className="space-y-4">
                                        {playerProfile.recentMatches.map((match, index) => (
                                            <div key={index} className="flex items-center justify-between p-4 bg-slate-700/50 rounded-lg hover:bg-slate-700 transition-colors duration-200">
                                                <div className="flex items-center space-x-4">
                                                    <div className={`w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm ${match.result === 'W' ? 'bg-green-600 text-white' : 'bg-red-600 text-white'
                                                        }`}>
                                                        {match.result}
                                                    </div>
                                                    <div>
                                                        <p className="text-white font-bold">vs {match.opponent}</p>
                                                        <p className="text-gray-400 text-sm">{match.date}</p>
                                                    </div>
                                                </div>
                                                <div className="text-right">
                                                    <p className="text-white font-bold">{match.score}</p>
                                                    <p className="text-yellow-400 text-sm">{match.points} pts</p>
                                                </div>
                                            </div>
                                        ))}
                                    </div>
                                </section>
                            </>
                        )}

                        {activeTab === 'stats' && (
                            <section className="bg-gradient-to-br from-slate-800 to-slate-700 rounded-2xl p-8 shadow-2xl border border-slate-600">
                                <h2 className="text-3xl font-black text-white mb-8">ESTADÍSTICAS DETALLADAS</h2>
                                <div className="space-y-6">
                                    <div className="grid md:grid-cols-3 gap-6">
                                        <div className="text-center p-6 bg-slate-700/50 rounded-xl">
                                            <div className="text-3xl font-mono font-black text-yellow-400 mb-2">15.8</div>
                                            <div className="text-white font-bold">Puntos por Partido</div>
                                        </div>
                                        <div className="text-center p-6 bg-slate-700/50 rounded-xl">
                                            <div className="text-3xl font-mono font-black text-blue-400 mb-2">2.4</div>
                                            <div className="text-white font-bold">Aces por Partido</div>
                                        </div>
                                        <div className="text-center p-6 bg-slate-700/50 rounded-xl">
                                            <div className="text-3xl font-mono font-black text-red-400 mb-2">3.3</div>
                                            <div className="text-white font-bold">Bloqueos por Partido</div>
                                        </div>
                                    </div>
                                </div>
                            </section>
                        )}

                        {activeTab === 'matches' && (
                            <section className="bg-gradient-to-br from-slate-800 to-slate-700 rounded-2xl p-8 shadow-2xl border border-slate-600">
                                <h2 className="text-3xl font-black text-white mb-8">CALENDARIO DE PARTIDOS</h2>
                                <div className="space-y-4">
                                    {upcomingMatches.length > 0 ? (
                                        upcomingMatches.map((match) => (
                                            <div key={match.id} className="p-4 bg-blue-600/20 rounded-lg border border-blue-600/30">
                                                <div className="flex justify-between items-center">
                                                    <div>
                                                        <p className="text-blue-400 font-bold text-lg">PRÓXIMO PARTIDO</p>
                                                        <p className="text-white">{match.home_team?.name} vs {match.away_team?.name}</p>
                                                        <p className="text-gray-400 text-sm">
                                                            {new Date(match.scheduled_at).toLocaleDateString('es-ES', {
                                                                day: 'numeric',
                                                                month: 'short',
                                                                year: 'numeric',
                                                                hour: '2-digit',
                                                                minute: '2-digit'
                                                            })}
                                                        </p>
                                                    </div>
                                                    <div className="text-right">
                                                        <p className="text-gray-400 text-sm">Coliseo Municipal</p>
                                                        <button className="bg-blue-600 text-white px-4 py-2 rounded-lg font-bold mt-2">
                                                            Ver Detalles
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        ))
                                    ) : (
                                        <div className="text-center py-8">
                                            <p className="text-gray-400">No hay partidos programados</p>
                                        </div>
                                    )}
                                </div>
                            </section>
                        )}

                        {activeTab === 'medical' && (
                            <section className="bg-gradient-to-br from-slate-800 to-slate-700 rounded-2xl p-8 shadow-2xl border border-slate-600">
                                <h2 className="text-3xl font-black text-white mb-8">ESTADO MÉDICO</h2>
                                <div className="space-y-6">
                                    <div className="flex items-center space-x-3 bg-green-600/20 p-4 rounded-lg border border-green-600/30">
                                        <div className="w-4 h-4 bg-green-400 rounded-full animate-pulse"></div>
                                        <span className="text-green-400 font-bold text-lg">✅ {playerProfile.medicalStatus}</span>
                                    </div>
                                    <div className="grid md:grid-cols-2 gap-6">
                                        <div>
                                            <h3 className="text-white font-bold mb-2">Certificado Médico</h3>
                                            <p className="text-gray-400 text-sm">Válido hasta: 15 Dic 2024</p>
                                        </div>
                                        <div>
                                            <h3 className="text-white font-bold mb-2">Última Revisión</h3>
                                            <p className="text-gray-400 text-sm">10 Nov 2024</p>
                                        </div>
                                    </div>
                                </div>
                            </section>
                        )}
                    </div>

                    {/* Sidebar */}
                    <div className="space-y-8">
                        {/* Digital ID Card */}
                        <section className="bg-gradient-to-br from-slate-800 to-slate-700 rounded-2xl p-6 shadow-2xl border border-slate-600">
                            <h3 className="text-2xl font-black text-white mb-6 flex items-center space-x-2">
                                <UserIcon className="w-6 h-6 text-blue-400" />
                                <span>CARNET DIGITAL</span>
                            </h3>
                            <div className="bg-gradient-to-r from-yellow-400 via-blue-600 to-red-600 p-1 rounded-xl">
                                <div className="bg-white rounded-lg p-6">
                                    <div className="text-center">
                                        <div className="w-20 h-20 rounded-full overflow-hidden mx-auto mb-4 border-4 border-gray-200">
                                            <img
                                                src={playerProfile.photo}
                                                alt={playerProfile.name}
                                                className="w-full h-full object-cover"
                                            />
                                        </div>
                                        <h4 className="font-black text-gray-800 text-lg">{playerProfile.name}</h4>
                                        <p className="text-gray-600 font-semibold">#{playerProfile.number} • {playerProfile.position}</p>
                                        <p className="text-gray-500 text-sm">{playerProfile.team}</p>

                                        <div className="mt-4 p-4 bg-gray-100 rounded-lg">
                                            <div className="w-24 h-24 bg-gray-800 mx-auto rounded-lg flex items-center justify-center">
                                                <span className="text-white text-xs">QR CODE</span>
                                            </div>
                                            <p className="text-xs text-gray-500 mt-2">ID: VPS-{playerProfile.id.toString().padStart(4, '0')}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>

                        {/* Notifications */}
                        <section className="bg-gradient-to-br from-slate-800 to-slate-700 rounded-2xl p-6 shadow-2xl border border-slate-600">
                            <h3 className="text-2xl font-black text-white mb-6 flex items-center space-x-2">
                                <Bell className="w-6 h-6 text-yellow-400" />
                                <span>NOTIFICACIONES</span>
                            </h3>
                            <div className="space-y-3">
                                {notifications.slice(0, 4).map((notification) => (
                                    <div key={notification.id} className={`p-3 rounded-lg ${notification.unread ? 'bg-blue-600/20 border border-blue-600/30' : 'bg-slate-700/50'
                                        }`}>
                                        <p className="text-white text-sm">{notification.message}</p>
                                        <p className="text-gray-400 text-xs mt-1">{notification.time}</p>
                                    </div>
                                ))}
                            </div>
                        </section>

                        {/* Quick Actions */}
                        <section className="space-y-3">
                            <Link
                                href="/player/statistics"
                                className="w-full bg-gradient-to-r from-yellow-400 to-yellow-500 text-black font-bold py-3 px-4 rounded-lg hover:from-yellow-500 hover:to-yellow-600 transition-all duration-200 flex items-center justify-center space-x-2"
                            >
                                <BarChart3 className="w-5 h-5" />
                                <span>VER ESTADÍSTICAS COMPLETAS</span>
                            </Link>
                            <Link
                                href="/player/team"
                                className="w-full bg-gradient-to-r from-blue-600 to-blue-700 text-white font-bold py-3 px-4 rounded-lg hover:from-blue-700 hover:to-blue-800 transition-all duration-200 flex items-center justify-center space-x-2"
                            >
                                <MessageCircle className="w-5 h-5" />
                                <span>MENSAJE AL ENTRENADOR</span>
                            </Link>
                        </section>
                    </div>
                </div>
            </div>
        </div>
    );
}