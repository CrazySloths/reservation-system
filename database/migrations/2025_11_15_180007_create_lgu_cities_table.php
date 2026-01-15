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
        Schema::connection('facilities_db')->create('lgu_cities', function (Blueprint $table) {
            $table->id();
            $table->string('city_name')->unique()->comment('Caloocan, Quezon City, etc.');
            $table->string('city_code')->unique()->comment('CLCN, QC, etc.');
            $table->text('description')->nullable();
            $table->enum('status', ['active', 'coming_soon', 'inactive'])->default('active');
            $table->boolean('has_external_integration')->default(false)->comment('Integrated with their systems');
            $table->json('integration_config')->nullable()->comment('API URLs, keys, etc.');
            $table->integer('facility_count')->default(0)->comment('Cached count of facilities');
            $table->timestamps();
            
            // Add indexes
            $table->index('status');
        });
        
        // Add LGU city reference to facilities table
        Schema::connection('facilities_db')->table('facilities', function (Blueprint $table) {
            $table->unsignedBigInteger('lgu_city_id')->nullable()->after('facility_id');
            $table->foreign('lgu_city_id')->references('id')->on('lgu_cities')->onDelete('set null');
            $table->index('lgu_city_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop foreign key from facilities first
        Schema::connection('facilities_db')->table('facilities', function (Blueprint $table) {
            $table->dropForeign(['lgu_city_id']);
            $table->dropIndex(['lgu_city_id']);
            $table->dropColumn('lgu_city_id');
        });
        
        // Then drop the lgu_cities table
        Schema::connection('facilities_db')->dropIfExists('lgu_cities');
    }
};

