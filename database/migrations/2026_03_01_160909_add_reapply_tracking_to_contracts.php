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
            $table->unsignedBigInteger('original_contract_id')->nullable()->after('id');
            $table->unsignedBigInteger('new_contract_id')->nullable()->after('original_contract_id');
            $table->timestamp('reapplied_at')->nullable()->after('rejected_at');
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
