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
        Schema::create('daily_report_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tod_roster_id'); // fk -> tod_rosters
            $table->date('report_date');
            $table->text('parade')->nullable();
            $table->text('break_time')->nullable();
            $table->text('lunch_time')->nullable();
            $table->text('teachers_attendance')->nullable();
            $table->text('daily_new_event')->nullable();
            $table->text('tod_remarks')->nullable();
            $table->text('headteacher_comment')->nullable();
            $table->enum('status', ['pending', 'approved', 'in progress'])->default('pending');
            $table->string('approved_by')->nullable(); //
            $table->timestamps();

            $table->foreign('tod_roster_id')->references('id')->on('tod_rosters')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_report_details');
    }
};
