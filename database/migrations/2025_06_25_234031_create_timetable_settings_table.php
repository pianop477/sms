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
        Schema::create('timetable_settings', function (Blueprint $table) {
            $table->unsignedBigInteger('school_id');
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->time('school_start_time')->default('08:00:00');
            $table->time('school_end_time')->default('14:10:00');
            $table->integer('lower_primary_period')->default(30); // Dakika
            $table->integer('upper_primary_period')->default(40); // Dakika
            $table->integer('tea_break_duration')->default(20);
            $table->integer('lunch_break_duration')->default(30);
            $table->json('working_days')->default(json_encode(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday']));
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('timetable_settings');
    }
};
