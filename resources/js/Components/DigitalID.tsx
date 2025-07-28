import { Player, User } from '@/types/global';
import { QrCodeIcon, UserIcon, CalendarIcon, MapPinIcon } from '@heroicons/react/24/outline';

interface DigitalIDProps {
    player: Player & { user?: User };
}

export default function DigitalID({ player }: DigitalIDProps) {
    return (
        <div className="bg-gradient-to-br from-indigo-500 to-purple-600 rounded-lg shadow-lg overflow-hidden">
            <div className="px-6 py-8 text-white">
                {/* Header */}
                <div className="text-center mb-6">
                    <h3 className="text-lg font-semibold mb-1">Cédula Digital</h3>
                    <p className="text-indigo-100 text-sm">Jugador de Voleibol</p>
                </div>

                {/* Player Photo */}
                <div className="flex justify-center mb-6">
                    <div className="w-24 h-24 bg-white rounded-full flex items-center justify-center">
                        <UserIcon className="w-12 h-12 text-gray-400" />
                    </div>
                </div>

                {/* Player Info */}
                <div className="text-center mb-6">
                    <h4 className="text-xl font-bold mb-1">
                        {player.user?.name || 'Jugador'}
                    </h4>
                    <p className="text-indigo-100 text-sm mb-2">
                        {player.jersey_number ? `#${player.jersey_number}` : ''} {player.jersey_number ? '•' : ''} {player.position}
                    </p>
                    {player.team && (
                        <p className="text-indigo-100 text-sm">
                            {player.team?.name}
                        </p>
                    )}
                </div>

                {/* Player Details */}
                <div className="space-y-3 mb-6">
                    <div className="flex items-center text-sm">
                        <CalendarIcon className="w-4 h-4 mr-2 text-indigo-200" />
                        <span className="text-indigo-100">Nacimiento:</span>
                        <span className="ml-2 font-medium">
                            {player.birth_date ? new Date(player.birth_date).toLocaleDateString('es-ES') : 'No especificado'}
                        </span>
                    </div>
                    
                    <div className="flex items-center text-sm">
                        <MapPinIcon className="w-4 h-4 mr-2 text-indigo-200" />
                        <span className="text-indigo-100">Altura:</span>
                        <span className="ml-2 font-medium">
                            {player.height ? `${player.height} cm` : 'No especificada'}
                        </span>
                    </div>

                    <div className="flex items-center text-sm">
                        <UserIcon className="w-4 h-4 mr-2 text-indigo-200" />
                        <span className="text-indigo-100">ID:</span>
                        <span className="ml-2 font-medium font-mono">
                            {player.id.toString().padStart(6, '0')}
                        </span>
                    </div>
                </div>

                {/* QR Code Section */}
                <div className="border-t border-indigo-400 pt-4">
                    <div className="flex items-center justify-between">
                        <div>
                            <p className="text-xs text-indigo-100 mb-1">Código QR</p>
                            <p className="text-xs text-indigo-200">Para verificación</p>
                        </div>
                        <div className="w-12 h-12 bg-white rounded flex items-center justify-center">
                            <QrCodeIcon className="w-8 h-8 text-indigo-600" />
                        </div>
                    </div>
                </div>

                {/* Validity */}
                <div className="mt-4 text-center">
                    <p className="text-xs text-indigo-200">
                        Válido hasta: {new Date(Date.now() + 365 * 24 * 60 * 60 * 1000).toLocaleDateString('es-ES')}
                    </p>
                </div>
            </div>

            {/* Bottom stripe */}
            <div className="h-2 bg-gradient-to-r from-yellow-400 to-red-500"></div>
        </div>
    );
}