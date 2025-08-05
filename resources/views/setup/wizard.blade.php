<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Configuración Inicial - VolleyPass</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/js/setup-wizard.tsx'])
</head>
<body>
    <div id="setup-wizard-root"></div>
</body>
</html>