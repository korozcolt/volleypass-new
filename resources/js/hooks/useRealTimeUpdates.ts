import { useEffect } from 'react';
import { Match } from '@/types/global';

interface UseRealTimeUpdatesProps {
    onMatchUpdate?: (match: Match) => void;
    onMatchScoreUpdate?: (matchId: number, homeScore: number, awayScore: number) => void;
    onMatchStatusChange?: (matchId: number, status: string) => void;
    onSetUpdate?: (matchId: number, setData: any) => void;
}

export function useRealTimeUpdates({
    onMatchUpdate,
    onMatchScoreUpdate,
    onMatchStatusChange,
    onSetUpdate
}: UseRealTimeUpdatesProps) {
    useEffect(() => {
        if (!window.Echo) {
            console.warn('Echo not available for real-time updates');
            return;
        }

        // Canal público para actualizaciones de partidos
        window.Echo.channel('matches')
            .listen('MatchUpdated', (e: { match: Match }) => {
                console.log('Match updated:', e.match);
                onMatchUpdate?.(e.match);
            })
            .listen('MatchScoreUpdated', (e: { matchId: number; homeScore: number; awayScore: number }) => {
                console.log('Match score updated:', e);
                onMatchScoreUpdate?.(e.matchId, e.homeScore, e.awayScore);
            })
            .listen('MatchStatusChanged', (e: { matchId: number; status: string }) => {
                console.log('Match status changed:', e);
                onMatchStatusChange?.(e.matchId, e.status);
            })
            .listen('SetUpdated', (e: { matchId: number; setData: any }) => {
                console.log('Set updated:', e);
                onSetUpdate?.(e.matchId, e.setData);
            });

        // Canal para actualizaciones de partidos en vivo
        window.Echo.channel('live-matches')
            .listen('LiveMatchUpdate', (e: { match: Match }) => {
                console.log('Live match update:', e.match);
                onMatchUpdate?.(e.match);
            });

        return () => {
            window.Echo.leaveChannel('matches');
            window.Echo.leaveChannel('live-matches');
        };
    }, [onMatchUpdate, onMatchScoreUpdate, onMatchStatusChange, onSetUpdate]);
}

// Hook específico para un partido individual
export function useMatchRealTimeUpdates(matchId: number, callbacks: UseRealTimeUpdatesProps) {
    useEffect(() => {
        if (!window.Echo || !matchId) {
            return;
        }

        // Canal específico para un partido
        window.Echo.channel(`match.${matchId}`)
            .listen('MatchUpdated', (e: { match: Match }) => {
                callbacks.onMatchUpdate?.(e.match);
            })
            .listen('ScoreUpdated', (e: { homeScore: number; awayScore: number }) => {
                callbacks.onMatchScoreUpdate?.(matchId, e.homeScore, e.awayScore);
            })
            .listen('StatusChanged', (e: { status: string }) => {
                callbacks.onMatchStatusChange?.(matchId, e.status);
            })
            .listen('SetUpdated', (e: { setData: any }) => {
                callbacks.onSetUpdate?.(matchId, e.setData);
            });

        return () => {
            window.Echo.leaveChannel(`match.${matchId}`);
        };
    }, [matchId, callbacks]);
}