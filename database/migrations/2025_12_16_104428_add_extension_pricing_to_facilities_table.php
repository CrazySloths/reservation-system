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
        Schema::table('facilities', function (Blueprint $table) {
            $table->decimal('per_person_extension_rate', 10, 2)->nullable()->after('per_person_rate');
            $table->integer('base_hours')->default(3)->after('per_person_extension_rate');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('facilities', function (Blueprint $table) {
            $table->dropColumn(['per_person_extension_rate', 'base_hours']);
        });
    }
};
