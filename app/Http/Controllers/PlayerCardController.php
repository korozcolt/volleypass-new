<?php

namespace App\Http\Controllers;

use App\Models\PlayerCard;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PlayerCardController extends Controller
{
    /**
     * Mostrar la tarjeta de un jugador por su número de carnet
     */
    public function show(string $cardNumber): View
    {
        $card = PlayerCard::where('card_number', $cardNumber)
            ->with([
                'player.user',
                'player.currentClub',
                'league'
            ])
            ->firstOrFail();

        return view('player-card', compact('card'));
    }

    /**
     * Descargar el carnet como PDF
     */
    public function download(string $cardNumber)
    {
        $card = PlayerCard::where('card_number', $cardNumber)
            ->with([
                'player.user',
                'player.currentClub',
                'league'
            ])
            ->firstOrFail();

        // Por ahora, redirigir a la vista del carnet
        // En el futuro se puede implementar generación de PDF
        return redirect()->route('player.card.show', $cardNumber);
    }
}