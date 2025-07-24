<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Índice para búsqueda optimizada de categorías por liga
        Schema::table('league_categories', function (Blueprint $table) {
            $table->index(
                ['league_id', 'gender', 'is_active', 'min_age', 'max_age'],
                'idx_league_categories_lookup'
            );
        });

        // Índice para asignación de categorías de jugadores
        Schema::table('players', function (Blueprint $table) {
            $table->index(
                ['current_club_id', 'category'],
                'idx_players_category_assignment'
            );
        });

        // Índice para estado de federación de jugadores
        Schema::table('players', function (Blueprint $table) {
            $table->index(
                ['federation_status', 'federation_expires_at'],
                'idx_players_federation_status'
            );
        });

        // Índice para verificación QR
        Schema::table('player_cards', function (Blueprint $table) {
            $table->index(
                ['qr_token', 'is_active', 'valid_until'],
                'idx_qr_verification'
            );
        });

        // Índice para torneos activos
        Schema::table('tournaments', function (Blueprint $table) {
            $table->index(
                ['status', 'start_date', 'end_date'],
                'idx_tournaments_active'
            );
        });

        // Índices adicionales para optimización de consultas frecuentes
        
        // Índice para jugadores activos por club
        Schema::table('players', function (Blueprint $table) {
            $table->index(
                ['current_club_id', 'is_active', 'created_at'],
                'idx_players_active_by_club'
            );
        });

        // Índice para certificados médicos próximos a vencer
        Schema::table('medical_certificates', function (Blueprint $table) {
            $table->index(
                ['expires_at', 'status'],
                'idx_medical_certificates_expiry'
            );
        });

        // Índice para carnets próximos a vencer
        Schema::table('player_cards', function (Blueprint $table) {
            $table->index(
                ['valid_until', 'is_active', 'player_id'],
                'idx_player_cards_expiry'
            );
        });

        // Índice para logs de generación de carnets
        Schema::table('card_generation_logs', function (Blueprint $table) {
            $table->index(
                ['status', 'created_at'],
                'idx_card_generation_logs_status'
            );
        });

        // Índice para notificaciones por usuario
        Schema::table('notifications', function (Blueprint $table) {
            $table->index(
                ['notifiable_id', 'notifiable_type', 'read_at'],
                'idx_notifications_user_read'
            );
        });

        // Índice para pagos pendientes
        Schema::table('payments', function (Blueprint $table) {
            $table->index(
                ['status', 'due_date', 'created_at'],
                'idx_payments_status_due'
            );
        });

        // Índice para registros de escaneo QR
        Schema::table('qr_scan_logs', function (Blueprint $table) {
            $table->index(
                ['scanned_at', 'verification_result'],
                'idx_qr_scan_logs_verification'
            );
        });

        // Índice para transferencias de jugadores
        Schema::table('player_transfers', function (Blueprint $table) {
            $table->index(
                ['status', 'effective_date'],
                'idx_player_transfers_status'
            );
        });

        // Índice para configuraciones de liga
        Schema::table('league_configurations', function (Blueprint $table) {
            $table->index(
                ['league_id', 'key'],
                'idx_league_configurations_lookup'
            );
        });

        // Índice para actividad de usuarios
        Schema::table('activity_log', function (Blueprint $table) {
            $table->index(
                ['subject_type', 'subject_id', 'created_at'],
                'idx_activity_log_subject'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Eliminar índices en orden inverso
        
        Schema::table('activity_log', function (Blueprint $table) {
            $table->dropIndex('idx_activity_log_subject');
        });

        Schema::table('league_configurations', function (Blueprint $table) {
            $table->dropIndex('idx_league_configurations_lookup');
        });

        Schema::table('player_transfers', function (Blueprint $table) {
            $table->dropIndex('idx_player_transfers_status');
        });

        Schema::table('qr_scan_logs', function (Blueprint $table) {
            $table->dropIndex('idx_qr_scan_logs_verification');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex('idx_payments_status_due');
        });

        Schema::table('notifications', function (Blueprint $table) {
            $table->dropIndex('idx_notifications_user_read');
        });

        Schema::table('card_generation_logs', function (Blueprint $table) {
            $table->dropIndex('idx_card_generation_logs_status');
        });

        Schema::table('player_cards', function (Blueprint $table) {
            $table->dropIndex('idx_player_cards_expiry');
        });

        Schema::table('medical_certificates', function (Blueprint $table) {
            $table->dropIndex('idx_medical_certificates_expiry');
        });

        Schema::table('players', function (Blueprint $table) {
            $table->dropIndex('idx_players_active_by_club');
        });

        Schema::table('tournaments', function (Blueprint $table) {
            $table->dropIndex('idx_tournaments_active');
        });

        Schema::table('player_cards', function (Blueprint $table) {
            $table->dropIndex('idx_qr_verification');
        });

        Schema::table('players', function (Blueprint $table) {
            $table->dropIndex('idx_players_federation_status');
        });

        Schema::table('players', function (Blueprint $table) {
            $table->dropIndex('idx_players_category_assignment');
        });

        Schema::table('league_categories', function (Blueprint $table) {
            $table->dropIndex('idx_league_categories_lookup');
        });
    }
};