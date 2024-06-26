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
        Schema::create('examination_results', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->unsignedBigInteger('course_id');
            $table->foreign('course_id')->references('id')->on('subjects')->onDelete('cascade');
            $table->unsignedBigInteger('class_id');
            $table->foreign('class_id')->references('id')->on('grades')->onDelete('cascade');
            $table->unsignedBigInteger('teacher_id');
            $table->foreign('teacher_id')->references('id')->on('teachers')->onDelete('cascade');
            $table->unsignedBigInteger('exam_type_id');
            $table->foreign('exam_type_id')->references('id')->on('examinations')->onDelete('cascade');
            $table->unsignedBigInteger('school_id');
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->float('score');
            $table->string('Exam_term');
            $table->string('status')->default(1);
            $table->date('exam_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('examination_results');
    }
};
