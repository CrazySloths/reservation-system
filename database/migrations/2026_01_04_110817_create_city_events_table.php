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
        Schema::connection('facilities_db')->create('city_events', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('facility_id');
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->string('event_title');
            $table->text('event_description')->nullable();
            $table->string('event_type')->default('government'); // government, emergency, maintenance
            $table->unsignedBigInteger('created_by'); // user_id from auth_db
            $table->enum('status', ['scheduled', 'ongoing', 'completed', 'cancelled'])->default('scheduled');
            $table->integer('affected_bookings_count')->default(0);
            $table->timestamps();
            $table->softDeletes();

            // Indexes for performance
            $table->index('facility_id');
            $table->index('start_time');
            $table->index('end_time');
            $table->index('created_by');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('facilities_db')->dropIfExists('city_events');
    }
};
