@if($player->current_card)
    <div class="w-full max-w-2xl mx-auto border rounded-lg overflow-hidden" style="aspect-ratio: 6/9; height: 80vh;">
        <iframe 
            src="{{ route('player.card.show', $player->current_card->card_number) }}"
            class="w-full h-full border-0"
            title="Carnet de {{ $player->user->full_name }}"
            loading="lazy"
        ></iframe>
    </div>
@else
    <div class="p-8 text-center text-gray-500">
        <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
        </svg>
        <h3 class="text-lg font-medium text-gray-900 mb-2">Sin carnet activo</h3>
        <p class="text-gray-500">Este jugador no tiene un carnet activo asignado.</p>
    </div>
@endif