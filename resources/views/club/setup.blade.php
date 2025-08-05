<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Configuración del Club - VolleyPass</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/js/club-setup.tsx'])
</head>
<body>
    <div id="club-setup-root"></div>
    
    <!-- Datos del club para JavaScript -->
    <script type="application/json" data-club>
        @json($club ?? null)
    </script>
</body>
</html>