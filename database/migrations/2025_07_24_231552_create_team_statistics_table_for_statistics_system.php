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
        Schema::create('team_statistics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained('teams')->onDelete('cascade');
            $table->foreignId('match_id')->nullable()->constrained('matches')->onDelete('cascade');
            $table->foreignId('tournament_id')->nullable()->constrained('tournaments')->onDelete('cascade');
            $table->string('season')->nullable();
            
            // Team Statistics
            $table->integer('sets_won')->default(0);
            $table->integer('sets_lost')->default(0);
            $table->integer('points_scored')->default(0);
            $table->integer('points_conceded')->default(0);
            
            // Detailed Statistics
            $table->integer('total_attacks')->default(0);
            $table->integer('successful_attacks')->default(0);
            $table->integer('attack_errors')->default(0);
            $table->decimal('attack_percentage', 5, 2)->default(0);
            
            $table->integer('total_serves')->default(0);
            $table->integer('service_aces')->default(0);
            $table->integer('service_errors')->default(0);
            $table->decimal('service_percentage', 5, 2)->default(0);
            
            $table->integer('total_receptions')->default(0);
            $table->integer('perfect_receptions')->default(0);
            $table->integer('reception_errors')->default(0);
            $table->decimal('reception_percentage', 5, 2)->default(0);
            
            $table->integer('total_blocks')->default(0);
            $table->integer('successful_blocks')->default(0);
            $table->integer('block_errors')->default(0);
            $table->decimal('block_percentage', 5, 2)->default(0);
            
            $table->integer('total_digs')->default(0);
            $table->integer('successful_digs')->default(0);
            $table->integer('dig_errors')->default(0);
            $table->decimal('dig_percentage', 5, 2)->default(0);
            
            // Team Performance
            $table->integer('matches_played')->default(0);
            $table->integer('matches_won')->default(0);
            $table->integer('matches_lost')->default(0);
            $table->decimal('win_percentage', 5, 2)->default(0);
            $table->decimal('performance_rating', 5, 2)->default(0);
            
            // Metadata
            $table->json('metadata')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['team_id', 'season']);
            $table->index(['tournament_id', 'team_id']);
            $table->index(['match_id', 'team_id']);
            $table->index('performance_rating');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('team_statistics');
    }
};
