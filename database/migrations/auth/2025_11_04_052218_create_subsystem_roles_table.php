<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'auth_db';

    public function up(): void
    {
        Schema::connection($this->connection)->create('subsystem_roles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subsystem_id')->constrained('subsystems')->onDelete('cascade');
            $table->string('role_name', 100);
            $table->text('description')->nullable();
            $table->unique(['subsystem_id', 'role_name'], 'unique_subsystem_role');
        });
    }

    public function down(): void
    {
        Schema::connection($this->connection)->dropIfExists('subsystem_roles');
    }
};
