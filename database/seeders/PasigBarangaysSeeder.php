<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PasigBarangaysSeeder extends Seeder
{
    /**
     * Seed Pasig City barangays with accurate zip codes.
     * 
     * Pasig has 2 districts and 30 barangays.
     * ZIP codes range: 1600-1613
     * 
     * Source: Philippine Postal Corporation
     */
    public function run(): void
    {
        $authDb = DB::connection('auth_db');

        $pasig = $authDb->table('cities')->where('code', 'PAC')->first();
        if (!$pasig) {
            $this->command->error('Pasig not found in cities table!');
            return;
        }

        $pasigDistricts = $authDb->table('districts')
            ->where('city_id', $pasig->id)
            ->orderBy('district_number')
            ->get();

        if ($pasigDistricts->count() === 0) {
            $this->command->error('No districts found for Pasig!');
            return;
        }

        $districtMap = [];
        foreach ($pasigDistricts as $district) {
            $districtMap[$district->district_number] = $district->id;
        }

        $authDb->table('barangays')->where('city_id', $pasig->id)->delete();

        // Pasig Barangays with ZIP codes
        $barangays = [
            // DISTRICT 1 (Western Pasig)
            ['city_id' => $pasig->id, 'district_id' => $districtMap[1], 'name' => 'Bagong Ilog', 'alternate_name' => null, 'zip_code' => '1600'],
            ['city_id' => $pasig->id, 'district_id' => $districtMap[1], 'name' => 'Bagong Katipunan', 'alternate_name' => null, 'zip_code' => '1600'],
            ['city_id' => $pasig->id, 'district_id' => $districtMap[1], 'name' => 'Bambang', 'alternate_name' => null, 'zip_code' => '1600'],
            ['city_id' => $pasig->id, 'district_id' => $districtMap[1], 'name' => 'Buting', 'alternate_name' => null, 'zip_code' => '1600'],
            ['city_id' => $pasig->id, 'district_id' => $districtMap[1], 'name' => 'Caniogan', 'alternate_name' => null, 'zip_code' => '1600'],
            ['city_id' => $pasig->id, 'district_id' => $districtMap[1], 'name' => 'Kalawaan', 'alternate_name' => null, 'zip_code' => '1600'],
            ['city_id' => $pasig->id, 'district_id' => $districtMap[1], 'name' => 'Kapasigan', 'alternate_name' => null, 'zip_code' => '1600'],
            ['city_id' => $pasig->id, 'district_id' => $districtMap[1], 'name' => 'Kapitolyo', 'alternate_name' => null, 'zip_code' => '1603'],
            ['city_id' => $pasig->id, 'district_id' => $districtMap[1], 'name' => 'Malinao', 'alternate_name' => null, 'zip_code' => '1600'],
            ['city_id' => $pasig->id, 'district_id' => $districtMap[1], 'name' => 'Oranbo', 'alternate_name' => null, 'zip_code' => '1600'],
            ['city_id' => $pasig->id, 'district_id' => $districtMap[1], 'name' => 'Palatiw', 'alternate_name' => null, 'zip_code' => '1600'],
            ['city_id' => $pasig->id, 'district_id' => $districtMap[1], 'name' => 'Pineda', 'alternate_name' => null, 'zip_code' => '1600'],
            ['city_id' => $pasig->id, 'district_id' => $districtMap[1], 'name' => 'Sagad', 'alternate_name' => null, 'zip_code' => '1600'],
            ['city_id' => $pasig->id, 'district_id' => $districtMap[1], 'name' => 'San Antonio', 'alternate_name' => null, 'zip_code' => '1600'],
            ['city_id' => $pasig->id, 'district_id' => $districtMap[1], 'name' => 'San Joaquin', 'alternate_name' => null, 'zip_code' => '1600'],
            ['city_id' => $pasig->id, 'district_id' => $districtMap[1], 'name' => 'San Jose', 'alternate_name' => null, 'zip_code' => '1600'],
            ['city_id' => $pasig->id, 'district_id' => $districtMap[1], 'name' => 'San Miguel', 'alternate_name' => null, 'zip_code' => '1600'],
            ['city_id' => $pasig->id, 'district_id' => $districtMap[1], 'name' => 'San Nicolas', 'alternate_name' => null, 'zip_code' => '1600'],
            ['city_id' => $pasig->id, 'district_id' => $districtMap[1], 'name' => 'Santa Cruz', 'alternate_name' => null, 'zip_code' => '1600'],
            ['city_id' => $pasig->id, 'district_id' => $districtMap[1], 'name' => 'Santa Rosa', 'alternate_name' => null, 'zip_code' => '1600'],
            ['city_id' => $pasig->id, 'district_id' => $districtMap[1], 'name' => 'Santo Tomas', 'alternate_name' => null, 'zip_code' => '1600'],
            ['city_id' => $pasig->id, 'district_id' => $districtMap[1], 'name' => 'Sumilang', 'alternate_name' => null, 'zip_code' => '1600'],
            ['city_id' => $pasig->id, 'district_id' => $districtMap[1], 'name' => 'Ugong', 'alternate_name' => 'Ortigas', 'zip_code' => '1604'],

            // DISTRICT 2 (Eastern Pasig)
            ['city_id' => $pasig->id, 'district_id' => $districtMap[2], 'name' => 'Dela Paz', 'alternate_name' => null, 'zip_code' => '1613'],
            ['city_id' => $pasig->id, 'district_id' => $districtMap[2], 'name' => 'Manggahan', 'alternate_name' => null, 'zip_code' => '1611'],
            ['city_id' => $pasig->id, 'district_id' => $districtMap[2], 'name' => 'Maybunga', 'alternate_name' => null, 'zip_code' => '1607'],
            ['city_id' => $pasig->id, 'district_id' => $districtMap[2], 'name' => 'Pinagbuhatan', 'alternate_name' => null, 'zip_code' => '1602'],
            ['city_id' => $pasig->id, 'district_id' => $districtMap[2], 'name' => 'Rosario', 'alternate_name' => null, 'zip_code' => '1609'],
            ['city_id' => $pasig->id, 'district_id' => $districtMap[2], 'name' => 'Santa Lucia', 'alternate_name' => null, 'zip_code' => '1608'],
            ['city_id' => $pasig->id, 'district_id' => $districtMap[2], 'name' => 'Santolan', 'alternate_name' => null, 'zip_code' => '1610'],
        ];

        foreach ($barangays as $barangay) {
            $authDb->table('barangays')->insert($barangay);
        }

        $totalBarangays = count($barangays);
        $this->command->info("✓ Pasig Barangays seeded successfully! ($totalBarangays barangays across 2 districts)");
    }
}

