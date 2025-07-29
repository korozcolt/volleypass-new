import React from 'react';
import { Head, Link } from '@inertiajs/react';
import { PageProps } from '@/types/global';
import MainLayout from '@/Layouts/MainLayout';
import {
    PlayIcon,
    UsersIcon,
    TrophyIcon,
    ShieldCheckIcon,
    BoltIcon,
    CheckCircleIcon,
    ArrowRightIcon,
    StarIcon,
    GlobeAltIcon,
    CircleStackIcon,
    LockClosedIcon,
    ChartBarIcon
} from '@heroicons/react/24/outline';

interface HomePageProps extends PageProps {
    systemConfig: {
        app_name: string;
        app_description?: string;
        contact_email?: string;
        contact_phone?: string;
        contact_address?: string;
    };
    featuredMatches: any[];
    upcomingTournaments: any[];
}

export default function HomePage({ auth, systemConfig, featuredMatches, upcomingTournaments }: HomePageProps) {
    const features = [
        {
            icon: <ShieldCheckIcon className="w-8 h-8 text-yellow-400" />,
            title: "Sistema Dual",
            description: "Gestión federada y descentralizada"
        },
        {
            icon: <BoltIcon className="w-8 h-8 text-blue-400" />,
            title: "Verificación QR",
            description: "Validación instantánea en tiempo real"
        },
        {
            icon: <TrophyIcon className="w-8 h-8 text-red-400" />,
            title: "Torneos Completos",
            description: "Desde inscripción hasta premiación"
        },
        {
            icon: <UsersIcon className="w-8 h-8 text-green-400" />,
            title: "Carnetización Digital",
            description: "Carnets con QR y renovación automática"
        }
    ];

    const stats = [
        { number: "95%", label: "Completado", color: "text-green-400" },
        { number: "45+", label: "Tablas BD", color: "text-blue-400" },
        { number: "13+", label: "Resources", color: "text-yellow-400" },
        { number: "30+", label: "Configuraciones", color: "text-red-400" }
    ];

    const technologies = [
        { name: "Laravel", version: "12.x", color: "bg-red-600" },
        { name: "Filament", version: "3.x", color: "bg-yellow-600" },
        { name: "Livewire", version: "3.x", color: "bg-purple-600" },
        { name: "MySQL", version: "8.0+", color: "bg-blue-600" }
    ];

    return (
        <MainLayout title="Inicio" user={auth?.user} currentRoute="/">
            {/* Hero Section */}
            <section className="relative overflow-hidden">
                <div className="absolute inset-0 bg-gradient-to-r from-black/70 via-black/50 to-black/70"></div>
                <div
                    className="relative bg-cover bg-center min-h-[600px] flex items-center"
                    style={{
                        backgroundImage: `url('https://images.pexels.com/photos/1103844/pexels-photo-1103844.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=2')`
                    }}
                >
                    <div className="px-4">
                        <div className="max-w-4xl mx-auto">
                            <div className="flex items-center space-x-4 mb-8">
                                <div className="w-16 h-16 bg-white rounded-full flex items-center justify-center shadow-2xl">
                                    <span className="text-3xl">🏐</span>
                                </div>
                                <div>
                                    <h1 className="text-6xl lg:text-7xl font-black text-white tracking-tight">
                                        {systemConfig.app_name || 'VolleyPass'}
                                    </h1>
                                    <p className="text-xl text-yellow-200 font-bold">Liga de Voleibol de Sucre</p>
                                </div>
                            </div>

                            <h2 className="text-4xl lg:text-5xl font-black text-white mb-6 leading-tight">
                                Plataforma Integral de
                                <span className="block text-yellow-400">Gestión Deportiva</span>
                            </h2>

                            <p className="text-xl text-gray-200 mb-8 leading-relaxed max-w-3xl">
                                {systemConfig.app_description || 'Sistema de digitalización y carnetización deportiva que centraliza el registro, verificación y gestión de jugadoras, entrenadores y clubes federados y descentralizados.'}
                            </p>

                            <div className="flex flex-col sm:flex-row gap-4 mb-8">
                                <Link
                                    href="/live-matches"
                                    className="bg-gradient-to-r from-yellow-400 to-yellow-500 text-black px-8 py-4 rounded-lg font-bold text-lg hover:from-yellow-500 hover:to-yellow-600 transition-all duration-200 shadow-xl transform hover:scale-105 flex items-center justify-center space-x-2"
                                >
                                    <PlayIcon className="w-6 h-6" />
                                    <span>Ver Partidos en Vivo</span>
                                </Link>
                                <Link
                                    href="/torneos"
                                    className="bg-gradient-to-r from-blue-600 to-blue-700 text-white px-8 py-4 rounded-lg font-bold text-lg hover:from-blue-700 hover:to-blue-800 transition-all duration-200 shadow-xl transform hover:scale-105 flex items-center justify-center space-x-2"
                                >
                                    <TrophyIcon className="w-6 h-6" />
                                    <span>Explorar Torneos</span>
                                </Link>
                            </div>

                            <div className="inline-flex items-center space-x-2 bg-green-600/20 backdrop-blur-sm text-green-400 px-4 py-2 rounded-full border border-green-600/30">
                                <CheckCircleIcon className="w-5 h-5" />
                                <span className="font-bold">95% Completado - Listo para Producción</span>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {/* Stats Section */}
            <section className="py-16 bg-gradient-to-r from-slate-800 to-slate-700">
                <div className="px-4">
                    <div className="grid md:grid-cols-4 gap-8">
                        {stats.map((stat, index) => (
                            <div key={index} className="text-center">
                                <div className={`text-5xl font-mono font-black ${stat.color} mb-2`}>
                                    {stat.number}
                                </div>
                                <div className="text-white font-bold text-lg">{stat.label}</div>
                            </div>
                        ))}
                    </div>
                </div>
            </section>

            {/* Features Section */}
            <section className="py-20">
                <div className="px-4">
                    <div className="text-center mb-16">
                        <h3 className="text-4xl font-black text-white mb-4">
                            Características Principales
                        </h3>
                        <p className="text-xl text-white max-w-3xl mx-auto">
                            Una plataforma completa que revoluciona la gestión deportiva en Colombia
                        </p>
                    </div>

                    <div className="grid lg:grid-cols-3 gap-8">
                        {features.map((feature, index) => (
                            <div key={index} className="bg-gradient-to-br from-slate-800 to-slate-700 rounded-2xl p-8 shadow-2xl hover:shadow-3xl transition-all duration-300 transform hover:scale-105 border border-slate-600">
                                <div className="flex items-center space-x-4 mb-6">
                                    {feature.icon}
                                    <h4 className="text-2xl font-black text-white">{feature.title}</h4>
                                </div>
                                <p className="text-white leading-relaxed">{feature.description}</p>
                            </div>
                        ))}
                    </div>
                </div>
            </section>

            {/* Technology Stack */}
            <section className="py-16 bg-gradient-to-r from-slate-800 to-slate-700">
                <div className="px-4">
                    <div className="text-center mb-12">
                        <h3 className="text-3xl font-black text-white mb-4">
                            Tecnologías de Vanguardia
                        </h3>
                        <p className="text-lg text-white">
                            Construido con las mejores herramientas del ecosistema PHP
                        </p>
                    </div>

                    <div className="flex flex-wrap justify-center gap-6">
                        {technologies.map((tech, index) => (
                            <div key={index} className={`${tech.color} text-white px-6 py-3 rounded-lg font-bold text-lg shadow-lg`}>
                                {tech.name} {tech.version}
                            </div>
                        ))}
                    </div>
                </div>
            </section>

            {/* CTA Section */}
            <section className="py-20">
                <div className="px-4 text-center">
                    <div className="max-w-4xl mx-auto">
                        <h3 className="text-4xl font-black text-white mb-6">
                            ¿Listo para Digitalizar tu Liga?
                        </h3>
                        <p className="text-xl text-white mb-8">
                            Únete a la revolución deportiva digital con {systemConfig.app_name || 'VolleyPass'}
                        </p>

                        <div className="flex flex-col sm:flex-row gap-4 justify-center">
                            {auth?.user ? (
                                <Link
                                    href="/dashboard"
                                    className="bg-gradient-to-r from-yellow-400 to-yellow-500 text-black px-8 py-4 rounded-lg font-bold text-lg hover:from-yellow-500 hover:to-yellow-600 transition-all duration-200 shadow-xl transform hover:scale-105 flex items-center justify-center space-x-2"
                                >
                                    <UsersIcon className="w-6 h-6" />
                                    <span>Acceder al Dashboard</span>
                                </Link>
                            ) : (
                                <Link
                                    href="/login"
                                    className="bg-gradient-to-r from-yellow-400 to-yellow-500 text-black px-8 py-4 rounded-lg font-bold text-lg hover:from-yellow-500 hover:to-yellow-600 transition-all duration-200 shadow-xl transform hover:scale-105 flex items-center justify-center space-x-2"
                                >
                                    <UsersIcon className="w-6 h-6" />
                                    <span>Iniciar Sesión</span>
                                </Link>
                            )}
                            <Link
                                href="/contacto"
                                className="bg-gradient-to-r from-blue-600 to-blue-700 text-white px-8 py-4 rounded-lg font-bold text-lg hover:from-blue-700 hover:to-blue-800 transition-all duration-200 shadow-xl transform hover:scale-105 flex items-center justify-center space-x-2"
                            >
                                <ArrowRightIcon className="w-6 h-6" />
                                <span>Solicitar Demo</span>
                            </Link>
                        </div>
                    </div>
                </div>
            </section>

            {/* Footer */}
            <footer className="bg-black/50 backdrop-blur-sm py-12 border-t border-slate-700">
                <div className="px-4">
                    <div className="grid md:grid-cols-3 gap-8">
                        <div>
                            <div className="flex items-center space-x-3 mb-4">
                                <div className="w-10 h-10 bg-white rounded-full flex items-center justify-center">
                                    <span className="text-xl">🏐</span>
                                </div>
                                <div>
                                    <h4 className="text-xl font-black text-white">{systemConfig.app_name || 'VolleyPass'}</h4>
                                    <p className="text-sm text-gray-100">Liga de Voleibol Sucre</p>
                                </div>
                            </div>
                            <p className="text-white">
                                Digitalizando el deporte, fortaleciendo la comunidad
                            </p>
                        </div>

                        <div>
                            <h5 className="text-lg font-bold text-white mb-4">Contacto</h5>
                            <div className="space-y-2 text-white">
                                <p>📧 {systemConfig.contact_email || 'liga@volleypass.sucre.gov.co'}</p>
                                <p>📱 {systemConfig.contact_phone || '+57 (5) 282-5555'}</p>
                                <p>🏢 {systemConfig.contact_address || 'Cra. 25 #16-50, Sincelejo, Sucre'}</p>
                            </div>
                        </div>

                        <div>
                            <h5 className="text-lg font-bold text-white mb-4">Enlaces</h5>
                            <div className="space-y-2">
                                <Link
                                    href="/live-matches"
                                    className="block text-gray-100 hover:text-yellow-400 transition-colors duration-200"
                                >
                                    Partidos en Vivo
                                </Link>
                                <Link
                                    href="/torneos"
                                    className="block text-gray-400 hover:text-yellow-400 transition-colors duration-200"
                                >
                                    Torneos
                                </Link>
                                <Link
                                    href="/dashboard"
                                    className="block text-gray-400 hover:text-yellow-400 transition-colors duration-200"
                                >
                                    Dashboard
                                </Link>
                            </div>
                        </div>
                    </div>

                    <div className="border-t border-slate-700 mt-8 pt-8 text-center">
                        <p className="text-white">
                            © 2024 {systemConfig.app_name || 'VolleyPass'} Sucre. Desarrollado con ❤️ para el voleibol sucreño.
                        </p>
                    </div>
                </div>
            </footer>
        </MainLayout>
    );
}
