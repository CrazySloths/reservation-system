<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::connection('facilities_db')->create('supplier_products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('supplier_id');
            
            $table->string('product_code', 50)->nullable();
            $table->string('product_name');
            $table->text('product_description')->nullable();
            $table->enum('product_category', ['meal', 'beverage', 'printing', 'material', 'service', 'other']);
            
            $table->json('specifications')->nullable();
            $table->string('unit_of_measure', 50)->default('piece');
            
            $table->decimal('current_price', 10, 2);
            $table->date('price_effective_date');
            
            $table->boolean('is_available')->default(true);
            
            $table->string('product_photo_url', 500)->nullable();
            $table->string('price_list_document_url', 500)->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('cascade');
            
            $table->index('supplier_id');
            $table->index('is_available');
            $table->index('product_category');
        });
    }

    public function down()
    {
        Schema::connection('facilities_db')->dropIfExists('supplier_products');
    }
};

