<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class TestUsersSeeder extends Seeder
{
    public function run(): void
    {
        $connection = 'auth_db';

        // Super Admin
        DB::connection($connection)->table('users')->insert([
            'username' => 'superadmin',
            'email' => 'jhonrey.manejo18@gmail.com',
            'full_name' => 'Jhon Rey Manejo',
            'password_hash' => Hash::make('jhonrey.manejo18@gmail.com'),
            'role_id' => 1, // super admin
            'subsystem_id' => null,
            'subsystem_role_id' => null,
            'status' => 'active',
            'is_email_verified' => 1,
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Admin (Public Facilities Admin)
        DB::connection($connection)->table('users')->insert([
            'username' => 'admin',
            'email' => 'llanetacristianpastoril@gmail.com',
            'full_name' => 'Llaneta Cristian Pastoril',
            'password_hash' => Hash::make('llanetacristianpastoril@gmail.com'),
            'role_id' => null, // No global role
            'subsystem_id' => 4, // Public Facilities
            'subsystem_role_id' => 1, // Admin role
            'status' => 'active',
            'is_email_verified' => 1,
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Staff (Reservations Staff)
        DB::connection($connection)->table('users')->insert([
            'username' => 'staff',
            'email' => 'lcristianmarkangelo@gmail.com',
            'full_name' => 'Cristian Mark Angelo',
            'password_hash' => Hash::make('lcristianmarkangelo@gmail.com'),
            'role_id' => null, // No global role
            'subsystem_id' => 4, // Public Facilities
            'subsystem_role_id' => 3, // Reservations Staff
            'status' => 'active',
            'is_email_verified' => 1,
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Treasurer (City Treasurer's Office)
        DB::connection($connection)->table('users')->insert([
            'username' => 'treasurer',
            'email' => 'llanetacrisanto4@gmail.com',
            'full_name' => 'Llaneta Crisanto',
            'password_hash' => Hash::make('llanetacrisanto4@gmail.com'),
            'role_id' => null, // No global role
            'subsystem_id' => 4, // Public Facilities
            'subsystem_role_id' => 5, // Treasurer
            'status' => 'active',
            'is_email_verified' => 1,
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // CBD Staff (City Budget Department)
        DB::connection($connection)->table('users')->insert([
            'username' => 'cbd',
            'email' => 'llanetacristianpastoril096@gmail.com',
            'full_name' => 'City Budget Department',
            'password_hash' => Hash::make('llanetacristianpastoril096@gmail.com'),
            'role_id' => null, // No global role
            'subsystem_id' => 4, // Public Facilities
            'subsystem_role_id' => 6, // CBD Staff
            'status' => 'active',
            'is_email_verified' => 1,
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        echo "SUCCESS: Test users created successfully!\n";
        echo "   Super Admin: jhonrey.manejo18@gmail.com\n";
        echo "   Admin: llanetacristianpastoril@gmail.com\n";
        echo "   Staff: lcristianmarkangelo@gmail.com\n";
        echo "   Treasurer: llanetacrisanto4@gmail.com\n";
        echo "   CBD: llanetacristianpastoril096@gmail.com\n";
        echo "   Password for all: (same as email)\n";
    }
}
