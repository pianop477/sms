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
        Schema::create('e_permits', function (Blueprint $table) {
            $table->id();
            $table->string('permit_number')->unique();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('parent_id')->constrained('parents')->onDelete('cascade');

            // Parent/Guardian submission data
            $table->string('guardian_name');
            $table->string('guardian_phone');
            $table->enum('guardian_type', ['parent', 'guardian']);
            $table->string('relationship');

            // Permission details
            $table->enum('reason', ['medical', 'family_matter', 'other']);
            $table->string('other_reason')->nullable();
            $table->date('departure_date');
            $table->timestamp('departure_time')->useCurrent();
            $table->date('expected_return_date');

            // Workflow status
            $table->enum('status', [
                'pending_class_teacher',
                'pending_duty_teacher',
                'pending_academic',
                'pending_head',
                'approved',
                'rejected',
                'completed',
                'cancelled'
            ])->default('pending_class_teacher');

            $table->text('rejection_reason')->nullable();

            // Class Teacher approval
            $table->foreignId('class_teacher_id')->nullable()->constrained('teachers');
            $table->timestamp('class_teacher_approved_at')->nullable();
            $table->enum('class_teacher_action', ['approved', 'rejected'])->nullable();
            $table->text('class_teacher_comment')->nullable();

            // Duty Teacher approval
            $table->foreignId('duty_teacher_id')->nullable()->constrained('teachers');
            $table->timestamp('duty_teacher_approved_at')->nullable();
            $table->enum('duty_teacher_action', ['approved', 'rejected'])->nullable();
            $table->text('duty_teacher_comment')->nullable();

            // Academic Teacher approval
            $table->foreignId('academic_teacher_id')->nullable()->constrained('teachers');
            $table->timestamp('academic_teacher_approved_at')->nullable();
            $table->enum('academic_teacher_action', ['approved', 'rejected'])->nullable();
            $table->text('academic_teacher_comment')->nullable();

            // Head Teacher approval
            $table->foreignId('head_teacher_id')->nullable()->constrained('teachers');
            $table->timestamp('head_teacher_approved_at')->nullable();
            $table->enum('head_teacher_action', ['approved', 'rejected'])->nullable();
            $table->text('head_teacher_comment')->nullable();

            // Return check-in data
            $table->timestamp('actual_return_date')->nullable();
            $table->boolean('is_late_return')->default(false);
            $table->text('late_return_reason')->nullable();
            $table->boolean('returned_alone')->nullable();
            $table->string('return_accompanied_by')->nullable();
            $table->string('return_guardian_type')->nullable();
            $table->string('return_relationship')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('teachers');
            $table->timestamp('verified_at')->nullable();

            // Documents
            $table->string('pdf_path')->nullable();
            $table->string('qr_code_path')->nullable();

            $table->timestamps();

            // Indexes
            $table->index('permit_number');
            $table->index('student_id');
            $table->index('status');
            $table->index(['status', 'created_at']);
            $table->index('departure_date');
            $table->index('expected_return_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('e_permits');
    }
};
