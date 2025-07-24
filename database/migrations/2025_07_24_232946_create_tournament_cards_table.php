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
        Schema::create('tournament_cards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tournament_id')->constrained()->onDelete('cascade');
            $table->foreignId('match_id')->nullable()->constrained('matches')->onDelete('cascade');
            $table->foreignId('player_id')->constrained()->onDelete('cascade');
            $table->foreignId('team_id')->constrained()->onDelete('cascade');
            $table->string('card_type'); // yellow, red, red_match, red_tournament
            $table->string('violation_type'); // misconduct, delay_of_game, etc.
            $table->text('description');
            $table->text('referee_notes')->nullable();
            $table->integer('set_number')->nullable();
            $table->integer('point_number')->nullable();
            $table->timestamp('issued_at');
            $table->foreignId('issued_by')->constrained('users')->onDelete('cascade'); // Referee
            $table->boolean('is_active')->default(true);
            $table->timestamp('expires_at')->nullable(); // For suspensions
            $table->json('sanctions')->nullable(); // Additional sanctions
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['tournament_id', 'player_id']);
            $table->index(['tournament_id', 'team_id']);
            $table->index(['match_id', 'set_number']);
            $table->index(['card_type', 'is_active']);
            $table->index(['issued_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tournament_cards');
    }
};
