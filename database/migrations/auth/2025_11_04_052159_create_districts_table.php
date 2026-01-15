<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'auth_db';

    public function up(): void
    {
        Schema::connection($this->connection)->create('districts', function (Blueprint $table) {
            $table->id();
            $table->integer('district_number')->unique();
            $table->string('name', 50);
        });
    }

    public function down(): void
    {
        Schema::connection($this->connection)->dropIfExists('districts');
    }
};
