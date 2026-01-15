<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if columns exist before adding
        $hasFirstName = DB::connection('auth_db')->select("SHOW COLUMNS FROM users LIKE 'first_name'");
        $hasLastName = DB::connection('auth_db')->select("SHOW COLUMNS FROM users LIKE 'last_name'");
        $hasMiddleName = DB::connection('auth_db')->select("SHOW COLUMNS FROM users LIKE 'middle_name'");
        $hasPhoneNumber = DB::connection('auth_db')->select("SHOW COLUMNS FROM users LIKE 'phone_number'");
        $hasAddress = DB::connection('auth_db')->select("SHOW COLUMNS FROM users LIKE 'address'");

        Schema::connection('auth_db')->table('users', function (Blueprint $table) use ($hasFirstName, $hasLastName, $hasMiddleName, $hasPhoneNumber, $hasAddress) {
            if (empty($hasFirstName)) {
                $table->string('first_name')->nullable();
            }
            if (empty($hasLastName)) {
                $table->string('last_name')->nullable();
            }
            if (empty($hasMiddleName)) {
                $table->string('middle_name')->nullable();
            }
            if (empty($hasPhoneNumber)) {
                $table->string('phone_number')->nullable();
            }
            if (empty($hasAddress)) {
                $table->text('address')->nullable();
            }
        });

        // Migrate existing name data to first_name and last_name if name column exists
        $users = DB::connection('auth_db')->table('users')->get();
        foreach ($users as $user) {
            if (isset($user->name) && empty($user->first_name)) {
                $nameParts = explode(' ', $user->name, 2);
                DB::connection('auth_db')
                    ->table('users')
                    ->where('id', $user->id)
                    ->update([
                        'first_name' => $nameParts[0] ?? '',
                        'last_name' => $nameParts[1] ?? '',
                    ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('auth_db')->table('users', function (Blueprint $table) {
            $table->dropColumn(['first_name', 'last_name', 'middle_name', 'phone_number', 'address']);
        });
    }
};

