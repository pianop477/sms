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
        Schema::create('fee_installments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('fee_structure_id');
            $table->string('name'); // Term 1, Term 2
            $table->decimal('amount', 12, 2); // per installment
            $table->decimal('cumulative_required', 12, 2); // muhimu sana 🔥
            $table->timestamp('start_date');
            $table->timestamp('end_date')->nullable();
            $table->integer('order'); // 1,2,3,4
            $table->timestamps();

            $table->foreign('fee_structure_id')->references('id')->on('fee_structures')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fee_installments');
    }
};
