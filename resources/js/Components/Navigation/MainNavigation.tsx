import React from 'react';
import { Link } from '@inertiajs/react';
import { User } from '@/types/global';
import { Bell } from 'lucide-react';

interface MainNavigationProps {
    user?: User;
    currentRoute?: string;
}

export default function MainNavigation({ user, currentRoute }: MainNavigationProps) {
    const isActive = (route: string) => currentRoute === route;

    const getNavLinkClass = (route: string) => {
        return isActive(route)
            ? "text-white bg-white/20 px-3 py-2 rounded-lg font-semibold transition-colors duration-200"
            : "text-white hover:text-yellow-200 font-semibold transition-colors duration-200";
    };

    return (
        <header className="bg-gradient-to-r from-yellow-400 via-blue-600 to-red-600 shadow-2xl sticky top-0 z-50">
            <div className="container mx-auto px-4">
                <div className="flex items-center justify-between h-16">
                    {/* Logo */}
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

                    {/* Navigation Menu */}
                    <nav className="hidden lg:flex items-center space-x-8">
                        <Link
                            href="/partidos"
                            className={getNavLinkClass('/partidos')}
                        >
                            Partidos
                        </Link>
                        <Link
                            href="/torneos"
                            className={getNavLinkClass('/torneos')}
                        >
                            Torneos
                        </Link>
                        <Link
                            href="/live-matches"
                            className={getNavLinkClass('/live-matches')}
                        >
                            En Vivo
                        </Link>

                        {/* User-specific navigation */}
                        {user && (
                            <Link
                                href="/dashboard"
                                className={getNavLinkClass('/dashboard')}
                            >
                                Dashboard
                            </Link>
                        )}

                        {/* Admin-specific navigation */}
                        {user?.roles?.some(role => ['admin', 'liga_admin'].includes(role.name)) && (
                            <Link
                                href="/admin"
                                className={getNavLinkClass('/admin')}
                            >
                                Admin
                            </Link>
                        )}
                    </nav>

                    {/* Right side actions */}
                    <div className="flex items-center space-x-4">
                        <Link
                            href="/contacto"
                            className="text-white hover:text-yellow-200 font-semibold transition-colors duration-200"
                        >
                            Contacto
                        </Link>

                        {/* User actions */}
                        {user ? (
                            <div className="flex items-center space-x-4">
                                <button className="relative p-2 text-white hover:text-yellow-200 transition-colors duration-200">
                                    <Bell className="w-5 h-5" />
                                    <div className="absolute -top-1 -right-1 w-3 h-3 bg-red-500 rounded-full animate-pulse"></div>
                                </button>
                                <Link
                                    href="/logout"
                                    method="post"
                                    as="button"
                                    className="text-white hover:text-yellow-200 font-semibold transition-colors duration-200"
                                >
                                    Salir
                                </Link>
                            </div>
                        ) : (
                            <Link
                                href="/login"
                                className="bg-white/20 text-white px-4 py-2 rounded-lg font-semibold hover:bg-white/30 transition-colors duration-200"
                            >
                                Iniciar Sesión
                            </Link>
                        )}
                    </div>
                </div>
            </div>
        </header>
    );
}
