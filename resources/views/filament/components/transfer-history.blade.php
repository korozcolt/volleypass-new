<div class="space-y-3">
    @if($transfers->isEmpty())
        <p class="text-sm text-gray-500">No hay transferencias registradas.</p>
    @else
        @foreach($transfers as $transfer)
            <div class="flex items-start space-x-3 p-3 bg-gray-50 rounded-lg">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                        </svg>
                    </div>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-medium text-gray-900">
                            @if($transfer->from_club_id && $transfer->to_club_id)
                                Transferencia: {{ $transfer->fromClub->name ?? 'Club anterior' }} → {{ $transfer->toClub->name ?? 'Nuevo club' }}
                            @elseif($transfer->to_club_id)
                                Registro inicial en {{ $transfer->toClub->name ?? 'Club' }}
                            @else
                                Transferencia registrada
                            @endif
                        </p>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            @if($transfer->status === 'approved') bg-green-100 text-green-800
                            @elseif($transfer->status === 'pending') bg-yellow-100 text-yellow-800
                            @elseif($transfer->status === 'rejected') bg-red-100 text-red-800
                            @else bg-gray-100 text-gray-800
                            @endif">
                            {{ ucfirst($transfer->status ?? 'pendiente') }}
                        </span>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">
                        {{ $transfer->created_at->format('d/m/Y H:i') }}
                        @if($transfer->approved_at)
                            • Aprobada: {{ $transfer->approved_at->format('d/m/Y H:i') }}
                        @endif
                    </p>
                    @if($transfer->notes)
                        <p class="text-xs text-gray-600 mt-2 italic">
                            {{ $transfer->notes }}
                        </p>
                    @endif
                </div>
            </div>
        @endforeach
    @endif
</div>