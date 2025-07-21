<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('card_number_reservations', function (Blueprint $table) {
            $table->id();
            $table->string('card_number', 20)->unique();
            $table->timestamp('reserved_at');
            $table->timestamp('expires_at');
            $table->string('reserved_by', 100)->nullable(); // Para tracking opcional
            $table->timestamps();

            // Índices para optimizar consultas
            $table->index('expires_at');
            $table->index(['card_number', 'expires_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('card_number_reservations');
    }
};
