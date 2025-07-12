<?php
// app/Http/Requests/Api/LoginRequest.php - CREAR

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // API pública para login
    }

    public function rules(): array
    {
        return [
            'email' => [
                'required',
                'email',
                'max:255'
            ],
            'password' => [
                'required',
                'string',
                'min:8'
            ],
            'device_name' => [
                'required',
                'string',
                'max:100',
                'regex:/^[a-zA-Z0-9\s\-_]+$/' // Solo caracteres seguros
            ]
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'El email es obligatorio',
            'email.email' => 'El email debe ser válido',
            'password.required' => 'La contraseña es obligatoria',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres',
            'device_name.required' => 'El nombre del dispositivo es obligatorio',
            'device_name.regex' => 'El nombre del dispositivo contiene caracteres no válidos'
        ];
    }
}
