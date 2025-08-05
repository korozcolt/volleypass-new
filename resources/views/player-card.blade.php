<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Carnet de Jugador - {{ $card->player->user->name }}</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/player-card.tsx'])
</head>
<body class="antialiased bg-gray-100">
    <div id="player-card-root" 
         data-card='@json($card->load(["player.user", "player.currentClub", "league"]))'>
    </div>
</body>
</html>