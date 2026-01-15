<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PhilippineRegionsSeeder extends Seeder
{
    /**
     * Seed the Philippine regions table.
     * Source: Philippine Statistics Authority (PSA)
     * Total: 17 Regions
     */
    public function run(): void
    {
        $authDb = DB::connection('auth_db');

        $regions = [
            ['id' => 1, 'code' => 'NCR', 'name' => 'National Capital Region', 'long_name' => 'National Capital Region (NCR)', 'psgc_code' => '130000000'],
            ['id' => 2, 'code' => 'CAR', 'name' => 'Cordillera Administrative Region', 'long_name' => 'Cordillera Administrative Region (CAR)', 'psgc_code' => '140000000'],
            ['id' => 3, 'code' => 'I', 'name' => 'Region I', 'long_name' => 'Ilocos Region (Region I)', 'psgc_code' => '010000000'],
            ['id' => 4, 'code' => 'II', 'name' => 'Region II', 'long_name' => 'Cagayan Valley (Region II)', 'psgc_code' => '020000000'],
            ['id' => 5, 'code' => 'III', 'name' => 'Region III', 'long_name' => 'Central Luzon (Region III)', 'psgc_code' => '030000000'],
            ['id' => 6, 'code' => 'IV-A', 'name' => 'Region IV-A', 'long_name' => 'CALABARZON (Region IV-A)', 'psgc_code' => '040000000'],
            ['id' => 7, 'code' => 'IV-B', 'name' => 'Region IV-B', 'long_name' => 'MIMAROPA (Region IV-B)', 'psgc_code' => '170000000'],
            ['id' => 8, 'code' => 'V', 'name' => 'Region V', 'long_name' => 'Bicol Region (Region V)', 'psgc_code' => '050000000'],
            ['id' => 9, 'code' => 'VI', 'name' => 'Region VI', 'long_name' => 'Western Visayas (Region VI)', 'psgc_code' => '060000000'],
            ['id' => 10, 'code' => 'VII', 'name' => 'Region VII', 'long_name' => 'Central Visayas (Region VII)', 'psgc_code' => '070000000'],
            ['id' => 11, 'code' => 'VIII', 'name' => 'Region VIII', 'long_name' => 'Eastern Visayas (Region VIII)', 'psgc_code' => '080000000'],
            ['id' => 12, 'code' => 'IX', 'name' => 'Region IX', 'long_name' => 'Zamboanga Peninsula (Region IX)', 'psgc_code' => '090000000'],
            ['id' => 13, 'code' => 'X', 'name' => 'Region X', 'long_name' => 'Northern Mindanao (Region X)', 'psgc_code' => '100000000'],
            ['id' => 14, 'code' => 'XI', 'name' => 'Region XI', 'long_name' => 'Davao Region (Region XI)', 'psgc_code' => '110000000'],
            ['id' => 15, 'code' => 'XII', 'name' => 'Region XII', 'long_name' => 'SOCCSKSARGEN (Region XII)', 'psgc_code' => '120000000'],
            ['id' => 16, 'code' => 'XIII', 'name' => 'Region XIII', 'long_name' => 'Caraga (Region XIII)', 'psgc_code' => '160000000'],
            ['id' => 17, 'code' => 'BARMM', 'name' => 'BARMM', 'long_name' => 'Bangsamoro Autonomous Region in Muslim Mindanao (BARMM)', 'psgc_code' => '150000000'],
        ];

        foreach ($regions as $region) {
            $authDb->table('regions')->insertOrIgnore($region);
        }

        $this->command->info('✓ 17 Philippine Regions seeded successfully!');
    }
}
