<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            <div class="flex items-center gap-2">
                <x-heroicon-o-bell class="h-5 w-5 text-warning-500" />
                Alertas del Sistema
            </div>
        </x-slot>

        <div class="space-y-4">
            @php
                $allAlerts = collect([
                    ...$this->getViewData()['medical_alerts'],
                    ...$this->getViewData()['payment_alerts'],
                    ...$this->getViewData()['performance_alerts'],
                    ...$this->getViewData()['system_alerts'],
                ]);
            @endphp

            @if($allAlerts->isEmpty())
                <div class="flex items-center justify-center py-8">
                    <div class="text-center">
                        <x-heroicon-o-check-circle class="mx-auto h-12 w-12 text-success-500" />
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">
                            Todo en orden
                        </h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            No hay alertas activas en el sistema.
                        </p>
                    </div>
                </div>
            @else
                @foreach($allAlerts->take(10) as $alert)
                    <div class="rounded-lg border p-4 {{ $this->getAlertClasses($alert['type']) }}">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                @svg($alert['icon'], 'h-5 w-5 ' . $this->getIconClasses($alert['type']))
                            </div>
                            <div class="ml-3 flex-1">
                                <div class="flex items-center justify-between">
                                    <h3 class="text-sm font-medium {{ $this->getTitleClasses($alert['type']) }}">
                                        {{ $alert['title'] }}
                                    </h3>
                                    @if(isset($alert['count']))
                                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $this->getBadgeClasses($alert['type']) }}">
                                            {{ $alert['count'] }}
                                        </span>
                                    @endif
                                </div>
                                <p class="mt-1 text-sm {{ $this->getMessageClasses($alert['type']) }}">
                                    {{ $alert['message'] }}
                                </p>
                                @if(isset($alert['action_url']) && isset($alert['action_label']))
                                    <div class="mt-3">
                                        <a href="{{ $alert['action_url'] }}" 
                                           class="inline-flex items-center rounded-md px-3 py-2 text-sm font-semibold shadow-sm focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 {{ $this->getActionClasses($alert['type']) }}">
                                            {{ $alert['action_label'] }}
                                            <x-heroicon-m-arrow-right class="ml-1.5 h-4 w-4" />
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach

                @if($allAlerts->count() > 10)
                    <div class="text-center pt-4">
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Y {{ $allAlerts->count() - 10 }} alertas más...
                        </p>
                    </div>
                @endif
            @endif
        </div>

        @if($allAlerts->isNotEmpty())
            <x-slot name="footerActions">
                <div class="flex items-center justify-between w-full">
                    <div class="text-xs text-gray-500 dark:text-gray-400">
                        Última actualización: {{ now()->format('H:i:s') }}
                    </div>
                    <div class="flex gap-2">
                        <x-filament::button
                            size="sm"
                            color="gray"
                            outlined
                            wire:click="$refresh"
                        >
                            <x-heroicon-m-arrow-path class="h-4 w-4" />
                            Actualizar
                        </x-filament::button>
                        
                        <x-filament::button
                            size="sm"
                            color="primary"
                            tag="a"
                            href="/admin/system/alerts"
                        >
                            Ver todas
                        </x-filament::button>
                    </div>
                </div>
            </x-slot>
        @endif
    </x-filament::section>

    @script
    <script>
        // Auto-refresh cada 30 segundos
        setInterval(() => {
            $wire.$refresh();
        }, 30000);
    </script>
    @endscript
</x-filament-widgets::widget>

@php
    // Métodos helper para las clases CSS
    function getAlertClasses($type) {
        return match($type) {
            'danger' => 'border-red-200 bg-red-50 dark:border-red-800 dark:bg-red-950',
            'warning' => 'border-yellow-200 bg-yellow-50 dark:border-yellow-800 dark:bg-yellow-950',
            'info' => 'border-blue-200 bg-blue-50 dark:border-blue-800 dark:bg-blue-950',
            'success' => 'border-green-200 bg-green-50 dark:border-green-800 dark:bg-green-950',
            default => 'border-gray-200 bg-gray-50 dark:border-gray-800 dark:bg-gray-950',
        };
    }
    
    function getIconClasses($type) {
        return match($type) {
            'danger' => 'text-red-500',
            'warning' => 'text-yellow-500',
            'info' => 'text-blue-500',
            'success' => 'text-green-500',
            default => 'text-gray-500',
        };
    }
    
    function getTitleClasses($type) {
        return match($type) {
            'danger' => 'text-red-800 dark:text-red-200',
            'warning' => 'text-yellow-800 dark:text-yellow-200',
            'info' => 'text-blue-800 dark:text-blue-200',
            'success' => 'text-green-800 dark:text-green-200',
            default => 'text-gray-800 dark:text-gray-200',
        };
    }
    
    function getMessageClasses($type) {
        return match($type) {
            'danger' => 'text-red-700 dark:text-red-300',
            'warning' => 'text-yellow-700 dark:text-yellow-300',
            'info' => 'text-blue-700 dark:text-blue-300',
            'success' => 'text-green-700 dark:text-green-300',
            default => 'text-gray-700 dark:text-gray-300',
        };
    }
    
    function getBadgeClasses($type) {
        return match($type) {
            'danger' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
            'warning' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
            'info' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
            'success' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
            default => 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200',
        };
    }
    
    function getActionClasses($type) {
        return match($type) {
            'danger' => 'bg-red-600 text-white hover:bg-red-500 focus-visible:outline-red-600',
            'warning' => 'bg-yellow-600 text-white hover:bg-yellow-500 focus-visible:outline-yellow-600',
            'info' => 'bg-blue-600 text-white hover:bg-blue-500 focus-visible:outline-blue-600',
            'success' => 'bg-green-600 text-white hover:bg-green-500 focus-visible:outline-green-600',
            default => 'bg-gray-600 text-white hover:bg-gray-500 focus-visible:outline-gray-600',
        };
    }
@endphp