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
        Schema::create('temporary_results', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('course_id');
            $table->unsignedBigInteger('class_id');
            $table->unsignedBigInteger('teacher_id');
            $table->unsignedBigInteger('exam_type_id');
            $table->unsignedBigInteger('school_id');
            $table->float('score')->nullable();
            $table->string('exam_term');
            $table->integer('marking_style');
            $table->string('status')->default('draft'); // draft or confirmed
            $table->date('exam_date');
            $table->timestamp('expiry_date')->nullable(); // muda wa mwisho wa editing
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('temporary_results');
    }
};
