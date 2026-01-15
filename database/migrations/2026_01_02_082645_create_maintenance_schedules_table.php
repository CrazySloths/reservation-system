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
        Schema::connection('facilities_db')->create('maintenance_schedules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('facility_id');
            $table->date('start_date')->comment('Maintenance start date');
            $table->date('end_date')->comment('Maintenance end date');
            $table->time('start_time')->nullable()->comment('Maintenance start time (optional - affects whole day if null)');
            $table->time('end_time')->nullable()->comment('Maintenance end time (optional)');
            $table->string('maintenance_type')->comment('routine, repair, renovation, inspection');
            $table->text('description')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_recurring')->default(false);
            $table->string('recurring_pattern')->nullable()->comment('daily, weekly, monthly, yearly');
            $table->unsignedBigInteger('created_by')->comment('Admin user ID who scheduled');
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('facility_id');
            $table->index('start_date');
            $table->index('end_date');
            $table->index('maintenance_type');
            $table->index('is_recurring');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('facilities_db')->dropIfExists('maintenance_schedules');
    }
};
