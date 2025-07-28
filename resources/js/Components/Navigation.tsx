import { useState } from 'react';
import { Link, router } from '@inertiajs/react';
import { User } from '@/types/global';
import { 
    Bars3Icon, 
    XMarkIcon, 
    HomeIcon, 
    PlayIcon, 
    EnvelopeIcon, 
    ChartBarIcon,
    UserIcon,
    ArrowRightOnRectangleIcon
} from '@heroicons/react/24/outline';

interface NavigationProps {
    user?: User;
}

export default function Navigation({ user }: NavigationProps) {
    const [isOpen, setIsOpen] = useState(false);

    const handleLogout = () => {
        router.post('/logout');
    };

    const navigation = [
        { name: 'Inicio', href: '/', icon: HomeIcon },
        { name: 'Partidos en Vivo', href: '/live-matches', icon: PlayIcon },
        { name: 'Dashboard', href: '/dashboard', icon: ChartBarIcon },
        { name: 'Contacto', href: '/contacto', icon: EnvelopeIcon },
    ];

    return (
        <nav className="bg-gradient-to-r from-slate-900 via-purple-900 to-slate-900 shadow-lg border-b border-purple-500/20">
            <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div className="flex items-center justify-between h-16">
                    <div className="flex items-center">
                        <div className="flex-shrink-0">
                            <Link href="/" className="text-white font-bold text-xl hover:text-yellow-300 transition-all duration-300">
                                🏐 VolleyPass
                            </Link>
                        </div>
                        <div className="hidden md:block">
                            <div className="ml-10 flex items-baseline space-x-2">
                                {navigation.map((item) => {
                                    const IconComponent = item.icon;
                                    return (
                                        <Link
                                            key={item.name}
                                            href={item.href}
                                            className="text-gray-100 hover:bg-gradient-to-r hover:from-purple-600 hover:to-blue-600 hover:text-white px-3 py-2 rounded-lg text-sm font-medium transition-all duration-300 flex items-center space-x-2 group border border-transparent hover:border-purple-400"
                                        >
                                            <IconComponent className="w-4 h-4 group-hover:scale-110 transition-transform duration-300" />
                                            <span>{item.name}</span>
                                        </Link>
                                    );
                                })}
                            </div>
                        </div>
                    </div>
                    <div className="hidden md:block">
                        <div className="ml-4 flex items-center md:ml-6">
                            {user ? (
                                <div className="flex items-center space-x-4">
                                    <div className="flex items-center space-x-2 text-white bg-slate-800 px-3 py-1 rounded-lg border border-slate-600">
                                        <UserIcon className="w-4 h-4 text-yellow-400" />
                                        <span className="text-sm font-medium">{user.name}</span>
                                    </div>
                                    <button
                                        onClick={handleLogout}
                                        className="text-gray-100 hover:bg-gradient-to-r hover:from-red-600 hover:to-pink-600 hover:text-white px-3 py-2 rounded-lg text-sm font-medium transition-all duration-300 flex items-center space-x-2 group border border-transparent hover:border-red-400"
                                    >
                                        <ArrowRightOnRectangleIcon className="w-4 h-4 group-hover:scale-110 transition-transform duration-300" />
                                        <span>Cerrar Sesión</span>
                                    </button>
                                </div>
                            ) : (
                                <Link
                                    href="/login"
                                    className="text-white bg-gradient-to-r from-green-600 to-blue-600 hover:from-green-700 hover:to-blue-700 px-4 py-2 rounded-lg text-sm font-medium transition-all duration-300 flex items-center space-x-2 group shadow-lg"
                                >
                                    <UserIcon className="w-4 h-4 group-hover:scale-110 transition-transform duration-300" />
                                    <span>Iniciar Sesión</span>
                                </Link>
                            )}
                        </div>
                    </div>
                    <div className="-mr-2 flex md:hidden">
                        <button
                            onClick={() => setIsOpen(!isOpen)}
                            className="bg-gradient-to-r from-purple-600 to-blue-600 inline-flex items-center justify-center p-2 rounded-lg text-white hover:from-purple-700 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-slate-900 focus:ring-purple-500 transition-all duration-300"
                        >
                            {isOpen ? (
                                <XMarkIcon className="block h-6 w-6" aria-hidden="true" />
                            ) : (
                                <Bars3Icon className="block h-6 w-6" aria-hidden="true" />
                            )}
                        </button>
                    </div>
                </div>
            </div>

            {isOpen && (
                <div className="md:hidden bg-gradient-to-b from-slate-900/95 to-purple-900/95 backdrop-blur-md border-t border-purple-500/20">
                    <div className="px-2 pt-2 pb-3 space-y-2 sm:px-3">
                        {navigation.map((item) => {
                            const IconComponent = item.icon;
                            return (
                                <Link
                                    key={item.name}
                                    href={item.href}
                                    className="text-gray-100 hover:bg-gradient-to-r hover:from-purple-600 hover:to-blue-600 hover:text-white block px-3 py-2 rounded-lg text-base font-medium transition-all duration-300 flex items-center space-x-3 border border-transparent hover:border-purple-400"
                                    onClick={() => setIsOpen(false)}
                                >
                                    <IconComponent className="w-5 h-5" />
                                    <span>{item.name}</span>
                                </Link>
                            );
                        })}
                        {user ? (
                            <>
                                <div className="flex items-center space-x-3 px-3 py-2 text-white bg-slate-800 rounded-lg border border-slate-600 mx-3">
                                    <UserIcon className="w-5 h-5 text-yellow-400" />
                                    <span className="text-base font-medium">{user.name}</span>
                                </div>
                                <button
                                    onClick={() => {
                                        handleLogout();
                                        setIsOpen(false);
                                    }}
                                    className="text-gray-100 hover:bg-gradient-to-r hover:from-red-600 hover:to-pink-600 hover:text-white block px-3 py-2 rounded-lg text-base font-medium w-full text-left transition-all duration-300 flex items-center space-x-3 border border-transparent hover:border-red-400"
                                >
                                    <ArrowRightOnRectangleIcon className="w-5 h-5" />
                                    <span>Cerrar Sesión</span>
                                </button>
                            </>
                        ) : (
                            <Link
                                href="/login"
                                className="text-white bg-gradient-to-r from-green-600 to-blue-600 hover:from-green-700 hover:to-blue-700 block px-3 py-2 rounded-lg text-base font-medium transition-all duration-300 flex items-center space-x-3 shadow-lg border border-green-500"
                                onClick={() => setIsOpen(false)}
                            >
                                <UserIcon className="w-5 h-5" />
                                <span>Iniciar Sesión</span>
                            </Link>
                        )}
                    </div>
                </div>
            )}
        </nav>
    );
}