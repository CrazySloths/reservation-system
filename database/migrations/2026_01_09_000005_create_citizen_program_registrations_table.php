<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::connection('facilities_db')->create('citizen_program_registrations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('government_program_booking_id');
            $table->unsignedBigInteger('citizen_id'); // Our user_id
            
            $table->enum('registration_status', ['registered', 'attended', 'cancelled', 'no_show'])->default('registered');
            $table->string('qr_code', 255)->nullable();
            
            $table->timestamp('registered_at')->useCurrent();
            $table->timestamp('attended_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            
            $table->foreign('government_program_booking_id', 'fk_registration_program')
                ->references('id')
                ->on('government_program_bookings')
                ->onDelete('cascade');
            
            // NOTE: No foreign key to users (different database)
            
            $table->unique(['government_program_booking_id', 'citizen_id'], 'unique_citizen_registration');
            $table->index('registration_status');
        });
    }

    public function down()
    {
        Schema::connection('facilities_db')->dropIfExists('citizen_program_registrations');
    }
};

