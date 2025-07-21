<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Error en generación automática de carnet</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #dc2626; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { background: #fafafa; padding: 30px; border-radius: 0 0 8px 8px; }
        .error-info { background: #fef2f2; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #dc2626; }
        .player-info { background: white; padding: 20px; border-radius: 8px; margin: 20px 0; border: 1px solid #e5e7eb; }
        .footer { text-align: center; margin-top: 30px; color: #6b7280; font-size: 14px; }
        .urgent { background: #fef2f2; color: #991b1b; padding: 10px 15px; border-radius: 6px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>⚠️ Error en generación automática</h1>
        </div>

        <div class="content">
            <div class="urgent">
                🚨 ATENCIÓN REQUERIDA: Error en el sistema de carnetización automática
            </div>

            <p>Estimado/a administrador/a,</p>

            <p>Se ha producido un error durante el proceso de generación automática de carnet en <strong>{{ $league_name }}</strong>. Se requiere intervención manual para resolver la situación.</p>

            <div class="player-info">
                <h3>👤 Información de la jugadora afectada</h3>
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="padding: 8px 0; font-weight: bold; width: 30%;">Jugadora:</td>
                        <td style="padding: 8px 0;">{{ $player_name }}</td>
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
                        <td style="padding: 8px 0; font-weight: bold;">Fecha del error:</td>
                        <td style="padding: 8px 0;">{{ $occurred_at }}</td>
                    </tr>
                </table>
            </div>

            <div class="error-info">
                <h3>❌ Detalles del error</h3>
                <p><strong>Mensaje de error:</strong></p>
                <div style="background: white; padding: 15px; border-radius: 4px; font-family: monospace; font-size: 14px; margin: 10px 0;">
                    {{ $error_message }}
                </div>
            </div>

            <div style="background: #fef3c7; padding: 20px; border-radius: 8px; border-left: 4px solid #f59e0b;">
                <h4 style="margin-top: 0; color: #92400e;">🔧 Acciones recomendadas</h4>
                <ol style="margin: 10px 0; padding-left: 20px;">
                    <li>Revisar los documentos de la jugadora en el panel de administración</li>
                    <li>Verificar que todos los datos requeridos estén completos</li>
                    <li>Comprobar el estado del club y la liga</li>
                    <li>Intentar la generación manual del carnet</li>
                    <li>Si el problema persiste, contactar al soporte técnico</li>
                </ol>
            </div>

            <div style="background: #f0f9ff; padding: 15px; border-radius: 6px; margin: 20px 0;">
                <p><strong>💡 Nota importante:</strong></p>
                <p style="margin: 5px 0; font-size: 14px;">El sistema continuará funcionando normalmente para otras generaciones. Este error afecta únicamente a la jugadora mencionada.</p>
            </div>

            <p>Por favor, revisa y resuelve esta situación lo antes posible para que la jugadora pueda obtener su carnet y participar en competencias.</p>

            <p>Si necesitas asistencia técnica, incluye este mensaje completo en tu solicitud de soporte.</p>

            <p>Saludos,<br>
            <strong>Sistema VolleyPass</strong></p>
        </div>

        <div class="footer">
            <p>Sistema de Carnetización Automática - VolleyPass<br>
            Alerta automática de error</p>
        </div>
    </div>
</body>
</html>
