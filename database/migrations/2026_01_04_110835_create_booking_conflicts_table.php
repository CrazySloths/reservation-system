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
        Schema::connection('facilities_db')->create('booking_conflicts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('booking_id');
            $table->unsignedBigInteger('city_event_id');
            $table->enum('status', ['pending', 'resolved'])->default('pending');
            $table->enum('citizen_choice', ['reschedule', 'refund', 'no_response'])->nullable();
            $table->dateTime('response_deadline');
            $table->dateTime('responded_at')->nullable();
            $table->dateTime('resolved_at')->nullable();
            $table->unsignedBigInteger('new_booking_id')->nullable(); // if rescheduled
            $table->text('admin_notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes for performance
            $table->index('booking_id');
            $table->index('city_event_id');
            $table->index('status');
            $table->index('response_deadline');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('facilities_db')->dropIfExists('booking_conflicts');
    }
};
