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
            // Pricing fields
            $table->decimal('base_rate', 10, 2)->after('end_time')->comment('Base facility rate (3 hours)');
            $table->decimal('extension_rate', 10, 2)->nullable()->after('base_rate')->comment('Extension charges');
            $table->decimal('equipment_total', 10, 2)->default(0)->after('extension_rate')->comment('Total equipment costs');
            $table->decimal('subtotal', 10, 2)->after('equipment_total')->comment('Before discounts');
            
            // Discount fields
            $table->string('city_of_residence')->nullable()->after('subtotal')->comment('User city for resident discount');
            $table->boolean('is_resident')->default(false)->after('city_of_residence')->comment('Eligible for 30% resident discount');
            $table->decimal('resident_discount_rate', 5, 2)->default(0)->after('is_resident')->comment('30% for residents');
            $table->decimal('resident_discount_amount', 10, 2)->default(0)->after('resident_discount_rate');
            
            // Special discount fields (Senior/PWD/Student)
            $table->string('special_discount_type')->nullable()->after('resident_discount_amount')->comment('senior, pwd, student');
            $table->string('special_discount_id_path')->nullable()->after('special_discount_type')->comment('Uploaded ID for verification');
            $table->decimal('special_discount_rate', 5, 2)->default(0)->after('special_discount_id_path')->comment('Additional 20%');
            $table->decimal('special_discount_amount', 10, 2)->default(0)->after('special_discount_rate');
            
            // Final pricing
            $table->decimal('total_discount', 10, 2)->default(0)->after('special_discount_amount');
            $table->decimal('total_amount', 10, 2)->after('total_discount')->comment('Final amount to pay');
            
            // Booking details
            $table->text('purpose')->nullable()->after('total_amount')->comment('Event purpose/description');
            $table->integer('expected_attendees')->nullable()->after('purpose');
            $table->text('special_requests')->nullable()->after('expected_attendees');
            
            // Document uploads
            $table->string('valid_id_path')->nullable()->after('special_requests')->comment('Government-issued ID');
            $table->string('supporting_doc_path')->nullable()->after('valid_id_path')->comment('Additional documents');
            
            // Rejection tracking
            $table->text('rejected_reason')->nullable()->after('supporting_doc_path');
            
            // Add indexes
            $table->index('is_resident');
            $table->index('special_discount_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('facilities_db')->table('bookings', function (Blueprint $table) {
            $table->dropIndex(['is_resident']);
            $table->dropIndex(['special_discount_type']);
            $table->dropColumn([
                'base_rate',
                'extension_rate',
                'equipment_total',
                'subtotal',
                'city_of_residence',
                'is_resident',
                'resident_discount_rate',
                'resident_discount_amount',
                'special_discount_type',
                'special_discount_id_path',
                'special_discount_rate',
                'special_discount_amount',
                'total_discount',
                'total_amount',
                'purpose',
                'expected_attendees',
                'special_requests',
                'valid_id_path',
                'supporting_doc_path',
                'rejected_reason',
            ]);
        });
    }
};
