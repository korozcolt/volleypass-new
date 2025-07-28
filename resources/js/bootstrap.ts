import axios from 'axios';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Configuración de Echo para WebSockets (solo si Pusher está configurado)
window.Pusher = Pusher;

// Obtener variables de entorno de Vite de forma segura
const pusherKey = (import.meta as any).env?.VITE_PUSHER_APP_KEY;
const pusherCluster = (import.meta as any).env?.VITE_PUSHER_APP_CLUSTER ?? 'mt1';
const pusherHost = (import.meta as any).env?.VITE_PUSHER_HOST;
const pusherPort = (import.meta as any).env?.VITE_PUSHER_PORT ?? 443;
const pusherScheme = (import.meta as any).env?.VITE_PUSHER_SCHEME ?? 'https';

if (pusherKey) {
    window.Echo = new Echo({
        broadcaster: 'pusher',
        key: pusherKey,
        cluster: pusherCluster,
        wsHost: pusherHost ? pusherHost : `ws-${pusherCluster}.pusher-channels.com`,
        wsPort: pusherPort,
        wssPort: pusherPort,
        forceTLS: pusherScheme === 'https',
        enabledTransports: ['ws', 'wss'],
    });
} else {
    // Echo no está configurado - funcionalidad en tiempo real deshabilitada
    console.info('Pusher no configurado - funcionalidad en tiempo real deshabilitada');
}