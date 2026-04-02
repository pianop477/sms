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
        Schema::table('fee_installments', function (Blueprint $table) {
            //
            $table->year('academic_year')->nullable()->after('fee_structure_id');
            $table->index('academic_year');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fee_installments', function (Blueprint $table) {
            //
            $table->dropColumn('academic_year');
        });
    }
};
