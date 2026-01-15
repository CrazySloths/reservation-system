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
        Schema::connection('facilities_db')->create('equipment_inventory', function (Blueprint $table) {
            $table->id('equipment_id');
            $table->string('equipment_name', 100);
            $table->string('category', 50)->nullable(); // 'AV Equipment', 'Furniture', 'Power', 'Event', etc.
            $table->integer('total_quantity')->default(0);
            $table->integer('available_quantity')->default(0);
            $table->integer('in_use_quantity')->default(0);
            $table->integer('maintenance_quantity')->default(0);
            $table->string('condition', 50)->default('good'); // 'excellent', 'good', 'fair', 'needs_repair'
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index('equipment_name');
            $table->index('category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('facilities_db')->dropIfExists('equipment_inventory');
    }
};
