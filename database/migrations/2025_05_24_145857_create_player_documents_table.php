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
        Schema::create('player_documents', function (Blueprint $table) {
             $table->id();
            $table->unsignedBigInteger('player_id');
            $table->string('document_type', 50); // usando enum DocumentType
            $table->string('document_format', 10); // usando enum DocumentFormat

            // Información del archivo
            $table->string('original_name', 255);
            $table->string('file_path', 500);
            $table->string('mime_type', 100);
            $table->unsignedInteger('file_size'); // en bytes
            $table->string('file_hash', 64); // SHA-256 para integridad

            // Estado del documento
            $table->string('status', 20)->default('pending'); // usando enum DocumentStatus
            $table->text('rejection_reason')->nullable();
            $table->date('issued_date')->nullable(); // Fecha del documento
            $table->date('expires_at')->nullable(); // Fecha de vencimiento

            // Proceso de aprobación
            $table->unsignedBigInteger('reviewed_by')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('review_notes')->nullable();

            // Metadatos adicionales
            $table->json('metadata')->nullable(); // Datos específicos por tipo
            $table->integer('version')->default(1); // Para reemplazos
            $table->boolean('is_required')->default(true);

            // Auditoría
            $table->unsignedBigInteger('uploaded_by');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Foreign keys
            $table->foreign('player_id')->references('id')->on('players')->onDelete('cascade');
            $table->foreign('reviewed_by')->references('id')->on('users');
            $table->foreign('uploaded_by')->references('id')->on('users');

            // Índices críticos
            $table->index(['player_id', 'document_type', 'status']);
            $table->index(['status', 'expires_at']);
            $table->index('file_hash'); // Para detección de duplicados
            $table->index(['reviewed_by', 'reviewed_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('player_documents');
    }
};
