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
        Schema::connection('auth_db')->create('cities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('province_id');
            $table->string('code', 20)->unique(); // e.g., 'QC', 'MNL', 'CEB'
            $table->string('name', 100); // e.g., 'Quezon City', 'Manila', 'Cebu City'
            $table->enum('type', ['city', 'municipality'])->default('municipality'); // City or Municipality
            $table->boolean('has_districts')->default(false); // True for cities with districts (e.g., Quezon City)
            $table->integer('psgc_code')->nullable(); // Philippine Standard Geographic Code
            $table->timestamps();
            
            $table->foreign('province_id')->references('id')->on('provinces')->onDelete('cascade');
            $table->index('province_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('auth_db')->dropIfExists('cities');
    }
};
