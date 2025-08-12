<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🧪 Test de Email - VolleyPass</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .emoji { font-size: 1.2em; }
    </style>
</head>
<body class="bg-gray-100 min-h-screen py-8">
    <div class="max-w-4xl mx-auto px-4">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">
                <span class="emoji">🧪</span> Test de Email - VolleyPass
            </h1>
            <p class="text-gray-600">
                Prueba la configuración de envío de emails usando Resend
            </p>
        </div>

        <!-- Configuración Actual -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">
                <span class="emoji">⚙️</span> Configuración Actual
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="font-medium text-gray-700 mb-2">Configuración de Mail</h3>
                    <ul class="space-y-1 text-sm">
                        <li><strong>Mailer:</strong> {{ $config['mailer'] }}</li>
                        <li><strong>From Address:</strong> {{ $config['from_address'] }}</li>
                        <li><strong>From Name:</strong> {{ $config['from_name'] }}</li>
                        <li><strong>Contact Email:</strong> {{ $config['contact_email'] }}</li>
                    </ul>
                </div>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="font-medium text-gray-700 mb-2">Estado del Sistema</h3>
                    <ul class="space-y-1 text-sm">
                        <li><strong>App Name:</strong> {{ $config['app_name'] }}</li>
                        <li><strong>Environment:</strong> {{ $config['app_env'] }}</li>
                        <li><strong>Resend Key:</strong>
                            @if($config['resend_configured'])
                                <span class="text-green-600">✅ Configurado</span>
                            @else
                                <span class="text-red-600">❌ No configurado</span>
                            @endif
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Formularios de Prueba -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Prueba Directa -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">
                    <span class="emoji">📧</span> Prueba Directa
                </h2>
                <p class="text-gray-600 mb-4">
                    Envía un email de prueba directamente desde el controlador web.
                </p>
                <form action="{{ route('test-email.send') }}" method="GET" class="space-y-4">
                    <div>
                        <label for="to" class="block text-sm font-medium text-gray-700 mb-1">
                            Email de destino:
                        </label>
                        <input
                            type="email"
                            id="to"
                            name="to"
                            value="{{ $config['contact_email'] }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="email@ejemplo.com"
                        >
                    </div>
                    <button
                        type="submit"
                        class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition duration-200"
                    >
                        <span class="emoji">🚀</span> Enviar Email de Prueba
                    </button>
                </form>
            </div>

            <!-- Prueba con Comando -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">
                    <span class="emoji">⚡</span> Prueba con Comando Artisan
                </h2>
                <p class="text-gray-600 mb-4">
                    Ejecuta el comando <code class="bg-gray-100 px-2 py-1 rounded">email:test</code> y muestra la salida.
                </p>
                <form action="{{ route('test-email.command') }}" method="GET" class="space-y-4">
                    <div>
                        <label for="to_command" class="block text-sm font-medium text-gray-700 mb-1">
                            Email de destino:
                        </label>
                        <input
                            type="email"
                            id="to_command"
                            name="to"
                            value="{{ $config['contact_email'] }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                            placeholder="email@ejemplo.com"
                        >
                    </div>
                    <button
                        type="submit"
                        class="w-full bg-green-600 text-white py-2 px-4 rounded-md hover:bg-green-700 transition duration-200"
                    >
                        <span class="emoji">⚡</span> Ejecutar Comando
                    </button>
                </form>
            </div>
        </div>

        <!-- Información Adicional -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mt-6">
            <h3 class="text-lg font-semibold text-blue-800 mb-2">
                <span class="emoji">💡</span> Información Importante
            </h3>
            <ul class="text-blue-700 space-y-1 text-sm">
                <li>• Asegúrate de que tu dominio esté verificado en Resend</li>
                <li>• El RESEND_KEY debe estar configurado en tu archivo .env</li>
                <li>• MAIL_MAILER debe estar configurado como 'resend'</li>
                <li>• Revisa tu bandeja de entrada y spam después de enviar</li>
            </ul>
        </div>
    </div>
</body>
</html>
