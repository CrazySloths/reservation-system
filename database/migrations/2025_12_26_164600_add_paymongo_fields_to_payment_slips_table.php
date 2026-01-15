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
        Schema::connection('facilities_db')->table('payment_slips', function (Blueprint $table) {
            $table->string('payment_intent_id')->nullable()->after('payment_method');
            $table->string('payment_source_id')->nullable()->after('payment_intent_id');
            $table->string('payment_source_type')->nullable()->after('payment_source_id'); // gcash, grab_pay, card, etc.
            $table->text('paymongo_response')->nullable()->after('payment_source_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('facilities_db')->table('payment_slips', function (Blueprint $table) {
            $table->dropColumn(['payment_intent_id', 'payment_source_id', 'payment_source_type', 'paymongo_response']);
        });
    }
};
