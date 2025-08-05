import React from 'react';
import { createRoot } from 'react-dom/client';
import '../css/app.css';

interface User {
    id: number;
    name: string;
    document_number: string;
}

interface Club {
    id: number;
    name: string;
    logo?: string;
}

interface League {
    id: number;
    name: string;
    short_name: string;
    foundation_date?: string;
    logo?: string;
}

interface Player {
    id: number;
    user: User;
    position?: string;
    category?: string;
    birth_date?: string;
    blood_type?: string;
    currentClub?: Club;
}

interface PlayerCard {
    id: number;
    card_number: string;
    player: Player;
    league: League;
    status: string;
    issued_at: string;
    expires_at: string;
}

interface PlayerCardComponentProps {
    card: PlayerCard;
}

const PlayerCardComponent: React.FC<PlayerCardComponentProps> = ({ card }) => {
    const formatDate = (dateString: string) => {
        return new Date(dateString).toLocaleDateString('es-ES', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric'
        });
    };

    const formatNumber = (number: string) => {
        return number.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    };

    const getPositionLabel = (position: string) => {
        const positions: { [key: string]: string } = {
            'setter': 'Armadora/Colocadora',
            'outside_hitter': 'Atacante Exterior',
            'middle_blocker': 'Bloqueadora Central',
            'opposite': 'Opuesta',
            'libero': 'Líbero',
            'defensive_specialist': 'Especialista Defensiva'
        };
        return positions[position] || position;
    };

    const getCategoryLabel = (category: string) => {
        const categories: { [key: string]: string } = {
            'mini': 'Mini (8-10 años)',
            'pre_mini': 'Pre-Mini (11-12 años)',
            'infantil': 'Infantil (13-14 años)',
            'cadete': 'Cadete (15-16 años)',
            'juvenil': 'Juvenil (17-18 años)',
            'mayores': 'Mayores (19+ años)',
            'masters': 'Masters (35+ años)'
        };
        return categories[category] || category;
    };

    const getStatusLabel = (status: string) => {
        const statuses: { [key: string]: string } = {
            'active': 'Activo',
            'inactive': 'Inactivo',
            'suspended': 'Suspendido',
            'expired': 'Expirado'
        };
        return statuses[status] || status;
    };

    // Generar código QR más convencional
    const generateQRCode = (text: string) => {
        const size = 80;
        const modules = 25; // QR más denso
        const moduleSize = size / modules;
        
        // Patrón más realista de QR
        const pattern = Array(modules).fill(0).map(() => 
            Array(modules).fill(0).map(() => Math.random() > 0.45 ? 1 : 0)
        );
        
        // Esquinas de posicionamiento (3 esquinas)
        const addPositionPattern = (startX: number, startY: number) => {
            for (let i = 0; i < 7; i++) {
                for (let j = 0; j < 7; j++) {
                    if (startX + i < modules && startY + j < modules) {
                        if ((i === 0 || i === 6 || j === 0 || j === 6) || (i >= 2 && i <= 4 && j >= 2 && j <= 4)) {
                            pattern[startX + i][startY + j] = 1;
                        } else if (i >= 1 && i <= 5 && j >= 1 && j <= 5) {
                            pattern[startX + i][startY + j] = 0;
                        }
                    }
                }
            }
        };
        
        // Agregar patrones de posicionamiento
        addPositionPattern(0, 0); // Esquina superior izquierda
        addPositionPattern(0, modules - 7); // Esquina superior derecha
        addPositionPattern(modules - 7, 0); // Esquina inferior izquierda
        
        // Líneas de timing
        for (let i = 8; i < modules - 8; i++) {
            pattern[6][i] = i % 2 === 0 ? 1 : 0;
            pattern[i][6] = i % 2 === 0 ? 1 : 0;
        }
        
        return (
            <svg width={size} height={size} className="bg-white border border-gray-300 rounded">
                {pattern.map((row, i) => 
                    row.map((cell, j) => 
                        cell ? <rect key={`${i}-${j}`} x={j * moduleSize} y={i * moduleSize} width={moduleSize} height={moduleSize} fill="black" /> : null
                    )
                )}
            </svg>
        );
    };

    const handlePrint = () => {
        window.print();
    };

    const handleDownloadPDF = () => {
        window.location.href = `/card/${card.card_number}/download`;
    };

    return (
        <div className="min-h-screen bg-gray-100 py-8 px-4">
            {/* Botones de control */}
            <div className="fixed top-4 right-4 z-50 flex space-x-2 no-print">
                <button
                    onClick={handlePrint}
                    className="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg shadow-lg transition-colors duration-200 flex items-center space-x-2"
                >
                    <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    <span>Imprimir</span>
                </button>
                <button
                    onClick={handleDownloadPDF}
                    className="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg shadow-lg transition-colors duration-200 flex items-center space-x-2"
                >
                    <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span>PDF</span>
                </button>
            </div>

            <div className="max-w-md mx-auto">
                {/* Tarjeta del Jugador - Proporción 9:6 vertical */}
                <div className="relative bg-gradient-to-br from-green-600 via-green-700 to-green-800 rounded-2xl shadow-2xl overflow-hidden" style={{ aspectRatio: '6/9' }}>
                    {/* Patrón de fondo diagonal */}
                    <div className="absolute inset-0">
                        <div className="absolute top-0 left-0 w-full h-full opacity-10">
                            <div className="absolute top-0 right-0 w-32 h-32 bg-white transform rotate-45 translate-x-16 -translate-y-16"></div>
                            <div className="absolute bottom-0 left-0 w-24 h-24 bg-white transform rotate-45 -translate-x-12 translate-y-12"></div>
                        </div>
                    </div>

                    {/* Número de carnet horizontal arriba a la izquierda */}
                    <div className="absolute top-4 left-4 z-10">
                        <div className="text-white text-sm font-bold">
                            No.: {card.card_number}
                        </div>
                    </div>

                    {/* Logo de la liga arriba a la derecha */}
                    <div className="absolute top-4 right-4 z-10">
                        <div className="text-center">
                            <div className="text-white text-xs font-bold text-center leading-tight">
                                {card.league.short_name}<br/>
                                {card.league.name}<br/>
                                <span className="bg-green-800 px-1 rounded text-xs">
                                    {card.league.foundation_date ? new Date(card.league.foundation_date).getFullYear() : new Date().getFullYear()}
                                </span>
                            </div>
                        </div>
                    </div>

                    {/* Foto de la jugadora en el centro - más grande */}
                    <div className="absolute top-16 left-1/2 transform -translate-x-1/2 z-10">
                        <div className="w-40 h-48 bg-gradient-to-b from-blue-400 to-blue-600 rounded-lg shadow-lg flex items-end justify-center overflow-hidden">
                            {/* Simulación de foto de jugadora */}
                            <div className="w-full h-full bg-gradient-to-t from-blue-800 to-blue-400 flex items-end justify-center">
                                <div className="text-white text-5xl font-bold mb-6">
                                    {card.player.user.name.charAt(0)}
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Tipo de sangre */}
                    <div className="absolute top-28 left-4 z-10">
                        <div className="bg-white rounded-full px-3 py-1 shadow-lg">
                            <span className="text-black text-sm font-bold">{card.player.blood_type || 'RH: O+'}</span>
                        </div>
                    </div>



                    {/* Logo del club en el área roja */}
                    <div className="absolute top-72 left-1/2 transform -translate-x-1/2 z-10">
                        <div className="w-20 h-20 bg-white rounded-full border-4 border-green-800 flex items-center justify-center shadow-lg">
                            <svg className="w-12 h-12 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                            </svg>
                        </div>
                    </div>

                    {/* Información de la jugadora */}
                    <div className="absolute bottom-0 left-0 right-0 z-10 p-4">
                        
                        {/* Nombre */}
                        <div className="text-center mb-3">
                            <h1 className="text-white text-xl font-bold leading-tight">
                                {card.player.user.name.toUpperCase()}
                            </h1>
                        </div>

                        {/* Información personal */}
                        <div className="text-white text-sm mb-3 text-center">
                            <div>ID: {formatNumber(card.player.user.document_number)} - SINCELEJO/SUCRE/COL</div>
                            {card.player.birth_date && (
                                <div>FECHA NACIMIENTO.: {formatDate(card.player.birth_date)}</div>
                            )}
                        </div>

                        {/* Club */}
                        {card.player.currentClub && (
                            <div className="text-center mb-4">
                                <div className="text-white text-lg font-bold">
                                    CLUB DEPORTIVO
                                </div>
                                <div className="text-white text-xl font-bold">
                                    {card.player.currentClub.name.toUpperCase()}
                                </div>
                            </div>
                        )}

                        {/* Información del carnet y QR */}
                        <div className="flex justify-between items-end">
                            <div className="flex-1">
                                <div className="text-white text-sm mb-2">
                                    <div className="mb-1">
                                        <span className="opacity-75">Estado: </span>
                                        <span className="font-bold">{getStatusLabel(card.status)}</span>
                                    </div>
                                    <div className="mb-1">
                                        <span className="opacity-75">Emisión: </span>
                                        <span className="font-bold">{formatDate(card.issued_at)}</span>
                                    </div>
                                    <div className="mb-1">
                                        <span className="opacity-75">Vencimiento: </span>
                                        <span className="font-bold">{formatDate(card.expires_at)}</span>
                                    </div>
                                    {card.player.position && (
                                        <div className="mb-1">
                                            <span className="opacity-75">Posición: </span>
                                            <span className="font-bold">{getPositionLabel(card.player.position)}</span>
                                        </div>
                                    )}
                                    {card.player.category && (
                                        <div className="mb-1">
                                            <span className="opacity-75">Categoría: </span>
                                            <span className="font-bold">{getCategoryLabel(card.player.category)}</span>
                                        </div>
                                    )}
                                </div>
                            </div>
                            
                            {/* Código QR */}
                            <div className="ml-4">
                                {generateQRCode(card.card_number)}
                            </div>
                        </div>

                        {/* Footer */}
                        <div className="flex justify-between items-center mt-3">
                            <div className="text-white text-xs">
                                <div className="flex items-center">
                                    <span>SEGUIMOS CONSTRUYENDO</span>
                                    <svg className="w-4 h-4 ml-1" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                                    </svg>
                                </div>
                                <div>VOLEIBOL PARA SUCRE</div>
                            </div>
                            
                            <div className="text-white text-xs font-bold">
                                LVS
                            </div>
                        </div>
                    </div>

                    {/* Fecha de vencimiento en la parte inferior */}
                    <div className="absolute bottom-2 left-1/2 transform -translate-x-1/2 z-10">
                        <div className="text-white text-xs font-bold whitespace-nowrap bg-black bg-opacity-50 px-2 py-1 rounded">
                            VENCE: {formatDate(card.expires_at)}
                        </div>
                    </div>
                </div>
            </div>

            {/* Estilos para impresión */}
            <style dangerouslySetInnerHTML={{
                __html: `
                    @media print {
                        body { margin: 0; }
                        .no-print { display: none !important; }
                        .min-h-screen { min-height: auto !important; }
                        .py-8 { padding-top: 0 !important; padding-bottom: 0 !important; }
                        .px-4 { padding-left: 0 !important; padding-right: 0 !important; }
                    }
                `
            }} />
        </div>
    );
};

// Inicializar la aplicación
const container = document.getElementById('player-card-root');
if (container) {
    const cardData = container.getAttribute('data-card');
    if (cardData) {
        const card = JSON.parse(cardData);
        const root = createRoot(container);
        root.render(<PlayerCardComponent card={card} />);
    }
}