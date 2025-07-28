import preset from './vendor/filament/support/tailwind.config.preset'
import forms from '@tailwindcss/forms'

export default {
    presets: [preset],
    content: [
        './app/Filament/**/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.{js,ts,jsx,tsx}',
        './vendor/filament/**/*.blade.php',
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', 'ui-sans-serif', 'system-ui'],
            },
            colors: {
                'vp-primary': {
                    50: 'var(--vp-primary-50)',
                    100: 'var(--vp-primary-100)',
                    200: 'var(--vp-primary-200)',
                    300: 'var(--vp-primary-300)',
                    400: 'var(--vp-primary-400)',
                    500: 'var(--vp-primary-500)',
                    600: 'var(--vp-primary-600)',
                    700: 'var(--vp-primary-700)',
                    800: 'var(--vp-primary-800)',
                    900: 'var(--vp-primary-900)',
                },
                'vp-secondary': {
                    50: 'var(--vp-secondary-50)',
                    100: 'var(--vp-secondary-100)',
                    200: 'var(--vp-secondary-200)',
                    300: 'var(--vp-secondary-300)',
                    400: 'var(--vp-secondary-400)',
                    500: 'var(--vp-secondary-500)',
                    600: 'var(--vp-secondary-600)',
                    700: 'var(--vp-secondary-700)',
                    800: 'var(--vp-secondary-800)',
                    900: 'var(--vp-secondary-900)',
                },
                'vp-accent': {
                    50: 'var(--vp-accent-50)',
                    100: 'var(--vp-accent-100)',
                    200: 'var(--vp-accent-200)',
                    300: 'var(--vp-accent-300)',
                    400: 'var(--vp-accent-400)',
                    500: 'var(--vp-accent-500)',
                    600: 'var(--vp-accent-600)',
                    700: 'var(--vp-accent-700)',
                    800: 'var(--vp-accent-800)',
                    900: 'var(--vp-accent-900)',
                },
            },
        },
    },
    plugins: [forms],
}
