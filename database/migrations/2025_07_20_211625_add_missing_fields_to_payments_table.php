<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // Agregar campos faltantes si no existen
            if (!Schema::hasColumn('payments', 'payment_date')) {
                $table->timestamp('payment_date')->nullable();
            }
            if (!Schema::hasColumn('payments', 'due_date')) {
                $table->timestamp('due_date')->nullable();
            }
            if (!Schema::hasColumn('payments', 'confirmed_at')) {
                $table->timestamp('confirmed_at')->nullable();
            }
            if (!Schema::hasColumn('payments', 'transaction_id')) {
                $table->string('transaction_id')->nullable();
            }
            if (!Schema::hasColumn('payments', 'gateway')) {
                $table->string('gateway', 100)->nullable();
            }
            if (!Schema::hasColumn('payments', 'description')) {
                $table->text('description')->nullable();
            }
            if (!Schema::hasColumn('payments', 'receipt')) {
                $table->json('receipt')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn([
                'payment_date',
                'due_date',
                'confirmed_at',
                'transaction_id',
                'gateway',
                'description',
                'receipt'
            ]);
        });
    }
};
