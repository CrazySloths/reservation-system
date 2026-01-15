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
        Schema::create('facilities', function (Blueprint $table) {
            $table->id();
            
            // Location
            $table->unsignedBigInteger('location_id');
            
            // Basic Information
            $table->string('facility_name', 255);
            $table->enum('facility_type', [
                'gymnasium',
                'convention_center',
                'function_hall',
                'sports_complex',
                'auditorium',
                'meeting_room',
                'other'
            ]);
            $table->text('description')->nullable();
            
            // Capacity
            $table->integer('capacity')->comment('Maximum number of people');
            
            // Pricing
            $table->decimal('hourly_rate', 10, 2)->nullable();
            $table->decimal('per_person_rate', 10, 2)->nullable();
            $table->decimal('deposit_amount', 10, 2)->nullable();
            
            // Amenities (JSON array)
            $table->json('amenities')->nullable()->comment('List of available amenities');
            
            // Rules & Guidelines
            $table->text('rules')->nullable();
            $table->text('terms_and_conditions')->nullable();
            
            // Availability
            $table->boolean('is_available')->default(true);
            $table->integer('advance_booking_days')->default(180)->comment('How many days in advance can book');
            $table->integer('min_booking_hours')->default(2)->comment('Minimum booking duration');
            $table->integer('max_booking_hours')->default(12)->comment('Maximum booking duration');
            
            // Operating Hours (JSON)
            $table->json('operating_hours')->nullable();
            
            // Address
            $table->text('address')->nullable();
            $table->text('google_maps_url')->nullable();
            
            // Status
            $table->enum('status', ['active', 'under_construction', 'under_maintenance', 'inactive'])->default('active');
            
            // Display Order
            $table->integer('display_order')->default(0);
            
            // Timestamps
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('location_id');
            $table->index('facility_type');
            $table->index('status');
            $table->index('is_available');
            $table->index('display_order');
            $table->index('deleted_at');
            
            // Foreign Keys
            $table->foreign('location_id')->references('id')->on('locations')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facilities');
    }
};
