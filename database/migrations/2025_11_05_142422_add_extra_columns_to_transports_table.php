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
        Schema::table('transports', function (Blueprint $table) {
            //
            $table->string('staff_id')->unique()->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('email')->unique()->nullable();
            $table->string('street_address')->nullable();
            $table->enum('job_title', ['cooks', 'matron', 'patron', 'cleaner', 'security guard', 'driver', 'other'])->default('college');
            $table->year('joining_year')->nullable();
            $table->enum('educational_level', ['university', 'college', 'high_school', 'secondary', 'primary', 'other'])->default('other');
            $table->string('profile_image')->nullable();
            $table->string('usertype')->default(6);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transports', function (Blueprint $table) {
            //
        });
    }
};
