<div class="flex items-center space-x-3">
    @if(\App\Models\SystemConfiguration::getValue('branding.logo_light'))
        <img 
            src="{{ asset(\App\Models\SystemConfiguration::getValue('branding.logo_light')) }}" 
            alt="{{ \App\Models\SystemConfiguration::getValue('app.name', 'VolleyPass') }}" 
            class="h-8 w-auto"
        >
    @endif
    <span class="text-xl font-semibold text-gray-800 dark:text-gray-200">
        {{ \App\Models\SystemConfiguration::getValue('app.name', 'VolleyPass') }} Admin
    </span>
</div>