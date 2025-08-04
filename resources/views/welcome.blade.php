<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="VolleyPass Sucre - Plataforma Integral de Gestión para Ligas de Voleibol. Sistema de Digitalización y Carnetización Deportiva para la Liga de Voleibol de Sucre, Colombia.">
    <meta name="keywords" content="voleibol, sucre, colombia, carnetización, deportes, liga, torneos, gestión deportiva, volleyball, sports management">
    <meta name="author" content="VolleyPass Sucre">
    <meta name="robots" content="index, follow">
    <meta name="theme-color" content="#475569">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url('/') }}">
    <meta property="og:title" content="VolleyPass Sucre - Gestión Integral de Voleibol">
    <meta property="og:description" content="Sistema de Digitalización y Carnetización Deportiva para la Liga de Voleibol de Sucre, Colombia">
    <meta property="og:image" content="{{ asset('images/logo-volley_pass_white_back.png') }}">
    <meta property="og:image:width" content="500">
    <meta property="og:image:height" content="500">
    <meta property="og:locale" content="es_CO">
    <meta property="og:site_name" content="VolleyPass Sucre">
    
    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="{{ url('/') }}">
    <meta name="twitter:title" content="VolleyPass Sucre - Gestión Integral de Voleibol">
    <meta name="twitter:description" content="Sistema de Digitalización y Carnetización Deportiva para la Liga de Voleibol de Sucre, Colombia">
    <meta name="twitter:image" content="{{ asset('images/logo-volley_pass_white_back.png') }}">
    
    <!-- Favicons -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    
    <!-- Canonical URL -->
    <link rel="canonical" href="{{ url('/') }}">
    
    <title>VolleyPass Sucre - Gestión Integral de Voleibol</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/welcome.js'])
    
    <!-- Structured Data -->
    <script type="application/ld+json">
    {!! json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'SportsOrganization',
        'name' => 'VolleyPass Sucre',
        'description' => 'Sistema de Digitalización y Carnetización Deportiva para la Liga de Voleibol de Sucre, Colombia',
        'url' => url('/'),
        'logo' => asset('images/logo-volley_pass_white_back.png'),
        'address' => [
            '@type' => 'PostalAddress',
            'addressLocality' => 'Sucre',
            'addressCountry' => 'Colombia'
        ],
        'sport' => 'Volleyball',
        'sameAs' => [
            url('/')
        ]
    ], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
    </script>
</head>
<body class="antialiased">
    <div id="welcome-root"></div>
</body>
</html>