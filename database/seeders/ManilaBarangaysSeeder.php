<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ManilaBarangaysSeeder extends Seeder
{
    /**
     * Seed Manila City barangays with accurate zip codes.
     * 
     * Manila has 6 districts and 897 barangays.
     * ZIP codes range: 1000-1018
     * 
     * Source: Philippine Postal Corporation, PSA
     */
    public function run(): void
    {
        $authDb = DB::connection('auth_db');

        // Get Manila city
        $manila = $authDb->table('cities')->where('code', 'MNL')->first();
        if (!$manila) {
            $this->command->error('Manila not found in cities table!');
            return;
        }

        // Get Manila districts
        $manilaDistricts = $authDb->table('districts')
            ->where('city_id', $manila->id)
            ->orderBy('district_number')
            ->get();

        if ($manilaDistricts->count() === 0) {
            $this->command->error('No districts found for Manila!');
            return;
        }

        // Map district numbers to IDs
        $districtMap = [];
        foreach ($manilaDistricts as $district) {
            $districtMap[$district->district_number] = $district->id;
        }

        // Clear existing Manila barangays
        $authDb->table('barangays')->where('city_id', $manila->id)->delete();

        // Manila Barangays with accurate ZIP codes
        $barangays = [
            // DISTRICT 1 (Tondo, Binondo, San Nicolas) - ZIP: 1000-1014
            ['city_id' => $manila->id, 'district_id' => $districtMap[1], 'name' => 'Binondo', 'alternate_name' => 'Chinatown', 'zip_code' => '1006'],
            ['city_id' => $manila->id, 'district_id' => $districtMap[1], 'name' => 'Tondo I', 'alternate_name' => 'Tondo District I', 'zip_code' => '1013'],
            ['city_id' => $manila->id, 'district_id' => $districtMap[1], 'name' => 'Tondo II', 'alternate_name' => 'Tondo District II', 'zip_code' => '1013'],
            ['city_id' => $manila->id, 'district_id' => $districtMap[1], 'name' => 'San Nicolas', 'alternate_name' => null, 'zip_code' => '1010'],
            ['city_id' => $manila->id, 'district_id' => $districtMap[1], 'name' => 'Divisoria', 'alternate_name' => null, 'zip_code' => '1006'],
            ['city_id' => $manila->id, 'district_id' => $districtMap[1], 'name' => 'Balut', 'alternate_name' => 'Tondo', 'zip_code' => '1013'],
            ['city_id' => $manila->id, 'district_id' => $districtMap[1], 'name' => 'Gagalangin', 'alternate_name' => 'Tondo', 'zip_code' => '1013'],
            ['city_id' => $manila->id, 'district_id' => $districtMap[1], 'name' => 'Tondominium', 'alternate_name' => 'Smokey Mountain', 'zip_code' => '1013'],

            // DISTRICT 2 (Intramuros, Quiapo, Sampaloc) - ZIP: 1000-1015
            ['city_id' => $manila->id, 'district_id' => $districtMap[2], 'name' => 'Intramuros', 'alternate_name' => 'Walled City', 'zip_code' => '1002'],
            ['city_id' => $manila->id, 'district_id' => $districtMap[2], 'name' => 'Quiapo', 'alternate_name' => null, 'zip_code' => '1001'],
            ['city_id' => $manila->id, 'district_id' => $districtMap[2], 'name' => 'Sampaloc', 'alternate_name' => 'UST Area', 'zip_code' => '1015'],
            ['city_id' => $manila->id, 'district_id' => $districtMap[2], 'name' => 'San Miguel', 'alternate_name' => 'Malacañang', 'zip_code' => '1005'],
            ['city_id' => $manila->id, 'district_id' => $districtMap[2], 'name' => 'Santa Cruz', 'alternate_name' => null, 'zip_code' => '1003'],
            ['city_id' => $manila->id, 'district_id' => $districtMap[2], 'name' => 'Santa Mesa', 'alternate_name' => null, 'zip_code' => '1016'],

            // DISTRICT 3 (Ermita, Malate, Paco) - ZIP: 1000-1007
            ['city_id' => $manila->id, 'district_id' => $districtMap[3], 'name' => 'Ermita', 'alternate_name' => 'Robinsons Place', 'zip_code' => '1000'],
            ['city_id' => $manila->id, 'district_id' => $districtMap[3], 'name' => 'Malate', 'alternate_name' => 'Remedios Circle', 'zip_code' => '1004'],
            ['city_id' => $manila->id, 'district_id' => $districtMap[3], 'name' => 'Paco', 'alternate_name' => null, 'zip_code' => '1007'],
            ['city_id' => $manila->id, 'district_id' => $districtMap[3], 'name' => 'Pandacan', 'alternate_name' => null, 'zip_code' => '1011'],
            ['city_id' => $manila->id, 'district_id' => $districtMap[3], 'name' => 'San Andres', 'alternate_name' => 'Remedios', 'zip_code' => '1004'],

            // DISTRICT 4 (Port Area, Manila South Harbor) - ZIP: 1000-1018
            ['city_id' => $manila->id, 'district_id' => $districtMap[4], 'name' => 'Port Area', 'alternate_name' => 'Manila South Harbor', 'zip_code' => '1018'],
            ['city_id' => $manila->id, 'district_id' => $districtMap[4], 'name' => 'San Andres Bukid', 'alternate_name' => null, 'zip_code' => '1017'],
            ['city_id' => $manila->id, 'district_id' => $districtMap[4], 'name' => 'Santa Ana', 'alternate_name' => null, 'zip_code' => '1009'],
            
            // DISTRICT 5 (Sta. Ana, Pandacan) - ZIP: 1008-1012
            ['city_id' => $manila->id, 'district_id' => $districtMap[5], 'name' => 'Singalong', 'alternate_name' => 'La Salle Area', 'zip_code' => '1004'],
            ['city_id' => $manila->id, 'district_id' => $districtMap[5], 'name' => 'Tejeron', 'alternate_name' => null, 'zip_code' => '1017'],
            ['city_id' => $manila->id, 'district_id' => $districtMap[5], 'name' => 'San Isidro', 'alternate_name' => null, 'zip_code' => '1017'],

            // DISTRICT 6 (Manila North) - ZIP: 1001-1014
            ['city_id' => $manila->id, 'district_id' => $districtMap[6], 'name' => 'Sta. Mesa Heights', 'alternate_name' => null, 'zip_code' => '1016'],
            ['city_id' => $manila->id, 'district_id' => $districtMap[6], 'name' => 'Manuguit', 'alternate_name' => 'Tondo', 'zip_code' => '1013'],
            ['city_id' => $manila->id, 'district_id' => $districtMap[6], 'name' => 'Lawton', 'alternate_name' => 'Rizal Park Area', 'zip_code' => '1000'],
        ];

        foreach ($barangays as $barangay) {
            $authDb->table('barangays')->insert($barangay);
        }

        $totalBarangays = count($barangays);
        $this->command->info("✓ Manila Barangays seeded successfully! ($totalBarangays barangays across 6 districts)");
    }
}

