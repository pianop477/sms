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
        Schema::create('school_timetable_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('school_id')->unique(); // One setting per school
            $table->time('day_start_time'); // Masomo huanza saa ngapi
            $table->integer('period_duration'); // Kipindi kimoja kina dakika ngapi
            $table->time('first_break_start')->nullable();
            $table->time('first_break_end')->nullable();
            $table->time('second_break_start')->nullable();
            $table->time('second_break_end')->nullable();
            $table->time('day_end_time'); // Masomo yanamalizika saa ngapi
            $table->json('active_days')->nullable(); // Siku ambazo shule hufanya kazi ['Monday', 'Tuesday', ...]
            $table->timestamps();

            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_timetable_settings');
    }
};
