<?php

namespace App\Livewire;

use Livewire\Component;

class InteractiveDemo extends Component
{
    public $activeTab = 'federados';

    public $demoData = [
        'federados' => [
            'title' => 'Registro Oficial de Jugadoras',
            'description' => 'Sistema completo de carnetización y gestión federativa con validación automática de documentos y pagos.',
            'icon' => 'shield',
            'color' => 'blue',
            'count' => 1247,
            'label' => 'Jugadoras Federadas',
            'features' => [
                'Carnetización automática con QR',
                'Validación de documentos médicos',
                'Control de pagos federativos',
                'Historial deportivo completo',
                'Verificación instantánea en partidos'
            ]
        ],
        'descentralizados' => [
            'title' => 'Ligas Descentralizadas',
            'description' => 'Gestión independiente de ligas alternas con reglas configurables y autonomía organizativa.',
            'icon' => 'globe',
            'color' => 'purple',
            'count' => 89,
            'label' => 'Clubes Registrados',
            'features' => [
                'Configuración de reglas personalizadas',
                'Gestión autónoma de categorías',
                'Control de traspasos independiente',
                'Torneos alternativos flexibles',
                'Reportes personalizados'
            ]
        ],
        'torneos' => [
            'title' => 'Gestión Inteligente de Torneos',
            'description' => 'Plataforma completa para organización de competencias con seguimiento en tiempo real y estadísticas avanzadas.',
            'icon' => 'trophy',
            'color' => 'green',
            'count' => 23,
            'label' => 'Torneos Activos',
            'features' => [
                'Programación automática de partidos',
                'Marcadores y estadísticas en vivo',
                'Generación automática de brackets',
                'Reportes y certificados digitales',
                'Integración con sistema de pagos'
            ]
        ]
    ];

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function render()
    {
        return view('livewire.interactive-demo');
    }
}
