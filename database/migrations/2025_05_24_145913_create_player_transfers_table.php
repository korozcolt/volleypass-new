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
        Schema::create('player_transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_id')->constrained()->onDelete('cascade');
            $table->foreignId('from_club_id')->constrained('clubs')->onDelete('cascade');
            $table->foreignId('to_club_id')->constrained('clubs')->onDelete('cascade');
            $table->foreignId('league_id')->constrained()->onDelete('cascade');
            $table->date('transfer_date');
            $table->date('effective_date')->nullable();
            $table->string('status')->default('pending');
            $table->text('reason')->nullable();
            $table->decimal('transfer_fee', 10, 2)->nullable();
            $table->string('currency', 3)->default('COP');
            $table->foreignId('requested_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('player_transfers');
    }
};
