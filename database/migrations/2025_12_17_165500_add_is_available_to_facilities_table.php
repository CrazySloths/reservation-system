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
        // Get connection for facilities_db
        $connection = config('database.connections.facilities_db.database') ? 'facilities_db' : 'mysql';
        
        Schema::connection($connection)->table('facilities', function (Blueprint $table) {
            $table->boolean('is_available')->default(true)->after('image_path')
                  ->comment('Whether this facility is currently available for public booking');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $connection = config('database.connections.facilities_db.database') ? 'facilities_db' : 'mysql';
        
        Schema::connection($connection)->table('facilities', function (Blueprint $table) {
            $table->dropColumn('is_available');
        });
    }
};

