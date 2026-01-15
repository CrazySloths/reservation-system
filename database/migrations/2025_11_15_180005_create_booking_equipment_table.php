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
        Schema::connection('facilities_db')->create('booking_equipment', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('booking_id');
            $table->unsignedBigInteger('equipment_item_id');
            $table->integer('quantity')->default(1)->comment('Number of units rented');
            $table->decimal('price_per_unit', 10, 2)->comment('Price at time of booking (locked)');
            $table->decimal('subtotal', 10, 2)->comment('quantity * price_per_unit');
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('booking_id')->references('id')->on('bookings')->onDelete('cascade');
            $table->foreign('equipment_item_id')->references('id')->on('equipment_items')->onDelete('cascade');
            
            // Indexes
            $table->index('booking_id');
            $table->index('equipment_item_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('facilities_db')->dropIfExists('booking_equipment');
    }
};

