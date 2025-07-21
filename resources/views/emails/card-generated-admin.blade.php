<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Carnet generado automáticamente</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #7c3aed; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { background: #fafafa; padding: 30px; border-radius: 0 0 8px 8px; }
        .card-info { background: white; padding: 20px; border-radius: 8px; margin: 20px 0; border: 1px solid #e5e7eb; }
        .stats-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin: 20px 0; }
        .stat-item { background: white; padding: 15px; border-radius: 6px; text-align: center; border: 1px solid #e5e7eb; }
        .stat-value { font-size: 24px; font-weight: bold; color: #7c3aed; }
        .stat-label { font-size: 12px; color: #6b7280; text-transform: uppercase; }
        .footer { text-align: center; margin-top: 30px; color: #6b7280; font-size: 14px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🤖 Carnet generado automáticamente</h1>
        </div>

        <div class="content">
            <p>Estimado/a administrador/a,</p>

            <p>El sistema de carnetización automática ha procesado exitosamente una nueva solicitud en <strong>{{ $league_name }}</strong>.</p>

            <div class="card-info">
                <h3>📊 Resumen de la generación</h3>

                <table style="width: 100%; border-collapse: collapse; margin-top: 15px;">
                    <tr style="border-bottom: 1px solid #e5e7eb;">
                        <td style="padding: 10px 0; font-weight: bold;">Jugadora:</td>
                        <td style="padding: 10px 0;">{{ $player_name }}</td>
                    </tr>
                    <tr style="border-bottom: 1px solid #e5e7eb;">
                        <td style="padding: 10px 0; font-weight: bold;">Club:</td>
                        <td style="padding: 10px 0;">{{ $club_name }}</td>
                    </tr>
                    <tr style="border-bottom: 1px solid #e5e7eb;">
                        <td style="padding: 10px 0; font-weight: bold;">Número de carnet:</td>
                        <td style="padding: 10px 0; color: #7c3aed; font-weight: bold;">{{ $card_number }}</td>
                    </tr>
                    <tr style="border-bottom: 1px solid #e5e7eb;">
                        <td style="padding: 10px 0; font-weight: bold;">Fecha y hora:</td>
                        <td style="padding: 10px 0;">{{ $generated_at }}</td>
                    </tr>
                    @if($processing_time)
                    <tr>
                        <td style="padding: 10px 0; font-weight: bold;">Tiempo de procesamiento:</td>
                        <td style="padding: 10px 0;">{{ $processing_time }}</td>
                    </tr>
                    @endif
                </table>
            </div>

            <div style="background: #f0f9ff; padding: 20px; border-radius: 8px; border-left: 4px solid #3b82f6;">
                <h4 style="margin-top: 0; color: #1e40af;">🔄 Proceso automático</h4>
                <ul style="margin: 10px 0; padding-left: 20px;">
                    <li>✅ Documentos validados automáticamente</li>
                    <li>✅ Número único generado</li>
                    <li>✅ Código QR de verificación creado</li>
                    <li>✅ Notificaciones enviadas</li>
                    <li>✅ Registro de auditoría completado</li>
                </ul>
            </div>

            <div style="background: #fffbeb; padding: 15px; border-radius: 6px; margin: 20px 0; border-left: 4px solid #f59e0b;">
                <p><strong>📈 Información del sistema:</strong></p>
                <p style="margin: 5px 0; font-size: 14px;">Este carnet fue generado completamente de forma automática sin intervención manual, garantizando consistencia y reduciendo errores.</p>
            </div>

            <p>El carnet está ahora activo y la jugadora puede participar en competencias oficiales. Todas las notificaciones han sido enviadas automáticamente.</p>

            <p>Para revisar estadísticas detalladas o gestionar carnets, accede al panel de administración.</p>

            <p>Saludos,<br>
            <strong>Sistema VolleyPass</strong></p>
        </div>

        <div class="footer">
            <p>Sistema de Carnetización Automática - VolleyPass<br>
            Mensaje generado automáticamente</p>
        </div>
    </div>
</body>
</html>
