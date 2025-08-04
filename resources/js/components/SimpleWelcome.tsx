import * as React from 'react';

export default function SimpleWelcome() {
  return (
    <div className="min-h-screen bg-blue-600 flex items-center justify-center">
      <div className="text-white text-center">
        <h1 className="text-4xl font-bold mb-4">Bienvenido a VolleyPass</h1>
        <p className="text-xl">Sistema funcionando correctamente</p>
      </div>
    </div>
  );
}