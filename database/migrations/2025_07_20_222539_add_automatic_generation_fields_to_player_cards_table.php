<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('player_cards', function (Blueprint $table) {
            // Agregar campos para sistema de carnetización automática
            if (!Schema::hasColumn('player_cards', 'league_id')) {
                $table->foreignId('league_id')->after('player_id')->constrained()->onDelete('cascade');
            }

            if (!Schema::hasColumn('player_cards', 'generation_status')) {
                $table->string('generation_status', 30)->after('status')->default('completed');
            }

            if (!Schema::hasColumn('player_cards', 'generation_started_at')) {
                $table->timestamp('generation_started_at')->after('approved_at')->nullable();
            }

            if (!Schema::hasColumn('player_cards', 'generation_completed_at')) {
                $table->timestamp('generation_completed_at')->after('generation_started_at')->nullable();
            }

            if (!Schema::hasColumn('player_cards', 'generation_metadata')) {
                $table->json('generation_metadata')->after('card_design_data')->nullable();
            }

            if (!Schema::hasColumn('player_cards', 'template_version')) {
                $table->string('template_version', 20)->after('version')->nullable();
            }

            if (!Schema::hasColumn('player_cards', 'qr_token')) {
                $table->string('qr_token', 128)->after('qr_code')->unique()->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('player_cards', function (Blueprint $table) {
            $table->dropForeign(['league_id']);
            $table->dropColumn([
                'league_id',
                'generation_status',
                'generation_started_at',
                'generation_completed_at',
                'generation_metadata',
                'template_version',
                'qr_token'
            ]);
        });
    }
};
