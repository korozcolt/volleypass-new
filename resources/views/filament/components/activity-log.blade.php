<div class="space-y-3">
    @if($activities->isEmpty())
        <p class="text-sm text-gray-500">No hay actividad registrada.</p>
    @else
        @foreach($activities as $activity)
            <div class="flex items-start space-x-3 p-3 bg-gray-50 rounded-lg">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900">
                        {{ $activity->description }}
                    </p>
                    <p class="text-xs text-gray-500">
                        {{ $activity->created_at->diffForHumans() }}
                    </p>
                    @if($activity->properties && count($activity->properties) > 0)
                        <div class="mt-2 text-xs text-gray-600">
                            @foreach($activity->properties as $key => $value)
                                <span class="inline-block bg-gray-200 rounded px-2 py-1 mr-1 mb-1">
                                    {{ $key }}: {{ is_array($value) ? json_encode($value) : $value }}
                                </span>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    @endif
</div>