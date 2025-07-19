<?php

return [
    'dashboard' => 'Panel de Control',
    'login' => [
        'title' => 'Iniciar Sesión',
        'heading' => 'Iniciar sesión en tu cuenta',
        'form' => [
            'email' => [
                'label' => 'Correo electrónico',
            ],
            'password' => [
                'label' => 'Contraseña',
            ],
            'remember' => [
                'label' => 'Recordarme',
            ],
        ],
        'actions' => [
            'authenticate' => [
                'label' => 'Iniciar sesión',
            ],
        ],
        'messages' => [
            'failed' => 'Estas credenciales no coinciden con nuestros registros.',
        ],
    ],
    'logout' => [
        'title' => 'Cerrar Sesión',
    ],
    'profile' => [
        'title' => 'Perfil',
        'heading' => 'Mi Perfil',
        'form' => [
            'name' => [
                'label' => 'Nombre',
            ],
            'email' => [
                'label' => 'Correo electrónico',
            ],
            'password' => [
                'label' => 'Nueva contraseña',
            ],
            'password_confirmation' => [
                'label' => 'Confirmar contraseña',
            ],
            'current_password' => [
                'label' => 'Contraseña actual',
            ],
        ],
        'actions' => [
            'save' => [
                'label' => 'Guardar cambios',
            ],
        ],
        'messages' => [
            'saved' => 'Perfil actualizado exitosamente.',
        ],
    ],
    'create_record' => [
        'title' => 'Crear :label',
        'breadcrumb' => 'Crear',
    ],
    'edit_record' => [
        'title' => 'Editar :label',
        'breadcrumb' => 'Editar',
    ],
    'view_record' => [
        'title' => 'Ver :label',
        'breadcrumb' => 'Ver',
    ],
    'list_records' => [
        'title' => ':label',
        'breadcrumb' => 'Lista',
    ],
];
