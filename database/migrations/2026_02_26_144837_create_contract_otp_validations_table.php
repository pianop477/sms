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
        Schema::create('contract_otp_validations', function (Blueprint $table) {
            $table->id();
            $table->string('user_id');
            $table->string('otp_code');
            $table->dateTime('requested_at');
            $table->dateTime('expires_at');
            $table->dateTime('verified_at')->nullable();
            $table->string('auth_token')->unique()->nullable();
            $table->string('ip_address')->nullable();
            $table->integer('token_ttl')->nullable();
            $table->boolean('is_active')->default(false);
            $table->boolean('is_used')->default(false);
            $table->boolean('is_expired')->default(false);
            $table->boolean('is_verified')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contract_otp_validations');
    }
};
