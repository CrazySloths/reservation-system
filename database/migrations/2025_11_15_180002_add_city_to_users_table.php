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
        Schema::table('users', function (Blueprint $table) {
            // City field for residency tracking
            $table->string('city')->nullable()->comment('City of residence (Caloocan, Quezon City, etc.)');
            
            // Boolean flag for quick residency check (automatically set based on city)
            $table->boolean('is_caloocan_resident')->default(false)->comment('Auto-tagged if city is Caloocan');
            
            // Add index for faster filtering
            $table->index('city');
            $table->index('is_caloocan_resident');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['city']);
            $table->dropIndex(['is_caloocan_resident']);
            $table->dropColumn(['city', 'is_caloocan_resident']);
        });
    }
};

