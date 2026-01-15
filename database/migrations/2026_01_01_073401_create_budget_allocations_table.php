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
        Schema::connection('facilities_db')->create('budget_allocations', function (Blueprint $table) {
            $table->id();
            $table->integer('fiscal_year');
            $table->enum('category', ['maintenance', 'equipment', 'operations', 'staff', 'utilities', 'other']);
            $table->string('category_name')->nullable(); // Custom category name
            $table->decimal('allocated_amount', 12, 2)->default(0);
            $table->decimal('spent_amount', 12, 2)->default(0);
            $table->decimal('remaining_amount', 12, 2)->default(0);
            $table->text('notes')->nullable();
            $table->string('approved_by')->nullable(); // Admin who approved this budget
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index('fiscal_year');
            $table->index('category');
            $table->unique(['fiscal_year', 'category']);
        });
        
        // Budget expenditures tracking table
        Schema::connection('facilities_db')->create('budget_expenditures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('budget_allocation_id')->constrained('budget_allocations')->onDelete('cascade');
            $table->enum('expenditure_type', ['maintenance', 'equipment_purchase', 'operational_cost', 'staff_salary', 'utility_bill', 'other']);
            $table->string('description');
            $table->decimal('amount', 12, 2);
            $table->date('expenditure_date');
            $table->string('invoice_number')->nullable();
            $table->string('vendor_name')->nullable();
            $table->foreignId('facility_id')->nullable(); // If expense is related to specific facility
            $table->text('notes')->nullable();
            $table->string('recorded_by')->nullable(); // Admin who recorded this
            $table->timestamps();
            
            // Indexes
            $table->index('budget_allocation_id');
            $table->index('expenditure_date');
            $table->index('facility_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('facilities_db')->dropIfExists('budget_expenditures');
        Schema::connection('facilities_db')->dropIfExists('budget_allocations');
    }
};
