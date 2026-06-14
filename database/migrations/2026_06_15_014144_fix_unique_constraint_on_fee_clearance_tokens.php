<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FixUniqueConstraintOnFeeClearanceTokens extends Migration
{
    public function up()
    {
        // Remove old wrong constraint
        Schema::table('fee_clearance_tokens', function (Blueprint $table) {
            $table->dropUnique('fee_clearance_tokens_student_id_installment_id_unique');
        });

        // Remove duplicates (keep latest token per student per year)
        DB::statement('
            DELETE t1 FROM fee_clearance_tokens t1
            INNER JOIN fee_clearance_tokens t2
            WHERE t1.student_id = t2.student_id
              AND t1.academic_year = t2.academic_year
              AND t1.id < t2.id
        ');

        // Add new correct unique constraint
        Schema::table('fee_clearance_tokens', function (Blueprint $table) {
            $table->unique(['student_id', 'academic_year'], 'unique_student_academic_year');
        });
    }

    public function down()
    {
        Schema::table('fee_clearance_tokens', function (Blueprint $table) {
            $table->dropUnique('unique_student_academic_year');
            $table->unique(['student_id', 'installment_id'], 'fee_clearance_tokens_student_id_installment_id_unique');
        });
    }
}
