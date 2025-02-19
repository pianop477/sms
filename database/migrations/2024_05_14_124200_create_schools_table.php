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
        Schema::create('schools', function (Blueprint $table) {
            $table->id();
            $table->string('school_name');
            $table->string('school_reg_no');
            $table->string('abbriv_code');
            $table->string('postal_address');
            $table->string('postal_name');
            $table->string('country');
            $table->string('sender_id')->nullable();
            $table->date('reg_date');
            $table->date('service_start_date')->nullable();
            $table->date('service_end_date')->nullable();
            $table->integer('service_duration')->nullable();
            $table->integer('status')->default(1);
            $table->string('logo');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schools');
    }
};
