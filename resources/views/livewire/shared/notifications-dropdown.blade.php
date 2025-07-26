<div class="max-h-96 overflow-y-auto">
    <!-- Header -->
    <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Notificaciones</h3>
            @if($unreadCount > 0)
                <button wire:click="markAllAsRead" 
                        class="text-sm text-vp-primary-600 hover:text-vp-primary-700 font-medium">
                    Marcar todas como leídas
                </button>
            @endif
        </div>
    </div>

    <!-- Notifications List -->
    <div class="divide-y divide-gray-200 dark:divide-gray-700">
        @forelse($notifications as $notification)
            <div class="px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors {{ $notification['read_at'] ? '' : 'bg-blue-50 dark:bg-blue-900/20' }}"
                 wire:key="notification-{{ $notification['id'] }}">
                <div class="flex items-start space-x-3">
                    <!-- Icon based on type -->
                    <div class="flex-shrink-0 mt-1">
                        @switch($notification['type'])
                            @case('match_started')
                                <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                                @break
                            @case('match_reminder')
                                <div class="w-2 h-2 bg-yellow-500 rounded-full"></div>
                                @break
                            @case('injury_report')
                                <div class="w-2 h-2 bg-red-500 rounded-full"></div>
                                @break
                            @default
                                <div class="w-2 h-2 bg-gray-500 rounded-full"></div>
                        @endswitch
                    </div>
                    
                    <!-- Content -->
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                            {{ $notification['data']['title'] ?? 'Notificación' }}
                        </p>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                            {{ $notification['data']['message'] ?? '' }}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-500 mt-2">
                            {{ \Carbon\Carbon::parse($notification['created_at'])->diffForHumans() }}
                        </p>
                    </div>
                    
                    <!-- Mark as read button -->
                    @if(!$notification['read_at'])
                        <button wire:click="markAsRead('{{ $notification['id'] }}')" 
                                class="flex-shrink-0 text-vp-primary-600 hover:text-vp-primary-700">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    @endif
                </div>
            </div>
        @empty
            <div class="px-4 py-8 text-center">
                <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM10.07 2.82l-.03.03a1.51 1.51 0 000 2.13l1.06 1.06 8.49 8.48a1.51 1.51 0 002.12 0l.03-.03a1.51 1.51 0 000-2.12L12.25 2.88a1.51 1.51 0 00-2.13 0l-.05.05z" />
                </svg>
                <p class="text-gray-500 dark:text-gray-400">No tienes notificaciones</p>
            </div>
        @endforelse
    </div>

    <!-- Footer -->
    @if(count($notifications) > 0)
        <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
            <a href="{{ route('notifications.index') }}" 
               class="block text-center text-sm text-vp-primary-600 hover:text-vp-primary-700 font-medium">
                Ver todas las notificaciones
            </a>
        </div>
    @endif
</div>