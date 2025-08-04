<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // Campos para relaciones polimórficas
            $table->string('payer_type')->nullable()->after('user_id');
            $table->unsignedBigInteger('payer_id')->nullable()->after('payer_type');
            $table->string('receiver_type')->nullable()->after('payer_id');
            $table->unsignedBigInteger('receiver_id')->nullable()->after('receiver_type');
            
            // Campo para jugador
            $table->foreignId('player_id')->nullable()->constrained()->after('receiver_id');
            
            // Campos para pagos mensuales
            $table->string('month_year')->nullable()->after('description'); // Formato: YYYY-MM
            $table->boolean('is_recurring')->default(false)->after('month_year');
            
            // Campos para comprobantes
            $table->string('receipt_url')->nullable()->after('is_recurring');
            $table->json('payment_proof')->nullable()->after('receipt_url');
            
            // Índices para mejorar rendimiento
            $table->index(['payer_type', 'payer_id']);
            $table->index(['receiver_type', 'receiver_id']);
            $table->index(['month_year']);
            $table->index(['type', 'status']);
            $table->index(['due_date', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // Eliminar índices
            $table->dropIndex(['payer_type', 'payer_id']);
            $table->dropIndex(['receiver_type', 'receiver_id']);
            $table->dropIndex(['month_year']);
            $table->dropIndex(['type', 'status']);
            $table->dropIndex(['due_date', 'status']);
            
            // Eliminar campos
            $table->dropForeign(['player_id']);
            $table->dropColumn([
                'payer_type',
                'payer_id', 
                'receiver_type',
                'receiver_id',
                'player_id',
                'month_year',
                'is_recurring',
                'receipt_url',
                'payment_proof'
            ]);
        });
    }
};
