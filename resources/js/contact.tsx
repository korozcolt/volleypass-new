import React from 'react';
import { createRoot } from 'react-dom/client';
import Contact from './components/Contact';

const container = document.getElementById('contact-root');
if (container) {
    const root = createRoot(container);
    root.render(<Contact />);
}