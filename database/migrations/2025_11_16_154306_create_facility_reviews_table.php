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
        Schema::connection('facilities_db')->create('facility_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('facility_id')->constrained('facilities', 'facility_id')->onDelete('cascade');
            $table->foreignId('booking_id')->constrained('bookings')->onDelete('cascade');
            $table->unsignedBigInteger('user_id'); // From auth_db
            $table->string('user_name');
            $table->integer('rating')->unsigned()->comment('1-5 stars');
            $table->text('review')->nullable();
            $table->boolean('is_verified')->default(false)->comment('Only for completed bookings');
            $table->boolean('is_visible')->default(true);
            $table->text('admin_response')->nullable();
            $table->unsignedBigInteger('admin_responder_id')->nullable();
            $table->timestamp('admin_responded_at')->nullable();
            $table->timestamps();
            
            // Add indexes
            $table->index('facility_id');
            $table->index('user_id');
            $table->index('rating');
            $table->index('is_visible');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('facilities_db')->dropIfExists('facility_reviews');
    }
};
