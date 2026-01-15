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
        Schema::connection('auth_db')->create('regions', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique(); // e.g., 'NCR', '01', '02'
            $table->string('name', 100); // e.g., 'National Capital Region', 'Region I'
            $table->string('long_name', 200)->nullable(); // e.g., 'National Capital Region (NCR)'
            $table->integer('psgc_code')->nullable(); // Philippine Standard Geographic Code
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('auth_db')->dropIfExists('regions');
    }
};
