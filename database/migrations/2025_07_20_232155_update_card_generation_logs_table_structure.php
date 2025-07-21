<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('card_generation_logs', function (Blueprint $table) {
            // Agregar campos faltantes si no existen
            if (!Schema::hasColumn('card_generation_logs', 'player_id')) {
                $table->foreignId('player_id')->after('id')->constrained()->onDelete('cascade');
            }
            if (!Schema::hasColumn('card_generation_logs', 'league_id')) {
                $table->foreignId('league_id')->after('player_id')->constrained()->onDelete('cascade');
            }
            if (!Schema::hasColumn('card_generation_logs', 'player_card_id')) {
                $table->foreignId('player_card_id')->after('league_id')->nullable()->constrained()->onDelete('set null');
            }
            if (!Schema::hasColumn('card_generation_logs', 'card_number')) {
                $table->string('card_number', 20)->after('player_card_id')->nullable();
            }
            if (!Schema::hasColumn('card_generation_logs', 'status')) {
                $table->string('status', 30)->after('card_number');
            }
            if (!Schema::hasColumn('card_generation_logs', 'processing_time_ms')) {
                $table->integer('processing_time_ms')->after('status')->nullable();
            }
            if (!Schema::hasColumn('card_generation_logs', 'error_message')) {
                $table->text('error_message')->after('processing_time_ms')->nullable();
            }
            if (!Schema::hasColumn('card_generation_logs', 'metadata')) {
                $table->json('metadata')->after('error_message')->nullable();
            }
            if (!Schema::hasColumn('card_generation_logs', 'generated_by')) {
                $table->string('generated_by', 20)->after('metadata')->default('system_auto');
            }
            if (!Schema::hasColumn('card_generation_logs', 'triggered_by')) {
                $table->foreignId('triggered_by')->after('generated_by')->nullable()->constrained('users')->onDelete('set null');
            }
            if (!Schema::hasColumn('card_generation_logs', 'started_at')) {
                $table->timestamp('started_at')->after('triggered_by')->nullable();
            }
            if (!Schema::hasColumn('card_generation_logs', 'completed_at')) {
                $table->timestamp('completed_at')->after('started_at')->nullable();
            }
            if (!Schema::hasColumn('card_generation_logs', 'validation_results')) {
                $table->json('validation_results')->after('completed_at')->nullable();
            }
            if (!Schema::hasColumn('card_generation_logs', 'retry_count')) {
                $table->integer('retry_count')->after('validation_results')->default(0);
            }
            if (!Schema::hasColumn('card_generation_logs', 'last_retry_at')) {
                $table->timestamp('last_retry_at')->after('retry_count')->nullable();
            }
        });

        // Agregar índices de manera segura
        try {
            Schema::table('card_generation_logs', function (Blueprint $table) {
                $table->index(['player_id', 'status'], 'cgl_player_status_idx');
                $table->index(['league_id', 'status'], 'cgl_league_status_idx');
                $table->index(['status', 'created_at'], 'cgl_status_created_idx');
                $table->index('card_number', 'cgl_card_number_idx');
            });
        } catch (\Exception $e) {
            // Los índices pueden ya existir, continuar
        }
    }

    public function down(): void
    {
        Schema::table('card_generation_logs', function (Blueprint $table) {
            // Eliminar índices
            try {
                $table->dropIndex('cgl_player_status_idx');
                $table->dropIndex('cgl_league_status_idx');
                $table->dropIndex('cgl_status_created_idx');
                $table->dropIndex('cgl_card_number_idx');
            } catch (\Exception $e) {
                // Continuar si los índices no existen
            }

            // Eliminar foreign keys y columnas
            if (Schema::hasColumn('card_generation_logs', 'player_id')) {
                $table->dropForeign(['player_id']);
                $table->dropColumn('player_id');
            }
            if (Schema::hasColumn('card_generation_logs', 'league_id')) {
                $table->dropForeign(['league_id']);
                $table->dropColumn('league_id');
            }
            if (Schema::hasColumn('card_generation_logs', 'player_card_id')) {
                $table->dropForeign(['player_card_id']);
                $table->dropColumn('player_card_id');
            }
            if (Schema::hasColumn('card_generation_logs', 'triggered_by')) {
                $table->dropForeign(['triggered_by']);
                $table->dropColumn('triggered_by');
            }

            $table->dropColumn([
                'card_number', 'status', 'processing_time_ms', 'error_message',
                'metadata', 'generated_by', 'started_at', 'completed_at',
                'validation_results', 'retry_count', 'last_retry_at'
            ]);
        });
    }
};
