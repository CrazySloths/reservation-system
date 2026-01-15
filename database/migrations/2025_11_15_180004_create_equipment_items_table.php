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
        Schema::connection('facilities_db')->create('equipment_items', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Equipment name (e.g., Monobloc Chair, Round Table)');
            $table->string('category')->comment('chairs, tables, sound_system, lighting, etc.');
            $table->text('description')->nullable();
            $table->decimal('price_per_unit', 10, 2)->comment('Rental price per unit');
            $table->integer('quantity_available')->default(0)->comment('Total units available');
            $table->boolean('is_available')->default(true)->comment('Can be rented');
            $table->string('image_path')->nullable()->comment('Photo of equipment');
            $table->timestamps();
            
            // Add indexes
            $table->index('category');
            $table->index('is_available');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('facilities_db')->dropIfExists('equipment_items');
    }
};

