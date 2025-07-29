import { useEffect } from 'react';
import Echo from 'laravel-echo';
import { Match, MatchSet } from '@/types/global';

interface UseMatchRealTimeUpdatesOptions {
    onMatchUpdate?: (match: Match) => void;
    onMatchScoreUpdate?: (matchId: number, homeScore: number, awayScore: number) => void;
    onSetUpdate?: (matchId: number, setData: MatchSet) => void;
    onMatchStatusChange?: (matchId: number, status: string, previousStatus: string) => void;
    onPlayerRotation?: (matchId: number, teamId: number, rotationData: any) => void;
}

export function useMatchRealTimeUpdates(
    matchId: number,
    options: UseMatchRealTimeUpdatesOptions = {}
) {
    useEffect(() => {
        if (!matchId) return;

        // Suscribirse al canal específico del partido
        const matchChannel = window.Echo?.channel(`match.${matchId}`);
        
        if (!matchChannel) {
            console.warn('Echo not available for real-time updates');
            return;
        }

        // Escuchar actualizaciones de puntuación
        matchChannel.listen('MatchScoreUpdated', (event: any) => {
            console.log('Score updated:', event);
            if (options.onMatchScoreUpdate) {
                options.onMatchScoreUpdate(
                    event.match_id,
                    event.homeScore,
                    event.awayScore
                );
            }
        });

        // Escuchar cambios de estado del partido
        matchChannel.listen('MatchStatusChanged', (event: any) => {
            console.log('Match status changed:', event);
            if (options.onMatchStatusChange) {
                options.onMatchStatusChange(
                    event.match_id,
                    event.new_status,
                    event.previous_status
                );
            }
            if (options.onMatchUpdate && event.match) {
                options.onMatchUpdate(event.match);
            }
        });

        // Escuchar actualizaciones de sets
        matchChannel.listen('SetUpdated', (event: any) => {
            console.log('Set updated:', event);
            if (options.onSetUpdate) {
                options.onSetUpdate(event.match_id, event.set);
            }
        });

        // Escuchar rotaciones de jugadores
        matchChannel.listen('PlayerRotationUpdated', (event: any) => {
            console.log('Player rotation updated:', event);
            if (options.onPlayerRotation) {
                options.onPlayerRotation(
                    event.match_id,
                    event.team_id,
                    event.rotation_data
                );
            }
        });

        // Cleanup al desmontar
        return () => {
            matchChannel.stopListening('MatchScoreUpdated');
            matchChannel.stopListening('MatchStatusChanged');
            matchChannel.stopListening('SetUpdated');
            matchChannel.stopListening('PlayerRotationUpdated');
        };
    }, [matchId, options]);
}

// Hook para escuchar todos los partidos en vivo
export function useLiveMatchesUpdates(
    onMatchUpdate?: (match: Match) => void,
    onNewLiveMatch?: (match: Match) => void
) {
    useEffect(() => {
        const liveMatchesChannel = window.Echo?.channel('live-matches');
        
        if (!liveMatchesChannel) {
            console.warn('Echo not available for live matches updates');
            return;
        }

        // Escuchar actualizaciones de partidos en vivo
        liveMatchesChannel.listen('MatchScoreUpdated', (event: any) => {
            if (onMatchUpdate && event.match) {
                onMatchUpdate(event.match);
            }
        });

        liveMatchesChannel.listen('MatchStatusChanged', (event: any) => {
            if (event.new_status === 'in_progress' && onNewLiveMatch && event.match) {
                onNewLiveMatch(event.match);
            }
            if (onMatchUpdate && event.match) {
                onMatchUpdate(event.match);
            }
        });

        return () => {
            liveMatchesChannel.stopListening('MatchScoreUpdated');
            liveMatchesChannel.stopListening('MatchStatusChanged');
        };
    }, [onMatchUpdate, onNewLiveMatch]);
}

// Hook para el panel de control de árbitros
export function useRefereeMatchControl(
    matchId: number,
    onScoreUpdate?: (homeScore: number, awayScore: number) => void,
    onSetComplete?: (setData: MatchSet) => void
) {
    const updateScore = async (team: 'home' | 'away', action: 'increment' | 'decrement', setNumber: number) => {
        try {
            const response = await fetch(`/referee/match/${matchId}/score`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                },
                body: JSON.stringify({
                    team,
                    action,
                    set_number: setNumber,
                }),
            });

            if (!response.ok) {
                throw new Error('Failed to update score');
            }

            const result = await response.json();
            if (result.success && onScoreUpdate) {
                // Aquí necesitaremos obtener los puntajes actualizados del resultado
                onScoreUpdate(result.data?.home_score || 0, result.data?.away_score || 0);
            }
            return result;
        } catch (error) {
            console.error('Error updating score:', error);
            throw error;
        }
    };

    const startNewSet = async () => {
        try {
            const response = await fetch(`/referee/match/${matchId}/new-set`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                },
            });

            if (!response.ok) {
                throw new Error('Failed to start new set');
            }

            const result = await response.json();
            if (result.success && onSetComplete) {
                onSetComplete(result.data);
            }
            return result;
        } catch (error) {
            console.error('Error starting new set:', error);
            throw error;
        }
    };

    const endSet = async () => {
        try {
            const response = await fetch(`/referee/match/${matchId}/end-set`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                },
            });

            if (!response.ok) {
                throw new Error('Failed to end set');
            }

            const result = await response.json();
            return result;
        } catch (error) {
            console.error('Error ending set:', error);
            throw error;
        }
    };

    const updateMatchStatus = async (status: string) => {
        try {
            const response = await fetch(`/referee/match/${matchId}/status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                },
                body: JSON.stringify({ status }),
            });

            if (!response.ok) {
                throw new Error('Failed to update match status');
            }

            const result = await response.json();
            return result;
        } catch (error) {
            console.error('Error updating match status:', error);
            throw error;
        }
    };

    return {
        updateScore,
        startNewSet,
        endSet,
        updateMatchStatus,
    };
}