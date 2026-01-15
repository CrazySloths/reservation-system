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
        Schema::connection('facilities_db')->table('government_program_bookings', function (Blueprint $table) {
            $table->json('equipment_provided')->nullable()->after('speaker_coordination_notes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('facilities_db')->table('government_program_bookings', function (Blueprint $table) {
            $table->dropColumn('equipment_provided');
        });
    }
};
