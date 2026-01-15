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
            $table->string('slip_number')->unique(); // PS-2025-0001
            $table->foreignId('booking_id')->constrained('bookings')->onDelete('cascade');
            // User IDs without FK constraints (users table is in different database)
            $table->unsignedBigInteger('user_id'); // citizen
            $table->unsignedBigInteger('generated_by'); // admin who approved
            $table->decimal('amount', 10, 2);
            $table->enum('status', ['unpaid', 'paid', 'expired'])->default('unpaid');
            $table->datetime('due_date'); // payment deadline
            $table->datetime('paid_at')->nullable();
            $table->string('payment_method')->nullable(); // cash, check, online
            $table->text('cashier_notes')->nullable();
            $table->unsignedBigInteger('paid_by_cashier')->nullable(); // admin/cashier who processed payment
            $table->timestamps();
            
            // Add indexes for foreign key columns
            $table->index('user_id');
            $table->index('generated_by');
            $table->index('paid_by_cashier');
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
