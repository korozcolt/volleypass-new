<?php

namespace App\Livewire;

use Livewire\Component;

class ProjectProgress extends Component
{
    public $modules = [
        [
            'name' => 'Backend & API',
            'icon' => 'server',
            'progress' => 100,
            'color' => 'emerald',
            'description' => 'Arquitectura completa con 45+ tablas, servicios, jobs y API REST. Sistema robusto y escalable.'
        ],
        [
            'name' => 'Panel de Administración',
            'icon' => 'settings',
            'progress' => 100,
            'color' => 'blue',
            'description' => 'Filament 3.x completamente implementado con gestión de usuarios, roles y configuraciones.'
        ],
        [
            'name' => 'Sistema de Federación',
            'icon' => 'shield',
            'progress' => 100,
            'color' => 'purple',
            'description' => 'Carnetización automática, pagos, validaciones y control federativo completamente funcional.'
        ],
        [
            'name' => 'Carnets Digitales',
            'icon' => 'qr-code',
            'progress' => 95,
            'color' => 'green',
            'description' => 'Generación automática de QR, verificación instantánea y gestión de estados implementada.'
        ],
        [
            'name' => 'Gestión de Torneos',
            'icon' => 'trophy',
            'progress' => 85,
            'color' => 'orange',
            'description' => 'Creación, programación y seguimiento de torneos. Desarrollando brackets automáticos.'
        ],
        [
            'name' => 'Dashboards Públicos',
            'icon' => 'chart-bar',
            'progress' => 75,
            'color' => 'cyan',
            'description' => 'Estadísticas públicas y reportes básicos. Implementando visualizaciones avanzadas.'
        ],
        [
            'name' => 'Tablero de Árbitros',
            'icon' => 'whistle',
            'progress' => 60,
            'color' => 'amber',
            'description' => 'Sistema de asignación y gestión de árbitros en desarrollo. Funcionalidades básicas implementadas.'
        ]
    ];

    public function render()
    {
        return view('livewire.project-progress');
    }
}
