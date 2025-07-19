<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mantenimiento - {{ $app_name }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }

        .container {
            text-align: center;
            max-width: 600px;
            padding: 2rem;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .logo {
            margin-bottom: 2rem;
        }

        .logo img {
            max-height: 80px;
            filter: brightness(0) invert(1);
        }

        h1 {
            font-size: 3rem;
            margin-bottom: 1rem;
            font-weight: 300;
        }

        .message {
            font-size: 1.25rem;
            margin-bottom: 2rem;
            opacity: 0.9;
            line-height: 1.6;
        }

        .spinner {
            display: inline-block;
            width: 40px;
            height: 40px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s ease-in-out infinite;
            margin-bottom: 1rem;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .footer {
            margin-top: 3rem;
            opacity: 0.7;
            font-size: 0.9rem;
        }

        .status-info {
            background: rgba(255, 255, 255, 0.1);
            padding: 1rem;
            border-radius: 10px;
            margin: 2rem 0;
            font-size: 0.9rem;
        }

        @media (max-width: 768px) {
            h1 {
                font-size: 2rem;
            }

            .message {
                font-size: 1rem;
            }

            .container {
                padding: 1rem;
                margin: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">
            <img src="{{ asset('images/logo-volley_pass_black_back.png') }}" alt="{{ $app_name }}">
        </div>

        <h1>🔧 Mantenimiento</h1>

        <div class="spinner"></div>

        <div class="message">
            {{ $message }}
        </div>

        <div class="status-info">
            <p><strong>Estado:</strong> Sistema en mantenimiento</p>
            <p><strong>Inicio:</strong> {{ now()->format('d/m/Y H:i') }}</p>
            <p><strong>Estimado:</strong> Unos minutos</p>
        </div>

        <div class="footer">
            <p><strong>{{ $app_name }}</strong></p>
            <p>Versión {{ $app_version }}</p>
            <p style="margin-top: 1rem; font-size: 0.8rem;">
                Si eres administrador, puedes acceder al
                <a href="/admin" style="color: #fbbf24; text-decoration: underline;">panel administrativo</a>
            </p>
        </div>
    </div>

    <script>
        // Auto-refresh cada 30 segundos
        setTimeout(function() {
            window.location.reload();
        }, 30000);

        // Mostrar tiempo transcurrido
        let startTime = new Date();
        setInterval(function() {
            let elapsed = Math.floor((new Date() - startTime) / 1000);
            let minutes = Math.floor(elapsed / 60);
            let seconds = elapsed % 60;
            document.title = `Mantenimiento (${minutes}:${seconds.toString().padStart(2, '0')}) - {{ $app_name }}`;
        }, 1000);
    </script>
</body>
</html>
