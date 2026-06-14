<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNotificationSentAndUniqueToFeeClearanceTokens extends Migration
{
    public function up()
    {
        Schema::table('fee_clearance_tokens', function (Blueprint $table) {
            if (!Schema::hasColumn('fee_clearance_tokens', 'notification_sent')) {
                $table->boolean('notification_sent')->default(false);
            }
        });

        // Remove any existing duplicate tokens before adding unique
        DB::statement('
            DELETE t1 FROM fee_clearance_tokens t1
            INNER JOIN fee_clearance_tokens t2
            WHERE t1.student_id = t2.student_id
              AND t1.academic_year = t2.academic_year
              AND t1.created_at < t2.created_at
        ');

        Schema::table('fee_clearance_tokens', function (Blueprint $table) {
            $table->unique(['student_id', 'academic_year'], 'unique_student_academic_year');
        });
    }

    public function down()
    {
        Schema::table('fee_clearance_tokens', function (Blueprint $table) {
            $table->dropUnique('unique_student_academic_year');
            $table->dropColumn('notification_sent');
        });
    }
}
