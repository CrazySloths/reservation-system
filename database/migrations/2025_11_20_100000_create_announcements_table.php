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
        Schema::connection('facilities_db')->create('announcements', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('content');
            $table->enum('type', ['general', 'maintenance', 'event', 'urgent', 'facility_update'])->default('general');
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->enum('target_audience', ['all', 'citizens', 'admins'])->default('all');
            $table->boolean('is_active')->default(true);
            $table->boolean('is_pinned')->default(false);
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->string('attachment_path')->nullable();
            $table->text('additional_info')->nullable();
            $table->timestamps();
            
            // Indexes for better performance
            $table->index(['is_active', 'start_date', 'end_date']);
            $table->index(['type', 'priority']);
            $table->index(['target_audience', 'is_pinned']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('facilities_db')->dropIfExists('announcements');
    }
};

