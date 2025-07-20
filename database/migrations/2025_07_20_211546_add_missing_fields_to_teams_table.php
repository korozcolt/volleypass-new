<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('teams', function (Blueprint $table) {
            // Agregar campos faltantes si no existen
            if (!Schema::hasColumn('teams', 'coach_id')) {
                $table->foreignId('coach_id')->nullable()->constrained()->onDelete('set null');
            }
            if (!Schema::hasColumn('teams', 'assistant_coach_id')) {
                $table->foreignId('assistant_coach_id')->nullable()->constrained('coaches')->onDelete('set null');
            }
            if (!Schema::hasColumn('teams', 'captain_id')) {
                $table->foreignId('captain_id')->nullable()->constrained('players')->onDelete('set null');
            }
            if (!Schema::hasColumn('teams', 'colors')) {
                $table->string('colors', 100)->nullable();
            }
            if (!Schema::hasColumn('teams', 'founded_date')) {
                $table->date('founded_date')->nullable();
            }
            if (!Schema::hasColumn('teams', 'description')) {
                $table->text('description')->nullable();
            }
            if (!Schema::hasColumn('teams', 'notes')) {
                $table->text('notes')->nullable();
            }
            if (!Schema::hasColumn('teams', 'settings')) {
                $table->json('settings')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->dropForeign(['coach_id']);
            $table->dropColumn('coach_id');
            $table->dropForeign(['assistant_coach_id']);
            $table->dropColumn('assistant_coach_id');
            $table->dropForeign(['captain_id']);
            $table->dropColumn('captain_id');
            $table->dropColumn(['colors', 'founded_date', 'description', 'notes', 'settings']);
        });
    }
};
