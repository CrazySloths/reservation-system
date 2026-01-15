<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'auth_db';

    public function up(): void
    {
        Schema::connection($this->connection)->create('subsystems', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique();
        });
    }

    public function down(): void
    {
        Schema::connection($this->connection)->dropIfExists('subsystems');
    }
};
