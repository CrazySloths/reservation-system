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
        // Clear previous incorrect migration and redo it properly
        $users = DB::connection('auth_db')->table('users')->get();
        
        foreach ($users as $user) {
            $updates = [];
            
            // For full_name, only split into first name and last name
            // Everything except the last word is first name, last word is last name
            // Middle name should be left blank since it wasn't collected during registration
            if (!empty($user->full_name)) {
                $nameParts = explode(' ', trim($user->full_name));
                
                if (count($nameParts) >= 2) {
                    // Last word is last name
                    $updates['last_name'] = array_pop($nameParts);
                    // Everything else is first name
                    $updates['first_name'] = implode(' ', $nameParts);
                    // Clear middle name since it wasn't provided during registration
                    $updates['middle_name'] = null;
                } elseif (count($nameParts) == 1) {
                    // Only one word, use it as first name
                    $updates['first_name'] = $nameParts[0];
                    $updates['last_name'] = null;
                    $updates['middle_name'] = null;
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
        // Rollback changes
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

