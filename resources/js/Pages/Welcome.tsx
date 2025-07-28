import { Head, Link } from '@inertiajs/react';
import { PageProps } from '@/types/global';
import AppLayout from '@/Layouts/AppLayout';

interface WelcomeProps extends PageProps {
    systemConfig: {
        app_name: string;
        app_description?: string;
        contact_email?: string;
        contact_phone?: string;
    };
    featuredMatches: any[];
    upcomingTournaments: any[];
}

export default function Welcome({ auth, systemConfig, featuredMatches, upcomingTournaments }: WelcomeProps) {
    return (
        <AppLayout user={auth?.user} title="Bienvenido">
            <Head title="Bienvenido" />
            
            {/* Hero Section */}
            <div className="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-lg shadow-lg p-8 mb-8 text-white">
                <div className="max-w-4xl mx-auto text-center">
                    <h1 className="text-4xl font-bold mb-4">
                        Bienvenido a {systemConfig.app_name}
                    </h1>
                    {systemConfig.app_description && (
                        <p className="text-xl mb-6 opacity-90">
                            {systemConfig.app_description}
                        </p>
                    )}
                    <div className="flex flex-col sm:flex-row gap-4 justify-center">
                        <Link
                            href="/live-matches"
                            className="bg-white text-indigo-600 px-6 py-3 rounded-lg font-semibold hover:bg-gray-100 transition-colors"
                        >
                            Ver Partidos en Vivo
                        </Link>
                        {!auth?.user && (
                            <Link
                                href="/login"
                                className="border-2 border-white text-white px-6 py-3 rounded-lg font-semibold hover:bg-white hover:text-indigo-600 transition-colors"
                            >
                                Iniciar Sesión
                            </Link>
                        )}
                    </div>
                </div>
            </div>

            {/* Featured Matches */}
            {featuredMatches.length > 0 && (
                <div className="mb-8">
                    <h2 className="text-2xl font-bold text-gray-900 mb-6">Partidos Destacados</h2>
                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        {featuredMatches.map((match) => (
                            <div key={match.id} className="bg-white rounded-lg shadow-md p-6">
                                <div className="flex justify-between items-center mb-4">
                                    <span className="text-sm text-gray-500">
                                        {new Date(match.scheduled_at).toLocaleDateString()}
                                    </span>
                                    <span className={`px-2 py-1 rounded-full text-xs font-semibold ${
                                        match.status === 'live' ? 'bg-red-100 text-red-800' :
                                        match.status === 'finished' ? 'bg-green-100 text-green-800' :
                                        'bg-gray-100 text-gray-800'
                                    }`}>
                                        {match.status === 'live' ? 'En Vivo' :
                                         match.status === 'finished' ? 'Finalizado' : 'Programado'}
                                    </span>
                                </div>
                                <div className="text-center">
                                    <div className="flex justify-between items-center">
                                        <div className="text-center">
                                            <p className="font-semibold">{match.home_team?.name}</p>
                                            {match.home_score !== null && (
                                                <p className="text-2xl font-bold text-indigo-600">{match.home_score}</p>
                                            )}
                                        </div>
                                        <div className="text-gray-700 font-bold">VS</div>
                                        <div className="text-center">
                                            <p className="font-semibold">{match.away_team?.name}</p>
                                            {match.away_score !== null && (
                                                <p className="text-2xl font-bold text-indigo-600">{match.away_score}</p>
                                            )}
                                        </div>
                                    </div>
                                </div>
                                {match.status === 'live' && (
                                    <div className="mt-4">
                                        <Link
                                            href={`/live-matches/${match.id}`}
                                            className="w-full bg-red-600 text-white py-2 px-4 rounded-lg hover:bg-red-700 transition-colors text-center block"
                                        >
                                            Ver en Vivo
                                        </Link>
                                    </div>
                                )}
                            </div>
                        ))}
                    </div>
                </div>
            )}

            {/* Upcoming Tournaments */}
            {upcomingTournaments.length > 0 && (
                <div className="mb-8">
                    <h2 className="text-2xl font-bold text-gray-900 mb-6">Próximos Torneos</h2>
                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        {upcomingTournaments.map((tournament) => (
                            <div key={tournament.id} className="bg-white rounded-lg shadow-md p-6">
                                <h3 className="text-lg font-semibold mb-2">{tournament.name}</h3>
                                {tournament.description && (
                                    <p className="text-gray-600 mb-4">{tournament.description}</p>
                                )}
                                <div className="text-sm text-gray-500">
                                    <p>Inicio: {new Date(tournament.start_date).toLocaleDateString()}</p>
                                    <p>Fin: {new Date(tournament.end_date).toLocaleDateString()}</p>
                                </div>
                                <div className="mt-4">
                                    <span className={`px-2 py-1 rounded-full text-xs font-semibold ${
                                        tournament.status === 'active' ? 'bg-green-100 text-green-800' :
                                        tournament.status === 'upcoming' ? 'bg-blue-100 text-blue-800' :
                                        'bg-gray-100 text-gray-800'
                                    }`}>
                                        {tournament.status === 'active' ? 'Activo' :
                                         tournament.status === 'upcoming' ? 'Próximo' : 'Finalizado'}
                                    </span>
                                </div>
                            </div>
                        ))}
                    </div>
                </div>
            )}

            {/* Call to Action */}
            <div className="bg-gray-50 rounded-lg p-8 text-center">
                <h2 className="text-2xl font-bold text-gray-900 mb-4">¿Necesitas más información?</h2>
                <p className="text-gray-600 mb-6">
                    Contáctanos para conocer más sobre nuestros servicios y cómo participar.
                </p>
                <Link
                    href="/contacto"
                    className="bg-indigo-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-indigo-700 transition-colors"
                >
                    Contactar
                </Link>
            </div>
        </AppLayout>
    );
}