<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'auth_db';

    public function up(): void
    {
        // Role Permissions pivot
        Schema::connection($this->connection)->create('role_permissions', function (Blueprint $table) {
            $table->foreignId('role_id')->constrained('roles')->onDelete('cascade');
            $table->foreignId('permission_id')->constrained('permissions')->onDelete('cascade');
            $table->primary(['role_id', 'permission_id']);
        });

        // User Permissions pivot (overrides)
        Schema::connection($this->connection)->create('user_permissions', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('permission_id')->constrained('permissions')->onDelete('cascade');
            $table->primary(['user_id', 'permission_id']);
        });

        // Subsystem Role Permissions pivot
        Schema::connection($this->connection)->create('subsystem_role_permissions', function (Blueprint $table) {
            $table->foreignId('subsystem_role_id')->constrained('subsystem_roles')->onDelete('cascade');
            $table->foreignId('permission_id')->constrained('permissions')->onDelete('cascade');
            $table->primary(['subsystem_role_id', 'permission_id'], 'sr_perm_primary');
        });
    }

    public function down(): void
    {
        Schema::connection($this->connection)->dropIfExists('subsystem_role_permissions');
        Schema::connection($this->connection)->dropIfExists('user_permissions');
        Schema::connection($this->connection)->dropIfExists('role_permissions');
    }
};
