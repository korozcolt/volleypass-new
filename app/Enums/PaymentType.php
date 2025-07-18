<?php

namespace App\Enums;

enum PaymentType: string
{
    case Federation = 'federation';
    case Registration = 'registration';
    case Tournament = 'tournament';
    case Transfer = 'transfer';
    case Fine = 'fine';
    case Other = 'other';

    public function getLabel(): string
    {
        return match($this) {
            self::Federation => 'Federación',
            self::Registration => 'Inscripción',
            self::Tournament => 'Torneo',
            self::Transfer => 'Traspaso',
            self::Fine => 'Multa',
            self::Other => 'Otro',
        };
    }

    public function getColor(): string
    {
        return match($this) {
            self::Federation => 'primary',
            self::Registration => 'success',
            self::Tournament => 'warning',
            self::Transfer => 'info',
            self::Fine => 'danger',
            self::Other => 'gray',
        };
    }
}
