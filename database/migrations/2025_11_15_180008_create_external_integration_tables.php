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
        // 1. Maintenance Schedule Integration (Bidirectional)
        Schema::connection('facilities_db')->create('maintenance_requests_sent', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('facility_id');
            $table->unsignedBigInteger('booking_id')->nullable();
            $table->json('request_data')->comment('Facility name, time, maintenance type');
            $table->string('external_request_id')->nullable();
            $table->enum('status', ['sent', 'acknowledged', 'failed'])->default('sent');
            $table->timestamps();
            
            $table->foreign('facility_id')->references('facility_id')->on('facilities')->onDelete('cascade');
            $table->foreign('booking_id')->references('id')->on('bookings')->onDelete('set null');
        });
        
        Schema::connection('facilities_db')->create('maintenance_schedules_received', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('facility_id');
            $table->string('external_schedule_id')->unique();
            $table->dateTime('maintenance_start');
            $table->dateTime('maintenance_end');
            $table->string('maintenance_team')->nullable();
            $table->enum('status', ['scheduled', 'in_progress', 'completed', 'cancelled'])->default('scheduled');
            $table->json('received_data')->comment('Full data from external system');
            $table->timestamps();
            
            $table->foreign('facility_id')->references('facility_id')->on('facilities')->onDelete('cascade');
            $table->index(['maintenance_start', 'maintenance_end'], 'maint_sched_dates_idx');
        });
        
        // 2. Energy Consumption Integration (Bidirectional)
        Schema::connection('facilities_db')->create('usage_reports_sent', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('facility_id');
            $table->unsignedBigInteger('booking_id');
            $table->json('usage_data')->comment('Duration, attendees, equipment');
            $table->string('external_report_id')->nullable();
            $table->enum('status', ['sent', 'acknowledged', 'failed'])->default('sent');
            $table->timestamps();
            
            $table->foreign('facility_id')->references('facility_id')->on('facilities')->onDelete('cascade');
            $table->foreign('booking_id')->references('id')->on('bookings')->onDelete('cascade');
        });
        
        Schema::connection('facilities_db')->create('energy_reports_received', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('facility_id');
            $table->unsignedBigInteger('booking_id')->nullable();
            $table->string('external_report_id')->unique();
            $table->decimal('energy_consumed_kwh', 10, 2);
            $table->decimal('energy_cost', 10, 2);
            $table->string('efficiency_rating')->nullable();
            $table->json('recommendations')->nullable();
            $table->json('received_data')->comment('Full data from external system');
            $table->timestamps();
            
            $table->foreign('facility_id')->references('facility_id')->on('facilities')->onDelete('cascade');
            $table->foreign('booking_id')->references('id')->on('bookings')->onDelete('set null');
        });
        
        // 3. Project Planning Integration (Incoming Only)
        Schema::connection('facilities_db')->create('external_projects', function (Blueprint $table) {
            $table->id();
            $table->string('external_project_id')->unique();
            $table->string('project_name');
            $table->string('contractor_name')->nullable();
            $table->text('description')->nullable();
            $table->date('project_start_date');
            $table->date('project_end_date');
            $table->json('affected_facilities')->comment('Array of facility IDs');
            $table->text('impact_description');
            $table->enum('status', ['planned', 'ongoing', 'completed', 'cancelled'])->default('planned');
            $table->json('received_data')->comment('Full data from external system');
            $table->timestamps();
            
            $table->index(['project_start_date', 'project_end_date'], 'ext_proj_dates_idx');
        });
        
        // 4. Road Maintenance Integration (Bidirectional)
        Schema::connection('facilities_db')->create('event_schedules_sent', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('booking_id');
            $table->json('event_data')->comment('Event name, facility, attendees, date/time');
            $table->string('external_request_id')->nullable();
            $table->enum('status', ['sent', 'acknowledged', 'failed'])->default('sent');
            $table->timestamps();
            
            $table->foreign('booking_id')->references('id')->on('bookings')->onDelete('cascade');
        });
        
        Schema::connection('facilities_db')->create('road_maintenance_received', function (Blueprint $table) {
            $table->id();
            $table->string('external_maintenance_id')->unique();
            $table->string('road_name');
            $table->enum('road_status', ['open', 'closed', 'partial'])->default('open');
            $table->dateTime('closure_start')->nullable();
            $table->dateTime('closure_end')->nullable();
            $table->text('alternative_routes')->nullable();
            $table->text('traffic_advisory')->nullable();
            $table->json('received_data')->comment('Full data from external system');
            $table->timestamps();
            
            $table->index(['closure_start', 'closure_end'], 'road_maint_closure_idx');
        });
        
        // 5. Treasurer's Office Integration Logs (beyond payment_slips fields)
        Schema::connection('facilities_db')->create('treasurer_webhooks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('payment_slip_id')->nullable();
            $table->string('webhook_type')->comment('payment_confirmed, payment_failed, refund_processed');
            $table->json('payload')->comment('Full webhook payload');
            $table->string('signature')->nullable()->comment('Webhook signature for validation');
            $table->string('source_ip')->nullable();
            $table->enum('status', ['received', 'processed', 'failed'])->default('received');
            $table->text('processing_notes')->nullable();
            $table->timestamps();
            
            $table->foreign('payment_slip_id')->references('id')->on('payment_slips')->onDelete('set null');
            $table->index('webhook_type');
        });
        
        Schema::connection('facilities_db')->create('treasurer_sync_log', function (Blueprint $table) {
            $table->id();
            $table->enum('sync_type', ['facility_data_sent', 'payment_received', 'refund_sent'])->default('facility_data_sent');
            $table->unsignedBigInteger('payment_slip_id')->nullable();
            $table->json('data_sent')->nullable();
            $table->json('response_received')->nullable();
            $table->enum('status', ['success', 'failed', 'pending'])->default('pending');
            $table->text('error_message')->nullable();
            $table->timestamps();
            
            $table->foreign('payment_slip_id')->references('id')->on('payment_slips')->onDelete('set null');
            $table->index('sync_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('facilities_db')->dropIfExists('treasurer_sync_log');
        Schema::connection('facilities_db')->dropIfExists('treasurer_webhooks');
        Schema::connection('facilities_db')->dropIfExists('road_maintenance_received');
        Schema::connection('facilities_db')->dropIfExists('event_schedules_sent');
        Schema::connection('facilities_db')->dropIfExists('external_projects');
        Schema::connection('facilities_db')->dropIfExists('energy_reports_received');
        Schema::connection('facilities_db')->dropIfExists('usage_reports_sent');
        Schema::connection('facilities_db')->dropIfExists('maintenance_schedules_received');
        Schema::connection('facilities_db')->dropIfExists('maintenance_requests_sent');
    }
};
