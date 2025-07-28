import { Link } from '@inertiajs/react';
import { Match } from '@/types/global';
import { ClockIcon, PlayIcon } from '@heroicons/react/24/outline';

interface LiveMatchCardProps {
    match: Match;
}

export default function LiveMatchCard({ match }: LiveMatchCardProps) {
    const getStatusColor = (status: string) => {
        switch (status) {
            case 'live':
                return 'bg-red-100 text-red-800';
            case 'finished':
                return 'bg-green-100 text-green-800';
            case 'upcoming':
                return 'bg-blue-100 text-blue-800';
            default:
                return 'bg-gray-100 text-gray-800';
        }
    };

    const getStatusText = (status: string) => {
        switch (status) {
            case 'live':
                return 'En Vivo';
            case 'finished':
                return 'Finalizado';
            case 'upcoming':
                return 'Próximo';
            default:
                return 'Programado';
        }
    };

    const formatDateTime = (dateString: string) => {
        const date = new Date(dateString);
        return {
            date: date.toLocaleDateString('es-ES', { 
                day: '2-digit', 
                month: '2-digit', 
                year: 'numeric' 
            }),
            time: date.toLocaleTimeString('es-ES', { 
                hour: '2-digit', 
                minute: '2-digit' 
            })
        };
    };

    const { date, time } = formatDateTime(match.scheduled_at);

    return (
        <div className={`bg-white rounded-lg shadow-md overflow-hidden transition-all duration-200 hover:shadow-lg ${
            match.status === 'live' ? 'ring-2 ring-red-500 ring-opacity-50' : ''
        }`}>
            {/* Header */}
            <div className="p-4 border-b border-gray-200">
                <div className="flex justify-between items-center">
                    <div className="text-sm text-gray-300">
                        <div className="flex items-center space-x-1">
                            <ClockIcon className="w-4 h-4" />
                            <span>{date} - {time}</span>
                        </div>
                        {match.tournament && (
                            <div className="mt-1 text-xs text-gray-300">
                                {match.tournament.name}
                            </div>
                        )}
                    </div>
                    <span className={`px-2 py-1 rounded-full text-xs font-semibold ${getStatusColor(match.status)}`}>
                        {match.status === 'live' && (
                            <span className="flex items-center space-x-1">
                                <span className="w-2 h-2 bg-red-500 rounded-full animate-pulse"></span>
                                <span>{getStatusText(match.status)}</span>
                            </span>
                        )}
                        {match.status !== 'live' && getStatusText(match.status)}
                    </span>
                </div>
            </div>

            {/* Teams and Score */}
            <div className="p-6">
                <div className="flex justify-between items-center">
                    {/* Home Team */}
                    <div className="text-center flex-1">
                        <div className="mb-2">
                            {match.home_team?.club?.logo && (
                                <img 
                                    src={match.home_team.club.logo} 
                                    alt={match.home_team.name}
                                    className="w-12 h-12 mx-auto mb-2 rounded-full object-cover"
                                />
                            )}
                            <h3 className="font-semibold text-gray-900 text-sm">
                                {match.home_team?.name || 'TBD'}
                            </h3>
                            {match.home_team?.club && (
                                <p className="text-xs text-gray-300">{match.home_team.club.name}</p>
                            )}
                        </div>
                        {(match.home_score !== null && match.home_score !== undefined) && (
                            <div className="text-3xl font-bold text-indigo-600">
                                {match.home_score}
                            </div>
                        )}
                    </div>

                    {/* VS Separator */}
                    <div className="px-4">
                        <div className="text-gray-400 font-bold text-lg">VS</div>
                        {match.status === 'live' && (
                            <div className="text-xs text-red-500 font-medium mt-1">LIVE</div>
                        )}
                    </div>

                    {/* Away Team */}
                    <div className="text-center flex-1">
                        <div className="mb-2">
                            {match.away_team?.club?.logo && (
                                <img 
                                    src={match.away_team.club.logo} 
                                    alt={match.away_team.name}
                                    className="w-12 h-12 mx-auto mb-2 rounded-full object-cover"
                                />
                            )}
                            <h3 className="font-semibold text-gray-900 text-sm">
                                {match.away_team?.name || 'TBD'}
                            </h3>
                            {match.away_team?.club && (
                                <p className="text-xs text-gray-300">{match.away_team.club.name}</p>
                            )}
                        </div>
                        {(match.away_score !== null && match.away_score !== undefined) && (
                            <div className="text-3xl font-bold text-indigo-600">
                                {match.away_score}
                            </div>
                        )}
                    </div>
                </div>

                {/* Sets Information */}
                {match.sets && match.sets.length > 0 && (
                    <div className="mt-4 pt-4 border-t border-gray-200">
                        <div className="text-xs text-gray-300 mb-2">Sets</div>
                        <div className="flex justify-center space-x-2">
                            {match.sets.map((set, _) => (
                                <div key={set.id} className="text-center">
                                    <div className="text-xs text-gray-300 mb-1">Set {set.set_number}</div>
                                    <div className="text-sm font-medium">
                                        {set.home_score} - {set.away_score}
                                    </div>
                                </div>
                            ))}
                        </div>
                    </div>
                )}
            </div>

            {/* Actions */}
            <div className="px-6 pb-4">
                {match.status === 'live' && (
                    <Link
                        href={`/live-matches/${match.id}`}
                        className="w-full bg-red-600 text-white py-2 px-4 rounded-lg hover:bg-red-700 transition-colors text-center block font-medium"
                    >
                        <div className="flex items-center justify-center space-x-2">
                            <PlayIcon className="w-4 h-4" />
                            <span>Ver en Vivo</span>
                        </div>
                    </Link>
                )}
                {match.status === 'upcoming' && (
                    <div className="w-full bg-gray-100 text-gray-600 py-2 px-4 rounded-lg text-center text-sm">
                        Partido programado
                    </div>
                )}
                {match.status === 'finished' && (
                    <Link
                        href={`/matches/${match.id}`}
                        className="w-full bg-gray-600 text-white py-2 px-4 rounded-lg hover:bg-gray-700 transition-colors text-center block font-medium"
                    >
                        Ver Resultado
                    </Link>
                )}
            </div>
        </div>
    );
}