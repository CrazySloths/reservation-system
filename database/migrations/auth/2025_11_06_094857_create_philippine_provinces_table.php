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
        Schema::connection('auth_db')->create('provinces', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('region_id');
            $table->string('code', 20)->unique(); // e.g., 'ABR', 'BTN', 'MNL'
            $table->string('name', 100); // e.g., 'Abra', 'Bataan', 'Metro Manila'
            $table->integer('psgc_code')->nullable(); // Philippine Standard Geographic Code
            $table->timestamps();
            
            $table->foreign('region_id')->references('id')->on('regions')->onDelete('cascade');
            $table->index('region_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('auth_db')->dropIfExists('provinces');
    }
};
