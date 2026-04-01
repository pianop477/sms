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
            $table->unsignedBigInteger('class_id')->nullable()->after('school_id');
            $table->foreign('class_id')->references('id')->on('grades')->onDelete('cascade');

            // Remove the unique constraint on name if exists, add composite unique
            $table->unique(['school_id', 'name', 'class_id'], 'unique_fee_structure_per_class');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fee_structures', function (Blueprint $table) {
            //
            $table->dropForeign(['class_id']);
            $table->dropColumn('class_id');
            $table->dropUnique('unique_fee_structure_per_class');
        });
    }
};
