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
            $table->unsignedBigInteger('facility_id');
            $table->unsignedBigInteger('booking_id');
            $table->unsignedBigInteger('user_id');
            $table->string('user_name');
            $table->tinyInteger('rating')->comment('1-5 stars');
            $table->text('review')->nullable();
            $table->boolean('is_verified')->default(false)->comment('Verified by admin');
            $table->boolean('is_visible')->default(true)->comment('Visible to public');
            $table->text('admin_response')->nullable();
            $table->timestamp('admin_response_at')->nullable();
            $table->string('moderation_status')->default('approved')->comment('approved, rejected, pending');
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Foreign keys
            $table->foreign('facility_id')->references('facility_id')->on('facilities')->onDelete('cascade');
            $table->foreign('booking_id')->references('id')->on('bookings')->onDelete('cascade');
            
            // Indexes
            $table->index('facility_id');
            $table->index('booking_id');
            $table->index('user_id');
            $table->index('is_visible');
            $table->index('rating');
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
