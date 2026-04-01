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
        Schema::table('student_fee_assignments', function (Blueprint $table) {
            //
            // Snapshot of class at assignment time
            $table->unsignedBigInteger('assigned_class_id')->nullable()->after('fee_structure_id');
            $table->foreign('assigned_class_id')->references('id')->on('grades');

            // Track why assignment was made
            $table->string('assignment_reason')->nullable()->after('assigned_class_id');

            // Track if this is the active assignment
            $table->boolean('is_active')->default(true)->after('assignment_reason');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_fee_assignments', function (Blueprint $table) {
            //
            $table->dropForeign(['assigned_class_id']);
            $table->dropColumn(['assigned_class_id', 'assignment_reason', 'is_active']);
        });
    }
};
