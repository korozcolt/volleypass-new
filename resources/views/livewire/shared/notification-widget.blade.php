<div x-data="{ 
    open: false, 
    notifications: [
        { id: 1, type: 'info', title: 'Próximo partido', message: 'vs Águilas Doradas - 15 Feb 19:00', time: '2h', read: false },
        { id: 2, type: 'success', title: 'Estadísticas actualizadas', message: 'Tus estadísticas del último partido han sido actualizadas', time: '1d', read: false },
        { id: 3, type: 'warning', title: 'Documentación', message: 'Tu carnet vence en 30 días', time: '3d', read: true }
    ],
    unreadCount: 2,
    markAsRead(id) {
        const notification = this.notifications.find(n => n.id === id);
        if (notification && !notification.read) {
            notification.read = true;
            this.unreadCount--;
        }
    },
    markAllAsRead() {
        this.notifications.forEach(n => n.read = true);
        this.unreadCount = 0;
    }
}" class="relative">
    <button @click="open = !open" class="relative p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
        <svg class="w-5 h-5 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM11 19H7a2 2 0 01-2-2V7a2 2 0 012-2h10a2 2 0 012 2v4M9 9h6m-6 4h6"></path>
        </svg>
        <span x-show="unreadCount > 0" 
              x-text="unreadCount" 
              class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center font-medium">
        </span>
    </button>

    <div x-show="open" 
         @click.away="open = false"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 transform scale-95"
         x-transition:enter-end="opacity-100 transform scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 transform scale-100"
         x-transition:leave-end="opacity-0 transform scale-95"
         class="absolute right-0 mt-2 w-80 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 z-50">
        
        <div class="p-4 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Notificaciones</h3>
                <button @click="markAllAsRead()" 
                        x-show="unreadCount > 0"
                        class="text-sm text-blue-600 dark:text-blue-400 hover:underline">
                    Marcar todas como leídas
                </button>
            </div>
        </div>

        <div class="max-h-96 overflow-y-auto">
            <template x-for="notification in notifications" :key="notification.id">
                <div @click="markAsRead(notification.id)" 
                     class="p-4 border-b border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer"
                     :class="{ 'bg-blue-50 dark:bg-blue-900/20': !notification.read }">
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0 mt-1">
                            <div x-show="notification.type === 'info'" class="w-2 h-2 bg-blue-500 rounded-full"></div>
                            <div x-show="notification.type === 'success'" class="w-2 h-2 bg-green-500 rounded-full"></div>
                            <div x-show="notification.type === 'warning'" class="w-2 h-2 bg-yellow-500 rounded-full"></div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 dark:text-white" x-text="notification.title"></p>
                            <p class="text-sm text-gray-600 dark:text-gray-300 mt-1" x-text="notification.message"></p>
                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-2" x-text="notification.time"></p>
                        </div>
                        <div x-show="!notification.read" class="flex-shrink-0">
                            <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <div class="p-4 border-t border-gray-200 dark:border-gray-700">
            <a href="/dashboard/player/notifications" class="block text-center text-sm text-blue-600 dark:text-blue-400 hover:underline">
                Ver todas las notificaciones
            </a>
        </div>
    </div>
</div>
