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
        Schema::create('notification_preferences', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('channel', 50); // mail, sms, push, whatsapp
            $table->string('notification_type', 50);
            $table->boolean('is_enabled')->default(true);
            $table->time('schedule_time')->nullable();
            $table->string('frequency', 20)->default('immediate'); // immediate, daily, weekly
            $table->json('metadata')->nullable();
            $table->timestamps();

            // Foreign key
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            // Unique constraint con nombre personalizado más corto
            $table->unique(['user_id', 'channel', 'notification_type'], 'notif_prefs_user_channel_type_unique');

            // Índice regular
            $table->index(['user_id', 'is_enabled'], 'notif_prefs_user_enabled_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_preferences');
    }
};
