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
        Schema::table('tournament_registrations', function (Blueprint $table) {
            // Add foreign key columns for the many-to-many relationship
            $table->foreignId('club_id')->constrained('clubs')->onDelete('cascade');
            $table->foreignId('tournament_id')->constrained('tournaments')->onDelete('cascade');
            
            // Add additional fields that might be useful for tournament registrations
            $table->string('status')->default('registered');
            $table->date('registration_date')->nullable();
            $table->text('notes')->nullable();
            
            // Add indexes for performance
            $table->index(['club_id', 'tournament_id']);
            $table->index('status');
            
            // Ensure unique registration per club per tournament
            $table->unique(['club_id', 'tournament_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tournament_registrations', function (Blueprint $table) {
            $table->dropForeign(['club_id']);
            $table->dropForeign(['tournament_id']);
            $table->dropIndex(['club_id', 'tournament_id']);
            $table->dropIndex(['status']);
            $table->dropUnique(['club_id', 'tournament_id']);
            $table->dropColumn(['club_id', 'tournament_id', 'status', 'registration_date', 'notes']);
        });
    }
};