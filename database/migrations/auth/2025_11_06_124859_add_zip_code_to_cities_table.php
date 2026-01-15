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
        Schema::connection('auth_db')->table('cities', function (Blueprint $table) {
            $table->string('zip_code', 10)->nullable()->after('has_districts');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('auth_db')->table('cities', function (Blueprint $table) {
            $table->dropColumn('zip_code');
        });
    }
};
