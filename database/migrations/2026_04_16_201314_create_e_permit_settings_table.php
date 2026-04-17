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
        Schema::create('e_permit_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->onDelete('cascade');
            $table->boolean('auto_approve_enabled')->default(false);
            $table->integer('max_requests_per_day')->default(3);
            $table->integer('late_return_grace_hours')->default(2);
            $table->boolean('require_parent_photo')->default(false);
            $table->json('allowed_reasons')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('e_permit_settings');
    }
};
