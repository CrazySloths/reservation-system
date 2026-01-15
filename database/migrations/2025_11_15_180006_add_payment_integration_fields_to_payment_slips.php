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
            // Payment gateway integration fields
            $table->string('payment_gateway')->nullable()->after('payment_method')->comment('gcash, paymaya, bank, cash');
            $table->string('gateway_transaction_id')->nullable()->after('payment_gateway')->comment('Transaction ID from payment gateway');
            $table->string('gateway_reference_number')->nullable()->after('gateway_transaction_id');
            $table->string('payment_receipt_url')->nullable()->after('gateway_reference_number')->comment('URL to payment receipt');
            $table->json('gateway_webhook_payload')->nullable()->after('payment_receipt_url')->comment('Full webhook data from gateway');
            
            // Treasurer's Office integration fields
            $table->string('treasurer_reference')->nullable()->after('gateway_webhook_payload')->comment('Reference number from Treasurer system');
            $table->string('or_number')->nullable()->after('treasurer_reference')->comment('Official Receipt number');
            $table->string('treasurer_status')->nullable()->after('or_number')->comment('confirmed, pending, rejected from Treasurer');
            $table->timestamp('sent_to_treasurer_at')->nullable()->after('treasurer_status');
            $table->timestamp('confirmed_by_treasurer_at')->nullable()->after('sent_to_treasurer_at');
            $table->string('treasurer_cashier_name')->nullable()->after('confirmed_by_treasurer_at');
            $table->string('treasurer_cashier_id')->nullable()->after('treasurer_cashier_name');
            
            // Add indexes
            $table->index('gateway_transaction_id');
            $table->index('or_number');
            $table->index('treasurer_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('facilities_db')->table('payment_slips', function (Blueprint $table) {
            $table->dropIndex(['gateway_transaction_id']);
            $table->dropIndex(['or_number']);
            $table->dropIndex(['treasurer_status']);
            $table->dropColumn([
                'payment_gateway',
                'gateway_transaction_id',
                'gateway_reference_number',
                'payment_receipt_url',
                'gateway_webhook_payload',
                'treasurer_reference',
                'or_number',
                'treasurer_status',
                'sent_to_treasurer_at',
                'confirmed_by_treasurer_at',
                'treasurer_cashier_name',
                'treasurer_cashier_id'
            ]);
        });
    }
};

