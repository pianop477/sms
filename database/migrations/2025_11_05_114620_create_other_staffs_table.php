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
        Schema::create('other_staffs', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('staff_id')->unique();
            $table->string('first_name');
            $table->string('last_name');
            $table->enum('gender', ['male', 'female']);
            $table->date('date_of_birth');
            $table->string('phone')->unique();
            $table->string('email')->unique()->nullable();
            $table->string('street_address');
            $table->enum('job_title', ['cooks', 'matron', 'patron', 'cleaner', 'security guard', 'driver', 'other']);
            $table->year('joining_year');
            $table->enum('educational_level', ['university', 'college', 'high_school', 'secondary', 'primary', 'other']);
            $table->string('profile_image')->nullable();
            $table->string('usertype')->default(6);
            $table->integer('status')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('other_staffs');
    }
};
