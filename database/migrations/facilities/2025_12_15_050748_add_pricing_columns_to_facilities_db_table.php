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
        Schema::connection('facilities_db')->table('facilities', function (Blueprint $table) {
            $table->decimal('per_person_rate', 10, 2)->nullable()->after('capacity');
            $table->decimal('hourly_rate', 10, 2)->nullable()->after('per_person_rate');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('facilities_db')->table('facilities', function (Blueprint $table) {
            $table->dropColumn(['per_person_rate', 'hourly_rate']);
        });
    }
};
