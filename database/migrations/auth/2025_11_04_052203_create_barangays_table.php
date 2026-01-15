<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'auth_db';

    public function up(): void
    {
        Schema::connection($this->connection)->create('barangays', function (Blueprint $table) {
            $table->id();
            $table->foreignId('district_id')->constrained('districts')->onDelete('cascade');
            $table->string('name', 100);
            $table->string('alternate_name', 200)->nullable();
            $table->index('district_id', 'idx_district');
        });
    }

    public function down(): void
    {
        Schema::connection($this->connection)->dropIfExists('barangays');
    }
};
