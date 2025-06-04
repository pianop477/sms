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
        Schema::create('holiday_packages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('school_id');
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->unsignedBigInteger('class_id');
            $table->foreign('class_id')->references('id')->on('grades')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->year('year');
            $table->enum('term', ['i', 'ii']);
            $table->string('file_path');
            $table->date('release_date')->nullable();
            $table->date('due_date')->nullable();
            $table->boolean('is_active')->default(false);
            $table->unsignedBigInteger('issued_by');
            $table->foreign('issued_by')->references('id')->on('users')->onDelete('cascade');
            $table->string('download_count')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('holiday_packages');
    }
};
