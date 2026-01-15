<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Step 1: Modify the status column to support enhanced statuses
        // Old statuses: pending, staff_verified, approved, rejected
        // New statuses: reserved, tentative, pending_approval, payment_pending, confirmed, expired, rejected
        
        DB::connection('facilities_db')->statement("ALTER TABLE bookings MODIFY COLUMN status VARCHAR(50) DEFAULT 'pending'");
        
        // Step 2: Add admin approval tracking fields
        Schema::connection('facilities_db')->table('bookings', function (Blueprint $table) {
            // Admin approval fields
            $table->unsignedBigInteger('admin_approved_by')->nullable()->after('staff_notes');
            $table->timestamp('admin_approved_at')->nullable()->after('admin_approved_by');
            $table->text('admin_approval_notes')->nullable()->after('admin_approved_at');
            
            // Reserved until timestamp (for 24-hour hold)
            $table->timestamp('reserved_until')->nullable()->after('admin_approval_notes')->comment('24-hour hold expires at this time');
            
            // Rejection tracking
            $table->string('rejection_category')->nullable()->after('rejected_reason')->comment('document_invalid, policy_violation, etc.');
            $table->unsignedBigInteger('rejected_by')->nullable()->after('rejection_category')->comment('ID of staff or admin who rejected');
            $table->timestamp('rejected_at')->nullable()->after('rejected_by');
            
            // Add foreign keys
            $table->foreign('admin_approved_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('rejected_by')->references('id')->on('users')->onDelete('set null');
            
            // Add index for status
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('facilities_db')->table('bookings', function (Blueprint $table) {
            $table->dropForeign(['admin_approved_by']);
            $table->dropForeign(['rejected_by']);
            $table->dropIndex(['status']);
            $table->dropColumn([
                'admin_approved_by',
                'admin_approved_at',
                'admin_approval_notes',
                'reserved_until',
                'rejection_category',
                'rejected_by',
                'rejected_at'
            ]);
        });
        
        // Revert status column to original enum
        DB::connection('facilities_db')->statement("ALTER TABLE bookings MODIFY COLUMN status VARCHAR(50) DEFAULT 'pending'");
    }
};

