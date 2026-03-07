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
        Schema::table('transports', function (Blueprint $table) {
            //
            $table->string('bank_account_number')->nullable()->after('nida');
            $table->string('bank_account_name')->nullable()->after('bank_account_number');
            $table->string('bank_name')->nullable()->after('bank_account_name');
            $table->string('alternative_phone')->nullable()->after('bank_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transports', function (Blueprint $table) {
            //
        });
    }
};
