import React from 'react';
import { createRoot } from 'react-dom/client';
import SetupWizard from './components/SetupWizard/SetupWizard';
import '../css/app.css';

// Hacer React disponible globalmente
(window as any).React = React;
(window as any).ReactDOM = { createRoot };

// Renderizar el componente SetupWizard
const container = document.getElementById('setup-wizard-root');
if (container) {
    const root = createRoot(container);
    root.render(<SetupWizard 
        onComplete={() => {
            // Redirigir al admin después de completar
            window.location.href = '/admin';
        }}
    />);
}