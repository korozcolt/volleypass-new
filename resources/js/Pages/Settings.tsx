import React from 'react';
import { Head, useForm } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { User } from '@/types/global';
import { CogIcon, BellIcon, ShieldCheckIcon } from '@heroicons/react/24/outline';

interface SettingsProps {
    user: User;
    settings?: {
        app_name: string;
        app_timezone: string;
        notifications_enabled: boolean;
        email_notifications: boolean;
        sms_notifications: boolean;
        maintenance_mode: boolean;
        registration_enabled: boolean;
        max_file_size: number;
    };
    status?: string;
}

export default function Settings({ user, settings, status }: SettingsProps) {
    const { data, setData, patch, processing, errors } = useForm({
        app_name: settings?.app_name || 'VolleyPass',
        app_timezone: settings?.app_timezone || 'America/Mexico_City',
        notifications_enabled: settings?.notifications_enabled || true,
        email_notifications: settings?.email_notifications || true,
        sms_notifications: settings?.sms_notifications || false,
        maintenance_mode: settings?.maintenance_mode || false,
        registration_enabled: settings?.registration_enabled || true,
        max_file_size: settings?.max_file_size || 10
    });

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        patch('/settings');
    };

    return (
        <AppLayout title="Configuración del Sistema" user={user}>
            <Head title="Configuración del Sistema" />
            
            <div className="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
                <div className="mb-8">
                    <h1 className="text-3xl font-bold text-gray-900">Configuración del Sistema</h1>
                    <p className="mt-2 text-gray-600">Administra la configuración general de la aplicación.</p>
                </div>

                {status && (
                    <div className="mb-6 rounded-md bg-green-50 p-4">
                        <div className="flex">
                            <div className="flex-shrink-0">
                                <svg className="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clipRule="evenodd" />
                                </svg>
                            </div>
                            <div className="ml-3">
                                <p className="text-sm font-medium text-green-800">
                                    {status}
                                </p>
                            </div>
                        </div>
                    </div>
                )}

                <form onSubmit={submit} className="space-y-8">
                    {/* General Settings */}
                    <div className="bg-white shadow rounded-lg">
                        <div className="px-4 py-5 sm:p-6">
                            <div className="flex items-center mb-4">
                                <CogIcon className="h-6 w-6 text-gray-400 mr-2" />
                                <h3 className="text-lg font-medium text-gray-900">Configuración General</h3>
                            </div>
                            
                            <div className="grid grid-cols-1 gap-6 sm:grid-cols-2">
                                <div>
                                    <label htmlFor="app_name" className="block text-sm font-medium text-gray-700">
                                        Nombre de la aplicación
                                    </label>
                                    <input
                                        type="text"
                                        id="app_name"
                                        name="app_name"
                                        className="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                        value={data.app_name}
                                        onChange={(e) => setData('app_name' as any, e.target.value)}
                                    />
                                    {errors.app_name && (
                                        <p className="mt-1 text-sm text-red-600">{errors.app_name}</p>
                                    )}
                                </div>

                                <div>
                                    <label htmlFor="app_timezone" className="block text-sm font-medium text-gray-700">
                                        Zona horaria
                                    </label>
                                    <select
                                        id="app_timezone"
                                        name="app_timezone"
                                        className="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                        value={data.app_timezone}
                                        onChange={(e) => setData('app_timezone' as any, e.target.value)}
                                    >
                                        <option value="America/Mexico_City">Ciudad de México (GMT-6)</option>
                                        <option value="America/New_York">Nueva York (GMT-5)</option>
                                        <option value="America/Los_Angeles">Los Ángeles (GMT-8)</option>
                                        <option value="Europe/Madrid">Madrid (GMT+1)</option>
                                        <option value="UTC">UTC (GMT+0)</option>
                                    </select>
                                    {errors.app_timezone && (
                                        <p className="mt-1 text-sm text-red-600">{errors.app_timezone}</p>
                                    )}
                                </div>

                                <div>
                                    <label htmlFor="max_file_size" className="block text-sm font-medium text-gray-700">
                                        Tamaño máximo de archivo (MB)
                                    </label>
                                    <input
                                        type="number"
                                        id="max_file_size"
                                        name="max_file_size"
                                        min="1"
                                        max="100"
                                        className="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                        value={data.max_file_size}
                                        onChange={(e) => setData('max_file_size' as any, parseInt(e.target.value))}
                                    />
                                    {errors.max_file_size && (
                                        <p className="mt-1 text-sm text-red-600">{errors.max_file_size}</p>
                                    )}
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Notification Settings */}
                    <div className="bg-white shadow rounded-lg">
                        <div className="px-4 py-5 sm:p-6">
                            <div className="flex items-center mb-4">
                                <BellIcon className="h-6 w-6 text-gray-400 mr-2" />
                                <h3 className="text-lg font-medium text-gray-900">Configuración de Notificaciones</h3>
                            </div>
                            
                            <div className="space-y-4">
                                <div className="flex items-center">
                                    <input
                                        id="notifications_enabled"
                                        name="notifications_enabled"
                                        type="checkbox"
                                        className="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                                        checked={data.notifications_enabled}
                                        onChange={(e) => setData('notifications_enabled' as any, e.target.checked)}
                                    />
                                    <label htmlFor="notifications_enabled" className="ml-2 block text-sm text-gray-900">
                                        Habilitar notificaciones del sistema
                                    </label>
                                </div>

                                <div className="flex items-center">
                                    <input
                                        id="email_notifications"
                                        name="email_notifications"
                                        type="checkbox"
                                        className="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                                        checked={data.email_notifications}
                                        onChange={(e) => setData('email_notifications' as any, e.target.checked)}
                                    />
                                    <label htmlFor="email_notifications" className="ml-2 block text-sm text-gray-900">
                                        Notificaciones por correo electrónico
                                    </label>
                                </div>

                                <div className="flex items-center">
                                    <input
                                        id="sms_notifications"
                                        name="sms_notifications"
                                        type="checkbox"
                                        className="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                                        checked={data.sms_notifications}
                                        onChange={(e) => setData('sms_notifications' as any, e.target.checked)}
                                    />
                                    <label htmlFor="sms_notifications" className="ml-2 block text-sm text-gray-900">
                                        Notificaciones por SMS
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Security & Access Settings */}
                    <div className="bg-white shadow rounded-lg">
                        <div className="px-4 py-5 sm:p-6">
                            <div className="flex items-center mb-4">
                                <ShieldCheckIcon className="h-6 w-6 text-gray-400 mr-2" />
                                <h3 className="text-lg font-medium text-gray-900">Seguridad y Acceso</h3>
                            </div>
                            
                            <div className="space-y-4">
                                <div className="flex items-center">
                                    <input
                                        id="registration_enabled"
                                        name="registration_enabled"
                                        type="checkbox"
                                        className="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                                        checked={data.registration_enabled}
                                        onChange={(e) => setData('registration_enabled' as any, e.target.checked)}
                                    />
                                    <label htmlFor="registration_enabled" className="ml-2 block text-sm text-gray-900">
                                        Permitir registro de nuevos usuarios
                                    </label>
                                </div>

                                <div className="flex items-center">
                                    <input
                                        id="maintenance_mode"
                                        name="maintenance_mode"
                                        type="checkbox"
                                        className="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                                        checked={data.maintenance_mode}
                                        onChange={(e) => setData('maintenance_mode' as any, e.target.checked)}
                                    />
                                    <label htmlFor="maintenance_mode" className="ml-2 block text-sm text-gray-900">
                                        Modo de mantenimiento
                                    </label>
                                </div>
                                
                                {data.maintenance_mode && (
                                    <div className="ml-6 p-3 bg-yellow-50 border border-yellow-200 rounded-md">
                                        <p className="text-sm text-yellow-800">
                                            ⚠️ El modo de mantenimiento deshabilitará el acceso para todos los usuarios excepto administradores.
                                        </p>
                                    </div>
                                )}
                            </div>
                        </div>
                    </div>

                    <div className="flex justify-end">
                        <button
                            type="submit"
                            disabled={processing}
                            className="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            {processing ? 'Guardando...' : 'Guardar configuración'}
                        </button>
                    </div>
                </form>
            </div>
        </AppLayout>
    );
}