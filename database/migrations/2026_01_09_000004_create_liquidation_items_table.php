<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::connection('facilities_db')->create('liquidation_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('government_program_booking_id');
            
            $table->enum('category', ['refreshments', 'materials', 'transportation', 'miscellaneous']);
            
            $table->string('supplier_name')->nullable();
            $table->string('official_receipt_number', 100)->nullable();
            $table->date('receipt_date')->nullable();
            $table->string('receipt_image_url', 500)->nullable();
            
            $table->string('item_description');
            $table->text('item_specification')->nullable();
            $table->integer('quantity');
            $table->decimal('unit_price', 10, 2);
            $table->decimal('total_price', 10, 2);
            
            $table->boolean('is_public_display')->default(true);
            
            $table->timestamps();
            
            $table->foreign('government_program_booking_id', 'fk_liquidation_program')
                ->references('id')
                ->on('government_program_bookings')
                ->onDelete('cascade');
            
            $table->index('category');
        });
    }

    public function down()
    {
        Schema::connection('facilities_db')->dropIfExists('liquidation_items');
    }
};

