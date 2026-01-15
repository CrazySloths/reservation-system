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
        Schema::connection('facilities_db')->table('booking_conflicts', function (Blueprint $table) {
            $table->enum('refund_method', ['cash', 'gcash', 'paymaya', 'bank_transfer'])->nullable()->after('admin_notes');
            $table->string('refund_account_name')->nullable()->after('refund_method');
            $table->string('refund_account_number')->nullable()->after('refund_account_name');
            $table->string('refund_bank_name')->nullable()->after('refund_account_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('facilities_db')->table('booking_conflicts', function (Blueprint $table) {
            $table->dropColumn(['refund_method', 'refund_account_name', 'refund_account_number', 'refund_bank_name']);
        });
    }
};
