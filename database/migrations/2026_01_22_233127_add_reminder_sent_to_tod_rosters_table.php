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
        Schema::table('tod_rosters', function (Blueprint $table) {
            //
            $table->boolean('reminder_sent')->default(false)->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tod_rosters', function (Blueprint $table) {
            //
            $table->dropColumn('reminder_sent');
        });
    }
};
