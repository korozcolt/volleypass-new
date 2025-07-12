<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\EventType;

class VerifyQrRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // API pública para verificadores
    }

    public function rules(): array
    {
        return [
            'qr_code' => [
                'required',
                'string',
                'size:64', // SHA-256 hash
                'regex:/^[a-f0-9]{64}$/' // Hex válido
            ],
            'verification_token' => [
                'nullable',
                'string',
                'size:64'
            ],
            'scanner_id' => [
                'required',
                'integer',
                'exists:users,id'
            ],
            'event_data' => 'nullable|array',
            'event_data.event_type' => [
                'nullable',
                'string',
                'in:' . implode(',', EventType::values())
            ],
            'event_data.location' => 'nullable|string|max:200',
            'event_data.match_id' => 'nullable|integer|exists:match_verifications,id',
            'location' => 'nullable|array',
            'location.latitude' => 'nullable|numeric|between:-90,90',
            'location.longitude' => 'nullable|numeric|between:-180,180',
            'device_info' => 'nullable|array',
            'notes' => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'qr_code.required' => 'El código QR es obligatorio',
            'qr_code.size' => 'Código QR inválido',
            'qr_code.regex' => 'Formato de código QR inválido',
            'scanner_id.required' => 'ID del verificador es obligatorio',
            'scanner_id.exists' => 'Verificador no válido',
            'location.latitude.between' => 'Latitud debe estar entre -90 y 90',
            'location.longitude.between' => 'Longitud debe estar entre -180 y 180',
        ];
    }
}
