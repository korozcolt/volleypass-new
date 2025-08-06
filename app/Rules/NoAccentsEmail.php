<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class NoAccentsEmail implements ValidationRule
{
    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Primero verificar que sea un email válido
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $fail('El campo :attribute debe ser una dirección de correo electrónico válida.');
            return;
        }

        // Verificar que no contenga tildes ni caracteres especiales
        $pattern = '/[áéíóúàèìòùâêîôûäëïöüñçÁÉÍÓÚÀÈÌÒÙÂÊÎÔÛÄËÏÖÜÑÇ]/u';
        
        if (preg_match($pattern, $value)) {
            $fail('El campo :attribute no puede contener tildes ni caracteres especiales.');
            return;
        }

        // Verificar que solo contenga caracteres ASCII válidos para emails
        if (!mb_check_encoding($value, 'ASCII')) {
            $fail('El campo :attribute solo puede contener caracteres ASCII.');
            return;
        }

        // Verificar caracteres permitidos específicamente
        $allowedPattern = '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/';
        
        if (!preg_match($allowedPattern, $value)) {
            $fail('El campo :attribute contiene caracteres no permitidos.');
        }
    }
}