import React from 'react';
import { createRoot } from 'react-dom/client';
import '../css/app.css';

// Basic React app for admin functionality
function App() {
    return (
        <div className="min-h-screen bg-gray-100">
            <div className="container mx-auto py-8">
                <h1 className="text-2xl font-bold text-gray-800">
                    VolleyPass Admin
                </h1>
                <p className="text-gray-600 mt-2">
                    Sistema de administración funcionando correctamente.
                </p>
            </div>
        </div>
    );
}

// Only mount if there's a root element (for admin pages that need React)
const rootElement = document.getElementById('app');
if (rootElement) {
    const root = createRoot(rootElement);
    root.render(<App />);
}

export default App;