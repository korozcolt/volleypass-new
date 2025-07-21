<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Nueva carnetización completada</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #1f2937; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { background: #f9fafb; padding: 30px; border-radius: 0 0 8px 8px; }
        .card-info { background: white; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #3b82f6; }
        .success-badge { background: #10b981; color: white; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: bold; }
        .footer { text-align: center; margin-top: 30px; color: #6b7280; font-size: 14px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📋 Nueva carnetización completada</h1>
        </div>

        <div class="content">
            <p>Estimado/a <strong>{{ $director_name }}</strong>,</p>

            <p>Te informamos que se ha completado exitosamente la generación automática de un nuevo carnet para una jugadora de tu club.</p>

            <div class="card-info">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                    <h3>📄 Detalles del carnet generado</h3>
                    <span class="success-badge">COMPLETADO</span>
                </div>

                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="padding: 8px 0; font-weight: bold; width: 30%;">Jugadora:</td>
                        <td style="padding: 8px 0;">{{ $player_name }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; font-weight: bold;">Número de carnet:</td>
                        <td style="padding: 8px 0; color: #3b82f6; font-weight: bold;">{{ $card_number }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; font-weight: bold;">Club:</td>
                        <td style="padding: 8px 0;">{{ $club_name }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; font-weight: bold;">Liga:</td>
                        <td style="padding: 8px 0;">{{ $league_name }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; font-weight: bold;">Generado:</td>
                        <td style="padding: 8px 0;">{{ $generated_at }}</td>
                    </tr>
                </table>
            </div>

            <div style="background: #ecfdf5; padding: 15px; border-radius: 6px; margin: 20px 0; border-left: 4px solid #10b981;">
                <p><strong>✅ Proceso automático completado sin errores</strong></p>
                <p style="margin: 5px 0;">La jugadora ya puede participar en competencias oficiales y ha recibido su carnet digital por correo electrónico.</p>
            </div>

            <p>El carnet incluye todas las validaciones requeridas y cumple con los estándares de la liga. No se requiere ninguna acción adicional de tu parte.</p>

            <p>Si necesitas verificar el estado del carnet o tienes alguna consulta, puedes acceder al panel de administración de tu club.</p>

            <p>Saludos cordiales,<br>
            <strong>Sistema VolleyPass</strong></p>
        </div>

        <div class="footer">
            <p>Este es un mensaje automático del sistema VolleyPass<br>
            Para consultas, contacta a tu administrador de liga</p>
        </div>
    </div>
</body>
</html>
