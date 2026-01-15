<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Add zip_code to barangays table.
     * In the Philippines, ZIP codes are assigned at the barangay level.
     */
    public function up(): void
    {
        Schema::connection('auth_db')->table('barangays', function (Blueprint $table) {
            $table->string('zip_code', 10)->nullable()->after('alternate_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('auth_db')->table('barangays', function (Blueprint $table) {
            $table->dropColumn('zip_code');
        });
    }
};

