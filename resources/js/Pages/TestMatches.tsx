import React from 'react';
import { Head } from '@inertiajs/react';

interface TestMatchesProps {
    user?: any;
    matches: any[];
}

export default function TestMatches({ user, matches }: TestMatchesProps) {
    return (
        <>
            <Head title="Test Partidos" />
            <div className="min-h-screen bg-gradient-to-br from-slate-900 via-blue-900 to-slate-800 p-8">
                <h1 className="text-4xl font-black text-white mb-4">Test Partidos</h1>
                <p className="text-white mb-4">Usuario: {user ? user.name : 'No autenticado'}</p>
                <p className="text-white mb-4">Partidos encontrados: {matches.length}</p>

                {matches.length > 0 && (
                    <div className="bg-slate-800 rounded-lg p-4">
                        <h2 className="text-white text-xl mb-4">Primeros 3 partidos:</h2>
                        {matches.slice(0, 3).map((match, index) => (
                            <div key={index} className="text-white mb-2">
                                <p>Partido {index + 1}: {match.home_team?.name || 'TBD'} vs {match.away_team?.name || 'TBD'}</p>
                                <p>Estado: {match.status}</p>
                                <p>Fecha: {match.scheduled_at}</p>
                                <hr className="my-2 border-slate-600" />
                            </div>
                        ))}
                    </div>
                )}
            </div>
        </>
    );
}
