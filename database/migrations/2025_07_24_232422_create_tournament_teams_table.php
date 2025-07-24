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
        Schema::create('tournament_teams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tournament_id')->constrained()->onDelete('cascade');
            $table->foreignId('team_id')->constrained()->onDelete('cascade');
            $table->enum('registration_status', ['pending', 'approved', 'rejected', 'withdrawn'])->default('pending');
            $table->datetime('registered_at');
            $table->datetime('approved_at')->nullable();
            $table->integer('group_number')->nullable();
            $table->integer('seed_position')->nullable();
            $table->json('roster_players'); // IDs de jugadoras inscritas
            $table->json('coaching_staff')->nullable(); // Cuerpo técnico
            $table->text('registration_notes')->nullable();
            $table->decimal('registration_fee', 10, 2)->nullable();
            $table->boolean('fee_paid')->default(false);
            $table->datetime('fee_paid_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->unique(['tournament_id', 'team_id']);
            $table->index(['tournament_id', 'registration_status']);
            $table->index(['tournament_id', 'group_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tournament_teams');
    }
};
