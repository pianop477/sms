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
        Schema::create('school_fees_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('school_id');
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->unsignedBigInteger('student_id')->index();
            $table->unsignedBigInteger('student_fee_id');
            $table->foreign('student_fee_id')->references('id')->on('school_fees')->onDelete('cascade');
            $table->decimal('amount', 12,2);
            $table->enum('payment_mode', ['bank', 'cash', 'mobile'])->default('bank');
            $table->string('installment')->nullable();
            $table->unsignedBigInteger('approved_by')->index();
            $table->timestamp('approved_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_fees_payments');
    }
};
