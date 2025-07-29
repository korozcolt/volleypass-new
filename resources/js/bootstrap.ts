import Echo from 'laravel-echo';
import Pusher from 'pusher-js';
import axios from 'axios';

window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Configuración de Echo para WebSockets (solo si Pusher está configurado)
window.Pusher = Pusher;

// Obtener variables de entorno de Vite de forma segura
const pusherKey = (import.meta as any).env?.VITE_PUSHER_APP_KEY;
const pusherCluster = (import.meta as any).env?.VITE_PUSHER_APP_CLUSTER ?? 'mt1';

if (pusherKey) {
    window.Echo = new Echo({
        broadcaster: 'pusher',
        key: pusherKey,
        cluster: pusherCluster,
        forceTLS: true,
        encrypted: true,
        enabledTransports: ['ws', 'wss'],
    });

    console.log('✅ Echo configurado correctamente con Pusher Cloud');
} else {
    console.warn('⚠️ VITE_PUSHER_APP_KEY no encontrada');
}
