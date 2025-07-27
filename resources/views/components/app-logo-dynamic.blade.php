@props([
    'variant' => 'auto', // 'auto', 'light', 'dark'
    'size' => 'md', // 'sm', 'md', 'lg', 'xl'
    'class' => ''
])

@php
    $sizeClasses = [
        'sm' => 'h-8',
        'md' => 'h-10',
        'lg' => 'h-12',
        'xl' => 'h-16'
    ];
    
    $logoLight = \App\Models\SystemConfiguration::getValue('branding.logo_light', '/images/logo-volley_pass_black_back.png');
    $logoDark = \App\Models\SystemConfiguration::getValue('branding.logo_dark', '/images/logo-volley_pass_white_back.png');
    $appName = \App\Models\SystemConfiguration::getValue('app.name', 'VolleyPass');
@endphp

@if($variant === 'auto')
    <!-- Logo que se adapta automáticamente al modo oscuro/claro -->
    <img 
        src="{{ $logoLight }}" 
        alt="{{ $appName }}" 
        class="{{ $sizeClasses[$size] }} w-auto dark:hidden {{ $class }}"
    >
    <img 
        src="{{ $logoDark }}" 
        alt="{{ $appName }}" 
        class="{{ $sizeClasses[$size] }} w-auto hidden dark:block {{ $class }}"
    >
@elseif($variant === 'light')
    <!-- Logo para fondos claros -->
    <img 
        src="{{ $logoLight }}" 
        alt="{{ $appName }}" 
        class="{{ $sizeClasses[$size] }} w-auto {{ $class }}"
    >
@elseif($variant === 'dark')
    <!-- Logo para fondos oscuros -->
    <img 
        src="{{ $logoDark }}" 
        alt="{{ $appName }}" 
        class="{{ $sizeClasses[$size] }} w-auto {{ $class }}"
    >
@endif