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
        Schema::create('e_permit_tracking_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('e_permit_id')->constrained('e_permits')->onDelete('cascade');
            $table->foreignId('teacher_id')->nullable()->constrained('teachers');
            $table->string('action');
            $table->string('stage');
            $table->text('comment')->nullable();
            $table->json('metadata')->nullable();
            $table->string('ip_address')->nullable();
            $table->timestamps();

            $table->index(['e_permit_id', 'stage']);
            $table->index('created_at');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('e_permit_tracking_logs');
    }
};
