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
        Schema::connection('auth_db')->table('districts', function (Blueprint $table) {
            // Drop the unique constraint on district_number
            // District numbers are only unique within a city, not globally
            $table->dropUnique('districts_district_number_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('auth_db')->table('districts', function (Blueprint $table) {
            $table->unique('district_number');
        });
    }
};

