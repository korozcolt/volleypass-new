import { PageProps as InertiaPageProps } from '@inertiajs/core';
import { AxiosInstance } from 'axios';
import { route as ziggyRoute } from 'ziggy-js';

declare global {
    interface Window {
        axios: AxiosInstance;
        Pusher: any;
        Echo: any;
    }

    var route: typeof ziggyRoute;
}

interface ImportMetaEnv {
    readonly VITE_APP_NAME: string;
    readonly VITE_PUSHER_APP_KEY: string;
    readonly VITE_PUSHER_HOST: string;
    readonly VITE_PUSHER_PORT: string;
    readonly VITE_PUSHER_SCHEME: string;
    readonly VITE_PUSHER_APP_CLUSTER: string;
}

interface ImportMeta {
    readonly env: ImportMetaEnv;
    readonly glob: (pattern: string) => Record<string, () => Promise<any>>;
}

export interface User {
    id: number;
    name: string;
    email: string;
    email_verified_at: string;
    roles: Role[];
    player?: Player;
    coach?: Coach;
    referee?: Referee;
}

export interface Role {
    id: number;
    name: string;
    guard_name: string;
}

export interface Player {
    id: number;
    user_id: number;
    club_id?: number;
    team_id?: number;
    position: string;
    jersey_number?: number;
    height?: number;
    weight?: number;
    birth_date: string;
    club?: Club;
    team?: Team;
}

export interface Coach {
    id: number;
    user_id: number;
    club_id?: number;
    license_number?: string;
    experience_years?: number;
    club?: Club;
}

export interface Referee {
    id: number;
    user_id: number;
    license_number: string;
    category: string;
    experience_years?: number;
}

export interface Club {
    id: number;
    name: string;
    logo?: string;
    founded_year?: number;
    address?: string;
    phone?: string;
    email?: string;
    teams?: Team[];
}

export interface Team {
    id: number;
    name: string;
    club_id: number;
    category: string;
    division?: string;
    club?: Club;
    players?: Player[];
}

export interface League {
    id: number;
    name: string;
    description?: string;
    start_date: string;
    end_date: string;
    status: string;
    tournaments?: Tournament[];
}

export interface Tournament {
    id: number;
    name: string;
    league_id: number;
    description?: string;
    start_date: string;
    end_date: string;
    status: string;
    league?: League;
    matches?: Match[];
}

export interface Match {
    id: number;
    tournament_id: number;
    home_team_id: number;
    away_team_id: number;
    referee_id?: number;
    scheduled_at: string;
    status: string;
    home_score?: number;
    away_score?: number;
    tournament?: Tournament;
    home_team?: Team;
    away_team?: Team;
    referee?: Referee;
    sets?: MatchSet[];
}

export interface MatchSet {
    id: number;
    match_id: number;
    set_number: number;
    home_score: number;
    away_score: number;
    status: string;
}

export type PageProps<T extends Record<string, unknown> = Record<string, unknown>> = T & {
    auth: {
        user: User;
    };
    ziggy: {
        location: string;
        query: Record<string, unknown>;
    };
    flash: {
        message?: string;
        error?: string;
        success?: string;
    };
};