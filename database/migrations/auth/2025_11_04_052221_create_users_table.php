<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'auth_db';

    public function up(): void
    {
        Schema::connection($this->connection)->create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username', 50)->unique();
            $table->string('email', 100)->unique();
            $table->string('full_name', 100);
            $table->string('password_hash', 255);
            
            // Role and subsystem relationships
            $table->foreignId('role_id')->nullable()->constrained('roles')->onDelete('set null');
            $table->foreignId('subsystem_id')->nullable()->constrained('subsystems')->onDelete('set null');
            $table->foreignId('subsystem_role_id')->nullable()->constrained('subsystem_roles')->onDelete('set null');
            
            // Personal information
            $table->date('birthdate')->nullable();
            $table->string('mobile_number', 15)->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->enum('civil_status', ['single', 'married', 'divorced', 'widowed', 'separated'])->nullable();
            $table->string('nationality', 50)->default('Filipino');
            
            // Address information
            $table->foreignId('district_id')->nullable()->constrained('districts')->onDelete('set null');
            $table->foreignId('barangay_id')->nullable()->constrained('barangays')->onDelete('set null');
            $table->text('current_address')->nullable();
            $table->string('zip_code', 10)->nullable();
            
            // ID Verification
            $table->string('valid_id_type', 50)->nullable();
            $table->string('valid_id_front_image', 255)->nullable();
            $table->string('valid_id_back_image', 255)->nullable();
            $table->string('selfie_with_id_image', 255)->nullable();
            $table->enum('id_verification_status', ['pending', 'verified', 'rejected'])->default('pending');
            $table->timestamp('id_verified_at')->nullable();
            $table->foreignId('id_verified_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('id_verification_notes')->nullable();
            
            // Account status
            $table->enum('status', ['active', 'inactive', 'banned'])->default('active');
            $table->timestamp('last_login')->nullable();
            
            // Email verification
            $table->boolean('is_email_verified')->default(false);
            $table->string('email_verification_token', 255)->nullable();
            $table->timestamp('email_verified_at')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index('username', 'idx_username');
            $table->index('email', 'idx_email');
            $table->index('status', 'idx_status');
            $table->index('district_id', 'idx_district');
            $table->index('barangay_id', 'idx_barangay');
            $table->index('id_verification_status', 'idx_id_verification');
        });
    }

    public function down(): void
    {
        Schema::connection($this->connection)->dropIfExists('users');
    }
};
