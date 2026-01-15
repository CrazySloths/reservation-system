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
        Schema::connection('auth_db')->table('users', function (Blueprint $table) {
            // Add new Philippine address fields
            $table->unsignedBigInteger('region_id')->nullable()->after('nationality');
            $table->unsignedBigInteger('province_id')->nullable()->after('region_id');
            $table->unsignedBigInteger('city_id')->nullable()->after('province_id');
            
            // Make district_id nullable (optional - only for cities with districts)
            $table->unsignedBigInteger('district_id')->nullable()->change();
            
            // barangay_id already exists, just make sure it's nullable
            $table->unsignedBigInteger('barangay_id')->nullable()->change();
            
            // Add foreign keys
            $table->foreign('region_id')->references('id')->on('regions')->onDelete('set null');
            $table->foreign('province_id')->references('id')->on('provinces')->onDelete('set null');
            $table->foreign('city_id')->references('id')->on('cities')->onDelete('set null');
            
            // Add indexes for better query performance
            $table->index('region_id');
            $table->index('province_id');
            $table->index('city_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('auth_db')->table('users', function (Blueprint $table) {
            // Drop foreign keys first
            $table->dropForeign(['region_id']);
            $table->dropForeign(['province_id']);
            $table->dropForeign(['city_id']);
            
            // Drop indexes
            $table->dropIndex(['region_id']);
            $table->dropIndex(['province_id']);
            $table->dropIndex(['city_id']);
            
            // Drop columns
            $table->dropColumn(['region_id', 'province_id', 'city_id']);
            
            // Revert district_id and barangay_id to non-nullable (if needed)
            // Note: We'll keep them nullable for flexibility
        });
    }
};
