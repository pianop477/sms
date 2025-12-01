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
        Schema::create('school_fees', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('school_id');
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->unsignedBigInteger('student_id')->index();
            $table->unsignedBigInteger('service_id');
            $table->foreign('service_id')->references('id')->on('payment_services')->onDelete('cascade');
            $table->unsignedBigInteger('class_id')->index();
            $table->string('academic_year');
            $table->string('control_number')->nullable();
            $table->decimal('amount', 12, 2);
            $table->timestamp('due_date')->nullable();
            $table->enum('status', ['active', 'full paid', 'cancelled', 'expired', 'overpaid'])->default('active');
            $table->boolean('is_cancelled')->default(0);
            $table->text('cancel_reason')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->unsignedBigInteger('created_by')->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_fees');
    }
};
