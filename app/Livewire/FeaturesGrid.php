<?php

namespace App\Livewire;

use Livewire\Component;

class FeaturesGrid extends Component
{
    public $features = [
        [
            'icon' => 'id-card',
            'title' => 'Carnetización Automática',
            'description' => 'Generación automática de carnets digitales con QR único para cada jugadora federada.',
            'color' => 'blue'
        ],
        [
            'icon' => 'shield-check',
            'title' => 'Sistema de Federación',
            'description' => 'Control completo de pagos federativos, validación de documentos y gestión de membresías.',
            'color' => 'purple'
        ],
        [
            'icon' => 'settings-2',
            'title' => 'Configuración Flexible',
            'description' => 'Cada liga configura sus propias categorías, reglas y normativas de forma independiente.',
            'color' => 'green'
        ],
        [
            'icon' => 'arrow-right-left',
            'title' => 'Control de Traspasos',
            'description' => 'Sistema automatizado de autorización y seguimiento de movimientos entre clubes.',
            'color' => 'orange'
        ],
        [
            'icon' => 'qr-code',
            'title' => 'Verificación Instantánea',
            'description' => 'Verificación QR en tiempo real para validar elegibilidad de jugadoras en partidos.',
            'color' => 'red'
        ],
        [
            'icon' => 'trophy',
            'title' => 'Gestión de Torneos',
            'description' => 'Plataforma completa para crear, programar y gestionar competencias con estadísticas en vivo.',
            'color' => 'indigo'
        ]
    ];

    public function render()
    {
        return view('livewire.features-grid');
    }
}
