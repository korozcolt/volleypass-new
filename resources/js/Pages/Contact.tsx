import { useState } from 'react';
import { Head, useForm } from '@inertiajs/react';
import { PageProps } from '@/types/global';
import AppLayout from '@/Layouts/AppLayout';
import { EnvelopeIcon, PhoneIcon, MapPinIcon } from '@heroicons/react/24/outline';

interface ContactProps extends PageProps {
    systemConfig: {
        contact_email?: string;
        contact_phone?: string;
        contact_address?: string;
        app_name: string;
    };
}

export default function Contact({ auth, systemConfig, flash }: ContactProps) {
    const { data, setData, post, processing, errors, reset } = useForm({
        name: '',
        email: '',
        subject: '',
        message: ''
    });

    const [submitted, setSubmitted] = useState(false);

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post('/contact', {
            onSuccess: () => {
                setSubmitted(true);
                reset();
            }
        });
    };

    return (
        <AppLayout user={auth.user}>
            <Head title="Contacto" />
            <div className="min-h-screen bg-gradient-to-br from-slate-900 via-blue-900 to-slate-800">
            
            {/* Header */}
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
                        <div className="w-32 h-32 rounded-full overflow-hidden border-4 border-white shadow-2xl bg-gradient-to-br from-green-500 to-blue-500 flex items-center justify-center">
                            <EnvelopeIcon className="w-16 h-16 text-white" />
                        </div>
                        <div className="text-white pb-2">
                            <h1 className="text-4xl font-black mb-2 flex items-center space-x-3">
                                <span>Contáctanos</span>
                                <EnvelopeIcon className="w-8 h-8 text-green-400" />
                            </h1>
                            <p className="text-xl font-semibold mb-1 text-yellow-200">
                                ¿Tienes alguna pregunta o necesitas ayuda? Estamos aquí para ayudarte.
                            </p>
                            <p className="text-lg text-gray-100 flex items-center space-x-2">
                                <span className="text-2xl">🏐</span>
                                <span>Liga de Voleibol Sucre</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div className="container mx-auto px-4 pb-12">
                <div className="max-w-6xl mx-auto">

                <div className="grid grid-cols-1 lg:grid-cols-2 gap-12">
                    {/* Contact Information */}
                    <div className="bg-gradient-to-br from-slate-800 to-slate-700 rounded-2xl p-8 shadow-2xl border border-slate-600">
                        <h2 className="text-2xl font-bold text-white mb-6">Información de Contacto</h2>
                        
                        <div className="space-y-6">
                            {systemConfig.contact_email && (
                                <div className="flex items-start space-x-4">
                                    <div className="flex-shrink-0">
                                        <EnvelopeIcon className="w-6 h-6 text-yellow-400" />
                                    </div>
                                    <div>
                                        <h3 className="text-lg font-medium text-white">Email</h3>
                                        <p className="text-gray-100">{systemConfig.contact_email}</p>
                                        <a 
                                            href={`mailto:${systemConfig.contact_email}`}
                                            className="text-yellow-400 hover:text-yellow-300 font-medium"
                                        >
                                            Enviar email
                                        </a>
                                    </div>
                                </div>
                            )}

                            {systemConfig.contact_phone && (
                                <div className="flex items-start space-x-4">
                                    <div className="flex-shrink-0">
                                        <PhoneIcon className="w-6 h-6 text-blue-400" />
                                    </div>
                                    <div>
                                        <h3 className="text-lg font-medium text-white">Teléfono</h3>
                                        <p className="text-gray-100">{systemConfig.contact_phone}</p>
                                        <a 
                                            href={`tel:${systemConfig.contact_phone}`}
                                            className="text-blue-400 hover:text-blue-300 font-medium"
                                        >
                                            Llamar ahora
                                        </a>
                                    </div>
                                </div>
                            )}

                            {systemConfig.contact_address && (
                                <div className="flex items-start space-x-4">
                                    <div className="flex-shrink-0">
                                        <MapPinIcon className="w-6 h-6 text-green-400" />
                                    </div>
                                    <div>
                                        <h3 className="text-lg font-medium text-white">Dirección</h3>
                                        <p className="text-gray-100">{systemConfig.contact_address}</p>
                                    </div>
                                </div>
                            )}
                        </div>

                        {/* Additional Info */}
                        <div className="mt-8 p-6 bg-indigo-50 rounded-lg">
                            <h3 className="text-lg font-medium text-indigo-900 mb-2">Horarios de Atención</h3>
                            <div className="text-indigo-700 space-y-1">
                                <p>Lunes a Viernes: 9:00 AM - 6:00 PM</p>
                                <p>Sábados: 9:00 AM - 2:00 PM</p>
                                <p>Domingos: Cerrado</p>
                            </div>
                        </div>
                    </div>

                    {/* Contact Form */}
                    <div className="bg-gradient-to-br from-slate-800 to-slate-700 rounded-2xl p-8 shadow-2xl border border-slate-600">
                        <h2 className="text-2xl font-bold text-white mb-6">Envíanos un Mensaje</h2>
                        
                        {submitted && (
                            <div className="mb-6 p-4 bg-green-900/50 border border-green-600 rounded-lg">
                                <div className="flex">
                                    <div className="text-green-400">
                                        <svg className="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clipRule="evenodd" />
                                        </svg>
                                    </div>
                                    <div className="ml-3">
                                        <p className="text-green-200 font-medium">¡Mensaje enviado exitosamente!</p>
                                        <p className="text-green-300 text-sm mt-1">Te responderemos lo antes posible.</p>
                                    </div>
                                </div>
                            </div>
                        )}

                        {flash?.success && (
                            <div className="mb-6 p-4 bg-green-900/50 border border-green-600 rounded-lg">
                                <p className="text-green-200">{flash.success}</p>
                            </div>
                        )}

                        {flash?.error && (
                            <div className="mb-6 p-4 bg-red-900/50 border border-red-600 rounded-lg">
                                <p className="text-red-200">{flash.error}</p>
                            </div>
                        )}

                        <form onSubmit={handleSubmit} className="space-y-6">
                            <div>
                                <label htmlFor="name" className="block text-sm font-medium text-white mb-2">
                                    Nombre Completo *
                                </label>
                                <input
                                    type="text"
                                    id="name"
                                    value={data.name}
                                    onChange={(e) => setData('name', e.target.value)}
                                    className={`w-full px-3 py-2 border rounded-lg shadow-sm bg-slate-700 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 ${
                                        errors.name ? 'border-red-500' : 'border-slate-600'
                                    }`}
                                    required
                                />
                                {errors.name && (
                                    <p className="mt-1 text-sm text-red-400">{errors.name}</p>
                                )}
                            </div>

                            <div>
                                <label htmlFor="email" className="block text-sm font-medium text-white mb-2">
                                    Email *
                                </label>
                                <input
                                    type="email"
                                    id="email"
                                    value={data.email}
                                    onChange={(e) => setData('email', e.target.value)}
                                    className={`w-full px-3 py-2 border rounded-lg shadow-sm bg-slate-700 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 ${
                                        errors.email ? 'border-red-500' : 'border-slate-600'
                                    }`}
                                    required
                                />
                                {errors.email && (
                                    <p className="mt-1 text-sm text-red-400">{errors.email}</p>
                                )}
                            </div>

                            <div>
                                <label htmlFor="subject" className="block text-sm font-medium text-white mb-2">
                                    Asunto *
                                </label>
                                <input
                                    type="text"
                                    id="subject"
                                    value={data.subject}
                                    onChange={(e) => setData('subject', e.target.value)}
                                    className={`w-full px-3 py-2 border rounded-lg shadow-sm bg-slate-700 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 ${
                                        errors.subject ? 'border-red-500' : 'border-slate-600'
                                    }`}
                                    required
                                />
                                {errors.subject && (
                                    <p className="mt-1 text-sm text-red-400">{errors.subject}</p>
                                )}
                            </div>

                            <div>
                                <label htmlFor="message" className="block text-sm font-medium text-white mb-2">
                                    Mensaje *
                                </label>
                                <textarea
                                    id="message"
                                    rows={6}
                                    value={data.message}
                                    onChange={(e) => setData('message', e.target.value)}
                                    className={`w-full px-3 py-2 border rounded-lg shadow-sm bg-slate-700 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 ${
                                        errors.message ? 'border-red-500' : 'border-slate-600'
                                    }`}
                                    required
                                />
                                {errors.message && (
                                    <p className="mt-1 text-sm text-red-400">{errors.message}</p>
                                )}
                            </div>

                            <div>
                                <button
                                    type="submit"
                                    disabled={processing}
                                    className={`w-full py-3 px-4 rounded-lg font-medium transition-colors ${
                                        processing
                                            ? 'bg-gray-600 cursor-not-allowed'
                                            : 'bg-gradient-to-r from-yellow-500 to-yellow-600 hover:from-yellow-600 hover:to-yellow-700 focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 focus:ring-offset-slate-800'
                                    } text-white shadow-lg`}
                                >
                                    {processing ? 'Enviando...' : 'Enviar Mensaje'}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                </div>
            </div>
            </div>
        </AppLayout>
    );
}