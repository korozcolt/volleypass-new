<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>VolleyPass - Bienvenido</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/js/welcome.tsx'])
</head>
<body>
    <div id="welcome-root"></div>
</body>
</html>