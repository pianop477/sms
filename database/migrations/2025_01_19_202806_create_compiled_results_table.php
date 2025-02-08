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
        Schema::create('compiled_results', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->unsignedBigInteger('exam_type_id');
            $table->foreign('exam_type_id')->references('id')->on('examinations')->onDelete('cascade');
            $table->unsignedBigInteger('class_id');
            $table->foreign('class_id')->references('id')->on('grades')->onDelete('cascade');
            $table->unsignedBigInteger('school_id');
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->unsignedBigInteger('course_id'); // Safu ya somo
            $table->foreign('course_id')->references('id')->on('subjects')->onDelete('cascade');
            $table->json('source_results'); // IDs za matokeo yaliyotumika
            $table->string('compiled_term');
            $table->float('total_score'); // Jumla ya alama kwa somo
            $table->float('average_score'); // Wastani wa alama kwa somo
            $table->integer('status')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('compiled_results');
    }
};
