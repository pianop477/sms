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
        Schema::create('academic_notifications', function (Blueprint $table) {
            $table->id();
            $table->string('notification_type'); // 'low_roster' au 'no_roster'
            $table->integer('pending_count')->nullable();
            $table->integer('active_count')->nullable();
            $table->timestamp('sent_at');
            $table->date('notification_date');
            $table->string('unique_key')->unique(); // TUTUMIE HII KUZUIA DUPLICATES
            $table->timestamps();

            // Indexes kwa ajili ya performance
            $table->index(['notification_date', 'notification_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('academic_notifications');
    }
};
