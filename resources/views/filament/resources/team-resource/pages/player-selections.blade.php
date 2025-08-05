<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Header con información del equipo -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-semibold text-gray-900">{{ $this->record->name }}</h2>
                    <p class="text-sm text-gray-600 mt-1">
                        {{ $this->record->department->name }} - {{ $this->record->gender->getLabel() }} - {{ $this->record->leagueCategory->name }}
                    </p>
                </div>
                <div class="flex space-x-4 text-sm">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-blue-600">{{ $this->record->players()->count() }}</div>
                        <div class="text-gray-600">En Equipo</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-yellow-600">
                            {{ \App\Models\Player::whereHas('club', function($q) { $q->where('department_id', $this->record->department_id); })
                                ->where('gender', $this->record->gender)
                                ->where('league_category_id', $this->record->league_category_id)
                                ->where('selection_status', \App\Enums\SelectionStatus::PRESELECCION)
                                ->count() }}
                        </div>
                        <div class="text-gray-600">Preselección</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-green-600">
                            {{ \App\Models\Player::whereHas('club', function($q) { $q->where('department_id', $this->record->department_id); })
                                ->where('gender', $this->record->gender)
                                ->where('league_category_id', $this->record->league_category_id)
                                ->where('selection_status', \App\Enums\SelectionStatus::SELECCION)
                                ->count() }}
                        </div>
                        <div class="text-gray-600">Selección</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Instrucciones -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800">Instrucciones</h3>
                    <div class="mt-2 text-sm text-blue-700">
                        <ul class="list-disc pl-5 space-y-1">
                            <li>Selecciona jugadoras individualmente usando el botón "Actualizar Selección"</li>
                            <li>Usa las acciones masivas para marcar múltiples jugadoras a la vez</li>
                            <li><strong>Preselección:</strong> Jugadoras consideradas para el equipo</li>
                            <li><strong>Selección:</strong> Jugadoras confirmadas en el equipo</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla de jugadores -->
        {{ $this->table }}
    </div>
</x-filament-panels::page>