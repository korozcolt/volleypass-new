<?php

namespace App\Services;

use App\Models\League;
use App\Models\Player;
use App\Models\PlayerCard;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CardNumberingService
{
    /**
     * Generar número único de carnet para una liga específica
     */
    public function generateCardNumber(League $league, ?int $year = null): string
    {
        $year = $year ?? now()->year;
        $maxAttempts = 10;
        $attempt = 0;

        do {
            $attempt++;
            $cardNumber = $this->buildCardNumber($league, $year);

            if ($this->validateUniqueness($cardNumber)) {
                // Reservar el número para evitar conflictos de concurrencia
                if ($this->reserveNumber($cardNumber)) {
                    Log::info("Número de carnet generado exitosamente", [
                        'card_number' => $cardNumber,
                        'league_id' => $league->id,
                        'year' => $year,
                        'attempt' => $attempt
                    ]);

                    return $cardNumber;
                }
            }

            Log::warning("Conflicto en generación de número de carnet", [
                'card_number' => $cardNumber,
                'league_id' => $league->id,
                'attempt' => $attempt
            ]);

        } while ($attempt < $maxAttempts);

        throw new \Exception("No se pudo generar un número único de carnet después de {$maxAttempts} intentos para la liga {$league->name}");
    }

    /**
     * Construir el número de carnet con formato [CÓDIGO_LIGA]-[AÑO]-[SECUENCIAL]
     */
    private function buildCardNumber(League $league, int $year): string
    {
        // Obtener código de liga (máximo 5 caracteres)
        $leagueCode = $this->getLeagueCode($league);

        // Obtener siguiente número secuencial
        $sequential = $this->getNextSequential($league, $year);

        return "{$leagueCode}-{$year}-" . str_pad($sequential, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Obtener código de liga normalizado
     */
    private function getLeagueCode(League $league): string
    {
        // Priorizar short_name si existe
        if (!empty($league->short_name)) {
            return strtoupper(substr($league->short_name, 0, 5));
        }

        // Generar código basado en el nombre
        $words = explode(' ', $league->name);
        $code = '';

        foreach ($words as $word) {
            if (strlen($code) < 5 && !empty($word)) {
                $code .= strtoupper(substr($word, 0, 1));
            }
        }

        // Si el código es muy corto, completar con las primeras letras del nombre
        if (strlen($code) < 3) {
            $code = strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $league->name), 0, 5));
        }

        return $code;
    }

    /**
     * Obtener el siguiente número secuencial para una liga y año
     */
    public function getNextSequential(League $league, int $year): int
    {
        return DB::transaction(function () use ($league, $year) {
            $lastCard = PlayerCard::where('league_id', $league->id)
                ->where('season', $year)
                ->lockForUpdate()
                ->orderBy('id', 'desc')
                ->first();

            if (!$lastCard) {
                return 1;
            }

            // Extraer el número secuencial del último carnet
            $parts = explode('-', $lastCard->card_number);
            if (count($parts) >= 3) {
                $lastSequential = (int) end($parts);
                return $lastSequential + 1;
            }

            // Fallback: contar todos los carnets de la liga en el año
            return PlayerCard::where('league_id', $league->id)
                ->where('season', $year)
                ->count() + 1;
        });
    }

    /**
     * Validar que el número de carnet sea único
     */
    public function validateUniqueness(string $cardNumber): bool
    {
        return !PlayerCard::where('card_number', $cardNumber)->exists();
    }

    /**
     * Reservar un número de carnet para evitar conflictos de concurrencia
     */
    public function reserveNumber(string $cardNumber): bool
    {
        try {
            // Crear un registro temporal para reservar el número
            DB::table('card_number_reservations')->insert([
                'card_number' => $cardNumber,
                'reserved_at' => now(),
                'expires_at' => now()->addMinutes(5), // Reserva por 5 minutos
            ]);

            return true;
        } catch (\Exception $e) {
            // Si falla la inserción, el número ya está reservado
            return false;
        }
    }

    /**
     * Liberar una reserva de número de carnet
     */
    public function releaseReservation(string $cardNumber): void
    {
        DB::table('card_number_reservations')
            ->where('card_number', $cardNumber)
            ->delete();
    }

    /**
     * Limpiar reservas expiradas
     */
    public function cleanExpiredReservations(): int
    {
        return DB::table('card_number_reservations')
            ->where('expires_at', '<', now())
            ->delete();
    }

    /**
     * Obtener estadísticas de numeración por liga
     */
    public function getNumberingStats(League $league, ?int $year = null): array
    {
        $year = $year ?? now()->year;

        $totalCards = PlayerCard::where('league_id', $league->id)
            ->where('season', $year)
            ->count();

        $lastCard = PlayerCard::where('league_id', $league->id)
            ->where('season', $year)
            ->orderBy('id', 'desc')
            ->first();

        return [
            'league_code' => $this->getLeagueCode($league),
            'year' => $year,
            'total_cards' => $totalCards,
            'next_sequential' => $totalCards + 1,
            'last_card_number' => $lastCard?->card_number,
            'capacity_remaining' => 999999 - $totalCards,
            'capacity_percentage' => round(($totalCards / 999999) * 100, 2)
        ];
    }

    /**
     * Validar formato de número de carnet
     */
    public function validateCardNumberFormat(string $cardNumber): bool
    {
        // Formato: CODIGO-YYYY-NNNNNN
        $pattern = '/^[A-Z]{2,5}-\d{4}-\d{6}$/';
        return preg_match($pattern, $cardNumber) === 1;
    }

    /**
     * Extraer información de un número de carnet
     */
    public function parseCardNumber(string $cardNumber): array
    {
        if (!$this->validateCardNumberFormat($cardNumber)) {
            throw new \InvalidArgumentException("Formato de número de carnet inválido: {$cardNumber}");
        }

        $parts = explode('-', $cardNumber);

        return [
            'league_code' => $parts[0],
            'year' => (int) $parts[1],
            'sequential' => (int) $parts[2],
            'full_number' => $cardNumber
        ];
    }
}
