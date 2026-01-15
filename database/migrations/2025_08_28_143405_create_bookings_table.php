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
    Schema::connection('facilities_db')->create('bookings', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('facility_id');
        $table->dateTime('start_time');
        $table->dateTime('end_time');
        $table->string('user_name'); // For now, we'll store the user's name as a string
        $table->string('status')->default('pending'); // pending, approved, rejected
        $table->timestamps();

        // Add a foreign key constraint to link to the facilities table
        $table->foreign('facility_id')->references('facility_id')->on('facilities')->onDelete('cascade');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('facilities_db')->dropIfExists('bookings');
    }
    
};
