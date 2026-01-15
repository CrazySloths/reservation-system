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
        Schema::connection('facilities_db')->table('payment_slips', function (Blueprint $table) {
            // Test mode flag
            $table->boolean('is_test_transaction')->default(false)->after('transaction_reference');
            
            // Detailed payment channel info
            $table->string('payment_channel')->nullable()->after('is_test_transaction'); // gcash, maya, bpi, bdo, etc.
            $table->string('account_name')->nullable()->after('payment_channel'); // Account name from payment
            $table->string('account_number')->nullable()->after('account_name'); // Last 4 digits
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('facilities_db')->table('payment_slips', function (Blueprint $table) {
            $table->dropColumn(['is_test_transaction', 'payment_channel', 'account_name', 'account_number']);
        });
    }
};
