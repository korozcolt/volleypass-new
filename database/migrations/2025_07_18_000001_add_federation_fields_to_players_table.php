<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('players', function (Blueprint $table) {
            // Campos de federación
            $table->string('federation_status', 30)->default('not_federated')->after('is_eligible');
            $table->date('federation_date')->nullable()->after('federation_status');
            $table->date('federation_expires_at')->nullable()->after('federation_date');
            $table->unsignedBigInteger('federation_payment_id')->nullable()->after('federation_expires_at');
            $table->text('federation_notes')->nullable()->after('federation_payment_id');

            // Índices
            $table->index('federation_status');
            $table->index('federation_expires_at');

            // Foreign key
            $table->foreign('federation_payment_id')->references('id')->on('payments');
        });
    }

    public function down(): void
    {
        Schema::table('players', function (Blueprint $table) {
            $table->dropForeign(['federation_payment_id']);
            $table->dropIndex(['federation_status']);
            $table->dropIndex(['federation_expires_at']);
            $table->dropColumn([
                'federation_status',
                'federation_date',
                'federation_expires_at',
                'federation_payment_id',
                'federation_notes'
            ]);
        });
    }
};
