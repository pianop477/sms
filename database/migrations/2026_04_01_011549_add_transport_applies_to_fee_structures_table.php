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
        Schema::table('fee_structures', function (Blueprint $table) {
            //
            $table->boolean('transport_applies')->default(true)->after('class_id');

            // Kama transport_applies = false, wanafunzi wote wanalipa sawa
            $table->boolean('is_hostel_class')->default(false)->after('transport_applies');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fee_structures', function (Blueprint $table) {
            //
            $table->dropColumn('transport_applies');
            $table->dropColumn('is_hostel_class');
        });
    }
};
