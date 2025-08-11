import laravel, { refreshPaths } from 'laravel-vite-plugin'

import { defineConfig } from 'vite'
import path from 'path'
import react from '@vitejs/plugin-react'
import tailwindcss from '@tailwindcss/vite'

export default defineConfig({
    plugins: [
        laravel([
            'resources/css/app.css',
            'resources/js/app.tsx',
            'resources/js/welcome.tsx',
            'resources/js/contact.tsx',
            'resources/js/setup-wizard.tsx',
            'resources/js/club-setup.tsx',
            'resources/js/player-card.tsx'
        ]),
        react(),
        tailwindcss(),
    ],
    resolve: {
        alias: {
            '@': path.resolve(__dirname, 'resources/js'),
        },
    },
    define: {
        global: 'globalThis',
    },
    server: {
        https: true,
        host: 'volleypass.kronnos.dev'
    },
})
