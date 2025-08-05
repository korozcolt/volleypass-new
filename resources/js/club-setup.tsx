import React from 'react';
import { createRoot } from 'react-dom/client';
import ClubSetupWizard from './components/ClubSetupWizard/ClubSetupWizard';
import '../css/app.css';

// Hacer React disponible globalmente
(window as any).React = React;
(window as any).ReactDOM = { createRoot };

// Obtener datos del club desde el DOM
const clubDataElement = document.querySelector('script[data-club]');
const clubData = clubDataElement ? JSON.parse(clubDataElement.textContent || '{}') : null;

// Renderizar el componente ClubSetupWizard
const container = document.getElementById('club-setup-root');
if (container) {
    const root = createRoot(container);
    root.render(<ClubSetupWizard 
        clubId={clubData?.id || null}
        onComplete={() => {
            // Redirigir al dashboard del club después de completar
            window.location.href = '/admin/clubs/' + (clubData?.id || '') + '/dashboard';
        }}
    />);
}