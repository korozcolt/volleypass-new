<div class="flex items-center space-x-2">
    <x-app-logo-dynamic size="md" />
    <div class="grid flex-1 text-start text-sm">
        <span class="mb-0.5 truncate leading-tight font-semibold text-gray-900 dark:text-white">
            {{ \App\Models\SystemConfiguration::getValue('app.name', 'VolleyPass') }}
        </span>
    </div>
</div>
