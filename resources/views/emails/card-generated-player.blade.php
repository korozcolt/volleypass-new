<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>¡Tu carnet VolleyPass está listo!</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #2563eb; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { background: #f8fafc; padding: 30px; border-radius: 0 0 8px 8px; }
        .card-info { background: white; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #10b981; }
        .button { display: inline-block; background: #10b981; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; margin: 10px 0; }
        .footer { text-align: center; margin-top: 30px; color: #6b7280; font-size: 14px; }
        .highlight { color: #2563eb; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🏐 ¡Tu carnet VolleyPass está listo!</h1>
        </div>

        <div class="content">
            <p>Hola <strong>{{ $player_name }}</strong>,</p>

            <p>¡Excelentes noticias! Tu carnet digital ha sido generado exitosamente y ya puedes participar en competencias oficiales.</p>

            <div class="card-info">
                <h3>📋 Información de tu carnet:</h3>
                <ul>
                    <li><strong>Número:</strong> <span class="highlight">{{ $card_number }}</span></li>
                    <li><strong>Liga:</strong> {{ $league_name }}</li>
                    <li><strong>Club:</strong> {{ $club_name }}</li>
                    <li><strong>Válido hasta:</strong> {{ $expires_at }}</li>
                </ul>
            </div>

            <p>Tu carnet incluye un código QR único que permite verificar tu elegibilidad de manera instantánea en cualquier competencia.</p>

            <div style="text-align: center;">
                <a href="{{ $download_url }}" class="button">📱 Descargar mi carnet</a>
            </div>

            <div style="background: #fef3c7; padding: 15px; border-radius: 6px; margin: 20px 0;">
                <p><strong>💡 Importante:</strong></p>
                <ul>
                    <li>Guarda tu carnet en tu teléfono para acceso offline</li>
                    <li>El código QR debe estar visible y legible</li>
                    <li>Renueva tu carnet antes de la fecha de vencimiento</li>
                </ul>
            </div>

            <p>Si tienes alguna pregunta o necesitas ayuda, no dudes en contactar a tu club o liga.</p>

            <p>¡Que tengas excelentes partidos! 🏆</p>
        </div>

        <div class="footer">
            <p>Este es un mensaje automático del sistema VolleyPass<br>
            No respondas a este correo</p>
        </div>
    </div>
</body>
</html>
