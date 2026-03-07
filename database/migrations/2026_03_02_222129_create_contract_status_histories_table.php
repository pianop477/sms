<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('contract_status_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('contract_id');
            $table->string('previous_status')->nullable();
            $table->string('new_status');
            $table->string('changed_by')->nullable(); // user email or ID
            $table->string('reason')->nullable(); // e.g., termination reason
            $table->json('metadata')->nullable(); // extra data (termination type, documents, etc.)
            $table->timestamps();

            $table->foreign('contract_id')->references('id')->on('school_constracts')->onDelete('cascade');
            $table->index('contract_id');
            $table->index('new_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contract_status_histories');
    }
};
