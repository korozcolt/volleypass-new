import React from 'react';
import { createRoot } from 'react-dom/client';
import Welcome from './components/Welcome';
import '../css/app.css';

// Hacer React disponible globalmente
(window as any).React = React;
(window as any).ReactDOM = { createRoot };

// Renderizar el componente Welcome
const container = document.getElementById('welcome-root');
if (container) {
    const root = createRoot(container);
    root.render(<Welcome />);
}