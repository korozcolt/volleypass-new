import React, { useEffect, useState } from 'react';
import { Head, Link, useForm } from '@inertiajs/react';
import { PageProps } from '@/types/global';
import { User, Lock, Eye, EyeOff, ArrowRight, Shield, Zap, Trophy, Users } from 'lucide-react';

interface LoginProps extends PageProps {
    status?: string;
    canResetPassword: boolean;
}

export default function Login({ status, canResetPassword }: LoginProps) {
    const { data, setData, post, processing, errors, reset } = useForm({
        email: '',
        password: '',
        remember: '',
    });

    const [showPassword, setShowPassword] = useState(false);

    useEffect(() => {
        return () => {
            reset('password');
        };
    }, []);

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post('/login');
    };

    const demoUsers = [
        { role: 'Liga Admin', email: 'liga@volleypass.com', icon: '🏛️', color: 'text-blue-400' },
        { role: 'Director Club', email: 'club@volleypass.com', icon: '🏐', color: 'text-red-400' },
        { role: 'Entrenador', email: 'coach@volleypass.com', icon: '👨‍🏫', color: 'text-purple-400' },
        { role: 'Jugadora', email: 'jugadora@volleypass.com', icon: '⭐', color: 'text-green-400' }
    ];

    const features = [
        {
            icon: <Shield className="w-6 h-6 text-yellow-400" />,
            title: "Sistema Dual",
            description: "Gestión federada y descentralizada"
        },
        {
            icon: <Zap className="w-6 h-6 text-blue-400" />,
            title: "Verificación QR",
            description: "Validación instantánea en tiempo real"
        },
        {
            icon: <Trophy className="w-6 h-6 text-red-400" />,
            title: "Torneos Completos",
            description: "Desde inscripción hasta premiación"
        },
        {
            icon: <Users className="w-6 h-6 text-green-400" />,
            title: "Carnetización Digital",
            description: "Carnets con QR y renovación automática"
        }
    ];

    return (
        <>
            <Head title="Iniciar Sesión - VolleyPass" />

            <div className="min-h-screen bg-gradient-to-br from-slate-900 via-blue-900 to-slate-800 flex items-center justify-center p-4">
                {/* Background Pattern */}
                <div className="absolute inset-0 opacity-5">
                    <div
                        className="absolute inset-0"
                        style={{
                            backgroundImage: `url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.1'%3E%3Ccircle cx='30' cy='30' r='2'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E")`,
                            backgroundSize: '60px 60px'
                        }}
                    ></div>
                </div>

                <div className="w-full max-w-6xl mx-auto grid lg:grid-cols-2 gap-8 items-center relative z-10">
                    {/* Left Side - Branding & Features */}
                    <div className="hidden lg:block space-y-8">
                        {/* Logo & Title */}
                        <div className="text-center lg:text-left">
                            <div className="flex items-center justify-center lg:justify-start space-x-4 mb-6">
                                <div className="w-16 h-16 bg-white rounded-full flex items-center justify-center shadow-2xl">
                                    <span className="text-3xl">🏐</span>
                                </div>
                                <div>
                                    <h1 className="text-4xl font-black text-white tracking-tight">VolleyPass</h1>
                                    <p className="text-yellow-200 font-bold">Sistema de Manejo de Ligas</p>
                                </div>
                            </div>
                            <h2 className="text-3xl font-black text-white mb-4">
                                Plataforma Integral de
                                <span className="block text-yellow-400">Gestión Deportiva</span>
                            </h2>
                            <p className="text-xl text-white leading-relaxed">
                                Sistema de digitalización y carnetización deportiva que centraliza el registro,
                                verificación y gestión de jugadoras, entrenadores y clubes.
                            </p>
                        </div>

                        {/* Features */}
                        <div className="grid grid-cols-2 gap-4">
                            {features.map((feature, index) => (
                                <div
                                    key={index}
                                    className="bg-slate-800/50 backdrop-blur-sm rounded-xl p-4 border border-slate-700 hover:border-slate-600 transition-all duration-200"
                                >
                                    <div className="flex items-center space-x-3 mb-2">
                                        {feature.icon}
                                        <h3 className="text-white font-bold text-sm">{feature.title}</h3>
                                    </div>
                                    <p className="text-white text-xs leading-relaxed">{feature.description}</p>
                                </div>
                            ))}
                        </div>

                        {/* Stats */}
                        <div className="flex justify-center lg:justify-start space-x-8">
                            <div className="text-center">
                                <div className="text-3xl font-mono font-black text-yellow-400">95%</div>
                                <div className="text-white font-bold text-sm">Completado</div>
                            </div>
                            <div className="text-center">
                                <div className="text-3xl font-mono font-black text-blue-400">45+</div>
                                <div className="text-white font-bold text-sm">Tablas BD</div>
                            </div>
                            <div className="text-center">
                                <div className="text-3xl font-mono font-black text-red-400">13+</div>
                                <div className="text-white font-bold text-sm">Resources</div>
                            </div>
                        </div>
                    </div>

                    {/* Right Side - Login Form */}
                    <div className="w-full max-w-md mx-auto">
                        {/* Mobile Logo */}
                        <div className="lg:hidden text-center mb-8">
                            <div className="flex items-center justify-center space-x-3 mb-4">
                                <div className="w-12 h-12 bg-white rounded-full flex items-center justify-center shadow-xl">
                                    <span className="text-2xl">🏐</span>
                                </div>
                                <div>
                                    <h1 className="text-3xl font-black text-white">VolleyPass</h1>
                                    <p className="text-yellow-200 text-sm font-bold">Liga de Voleibol Sucre</p>
                                </div>
                            </div>
                        </div>

                        {/* Login Card */}
                        <div className="bg-white/95 backdrop-blur-sm rounded-2xl shadow-2xl p-8 border border-white/20">
                            <div className="text-center mb-8">
                                <h3 className="text-3xl font-black text-gray-800 mb-2">Iniciar Sesión</h3>
                                <p className="text-gray-600">Accede a tu cuenta VolleyPass</p>
                            </div>

                            {status && (
                                <div className="mb-6 rounded-lg bg-green-50 p-4 border border-green-200">
                                    <div className="text-sm text-green-700 flex items-center space-x-2">
                                        <span>✅</span>
                                        <span>{status}</span>
                                    </div>
                                </div>
                            )}

                            <form onSubmit={handleSubmit} className="space-y-6">
                                {/* Email Field */}
                                <div>
                                    <label htmlFor="email" className="block text-sm font-bold text-gray-700 mb-2">
                                        Correo Electrónico
                                    </label>
                                    <div className="relative">
                                        <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <User className="h-5 w-5 text-gray-400" />
                                        </div>
                                        <input
                                            type="email"
                                            id="email"
                                            name="email"
                                            value={data.email}
                                            onChange={(e) => setData('email', e.target.value)}
                                            className={`block w-full pl-10 pr-3 py-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 ${
                                                errors.email ? 'border-red-500 bg-red-50' : 'border-gray-300 bg-white hover:border-gray-400'
                                            }`}
                                            placeholder="tu@email.com"
                                            autoComplete="email"
                                            required
                                        />
                                    </div>
                                    {errors.email && (
                                        <p className="mt-1 text-sm text-red-600 flex items-center space-x-1">
                                            <span>⚠️</span>
                                            <span>{errors.email}</span>
                                        </p>
                                    )}
                                </div>

                                {/* Password Field */}
                                <div>
                                    <label htmlFor="password" className="block text-sm font-bold text-gray-700 mb-2">
                                        Contraseña
                                    </label>
                                    <div className="relative">
                                        <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <Lock className="h-5 w-5 text-gray-400" />
                                        </div>
                                        <input
                                            type={showPassword ? 'text' : 'password'}
                                            id="password"
                                            name="password"
                                            value={data.password}
                                            onChange={(e) => setData('password', e.target.value)}
                                            className={`block w-full pl-10 pr-12 py-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 ${
                                                errors.password ? 'border-red-500 bg-red-50' : 'border-gray-300 bg-white hover:border-gray-400'
                                            }`}
                                            placeholder="••••••••"
                                            autoComplete="current-password"
                                            required
                                        />
                                        <button
                                            type="button"
                                            onClick={() => setShowPassword(!showPassword)}
                                            className="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 transition-colors duration-200"
                                        >
                                            {showPassword ? <EyeOff className="h-5 w-5" /> : <Eye className="h-5 w-5" />}
                                        </button>
                                    </div>
                                    {errors.password && (
                                        <p className="mt-1 text-sm text-red-600 flex items-center space-x-1">
                                            <span>⚠️</span>
                                            <span>{errors.password}</span>
                                        </p>
                                    )}
                                </div>

                                {/* Remember & Forgot Password */}
                                <div className="flex items-center justify-between">
                                    <div className="flex items-center">
                                        <input
                                            id="remember"
                                            name="remember"
                                            type="checkbox"
                                            checked={!!data.remember}
                                            onChange={(e) => setData('remember', e.target.checked ? '1' : '')}
                                            className="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                        />
                                        <label htmlFor="remember" className="ml-2 block text-sm text-gray-900 font-medium">
                                            Recordarme
                                        </label>
                                    </div>

                                    {canResetPassword && (
                                        <div className="text-sm">
                                            <Link
                                                href="/forgot-password"
                                                className="font-medium text-blue-600 hover:text-blue-800 transition-colors duration-200"
                                            >
                                                ¿Olvidaste tu contraseña?
                                            </Link>
                                        </div>
                                    )}
                                </div>

                                {/* Submit Button */}
                                <button
                                    type="submit"
                                    disabled={processing}
                                    className="w-full bg-gradient-to-r from-yellow-400 via-blue-600 to-red-600 text-white font-bold py-3 px-4 rounded-lg hover:from-yellow-500 hover:via-blue-700 hover:to-red-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 transform hover:scale-[1.02] disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none flex items-center justify-center space-x-2"
                                >
                                    {processing ? (
                                        <>
                                            <div className="w-5 h-5 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
                                            <span>Iniciando sesión...</span>
                                        </>
                                    ) : (
                                        <>
                                            <span>Iniciar Sesión</span>
                                            <ArrowRight className="w-5 h-5" />
                                        </>
                                    )}
                                </button>
                            </form>

                            {/* Demo Users */}
                            <div className="mt-8 pt-6 border-t border-gray-200">
                                <p className="text-center text-sm font-bold text-gray-700 mb-4">👨‍💻 Usuarios de Prueba:</p>
                                <div className="grid grid-cols-2 gap-2">
                                    {demoUsers.map((user, index) => (
                                        <button
                                            key={index}
                                            onClick={() => setData({ email: user.email, password: 'password', remember: '' })}
                                            className="text-left p-2 rounded-lg bg-gray-50 hover:bg-gray-100 transition-colors duration-200 border border-gray-200 hover:border-gray-300"
                                        >
                                            <div className="flex items-center space-x-2">
                                                <span className="text-lg">{user.icon}</span>
                                                <div>
                                                    <p className={`text-xs font-bold ${user.color}`}>{user.role}</p>
                                                    <p className="text-xs text-gray-600 truncate">{user.email}</p>
                                                </div>
                                            </div>
                                        </button>
                                    ))}
                                </div>
                                <p className="text-center text-xs text-gray-500 mt-3">
                                    Contraseña para todos: <code className="bg-gray-200 px-1 rounded">password</code>
                                </p>
                            </div>

                            {/* Back to Home */}
                            <div className="mt-6 text-center">
                                <Link
                                    href="/"
                                    className="text-blue-600 hover:text-blue-800 font-medium text-sm transition-colors duration-200"
                                >
                                    ← Volver al inicio
                                </Link>
                            </div>
                        </div>

                        {/* Footer Note */}
                        <div className="text-center mt-6">
                            <p className="text-gray-400 text-sm">🔒 Acceso seguro con autenticación Laravel</p>
                            <p className="text-gray-500 text-xs mt-1">¿No tienes cuenta? El registro es interno únicamente</p>
                        </div>
                    </div>
                </div>
            </div>
        </>
    );
}
