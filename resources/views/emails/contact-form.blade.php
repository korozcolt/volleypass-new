<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Solicitud de Demo - VolleyPass</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #3b82f6; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { background: #f9f9f9; padding: 20px; border-radius: 0 0 8px 8px; }
        .info-item { margin-bottom: 15px; }
        .label { font-weight: bold; color: #374151; }
        .value { color: #1f2937; }
        .message-box { background: white; padding: 15px; border-radius: 6px; margin-top: 15px; border-left: 4px solid #3b82f6; }
    </style>
</head>
<body>
    <div class="header">
        <h1>🏐 VolleyPass</h1>
        <p>Nueva Solicitud de Demo</p>
    </div>
    <div class="content">
        <p><strong>Fecha:</strong> {{ $contactData['submitted_at'] }}</p>
        
        <div class="info-item">
            <span class="label">Nombre Completo:</span>
            <span class="value">{{ $contactData['full_name'] }}</span>
        </div>
        
        <div class="info-item">
            <span class="label">Email:</span>
            <span class="value">{{ $contactData['email'] }}</span>
        </div>
        
        <div class="info-item">
            <span class="label">Teléfono:</span>
            <span class="value">{{ $contactData['phone'] }}</span>
        </div>
        
        @if(!empty($contactData['phone_secondary']))
        <div class="info-item">
            <span class="label">Teléfono Secundario:</span>
            <span class="value">{{ $contactData['phone_secondary'] }}</span>
        </div>
        @endif
        
        <div class="info-item">
            <span class="label">Departamento:</span>
            <span class="value">{{ $contactData['department'] }}</span>
        </div>
        
        <div class="info-item">
            <span class="label">Municipio:</span>
            <span class="value">{{ $contactData['city'] }}</span>
        </div>
        
        <div class="message-box">
            <div class="label">Mensaje:</div>
            <div class="value">{!! nl2br(e($contactData['message'])) !!}</div>
        </div>
    </div>
</body>
</html>