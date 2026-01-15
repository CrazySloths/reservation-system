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
        Schema::connection('auth_db')->table('barangays', function (Blueprint $table) {
            // Add city_id column
            $table->unsignedBigInteger('city_id')->nullable()->after('id');
            
            // Add foreign key
            $table->foreign('city_id')->references('id')->on('cities')->onDelete('cascade');
            $table->index('city_id');
            
            // Make district_id nullable (not all barangays have districts)
            $table->unsignedBigInteger('district_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('auth_db')->table('barangays', function (Blueprint $table) {
            // Drop foreign key and column
            $table->dropForeign(['city_id']);
            $table->dropColumn('city_id');
            
            // Revert district_id to non-nullable
            $table->unsignedBigInteger('district_id')->nullable(false)->change();
        });
    }
};
