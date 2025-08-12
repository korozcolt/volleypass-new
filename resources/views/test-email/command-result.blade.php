<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>⚡ Resultado del Comando - VolleyPass</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .emoji { font-size: 1.2em; }
        .command-output {
            font-family: 'Courier New', monospace;
            white-space: pre-wrap;
            word-wrap: break-word;
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen py-8">
    <div class="max-w-4xl mx-auto px-4">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">
                <span class="emoji">⚡</span> Resultado del Comando Artisan
            </h1>
            <a href="{{ route('test-email.index') }}" class="text-blue-600 hover:text-blue-800">
                ← Volver al inicio
            </a>
        </div>

        <!-- Información de Ejecución -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">
                <span class="emoji">📊</span> Información de Ejecución
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="font-medium text-gray-700 mb-2">Estado</h3>
                    @if($success)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            ✅ Exitoso
                        </span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                            ❌ Error
                        </span>
                    @endif
                    @if(isset($exit_code))
                        <p class="mt-1"><strong>Exit Code:</strong> {{ $exit_code }}</p>
                    @endif
                </div>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="font-medium text-gray-700 mb-2">Tiempo</h3>
                    <p><strong>Ejecutado:</strong> {{ $executed_at }}</p>
                    @if(isset($duration))
                        <p><strong>Duración:</strong> {{ $duration }}ms</p>
                    @endif
                </div>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="font-medium text-gray-700 mb-2">Parámetros</h3>
                    @if(isset($parameters['to']))
                        <p><strong>Email:</strong> {{ $parameters['to'] }}</p>
                    @endif
                    <p><strong>Comando:</strong> <code>email:test</code></p>
                </div>
            </div>
        </div>

        <!-- Salida del Comando -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">
                <span class="emoji">💻</span> Salida del Comando
            </h2>
            @if(isset($command_output))
                <div class="bg-gray-900 text-green-400 p-4 rounded-lg overflow-x-auto">
                    <pre class="command-output text-sm">{{ $command_output }}</pre>
                </div>
            @elseif(isset($error))
                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                    <p class="text-red-800 font-medium">{{ $message ?? 'Error ejecutando el comando' }}</p>
                    <div class="mt-3 p-3 bg-red-100 rounded-lg">
                        <p class="text-red-700 text-sm font-mono">{{ $error }}</p>
                    </div>
                </div>
            @else
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <p class="text-yellow-800">No se pudo capturar la salida del comando.</p>
                </div>
            @endif
        </div>

        <!-- Configuración del Sistema -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">
                <span class="emoji">⚙️</span> Configuración del Sistema
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

        <!-- Interpretación del Resultado -->
        @if(isset($command_output))
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-6">
                <h2 class="text-xl font-semibold text-blue-800 mb-4">
                    <span class="emoji">🔍</span> Interpretación del Resultado
                </h2>
                @if($success)
                    <div class="text-blue-700">
                        <p class="font-medium mb-2">✅ El comando se ejecutó correctamente</p>
                        <ul class="text-sm space-y-1">
                            <li>• El email fue enviado exitosamente</li>
                            <li>• La configuración de Resend está funcionando</li>
                            <li>• El sistema puede enviar emails desde la línea de comandos</li>
                        </ul>
                    </div>
                @else
                    <div class="text-blue-700">
                        <p class="font-medium mb-2">❌ El comando falló</p>
                        <ul class="text-sm space-y-1">
                            <li>• Revisa la configuración de Resend</li>
                            <li>• Verifica que el dominio esté verificado</li>
                            <li>• Comprueba que RESEND_KEY sea válido</li>
                        </ul>
                    </div>
                @endif
            </div>
        @endif

        <!-- Acciones -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">
                <span class="emoji">🔄</span> Acciones Disponibles
            </h2>
            <div class="flex flex-wrap gap-4">
                <a href="{{ route('test-email.index') }}"
                   class="bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition duration-200">
                    <span class="emoji">🔙</span> Volver al Inicio
                </a>
                <a href="{{ route('test-email.send') }}"
                   class="bg-green-600 text-white py-2 px-4 rounded-md hover:bg-green-700 transition duration-200">
                    <span class="emoji">📧</span> Prueba Directa
                </a>
                <button onclick="window.location.reload()"
                        class="bg-yellow-600 text-white py-2 px-4 rounded-md hover:bg-yellow-700 transition duration-200">
                    <span class="emoji">🔄</span> Ejecutar de Nuevo
                </button>
                <button onclick="window.print()"
                        class="bg-gray-600 text-white py-2 px-4 rounded-md hover:bg-gray-700 transition duration-200">
                    <span class="emoji">🖨️</span> Imprimir Resultado
                </button>
            </div>
        </div>
    </div>
</body>
</html>
