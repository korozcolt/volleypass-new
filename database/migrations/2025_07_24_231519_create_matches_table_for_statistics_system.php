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
        Schema::create('matches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tournament_id')->constrained('tournaments')->onDelete('cascade');
            $table->foreignId('home_team_id')->constrained('teams')->onDelete('cascade');
            $table->foreignId('away_team_id')->constrained('teams')->onDelete('cascade');
            $table->foreignId('league_id')->nullable()->constrained('leagues')->onDelete('set null');
            
            // Match details
            $table->integer('match_number')->nullable();
            $table->datetime('scheduled_at');
            $table->datetime('started_at')->nullable();
            $table->datetime('finished_at')->nullable();
            $table->string('venue')->nullable();
            $table->string('status')->default('Scheduled'); // Scheduled, In_Progress, Finished, Cancelled, Postponed
            $table->string('phase')->nullable(); // GROUP_STAGE, QUARTERFINALS, SEMIFINALS, FINAL, etc.
            $table->integer('round')->nullable();
            
            // Scores
            $table->json('sets_score')->nullable(); // [{"home": 25, "away": 23}, ...]
            $table->json('points_score')->nullable(); // {"home": 75, "away": 68}
            $table->foreignId('winner_team_id')->nullable()->constrained('teams')->onDelete('set null');
            
            // Officials
            $table->json('referees')->nullable(); // ["referee1", "referee2"]
            $table->string('scorer')->nullable();
            
            // Metadata
            $table->integer('duration_minutes')->nullable();
            $table->json('events')->nullable(); // timeouts, substitutions, etc.
            $table->json('statistics')->nullable();
            $table->text('notes')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['tournament_id', 'status']);
            $table->index(['home_team_id', 'away_team_id']);
            $table->index(['league_id', 'phase']);
            $table->index(['scheduled_at', 'status']);
            $table->index('winner_team_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('matches');
    }
};
