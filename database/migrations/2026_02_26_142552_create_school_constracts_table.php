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
        Schema::create('school_constracts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('school_id')->index();
            $table->string('applicant_id');
            $table->unsignedBigInteger('holder_id')->index()->nullable();
            $table->string('contract_type');
            $table->string('job_title')->nullable();
            $table->decimal('basic_salary', 15, 2)->nullable();
            $table->decimal('allowances', 15, 2)->nullable();
            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date')->nullable();
            $table->integer('duration')->comment('Duration in months')->nullable();
            $table->string('applicant_file_path');
            $table->dateTime('applied_at');
            $table->dateTime('approved_at')->nullable();
            $table->dateTime('activated_at')->nullable();
            $table->string('contract_file_path')->nullable();
            $table->string('verify_token')->unique()->nullable();
            $table->string('qr_code_path')->nullable();
            $table->boolean('is_active')->default(false);
            $table->string('approved_by')->nullable();
            $table->text('remarks')->nullable();
            $table->dateTime('rejected_at')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected', 'activated', 'terminated', 'expired'])->default('pending');
            $table->softDeletes(); // deleted_at column
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_constracts');
    }
};
