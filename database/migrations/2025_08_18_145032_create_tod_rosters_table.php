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
        Schema::create('tod_rosters', function (Blueprint $table) {
            $table->id();
            $table->string('roster_id');
            $table->unsignedBigInteger('teacher_id'); // fk -> users/teachers
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->enum('status', ['active', 'pending', 'completed', 'cancelled'])->default('pending');
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->boolean('is_completed')->default(false);
            $table->timestamps();

            $table->foreign('teacher_id')->references('id')->on('teachers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tod_rosters');
    }
};
