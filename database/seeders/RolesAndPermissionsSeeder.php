<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $connection = 'auth_db';

        // Insert Roles
        DB::connection($connection)->table('roles')->insert([
            ['name' => 'super admin'],
            ['name' => 'citizen'],
        ]);

        // Insert Subsystems
        DB::connection($connection)->table('subsystems')->insert([
            ['name' => 'Infrastructure Project Management'],
            ['name' => 'Utility Billing and Monitoring Management (Water, Electricity)'],
            ['name' => 'Road and Transportation Infrastructure Monitoring'],
            ['name' => 'Public Facilities Reservation System'], // ID: 4
            ['name' => 'Community Infrastructure Maintenance Management'],
            ['name' => 'Urban Planning and Development'],
            ['name' => 'Land Registration and Titling System'],
            ['name' => 'Housing and Resettlement Management'],
            ['name' => 'Renewable Energy Project Management'],
            ['name' => 'Energy Efficiency and Conservative Management'],
        ]);

        // Insert Subsystem Roles for Public Facilities (subsystem_id = 4)
        DB::connection($connection)->table('subsystem_roles')->insert([
            [
                'subsystem_id' => 4,
                'role_name' => 'Admin',
                'description' => 'Facilities system administrator'
            ],
            [
                'subsystem_id' => 4,
                'role_name' => 'Facility Manager',
                'description' => 'Manage facility operations'
            ],
            [
                'subsystem_id' => 4,
                'role_name' => 'Reservations Staff',
                'description' => 'Handle reservations'
            ],
            [
                'subsystem_id' => 4,
                'role_name' => 'Applicant',
                'description' => 'Facility reservation applicant'
            ],
            [
                'subsystem_id' => 4,
                'role_name' => 'Treasurer',
                'description' => 'City Treasurer\'s Office - Payment verification and receipt generation'
            ],
        ]);

        // Insert Basic Permissions
        DB::connection($connection)->table('permissions')->insert([
            ['name' => 'view_users', 'description' => 'View user list and details'],
            ['name' => 'edit_users', 'description' => 'Edit user information'],
            ['name' => 'delete_users', 'description' => 'Delete users'],
            ['name' => 'manage_roles', 'description' => 'Manage roles and assignments'],
            ['name' => 'view_audit_logs', 'description' => 'View audit logs'],
            ['name' => 'reset_passwords', 'description' => 'Reset user passwords'],
            ['name' => 'access_facilities', 'description' => 'Access facilities subsystem'],
        ]);

        // Assign all permissions to super admin (role_id = 1)
        $permissions = DB::connection($connection)->table('permissions')->pluck('id');
        foreach ($permissions as $permissionId) {
            DB::connection($connection)->table('role_permissions')->insert([
                'role_id' => 1,
                'permission_id' => $permissionId,
            ]);
        }

        echo "SUCCESS: Roles, subsystems, and permissions seeded successfully!\n";
    }
}
