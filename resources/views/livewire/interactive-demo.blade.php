<div class="max-w-4xl mx-auto animate-on-scroll">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
        <!-- Tabs -->
        <div class="border-b border-gray-200 dark:border-gray-700">
            <nav class="flex">
                @foreach($demoData as $key => $data)
                <button
                    wire:click="setActiveTab('{{ $key }}')"
                    class="flex-1 py-4 px-6 text-sm font-medium text-center border-b-2 transition-colors {{ $activeTab === $key ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300' }}">
                    {{ ucfirst($key === 'federados' ? 'Equipos Federados' : ($key === 'descentralizados' ? 'Ligas Alternas' : 'Gestión Torneos')) }}
                </button>
                @endforeach
            </nav>
        </div>

        <!-- Tab Content -->
        <div class="p-8">
            @if(isset($demoData[$activeTab]))
            @php $currentData = $demoData[$activeTab] @endphp
            <div class="space-y-6">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 bg-{{ $currentData['color'] }}-100 dark:bg-{{ $currentData['color'] }}-900 rounded-lg flex items-center justify-center">
                        @switch($currentData['icon'])
                            @case('shield')
                                <svg class="w-6 h-6 text-{{ $currentData['color'] }}-600 dark:text-{{ $currentData['color'] }}-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                </svg>
                                @break
                            @case('globe')
                                <svg class="w-6 h-6 text-{{ $currentData['color'] }}-600 dark:text-{{ $currentData['color'] }}-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                @break
                            @case('trophy')
                                <svg class="w-6 h-6 text-{{ $currentData['color'] }}-600 dark:text-{{ $currentData['color'] }}-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                                </svg>
                                @break
                        @endswitch
                    </div>
                    <div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">{{ $currentData['title'] }}</h3>
                        <p class="text-gray-600 dark:text-gray-400">{{ $currentData['description'] }}</p>
                    </div>
                </div>

                <div class="grid md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <h4 class="font-semibold flex items-center text-gray-900 dark:text-white">
                            <svg class="w-4 h-4 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            {{ $activeTab === 'federados' ? 'Registro Oficial' : ($activeTab === 'descentralizados' ? 'Autonomía Total' : 'Control Total') }}
                        </h4>
                        <ul class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                            @foreach($currentData['features'] as $feature)
                            <li>• {{ $feature }}</li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="bg-gradient-to-br from-{{ $currentData['color'] }}-50 to-{{ $currentData['color'] }}-100 dark:from-{{ $currentData['color'] }}-900/20 dark:to-{{ $currentData['color'] }}-800/20 rounded-lg p-6">
                        <div class="text-center">
                            <div class="w-16 h-16 bg-{{ $currentData['color'] }}-600 rounded-full flex items-center justify-center mx-auto mb-4">
                                @switch($currentData['icon'])
                                    @case('shield')
                                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                        </svg>
                                        @break
                                    @case('globe')
                                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        @break
                                    @case('trophy')
                                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                                        </svg>
                                        @break
                                @endswitch
                            </div>
                            <div class="text-2xl font-bold text-{{ $currentData['color'] }}-600">{{ $currentData['count'] }}</div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">{{ $currentData['label'] }}</div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
