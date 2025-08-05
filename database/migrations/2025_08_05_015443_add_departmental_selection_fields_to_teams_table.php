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
        Schema::table('teams', function (Blueprint $table) {
            // Tipo de equipo: 'club' (equipo de club) o 'selection' (selección departamental)
            $table->string('team_type', 20)->default('club')->after('club_id');
            
            // Liga a la que pertenece directamente (para selecciones departamentales)
            $table->foreignId('league_id')->nullable()->constrained()->onDelete('cascade')->after('team_type');
            
            // Departamento que representa (para selecciones departamentales)
            $table->foreignId('department_id')->nullable()->constrained()->onDelete('set null')->after('league_id');
            
            // Permitir que club_id sea nullable para selecciones departamentales
            $table->unsignedBigInteger('club_id')->nullable()->change();
            
            // Índices para optimizar consultas
            $table->index(['team_type', 'league_id']);
            $table->index(['team_type', 'department_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->dropIndex(['team_type', 'league_id']);
            $table->dropIndex(['team_type', 'department_id']);
            $table->dropForeign(['league_id']);
            $table->dropForeign(['department_id']);
            $table->dropColumn(['team_type', 'league_id', 'department_id']);
            
            // Restaurar club_id como no nullable
            $table->unsignedBigInteger('club_id')->nullable(false)->change();
        });
    }
};
