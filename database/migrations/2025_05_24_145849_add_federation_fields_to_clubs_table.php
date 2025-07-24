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
        Schema::table('clubs', function (Blueprint $table) {
            // Campos de federación
            $table->boolean('es_federado')->default(false)->after('is_active');
            $table->enum('tipo_federacion', ['departamental', 'nacional'])->nullable()->after('es_federado');
            $table->string('codigo_federacion', 20)->unique()->nullable()->after('tipo_federacion');
            $table->date('vencimiento_federacion')->nullable()->after('codigo_federacion');
            $table->text('observaciones_federacion')->nullable()->after('vencimiento_federacion');

            // Campos adicionales para compatibilidad con el sistema
            $table->string('nombre', 150)->nullable()->after('name');
            $table->string('nombre_corto', 50)->nullable()->after('short_name');
            $table->unsignedBigInteger('departamento_id')->nullable()->after('city_id');
            $table->string('direccion')->nullable()->after('address');
            $table->string('telefono', 20)->nullable()->after('phone');
            $table->date('fundacion')->nullable()->after('foundation_date');

            // Índices para optimización
            $table->index('es_federado');
            $table->index('tipo_federacion');
            $table->index('vencimiento_federacion');
            $table->index('departamento_id');

            // Foreign key para departamento
            $table->foreign('departamento_id')->references('id')->on('departments');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clubs', function (Blueprint $table) {
            // Eliminar foreign keys primero
            $table->dropForeign(['departamento_id']);

            // Eliminar índices
            $table->dropIndex(['es_federado']);
            $table->dropIndex(['tipo_federacion']);
            $table->dropIndex(['vencimiento_federacion']);
            $table->dropIndex(['departamento_id']);

            // Eliminar columnas
            $table->dropColumn([
                'es_federado',
                'tipo_federacion',
                'codigo_federacion',
                'vencimiento_federacion',
                'observaciones_federacion',
                'nombre',
                'nombre_corto',
                'departamento_id',
                'direccion',
                'telefono',
                'fundacion'
            ]);
        });
    }
};
