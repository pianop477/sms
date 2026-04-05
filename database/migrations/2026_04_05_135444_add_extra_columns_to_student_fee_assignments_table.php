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
                        //
            $table->year('academic_year')->nullable()->after('fee_structure_id');
            $table->dropUnique('student_fee_assignments_student_id_unique');

            // 2. Add composite unique key for student_id and academic_year
            $table->index('academic_year');
            $table->unique(['student_id', 'academic_year'], 'uk_student_year');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_fee_assignments', function (Blueprint $table) {

            $table->dropUnique('uk_student_year');
            $table->unique('student_id', 'student_fee_assignments_student_id_unique');
        });
    }
};
