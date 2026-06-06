<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('report_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('job_id')->unique();
            $table->string('report_type')->default('individual');
             $table->string('report_title')->nullable();
             $table->string('exam_type')->nullable();
             $table->string('month')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->string('status')->default('pending'); // pending, processing, completed, failed
            $table->string('file_path')->nullable();
            $table->string('file_name')->nullable();
            $table->integer('total_students')->default(0);
            $table->integer('processed_students')->default(0);
            $table->text('error_message')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('report_jobs');
    }
};
