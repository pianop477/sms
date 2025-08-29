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
        Schema::create('daily_report_attendances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('daily_report_id'); // fk -> daily_reports
            $table->unsignedBigInteger('class_id');        // fk -> grades
            $table->string('group')->nullable();           // mkondo (A, B, C...)
            $table->integer('registered_boys')->default(0);
            $table->integer('registered_girls')->default(0);
            $table->integer('present_boys')->default(0);
            $table->integer('present_girls')->default(0);
            $table->integer('absent_boys')->default(0);
            $table->integer('absent_girls')->default(0);
            $table->integer('permission_boys')->default(0);
            $table->integer('permission_girls')->default(0);
            $table->timestamps();

            $table->foreign('daily_report_id')->references('id')->on('daily_report_details')->onDelete('cascade');
            $table->foreign('class_id')->references('id')->on('grades')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_report_attendances');
    }
};
