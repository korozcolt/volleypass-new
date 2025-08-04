@extends('layouts.setup')

@section('title', 'Configuración Inicial del Sistema')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="mx-auto h-16 w-16 bg-indigo-600 rounded-full flex items-center justify-center mb-4">
                <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Configuración Inicial del Sistema</h1>
            <p class="text-lg text-gray-600">Configure su sistema de gestión de voleibol paso a paso</p>
        </div>

        <!-- Progress Bar -->
        <div class="mb-8">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-sm font-medium text-gray-700">Progreso General</span>
                    <span class="text-sm font-medium text-indigo-600">{{ $progress }}%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-indigo-600 h-2 rounded-full transition-all duration-300" style="width: {{ $progress }}%"></div>
                </div>
            </div>
        </div>

        <!-- Steps Overview -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
            <h2 class="text-xl font-semibold text-gray-900 mb-6">Pasos de Configuración</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($steps as $stepNumber => $stepInfo)
                    @php
                        $stepState = App\Models\SetupState::where('step', $stepNumber)->first();
                        $isCompleted = $stepState && $stepState->status === App\Enums\SetupStatus::Completed;
                        $isCurrent = $stepNumber === $currentStep;
                    @endphp
                    <div class="relative">
                        <div class="@if($isCompleted) bg-green-50 border-green-200 @elseif($isCurrent) bg-indigo-50 border-indigo-200 @else bg-gray-50 border-gray-200 @endif border rounded-lg p-4 transition-all duration-200 hover:shadow-md">
                            <div class="flex items-center mb-2">
                                <div class="@if($isCompleted) bg-green-500 @elseif($isCurrent) bg-indigo-500 @else bg-gray-300 @endif w-8 h-8 rounded-full flex items-center justify-center mr-3">
                                    @if($isCompleted)
                                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                    @else
                                        <span class="@if($isCurrent) text-white @else text-gray-600 @endif text-sm font-medium">{{ $stepNumber }}</span>
                                    @endif
                                </div>
                                <h3 class="@if($isCompleted) text-green-900 @elseif($isCurrent) text-indigo-900 @else text-gray-700 @endif font-medium">{{ $stepInfo['title'] }}</h3>
                            </div>
                            <p class="@if($isCompleted) text-green-700 @elseif($isCurrent) text-indigo-700 @else text-gray-500 @endif text-sm">{{ $stepInfo['description'] }}</p>
                            
                            @if($isCurrent)
                                <div class="mt-3">
                                    <a href="{{ route('setup.wizard.step', $stepNumber) }}" class="inline-flex items-center px-3 py-1 border border-transparent text-sm font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 transition-colors duration-200">
                                        Continuar
                                        <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </a>
                                </div>
                            @elseif($isCompleted)
                                <div class="mt-3">
                                    <a href="{{ route('setup.wizard.step', $stepNumber) }}" class="inline-flex items-center px-3 py-1 border border-transparent text-sm font-medium rounded-md text-green-700 bg-green-100 hover:bg-green-200 transition-colors duration-200">
                                        Revisar
                                        <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-between items-center">
            <div>
                @if(app()->environment('local'))
                    <form action="{{ route('setup.wizard.reset') }}" method="POST" class="inline" onsubmit="return confirm('¿Está seguro de reiniciar la configuración?')">
                        @csrf
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-red-300 text-sm font-medium rounded-md text-red-700 bg-white hover:bg-red-50 transition-colors duration-200">
                            <svg class="mr-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            Reiniciar Setup
                        </button>
                    </form>
                @endif
            </div>
            
            <div>
                @if($currentStep)
                    <a href="{{ route('setup.wizard.step', $currentStep) }}" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 transition-colors duration-200">
                        @if($currentStep === 1)
                            Comenzar Configuración
                        @else
                            Continuar Configuración
                        @endif
                        <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                        </svg>
                    </a>
                @else
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-green-600 hover:bg-green-700 transition-colors duration-200">
                        <svg class="mr-2 w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        Ir al Dashboard
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection