<?php

namespace App\Traits;

trait HasValidation
{
    /**
     * Valida un documento de identidad colombiano
     */
    public function validateColombianId(string $id): bool
    {
        // Remover caracteres no numéricos
        $id = preg_replace('/[^0-9]/', '', $id);

        // Verificar longitud
        if (strlen($id) < 6 || strlen($id) > 10) {
            return false;
        }

        // Algoritmo de validación para cédula colombiana
        $sum = 0;
        $odd = true;

        for ($i = strlen($id) - 2; $i >= 0; $i--) {
            $digit = intval($id[$i]);
            if ($odd) {
                $digit *= 2;
                if ($digit > 9) {
                    $digit = $digit % 10 + intval($digit / 10);
                }
            }
            $sum += $digit;
            $odd = !$odd;
        }

        $checkDigit = (10 - ($sum % 10)) % 10;
        return $checkDigit == intval($id[strlen($id) - 1]);
    }

    /**
     * Valida un email
     */
    public function validateEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Valida un teléfono colombiano
     */
    public function validateColombianPhone(string $phone): bool
    {
        // Remover caracteres no numéricos
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Celular: 10 dígitos empezando por 3
        // Fijo: 7 dígitos o 10 dígitos (con código de área)
        return preg_match('/^3[0-9]{9}$/', $phone) || // Celular
               preg_match('/^[1-8][0-9]{6}$/', $phone) || // Fijo 7 dígitos
               preg_match('/^[1-8][0-9]{9}$/', $phone); // Fijo con código área
    }

    /**
     * Valida una fecha de nacimiento
     */
    public function validateBirthDate(string $date, int $minAge = 8, int $maxAge = 100): bool
    {
        try {
            $birthDate = new \DateTime($date);
            $today = new \DateTime();
            $age = $today->diff($birthDate)->y;

            return $age >= $minAge && $age <= $maxAge;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Calcula la edad a partir de una fecha de nacimiento
     */
    public function calculateAge(string $birthDate): int
    {
        $birth = new \DateTime($birthDate);
        $today = new \DateTime();
        return $today->diff($birth)->y;
    }
}
