<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Get all users and migrate their data
        $users = DB::connection('auth_db')->table('users')->get();
        
        foreach ($users as $user) {
            $updates = [];
            
            // Parse full_name into first_name, middle_name, last_name
            if (!empty($user->full_name)) {
                $nameParts = explode(' ', trim($user->full_name));
                
                if (count($nameParts) >= 1) {
                    $updates['first_name'] = $nameParts[0];
                }
                
                if (count($nameParts) >= 2) {
                    $updates['last_name'] = $nameParts[count($nameParts) - 1];
                }
                
                if (count($nameParts) > 2) {
                    // Everything between first and last name is middle name
                    $middleNames = array_slice($nameParts, 1, count($nameParts) - 2);
                    $updates['middle_name'] = implode(' ', $middleNames);
                }
            }
            
            // Copy mobile_number to phone_number
            if (!empty($user->mobile_number)) {
                $updates['phone_number'] = $user->mobile_number;
            }
            
            // Copy current_address to address
            if (!empty($user->current_address)) {
                $updates['address'] = $user->current_address;
            }
            
            // Update the user if there are any changes
            if (!empty($updates)) {
                DB::connection('auth_db')
                    ->table('users')
                    ->where('id', $user->id)
                    ->update($updates);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Clear the migrated data
        DB::connection('auth_db')
            ->table('users')
            ->update([
                'first_name' => null,
                'last_name' => null,
                'middle_name' => null,
                'phone_number' => null,
                'address' => null,
            ]);
    }
};

