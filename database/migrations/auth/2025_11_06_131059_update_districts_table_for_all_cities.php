<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Update districts table to support ALL cities, not just Quezon City.
     * Districts come BEFORE barangays in the selection flow.
     */
    public function up(): void
    {
        Schema::connection('auth_db')->table('districts', function (Blueprint $table) {
            // Add city_id to link districts to specific cities
            $table->unsignedBigInteger('city_id')->nullable()->after('id');
            
            // Add foreign key constraint
            $table->foreign('city_id')->references('id')->on('cities')->onDelete('cascade');
            
            // Add district type (congressional, city/municipal, etc.)
            $table->string('type', 50)->default('city')->after('name'); // 'congressional', 'city', 'municipal'
            
            // Make district_number nullable (some cities don't use numbers)
            $table->integer('district_number')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('auth_db')->table('districts', function (Blueprint $table) {
            $table->dropForeign(['city_id']);
            $table->dropColumn(['city_id', 'type']);
        });
    }
};
