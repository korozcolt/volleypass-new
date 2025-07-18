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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('club_id')->nullable();
            $table->unsignedBigInteger('league_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('type', 30); // federation, registration, tournament, etc.
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('COP');
            $table->string('reference_number', 100)->unique();
            $table->string('payment_method', 50)->nullable();
            $table->string('status', 30)->default('pending');
            $table->datetime('paid_at')->nullable();
            $table->datetime('verified_at')->nullable();
            $table->unsignedBigInteger('verified_by')->nullable();
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Foreign keys
            $table->foreign('club_id')->references('id')->on('clubs');
            $table->foreign('league_id')->references('id')->on('leagues');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('verified_by')->references('id')->on('users');

            // Indices
            $table->index(['status', 'type']);
            $table->index('paid_at');
            $table->index('verified_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
