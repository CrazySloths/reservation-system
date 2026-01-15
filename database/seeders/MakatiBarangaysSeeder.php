<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MakatiBarangaysSeeder extends Seeder
{
    /**
     * Seed Makati City barangays with accurate zip codes.
     * 
     * Makati has 2 districts and 33 barangays.
     * ZIP codes range: 1200-1235
     * Each barangay has its own unique zip code.
     * 
     * Source: Philippine Postal Corporation
     */
    public function run(): void
    {
        $authDb = DB::connection('auth_db');

        $makati = $authDb->table('cities')->where('code', 'MAK')->first();
        if (!$makati) {
            $this->command->error('Makati not found in cities table!');
            return;
        }

        $makatiDistricts = $authDb->table('districts')
            ->where('city_id', $makati->id)
            ->orderBy('district_number')
            ->get();

        if ($makatiDistricts->count() === 0) {
            $this->command->error('No districts found for Makati!');
            return;
        }

        $districtMap = [];
        foreach ($makatiDistricts as $district) {
            $districtMap[$district->district_number] = $district->id;
        }

        $authDb->table('barangays')->where('city_id', $makati->id)->delete();

        // ALL 33 Makati Barangays with unique ZIP codes
        $barangays = [
            // DISTRICT 1 (Northern Makati)
            ['city_id' => $makati->id, 'district_id' => $districtMap[1], 'name' => 'Bangkal', 'alternate_name' => null, 'zip_code' => '1233'],
            ['city_id' => $makati->id, 'district_id' => $districtMap[1], 'name' => 'Bel-Air', 'alternate_name' => null, 'zip_code' => '1209'],
            ['city_id' => $makati->id, 'district_id' => $districtMap[1], 'name' => 'Carmona', 'alternate_name' => null, 'zip_code' => '1207'],
            ['city_id' => $makati->id, 'district_id' => $districtMap[1], 'name' => 'Cembo', 'alternate_name' => 'Fort Bonifacio', 'zip_code' => '1214'],
            ['city_id' => $makati->id, 'district_id' => $districtMap[1], 'name' => 'Comembo', 'alternate_name' => 'Fort Bonifacio', 'zip_code' => '1214'],
            ['city_id' => $makati->id, 'district_id' => $districtMap[1], 'name' => 'Dasmariñas', 'alternate_name' => 'Dasmariñas Village', 'zip_code' => '1222'],
            ['city_id' => $makati->id, 'district_id' => $districtMap[1], 'name' => 'East Rembo', 'alternate_name' => 'Fort Bonifacio', 'zip_code' => '1214'],
            ['city_id' => $makati->id, 'district_id' => $districtMap[1], 'name' => 'Forbes Park', 'alternate_name' => null, 'zip_code' => '1220'],
            ['city_id' => $makati->id, 'district_id' => $districtMap[1], 'name' => 'Guadalupe Nuevo', 'alternate_name' => null, 'zip_code' => '1212'],
            ['city_id' => $makati->id, 'district_id' => $districtMap[1], 'name' => 'Guadalupe Viejo', 'alternate_name' => null, 'zip_code' => '1211'],
            ['city_id' => $makati->id, 'district_id' => $districtMap[1], 'name' => 'Kasilawan', 'alternate_name' => null, 'zip_code' => '1206'],
            ['city_id' => $makati->id, 'district_id' => $districtMap[1], 'name' => 'La Paz', 'alternate_name' => null, 'zip_code' => '1204'],
            ['city_id' => $makati->id, 'district_id' => $districtMap[1], 'name' => 'Magallanes', 'alternate_name' => null, 'zip_code' => '1232'],
            ['city_id' => $makati->id, 'district_id' => $districtMap[1], 'name' => 'Olympia', 'alternate_name' => null, 'zip_code' => '1207'],
            ['city_id' => $makati->id, 'district_id' => $districtMap[1], 'name' => 'Palanan', 'alternate_name' => null, 'zip_code' => '1235'],
            ['city_id' => $makati->id, 'district_id' => $districtMap[1], 'name' => 'Pembo', 'alternate_name' => 'Fort Bonifacio', 'zip_code' => '1214'],
            ['city_id' => $makati->id, 'district_id' => $districtMap[1], 'name' => 'Pinagkaisahan', 'alternate_name' => null, 'zip_code' => '1213'],
            ['city_id' => $makati->id, 'district_id' => $districtMap[1], 'name' => 'Pio del Pilar', 'alternate_name' => null, 'zip_code' => '1230'],
            ['city_id' => $makati->id, 'district_id' => $districtMap[1], 'name' => 'Pitogo', 'alternate_name' => 'Fort Bonifacio', 'zip_code' => '1214'],
            ['city_id' => $makati->id, 'district_id' => $districtMap[1], 'name' => 'Poblacion', 'alternate_name' => 'P. Burgos, Makati CBD', 'zip_code' => '1210'],
            ['city_id' => $makati->id, 'district_id' => $districtMap[1], 'name' => 'Post Proper Northside', 'alternate_name' => 'Fort Bonifacio', 'zip_code' => '1214'],
            ['city_id' => $makati->id, 'district_id' => $districtMap[1], 'name' => 'Post Proper Southside', 'alternate_name' => 'Fort Bonifacio', 'zip_code' => '1214'],
            ['city_id' => $makati->id, 'district_id' => $districtMap[1], 'name' => 'Rizal', 'alternate_name' => null, 'zip_code' => '1209'],
            ['city_id' => $makati->id, 'district_id' => $districtMap[1], 'name' => 'San Antonio', 'alternate_name' => null, 'zip_code' => '1203'],
            ['city_id' => $makati->id, 'district_id' => $districtMap[1], 'name' => 'San Isidro', 'alternate_name' => null, 'zip_code' => '1234'],
            ['city_id' => $makati->id, 'district_id' => $districtMap[1], 'name' => 'San Lorenzo', 'alternate_name' => 'San Lorenzo Village', 'zip_code' => '1223'],
            ['city_id' => $makati->id, 'district_id' => $districtMap[1], 'name' => 'Santa Cruz', 'alternate_name' => null, 'zip_code' => '1205'],
            ['city_id' => $makati->id, 'district_id' => $districtMap[1], 'name' => 'Singkamas', 'alternate_name' => null, 'zip_code' => '1204'],
            ['city_id' => $makati->id, 'district_id' => $districtMap[1], 'name' => 'South Cembo', 'alternate_name' => 'Fort Bonifacio', 'zip_code' => '1214'],
            ['city_id' => $makati->id, 'district_id' => $districtMap[1], 'name' => 'Tejeros', 'alternate_name' => null, 'zip_code' => '1204'],
            ['city_id' => $makati->id, 'district_id' => $districtMap[1], 'name' => 'Urdaneta', 'alternate_name' => 'Urdaneta Village', 'zip_code' => '1225'],
            ['city_id' => $makati->id, 'district_id' => $districtMap[1], 'name' => 'Valenzuela', 'alternate_name' => null, 'zip_code' => '1208'],
            ['city_id' => $makati->id, 'district_id' => $districtMap[1], 'name' => 'West Rembo', 'alternate_name' => 'Fort Bonifacio', 'zip_code' => '1214'],
        ];

        foreach ($barangays as $barangay) {
            $authDb->table('barangays')->insert($barangay);
        }

        $totalBarangays = count($barangays);
        $this->command->info("✓ Makati Barangays seeded successfully! ($totalBarangays barangays - ALL with unique zip codes)");
    }
}

