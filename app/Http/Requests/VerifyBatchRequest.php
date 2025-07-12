<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VerifyBatchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'qr_codes' => 'required|array|min:1|max:50', // Máximo 50 carnets por batch
            'qr_codes.*.qr_code' => [
                'required',
                'string',
                'size:64',
                'regex:/^[a-f0-9]{64}$/'
            ],
            'qr_codes.*.verification_token' => 'nullable|string|size:64',
            'scanner_id' => 'required|integer|exists:users,id',
            'event_data' => 'nullable|array',
            'event_data.event_type' => 'nullable|string',
            'event_data.location' => 'nullable|string|max:200',
        ];
    }

    public function messages(): array
    {
        return [
            'qr_codes.required' => 'Lista de códigos QR es obligatoria',
            'qr_codes.max' => 'Máximo 50 códigos QR por lote',
            'qr_codes.*.qr_code.required' => 'Cada código QR es obligatorio',
            'qr_codes.*.qr_code.size' => 'Formato de código QR inválido',
        ];
    }
}
