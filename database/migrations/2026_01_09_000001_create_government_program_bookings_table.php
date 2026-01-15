<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Specify the correct database connection
        Schema::connection('facilities_db')->create('government_program_bookings', function (Blueprint $table) {
            $table->id();
            
            // Integration with Energy Efficiency System
            $table->string('source_system', 100)->default('Energy Efficiency');
            $table->string('source_seminar_id', 50); // Links to ener_nova_capri.seminars.seminar_id
            $table->string('source_database', 100)->default('ener_nova_capri');
            
            // Organizer Details (from their users table)
            $table->string('organizer_user_id', 50); // Links to their users.user_id
            $table->string('organizer_name');
            $table->string('organizer_contact', 20);
            $table->string('organizer_email')->nullable();
            $table->string('organizer_area')->nullable(); // Their barangay
            
            // Event Details (from their seminars table)
            $table->string('program_title');
            $table->enum('program_type', ['seminar', 'training', 'workshop', 'community_event', 'other'])->default('seminar');
            $table->text('program_description')->nullable();
            $table->date('event_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('expected_attendees')->default(0);
            $table->integer('actual_attendees')->default(0);
            
            // Their requested location vs Our assigned facility
            $table->string('requested_location')->nullable(); // What they want
            $table->unsignedBigInteger('assigned_facility_id')->nullable(); // What we assign
            
            // Coordination
            $table->enum('coordination_status', [
                'pending_review',
                'organizer_contacted',
                'speaker_coordinating',
                'fund_requested',
                'fund_approved',
                'facility_assigned',
                'confirmed',
                'completed',
                'cancelled'
            ])->default('pending_review');
            
            $table->json('call_log')->nullable(); // Admin-organizer calls
            $table->text('coordination_notes')->nullable();
            
            // Speaker Coordination
            $table->integer('number_of_speakers')->default(1);
            $table->json('speaker_details')->nullable();
            $table->text('speaker_coordination_notes')->nullable();
            $table->boolean('speakers_confirmed')->default(false);
            
            // Budget & Funding
            $table->decimal('requested_amount', 15, 2)->default(0);
            $table->decimal('approved_amount', 15, 2)->default(0);
            $table->decimal('actual_spent', 15, 2)->default(0);
            $table->json('fund_breakdown')->nullable();
            $table->boolean('is_fee_waived')->default(true);
            
            // Finance Integration
            $table->string('finance_request_id', 50)->nullable();
            $table->enum('finance_status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->date('finance_approved_date')->nullable();
            $table->string('finance_check_number', 50)->nullable();
            
            // Transparency
            $table->boolean('pre_event_transparency_published')->default(false);
            $table->boolean('post_event_transparency_published')->default(false);
            $table->boolean('is_public_display')->default(true);
            
            // Liquidation
            $table->boolean('liquidation_required')->default(true);
            $table->boolean('liquidation_submitted')->default(false);
            $table->date('liquidation_date')->nullable();
            
            // Event Outcome
            $table->decimal('event_rating', 3, 2)->nullable();
            $table->text('feedback_summary')->nullable();
            $table->json('attendance_data')->nullable(); // From their attendance table
            
            // Assignment
            $table->unsignedBigInteger('assigned_admin_id')->nullable();
            $table->timestamp('assigned_at')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // NOTE: No foreign keys across databases (multi-database architecture)
            // Relationships handled via Eloquent models
            
            // Indexes
            $table->index('source_seminar_id');
            $table->index('coordination_status');
            $table->index('event_date');
            $table->index('finance_status');
        });
    }

    public function down()
    {
        Schema::connection('facilities_db')->dropIfExists('government_program_bookings');
    }
};

