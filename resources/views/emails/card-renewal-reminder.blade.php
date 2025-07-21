<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Recordatorio de renovación de carnet</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #f59e0b; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { background: #fffbeb; padding: 30px; border-radius: 0 0 8px 8px; }
        .card-info { background: white; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #f59e0b; }
        .button { display: inline-block; background: #f59e0b; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; margin: 10px 0; }
        .countdown { background: #fef3c7; padding: 20px; border-radius: 8px; text-align: center; margin: 20px 0; }
        .countdown-number { font-size: 48px; font-weight: bold; color: #d97706; }
        .footer { text-align: center; margin-top: 30px; color: #6b7280; font-size: 14px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>⏰ Recordatorio de renovación</h1>
        </div>

        <div class="content">
            <p>Hola <strong>{{ $player_name }}</strong>,</p>

            <p>Te recordamos que tu carnet VolleyPass está próximo a vencer. Es importante que lo renueves para continuar participando en competencias oficiales.</p>

            <div class="countdown">
                <div class="countdown-number">{{ $days_until_expiry }}</div>
                <div style="font-weight: bold; color: #92400e;">
                    {{ $days_until_expiry == 1 ? 'DÍA RESTANTE' : 'DÍAS RESTANTES' }}
                </div>
            </div>

            <div class="card-info">
                <h3>📋 Información de tu carnet:</h3>
                <ul>
                    <li><strong>Número:</strong> {{ $card_number }}</li>
                    <li><strong>Fecha de vencimiento:</strong> <span style="color: #d97706; font-weight: bold;">{{ $expires_at }}</span></li>
                </ul>
            </div>

            @if($days_until_expiry <= 7)
            <div style="background: #fef2f2; padding: 15px; border-radius: 6px; margin: 20px 0; border-left: 4px solid #dc2626;">
                <p><strong>🚨 URGENTE:</strong></p>
                <p style="margin: 5px 0;">Tu carnet vence en {{ $days_until_expiry }} {{ $days_until_expiry == 1 ? 'día' : 'días' }}. Después de esta fecha no podrás participar en competencias hasta que renueves.</p>
            </div>
            @else
            <div style="background: #fef3c7; padding: 15px; border-radius: 6px; margin: 20px 0; border-left: 4px solid #f59e0b;">
                <p><strong>⚠️ Importante:</strong></p>
                <p style="margin: 5px 0;">Renueva tu carnet con anticipación para evitar interrupciones en tu participación deportiva.</p>
            </div>
            @endif

            <div style="text-align: center;">
                <a href="{{ $renewal_url }}" class="button">🔄 Renovar mi carnet</a>
            </div>

            <div style="background: white; padding: 20px; border-radius: 8px; margin: 20px 0;">
                <h4>📝 Proceso de renovación:</h4>
                <ol>
                    <li>Haz clic en el botón "Renovar mi carnet"</li>
                    <li>Verifica que tus datos estén actualizados</li>
                    <li>Sube los documentos requeridos (si es necesario)</li>
                    <li>Tu carnet se renovará automáticamente una vez aprobado</li>
                </ol>
            </div>

            <p>Si tienes alguna pregunta sobre el proceso de renovación, contacta a tu club o liga.</p>

            <p>¡No dejes que tu carnet expire! 🏐</p>
        </div>

        <div class="footer">
            <p>Este es un recordatorio automático del sistema VolleyPass<br>
            No respondas a este correo</p>
        </div>
    </div>
</body>
</html>
