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
        Schema::connection('facilities_db')->table('bookings', function (Blueprint $table) {
            // Add staff verification fields
            $table->unsignedBigInteger('staff_verified_by')->nullable()->after('status');
            $table->timestamp('staff_verified_at')->nullable()->after('staff_verified_by');
            $table->text('staff_notes')->nullable()->after('staff_verified_at');
            
            // Add foreign key for staff who verified
            $table->foreign('staff_verified_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('facilities_db')->table('bookings', function (Blueprint $table) {
            $table->dropForeign(['staff_verified_by']);
            $table->dropColumn(['staff_verified_by', 'staff_verified_at', 'staff_notes']);
        });
    }
};
