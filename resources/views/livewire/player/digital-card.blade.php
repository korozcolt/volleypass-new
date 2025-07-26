<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Mi Carnet Digital') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Carnet Digital -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg mb-8">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Carnet Digital</h3>
                        <div class="flex space-x-2">
                            <button wire:click="downloadCard" 
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                </svg>
                                Descargar
                            </button>
                            <button wire:click="shareCard" 
                                    class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M15 8a3 3 0 10-2.977-2.63l-4.94 2.47a3 3 0 100 4.319l4.94 2.47a3 3 0 10.895-1.789l-4.94-2.47a3.027 3.027 0 000-.74l4.94-2.47C13.456 7.68 14.19 8 15 8z"/>
                                </svg>
                                Compartir
                            </button>
                        </div>
                    </div>

                    <!-- Carnet Visual -->
                    <div class="max-w-md mx-auto">
                        <div class="bg-gradient-to-br from-blue-600 to-purple-700 rounded-xl p-6 text-white shadow-2xl transform hover:scale-105 transition-transform duration-300">
                            <!-- Header del Carnet -->
                            <div class="flex items-center justify-between mb-6">
                                <div class="text-xs font-semibold opacity-90">VOLLEYPASS</div>
                                <div class="text-xs opacity-75">{{ now()->year }}</div>
                            </div>

                            <!-- Foto y Info Principal -->
                            <div class="flex items-center space-x-4 mb-6">
                                @if($player->user->profile_photo_path)
                                    <img class="h-16 w-16 rounded-full border-2 border-white/20" 
                                         src="{{ Storage::url($player->user->profile_photo_path) }}" 
                                         alt="{{ $player->user->name }}">
                                @else
                                    <div class="h-16 w-16 rounded-full bg-white/20 border-2 border-white/20 flex items-center justify-center">
                                        <span class="text-xl font-bold">{{ substr($player->user->name, 0, 1) }}</span>
                                    </div>
                                @endif
                                <div class="flex-1">
                                    <h3 class="text-lg font-bold">{{ $player->user->name }}</h3>
                                    @if($player->position)
                                        <p class="text-sm opacity-90">{{ $player->position }}</p>
                                    @endif
                                    @if($player->jersey_number)
                                        <p class="text-xs opacity-75">#{{ $player->jersey_number }}</p>
                                    @endif
                                </div>
                            </div>

                            <!-- Información del Jugador -->
                            <div class="grid grid-cols-2 gap-4 mb-6 text-xs">
                                <div>
                                    <div class="opacity-75 mb-1">CI/Documento</div>
                                    <div class="font-semibold">{{ $player->document_number ?? 'No registrado' }}</div>
                                </div>
                                <div>
                                    <div class="opacity-75 mb-1">Fecha Nac.</div>
                                    <div class="font-semibold">
                                        {{ $player->birth_date ? $player->birth_date->format('d/m/Y') : 'No registrada' }}
                                    </div>
                                </div>
                                @if($player->height)
                                    <div>
                                        <div class="opacity-75 mb-1">Altura</div>
                                        <div class="font-semibold">{{ $player->height }} cm</div>
                                    </div>
                                @endif
                                @if($player->weight)
                                    <div>
                                        <div class="opacity-75 mb-1">Peso</div>
                                        <div class="font-semibold">{{ $player->weight }} kg</div>
                                    </div>
                                @endif
                            </div>

                            <!-- QR Code -->
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <div class="text-xs opacity-75 mb-1">Estado</div>
                                    <div class="flex items-center">
                                        <div class="w-2 h-2 bg-green-400 rounded-full mr-2"></div>
                                        <span class="text-xs font-semibold">{{ $cardStatus ?? 'Activo' }}</span>
                                    </div>
                                </div>
                                <div class="bg-white p-2 rounded-lg">
                                    @if($qrCode)
                                        {!! $qrCode !!}
                                    @else
                                        <div class="w-16 h-16 bg-gray-200 rounded flex items-center justify-center">
                                            <svg class="w-8 h-8 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h3a1 1 0 011 1v3a1 1 0 01-1 1H4a1 1 0 01-1-1V4zm2 2V5h1v1H5zM3 13a1 1 0 011-1h3a1 1 0 011 1v3a1 1 0 01-1 1H4a1 1 0 01-1-1v-3zm2 2v-1h1v1H5zM13 4a1 1 0 011-1h3a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1V4zm2 2V5h1v1h-1zM11 4a1 1 0 100-2 1 1 0 000 2zM11 7a1 1 0 100-2 1 1 0 000 2zM11 10a1 1 0 100-2 1 1 0 000 2zM11 13a1 1 0 100-2 1 1 0 000 2zM11 16a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/>
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Footer -->
                            <div class="mt-4 pt-4 border-t border-white/20 text-center">
                                <div class="text-xs opacity-75">ID: {{ $player->id ?? 'VP' . str_pad($player->user->id, 6, '0', STR_PAD_LEFT) }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Información Médica -->
            @if($medicalInfo)
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg mb-8">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Información Médica</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @if($medicalInfo['blood_type'])
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tipo de Sangre</label>
                                    <div class="text-sm text-gray-900 dark:text-white">{{ $medicalInfo['blood_type'] }}</div>
                                </div>
                            @endif
                            @if($medicalInfo['allergies'])
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Alergias</label>
                                    <div class="text-sm text-gray-900 dark:text-white">{{ $medicalInfo['allergies'] }}</div>
                                </div>
                            @endif
                            @if($medicalInfo['emergency_contact'])
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Contacto de Emergencia</label>
                                    <div class="text-sm text-gray-900 dark:text-white">{{ $medicalInfo['emergency_contact'] }}</div>
                                </div>
                            @endif
                            @if($medicalInfo['emergency_phone'])
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Teléfono de Emergencia</label>
                                    <div class="text-sm text-gray-900 dark:text-white">{{ $medicalInfo['emergency_phone'] }}</div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            <!-- Instrucciones de Uso -->
            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-blue-900 dark:text-blue-100 mb-4">Instrucciones de Uso</h3>
                <div class="space-y-3 text-sm text-blue-800 dark:text-blue-200">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span>Presenta este carnet digital en torneos y eventos oficiales</span>
                    </div>
                    <div class="flex items-start">
                        <svg class="w-5 h-5 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span>El código QR permite verificación rápida de tu identidad</span>
                    </div>
                    <div class="flex items-start">
                        <svg class="w-5 h-5 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span>Mantén tu información actualizada en tu perfil</span>
                    </div>
                    <div class="flex items-start">
                        <svg class="w-5 h-5 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span>Puedes descargar una versión PDF para imprimir si es necesario</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>