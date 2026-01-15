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
        Schema::connection('facilities_db')->create('payment_slips', function (Blueprint $table) {
            $table->id();
            $table->string('slip_number', 50)->unique(); // PS-2025-001234
            $table->unsignedBigInteger('booking_id')->unique();
            $table->decimal('amount_due', 10, 2);
            $table->timestamp('payment_deadline'); // 48 hours from generation
            $table->enum('status', ['unpaid', 'paid', 'expired'])->default('unpaid');
            
            // Payment details (filled when paid)
            $table->enum('payment_method', ['cash', 'gcash', 'paymaya', 'bank_transfer', 'credit_card'])->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->unsignedBigInteger('verified_by')->nullable(); // Treasurer user ID
            $table->string('transaction_reference', 255)->nullable(); // For cashless payments
            
            // Additional info
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('booking_id')->references('id')->on('bookings')->onDelete('cascade');
            
            // Indexes
            $table->index('status');
            $table->index('payment_deadline');
            $table->index('paid_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('facilities_db')->dropIfExists('payment_slips');
    }
};
