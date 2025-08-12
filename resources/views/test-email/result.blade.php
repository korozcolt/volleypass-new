<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>📧 Resultado del Test - VolleyPass</title>
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
                <span class="emoji">📧</span> Resultado del Test de Email
            </h1>
            <a href="{{ route('test-email.index') }}" class="text-blue-600 hover:text-blue-800">
                ← Volver al inicio
            </a>
        </div>

        <!-- Resultado Principal -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            @if($success)
                <div class="bg-green-50 border border-green-200 rounded-lg p-6">
                    <div class="flex items-center mb-4">
                        <span class="emoji text-2xl mr-3">✅</span>
                        <h2 class="text-xl font-semibold text-green-800">¡Email Enviado Exitosamente!</h2>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div>
                            <p><strong>Enviado a:</strong> {{ $sent_to }}</p>
                            <p><strong>Fecha y hora:</strong> {{ $sent_at }}</p>
                        </div>
                        <div>
                            @if(isset($duration))
                                <p><strong>Tiempo de envío:</strong> {{ $duration }}ms</p>
                            @endif
                            <p><strong>Estado:</strong> <span class="text-green-600">Enviado</span></p>
                        </div>
                    </div>
                    <div class="mt-4 p-4 bg-green-100 rounded-lg">
                        <p class="text-green-800 font-medium">{{ $message }}</p>
                        <p class="text-green-700 text-sm mt-2">
                            Revisa tu bandeja de entrada en <strong>{{ $sent_to }}</strong>.
                            Si no lo encuentras, revisa también la carpeta de spam.
                        </p>
                    </div>
                </div>
            @else
                <div class="bg-red-50 border border-red-200 rounded-lg p-6">
                    <div class="flex items-center mb-4">
                        <span class="emoji text-2xl mr-3">❌</span>
                        <h2 class="text-xl font-semibold text-red-800">Error al Enviar Email</h2>
                    </div>
                    <div class="mb-4">
                        <p class="text-red-800 font-medium">{{ $message }}</p>
                        @if(isset($error))
                            <div class="mt-3 p-3 bg-red-100 rounded-lg">
                                <p class="text-red-700 text-sm font-mono">{{ $error }}</p>
                            </div>
                        @endif
                    </div>

                    @if(isset($suggestions))
                        <div class="mt-4">
                            <h3 class="font-semibold text-red-800 mb-2">Sugerencias para solucionar:</h3>
                            <ul class="text-red-700 text-sm space-y-1">
                                @foreach($suggestions as $suggestion)
                                    <li>• {{ $suggestion }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            @endif
        </div>

        <!-- Configuración Actual -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">
                <span class="emoji">⚙️</span> Configuración Utilizada
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

        <!-- Acciones -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">
                <span class="emoji">🔄</span> Próximos Pasos
            </h2>
            <div class="flex flex-wrap gap-4">
                <a href="{{ route('test-email.index') }}"
                   class="bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition duration-200">
                    <span class="emoji">🔙</span> Hacer Otra Prueba
                </a>
                <a href="{{ route('test-email.command') }}"
                   class="bg-green-600 text-white py-2 px-4 rounded-md hover:bg-green-700 transition duration-200">
                    <span class="emoji">⚡</span> Probar con Comando
                </a>
                @if($success)
                    <button onclick="window.print()"
                            class="bg-gray-600 text-white py-2 px-4 rounded-md hover:bg-gray-700 transition duration-200">
                        <span class="emoji">🖨️</span> Imprimir Resultado
                    </button>
                @endif
            </div>
        </div>
    </div>
</body>
</html>
