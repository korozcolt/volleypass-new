import React from 'react';
import { Head, useForm } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { User } from '@/types/global';
import { UserIcon, EnvelopeIcon, PhoneIcon, MapPinIcon, CalendarIcon } from '@heroicons/react/24/outline';
import { ProfileSkeleton, UserSkeleton } from '@/Components/Skeleton';

interface ProfileProps {
    user: User;
    status?: string;
}

export default function Profile({ user, status }: ProfileProps) {
    const { data, setData, patch, processing, errors } = useForm({
        name: user.name || '',
        email: user.email || '',
        phone: '',
        address: '',
        birth_date: ''
    });

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        patch('/profile');
    };

    return (
        <div className="min-h-screen bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900">
            <Head title="Mi Perfil" />
            
            {/* Header */}
            <div className="relative bg-gradient-to-r from-slate-800 to-slate-700 py-16">
                <div className="absolute inset-0 bg-black/20"></div>
                <div className="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="text-center">
                        <UserIcon className="mx-auto h-16 w-16 text-yellow-400 mb-4" />
                        <h1 className="text-4xl font-bold text-white mb-2">Mi Perfil</h1>
                        <p className="text-xl text-gray-100">Gestiona tu información personal</p>
                    </div>
                </div>
            </div>
            
            <div className="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8">


                <div className="bg-gradient-to-br from-slate-800 to-slate-700 shadow-2xl rounded-2xl border border-slate-600">
                    <div className="px-4 py-5 sm:p-6">
                        <div className="flex items-center mb-6">
                            <div className="flex-shrink-0">
                                <div className="h-20 w-20 rounded-full bg-gradient-to-br from-yellow-400 to-yellow-500 flex items-center justify-center shadow-lg">
                                    <UserIcon className="h-10 w-10 text-slate-800" />
                                </div>
                            </div>
                            <div className="ml-6">
                                <h1 className="text-2xl font-bold text-white">{user.name}</h1>
                                <p className="text-sm text-yellow-400 capitalize font-medium">{user.roles?.[0]?.name || 'Usuario'}</p>
                                <p className="text-sm text-gray-100">{user.email}</p>
                            </div>
                        </div>

                        {status && (
                            <div className="mb-6 rounded-md bg-green-900/50 border border-green-600 p-4">
                                <div className="flex">
                                    <div className="flex-shrink-0">
                                        <svg className="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                            <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clipRule="evenodd" />
                                        </svg>
                                    </div>
                                    <div className="ml-3">
                                        <p className="text-sm font-medium text-green-200">
                                            {status}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        )}

                        <form onSubmit={submit} className="space-y-6">
                            <div className="grid grid-cols-1 gap-6 sm:grid-cols-2">
                                {/* Name */}
                                <div>
                                    <label htmlFor="name" className="block text-sm font-medium text-white">
                                        Nombre completo
                                    </label>
                                    <div className="mt-1 relative">
                                        <input
                                            type="text"
                                            id="name"
                                            name="name"
                                            className="block w-full px-3 py-2 pl-10 border border-slate-600 rounded-md shadow-sm bg-slate-700 text-white placeholder-gray-400 focus:outline-none focus:ring-yellow-500 focus:border-yellow-500 sm:text-sm"
                                            value={data.name}
                                            onChange={(e) => setData('name' as any, e.target.value)}
                                        />
                                        <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <UserIcon className="h-5 w-5 text-gray-400" />
                                        </div>
                                    </div>
                                    {errors.name && (
                                        <p className="mt-1 text-sm text-red-400">{errors.name}</p>
                                    )}
                                </div>

                                {/* Email */}
                                <div>
                                    <label htmlFor="email" className="block text-sm font-medium text-white">
                                        Correo electrónico
                                    </label>
                                    <div className="mt-1 relative">
                                        <input
                                            type="email"
                                            id="email"
                                            name="email"
                                            className="block w-full px-3 py-2 pl-10 border border-slate-600 rounded-md shadow-sm bg-slate-700 text-white placeholder-gray-400 focus:outline-none focus:ring-yellow-500 focus:border-yellow-500 sm:text-sm"
                                            value={data.email}
                                            onChange={(e) => setData('email' as any, e.target.value)}
                                        />
                                        <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <EnvelopeIcon className="h-5 w-5 text-gray-400" />
                                        </div>
                                    </div>
                                    {errors.email && (
                                        <p className="mt-1 text-sm text-red-400">{errors.email}</p>
                                    )}
                                </div>

                                {/* Phone */}
                                <div>
                                    <label htmlFor="phone" className="block text-sm font-medium text-white">
                                        Teléfono
                                    </label>
                                    <div className="mt-1 relative">
                                        <input
                                            type="tel"
                                            id="phone"
                                            name="phone"
                                            className="block w-full px-3 py-2 pl-10 border border-slate-600 rounded-md shadow-sm bg-slate-700 text-white placeholder-gray-400 focus:outline-none focus:ring-yellow-500 focus:border-yellow-500 sm:text-sm"
                                            value={data.phone}
                                            onChange={(e) => setData('phone' as any, e.target.value)}
                                        />
                                        <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <PhoneIcon className="h-5 w-5 text-gray-400" />
                                        </div>
                                    </div>
                                    {errors.phone && (
                                        <p className="mt-1 text-sm text-red-400">{errors.phone}</p>
                                    )}
                                </div>

                                {/* Birth Date */}
                                <div>
                                    <label htmlFor="birth_date" className="block text-sm font-medium text-white">
                                        Fecha de nacimiento
                                    </label>
                                    <div className="mt-1 relative">
                                        <input
                                            type="date"
                                            id="birth_date"
                                            name="birth_date"
                                            className="block w-full px-3 py-2 pl-10 border border-slate-600 rounded-md shadow-sm bg-slate-700 text-white placeholder-gray-400 focus:outline-none focus:ring-yellow-500 focus:border-yellow-500 sm:text-sm"
                                            value={data.birth_date}
                                            onChange={(e) => setData('birth_date' as any, e.target.value)}
                                        />
                                        <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <CalendarIcon className="h-5 w-5 text-gray-400" />
                                        </div>
                                    </div>
                                    {errors.birth_date && (
                                        <p className="mt-1 text-sm text-red-400">{errors.birth_date}</p>
                                    )}
                                </div>
                            </div>

                            {/* Address */}
                            <div>
                                <label htmlFor="address" className="block text-sm font-medium text-white">
                                    Dirección
                                </label>
                                <div className="mt-1 relative">
                                    <textarea
                                        id="address"
                                        name="address"
                                        rows={3}
                                        className="block w-full px-3 py-2 pl-10 border border-slate-600 rounded-md shadow-sm bg-slate-700 text-white placeholder-gray-400 focus:outline-none focus:ring-yellow-500 focus:border-yellow-500 sm:text-sm"
                                        value={data.address}
                                        onChange={(e) => setData('address' as any, e.target.value)}
                                    />
                                    <div className="absolute top-3 left-0 pl-3 flex items-start pointer-events-none">
                                        <MapPinIcon className="h-5 w-5 text-gray-400" />
                                    </div>
                                </div>
                                {errors.address && (
                                    <p className="mt-1 text-sm text-red-400">{errors.address}</p>
                                )}
                            </div>

                            <div className="flex justify-end">
                                <button
                                    type="submit"
                                    disabled={processing}
                                    className="inline-flex justify-center py-3 px-6 border border-transparent shadow-lg text-sm font-medium rounded-lg text-white bg-gradient-to-r from-yellow-500 to-yellow-600 hover:from-yellow-600 hover:to-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 focus:ring-offset-slate-800 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200"
                                >
                                    {processing ? 'Guardando...' : 'Guardar cambios'}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    );
}