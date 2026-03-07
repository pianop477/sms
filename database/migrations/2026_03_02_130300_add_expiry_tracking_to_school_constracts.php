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
        Schema::table('school_constracts', function (Blueprint $table) {
            //
            $table->timestamp('expired_at')->nullable()->after('activated_at');
            $table->timestamp('reminder_sent_at')->nullable()->after('expired_at');
            $table->timestamp('warning_sent_at')->nullable()->after('reminder_sent_at');
            $table->index(['status', 'end_date']);
            $table->index(['applicant_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('school_constracts', function (Blueprint $table) {
            //
        });
    }
};
