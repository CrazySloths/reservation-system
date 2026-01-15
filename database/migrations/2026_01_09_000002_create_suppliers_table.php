<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::connection('facilities_db')->create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('supplier_name');
            $table->enum('supplier_type', ['food_service', 'printing', 'transportation', 'supplies', 'other']);
            
            $table->string('contact_person')->nullable();
            $table->string('contact_phone', 20)->nullable();
            $table->string('contact_email')->nullable();
            $table->text('business_address')->nullable();
            
            $table->string('business_permit_number', 100)->nullable();
            $table->string('tin_number', 50)->nullable();
            $table->string('bir_registration', 100)->nullable();
            
            $table->boolean('is_active')->default(true);
            $table->boolean('is_verified')->default(false);
            $table->boolean('is_preferred_supplier')->default(false);
            
            $table->unsignedBigInteger('created_by_user_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // NOTE: No foreign keys across databases
            
            $table->index('supplier_type');
            $table->index('is_active');
        });
    }

    public function down()
    {
        Schema::connection('facilities_db')->dropIfExists('suppliers');
    }
};

